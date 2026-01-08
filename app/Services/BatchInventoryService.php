<?php

namespace App\Services;

use App\Models\Barang;
use App\Models\BarangBatch;
use App\Models\BatchTransaction;
use App\Models\PembelianBarang;
use App\Models\Penjualan;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class BatchInventoryService
{
    /**
     * Mode batching: 'FIFO' atau 'FEFO'
     */
    protected $mode = 'FEFO'; // Default menggunakan FEFO

    /**
     * Set mode batching
     */
    public function setMode($mode)
    {
        $this->mode = in_array($mode, ['FIFO', 'FEFO']) ? $mode : 'FEFO';
        return $this;
    }

    /**
     * Tambah batch baru dari pembelian
     */
    public function addBatch(PembelianBarang $pembelian, $tanggalKadaluarsa = null)
    {
        return DB::transaction(function () use ($pembelian, $tanggalKadaluarsa) {
            $batch = BarangBatch::create([
                'barang_id' => $pembelian->barang_id,
                'pembelian_barang_id' => $pembelian->id,
                'batch_number' => BarangBatch::generateBatchNumber(),
                'harga_beli' => $pembelian->harga_satuan,
                'stok_awal' => $pembelian->jumlah,
                'stok_tersedia' => $pembelian->jumlah,
                'tanggal_masuk' => $pembelian->tanggal_pembelian,
                'tanggal_kadaluarsa' => $tanggalKadaluarsa,
                'keterangan' => $pembelian->keterangan,
            ]);

            // Record transaksi masuk
            BatchTransaction::create([
                'barang_batch_id' => $batch->id,
                'tipe' => 'masuk',
                'jumlah' => $pembelian->jumlah,
                'stok_sebelum' => 0,
                'stok_sesudah' => $pembelian->jumlah,
                'keterangan' => 'Stock masuk dari pembelian #' . $pembelian->id,
            ]);

            // Update kuantitas barang
            $pembelian->barang->syncKuantitasFromBatches();

            return $batch;
        });
    }

    /**
     * Kurangi stok dari batch menggunakan FIFO/FEFO
     */
    public function reduceStock(Barang $barang, int $jumlah, Penjualan $penjualan = null)
    {
        return DB::transaction(function () use ($barang, $jumlah, $penjualan) {
            // Get available batches dengan ordering sesuai mode
            $batches = $this->mode === 'FEFO' 
                ? $barang->getAvailableBatches(true)
                : $barang->getAvailableBatches(false);

            if ($batches->sum('stok_tersedia') < $jumlah) {
                throw new \Exception('Stok tidak mencukupi. Tersedia: ' . $batches->sum('stok_tersedia') . ', Dibutuhkan: ' . $jumlah);
            }

            $sisaJumlah = $jumlah;
            $transactions = [];

            foreach ($batches as $batch) {
                if ($sisaJumlah <= 0) break;

                $jumlahDiambil = min($batch->stok_tersedia, $sisaJumlah);
                $stokSebelum = $batch->stok_tersedia;

                // Update stok batch
                $batch->stok_tersedia -= $jumlahDiambil;
                $batch->save();

                // Record transaksi
                $transaction = BatchTransaction::create([
                    'barang_batch_id' => $batch->id,
                    'penjualan_id' => $penjualan ? $penjualan->id : null,
                    'tipe' => 'keluar',
                    'jumlah' => $jumlahDiambil,
                    'stok_sebelum' => $stokSebelum,
                    'stok_sesudah' => $batch->stok_tersedia,
                    'keterangan' => $penjualan 
                        ? 'Stock keluar untuk penjualan #' . $penjualan->id 
                        : 'Stock keluar (adjustment)',
                ]);

                $transactions[] = [
                    'batch' => $batch,
                    'transaction' => $transaction,
                    'jumlah_diambil' => $jumlahDiambil,
                ];

                $sisaJumlah -= $jumlahDiambil;
            }

            // Update kuantitas barang
            $barang->syncKuantitasFromBatches();

            return $transactions;
        });
    }

    /**
     * Kembalikan stok ke batch (untuk rollback penjualan)
     */
    public function returnStock(Penjualan $penjualan)
    {
        return DB::transaction(function () use ($penjualan) {
            // Ambil semua transaksi batch dari penjualan ini
            $batchTransactions = BatchTransaction::where('penjualan_id', $penjualan->id)
                ->where('tipe', 'keluar')
                ->get();

            foreach ($batchTransactions as $transaction) {
                $batch = $transaction->barangBatch;
                $stokSebelum = $batch->stok_tersedia;

                // Kembalikan stok
                $batch->stok_tersedia += $transaction->jumlah;
                $batch->save();

                // Record transaksi pengembalian
                BatchTransaction::create([
                    'barang_batch_id' => $batch->id,
                    'penjualan_id' => $penjualan->id,
                    'tipe' => 'masuk',
                    'jumlah' => $transaction->jumlah,
                    'stok_sebelum' => $stokSebelum,
                    'stok_sesudah' => $batch->stok_tersedia,
                    'keterangan' => 'Return stock dari pembatalan penjualan #' . $penjualan->id,
                ]);
            }

            // Update kuantitas barang
            $penjualan->barang->syncKuantitasFromBatches();

            return true;
        });
    }

    /**
     * Hitung HPP (Harga Pokok Penjualan) dari batch yang digunakan
     */
    public function calculateHPP(Penjualan $penjualan)
    {
        $transactions = BatchTransaction::where('penjualan_id', $penjualan->id)
            ->where('tipe', 'keluar')
            ->with('barangBatch')
            ->get();

        $totalHPP = 0;

        foreach ($transactions as $transaction) {
            $totalHPP += $transaction->barangBatch->harga_beli * $transaction->jumlah;
        }

        return $totalHPP;
    }

    /**
     * Get laporan batch yang akan kadaluarsa
     */
    public function getExpiringBatches($days = 30)
    {
        $targetDate = Carbon::now()->addDays($days)->toDateString();

        return BarangBatch::whereNotNull('tanggal_kadaluarsa')
            ->where('tanggal_kadaluarsa', '<=', $targetDate)
            ->where('tanggal_kadaluarsa', '>=', Carbon::now()->toDateString())
            ->where('stok_tersedia', '>', 0)
            ->with('barang')
            ->orderBy('tanggal_kadaluarsa', 'asc')
            ->get();
    }

    /**
     * Get laporan batch yang sudah kadaluarsa
     */
    public function getExpiredBatches()
    {
        return BarangBatch::whereNotNull('tanggal_kadaluarsa')
            ->where('tanggal_kadaluarsa', '<', Carbon::now()->toDateString())
            ->where('stok_tersedia', '>', 0)
            ->with('barang')
            ->orderBy('tanggal_kadaluarsa', 'asc')
            ->get();
    }

    /**
     * Adjustment stok batch (untuk koreksi stok)
     */
    public function adjustStock(BarangBatch $batch, int $newStock, string $keterangan = null)
    {
        return DB::transaction(function () use ($batch, $newStock, $keterangan) {
            $stokSebelum = $batch->stok_tersedia;
            $selisih = $newStock - $stokSebelum;

            $batch->stok_tersedia = $newStock;
            $batch->save();

            BatchTransaction::create([
                'barang_batch_id' => $batch->id,
                'tipe' => 'adjustment',
                'jumlah' => abs($selisih),
                'stok_sebelum' => $stokSebelum,
                'stok_sesudah' => $newStock,
                'keterangan' => $keterangan ?? 'Stock adjustment',
            ]);

            // Update kuantitas barang
            $batch->barang->syncKuantitasFromBatches();

            return $batch;
        });
    }
}
