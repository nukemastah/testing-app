<?php

namespace App\Observers;

use App\Models\DetailHjual;
use App\Services\BatchInventoryService;
use Illuminate\Support\Facades\Log;

class DetailHjualObserver
{
    protected $batchService;

    public function __construct()
    {
        $this->batchService = new BatchInventoryService();
        // Set mode FEFO (First Expired First Out) sebagai default
        $this->batchService->setMode('FEFO');
    }

    /**
     * Handle the DetailHjual "created" event.
     * Ini akan dipanggil setelah detail penjualan dibuat
     */
    public function created(DetailHjual $detailHjual): void
    {
        try {
            // Kurangi stok dari batch menggunakan FIFO/FEFO
            // Note: Trigger database sudah mengurangi kuantitas di tabel barangs,
            // jadi kita hanya perlu mengelola batch transactions
            $this->batchService->reduceStock(
                $detailHjual->barang,
                $detailHjual->quantity,
                null // Kita tidak punya penjualan_id di sini karena menggunakan nota system
            );
        } catch (\Exception $e) {
            // Log error tapi jangan break transaksi karena trigger sudah handle stok
            Log::error('BatchInventoryService Error pada DetailHjual created: ' . $e->getMessage(), [
                'detail_hjual_id' => $detailHjual->id,
                'barang_id' => $detailHjual->barang_id,
                'quantity' => $detailHjual->quantity,
            ]);
            
            // Jika batch tidak cukup, rollback dengan throw exception
            if (strpos($e->getMessage(), 'Stok tidak mencukupi') !== false) {
                throw $e;
            }
        }
    }

    /**
     * Handle the DetailHjual "deleting" event.
     * Ini akan dipanggil sebelum detail penjualan dihapus
     */
    public function deleting(DetailHjual $detailHjual): void
    {
        try {
            // Kembalikan stok ke batch
            // Note: Trigger database sudah menambah kuantitas di tabel barangs,
            // jadi kita hanya perlu mengelola batch transactions untuk kembalikan stok ke batch yang sesuai
            
            // Kita perlu kembalikan stok ke batch (FIFO reverse)
            // Untuk simplicity, kita akan kembalikan ke batch yang paling baru
            $batches = $detailHjual->barang->batches()
                ->orderBy('tanggal_masuk', 'desc')
                ->orderBy('id', 'desc')
                ->get();

            $sisaQty = $detailHjual->quantity;
            
            foreach ($batches as $batch) {
                if ($sisaQty <= 0) break;
                
                // Kembalikan stok ke batch
                $batch->stok_tersedia += min($sisaQty, $detailHjual->quantity);
                $batch->save();
                
                $sisaQty -= min($sisaQty, $detailHjual->quantity);
            }

            // Sinkronkan kuantitas barang
            $detailHjual->barang->syncKuantitasFromBatches();
            
        } catch (\Exception $e) {
            Log::error('BatchInventoryService Error pada DetailHjual deleting: ' . $e->getMessage(), [
                'detail_hjual_id' => $detailHjual->id,
                'barang_id' => $detailHjual->barang_id,
                'quantity' => $detailHjual->quantity,
            ]);
        }
    }
}
