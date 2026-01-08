<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Pemasok;
use App\Models\PembelianBarang;
use App\Models\PembayaranPembelian;
use App\Services\BatchInventoryService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class BarangController extends Controller
{
    public function index(Request $request)
    {
        $barangs = Barang::latest()->paginate(7);
        $pemasoks = Pemasok::all();
        $deletedBarang = Barang::onlyTrashed()->latest()->first();

        // Filter berdasarkan waktu
        $dailyCount = Barang::whereDate('created_at', Carbon::today())->count();
        $weeklyCount = Barang::whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->count();
        $monthlyCount = Barang::whereMonth('created_at', Carbon::now()->month)->count();
        $yearlyCount = Barang::whereYear('created_at', Carbon::now()->year)->count();

        return view('barang.index', compact('barangs', 'pemasoks', 'deletedBarang', 'dailyCount', 'weeklyCount', 'monthlyCount', 'yearlyCount'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'harga' => 'required|integer|min:0',
            'harga_jual' => 'required|integer|min:0',
            'kuantitas' => 'required|integer|min:1',
            'pemasok_id' => 'nullable|exists:pemasoks,id',
        ]);

        try {
            DB::beginTransaction();

            // Create barang
            $barang = Barang::create($request->only('nama', 'harga', 'harga_jual', 'kuantitas', 'pemasok_id'));

            // Jika ada pemasok, buat record pembelian dan batch
            if ($request->pemasok_id) {
                $pembelian = PembelianBarang::create([
                    'barang_id' => $barang->id,
                    'pemasok_id' => $request->pemasok_id,
                    'jumlah' => $request->kuantitas,
                    'harga_satuan' => $request->harga,
                    'total_harga' => $request->kuantitas * $request->harga,
                    'tanggal_pembelian' => now(),
                    'status_pembayaran' => 'belum bayar',
                    'keterangan' => 'Pembelian awal - Barang baru ditambahkan',
                ]);

                // Buat batch untuk tracking stok
                $batchService = new BatchInventoryService();
                $batchService->addBatch($pembelian, null);
            } else {
                // Jika tidak ada pemasok, tetap buat batch untuk stok existing
                $batch = \App\Models\BarangBatch::create([
                    'barang_id' => $barang->id,
                    'pembelian_barang_id' => null,
                    'batch_number' => \App\Models\BarangBatch::generateBatchNumber(),
                    'harga_beli' => $request->harga,
                    'stok_awal' => $request->kuantitas,
                    'stok_tersedia' => $request->kuantitas,
                    'tanggal_masuk' => now(),
                    'tanggal_kadaluarsa' => null,
                    'keterangan' => 'Initial stock - Barang baru tanpa pemasok',
                ]);
            }

            DB::commit();

            return redirect()->route('barang.index')->with('success', 'Barang berhasil ditambahkan!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Gagal menambahkan barang: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        $barang = Barang::findOrFail($id);
        $pemasoks = Pemasok::all();
        $barangs = Barang::latest()->get();
        $deletedBarang = Barang::onlyTrashed()->latest()->first();

        // Filter berdasarkan waktu (untuk konsistensi dengan index)
        $dailyCount = Barang::whereDate('created_at', Carbon::today())->count();
        $weeklyCount = Barang::whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->count();
        $monthlyCount = Barang::whereMonth('created_at', Carbon::now()->month)->count();
        $yearlyCount = Barang::whereYear('created_at', Carbon::now()->year)->count();

        return view('barang.edit', compact('barang', 'pemasoks', 'barangs', 'deletedBarang', 'dailyCount', 'weeklyCount', 'monthlyCount', 'yearlyCount'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'harga' => 'required|integer|min:0',
            'harga_jual' => 'required|integer|min:0',
            'kuantitas' => 'required|integer|min:1',
            'pemasok_id' => 'nullable|exists:pemasoks,id',
        ]);

        $barang = Barang::findOrFail($id);
        $barang->update($request->only('nama', 'harga', 'harga_jual', 'kuantitas', 'pemasok_id'));

        return redirect()->route('barang.index')->with('success', 'Barang berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $barang = Barang::findOrFail($id);
        $barang->delete();

        return redirect()->route('barang.index')->with('success', 'Barang berhasil dihapus!');
    }

    public function undo()
    {
        $barang = Barang::onlyTrashed()->latest()->first();

        if ($barang) {
            $barang->restore();
            return redirect()->route('barang.index')->with('success', 'Barang berhasil dipulihkan!');
        }

        return redirect()->route('barang.index')->with('error', 'Tidak ada barang untuk di-undo.');
    }

    public function showAddStock($id)
    {
        $barang = Barang::findOrFail($id);
        $pemasoks = Pemasok::all();
        
        return view('barang.add-stock', compact('barang', 'pemasoks'));
    }

    public function addStock(Request $request, $id)
    {
        $request->validate([
            'jumlah' => 'required|integer|min:1',
            'harga_beli' => 'required|integer|min:0',
            'pemasok_id' => 'required|exists:pemasoks,id',
            'tanggal_pembelian' => 'required|date',
            'tanggal_kadaluarsa' => 'nullable|date|after:tanggal_pembelian',
        ]);

        $barang = Barang::findOrFail($id);
        $totalHarga = $request->input('jumlah') * $request->input('harga_beli');

        try {
            DB::beginTransaction();

            // Buat record pembelian
            $pembelian = PembelianBarang::create([
                'barang_id' => $id,
                'pemasok_id' => $request->input('pemasok_id'),
                'jumlah' => $request->input('jumlah'),
                'harga_satuan' => $request->input('harga_beli'),
                'total_harga' => $totalHarga,
                'tanggal_pembelian' => $request->input('tanggal_pembelian'),
                'status_pembayaran' => 'belum bayar',
                'keterangan' => 'Penambahan stok dari fitur tambah stok',
            ]);

            // Buat batch untuk tracking stok dengan FIFO/FEFO
            $batchService = new BatchInventoryService();
            $batchService->addBatch($pembelian, $request->input('tanggal_kadaluarsa'));

            // Update stok barang (akan otomatis ter-update melalui batch service)
            // Tapi kita tetap manual increment untuk kompatibilitas
            $barang->increment('kuantitas', $request->input('jumlah'));

            // Note: PembayaranPembelian akan dibuat manual oleh user di menu Pembayaran Pembelian
            // Status pembelian 'belum bayar' akan membuat pembelian ini muncul di dropdown

            DB::commit();

            return redirect()->route('barang.index')
                ->with('success', "Stok barang {$barang->nama} berhasil ditambahkan sebanyak {$request->input('jumlah')} unit! Silakan lakukan pembayaran di menu Pembayaran Pembelian.");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menambahkan stok: ' . $e->getMessage());
        }
    }

    public function showBatches($id)
    {
        $barang = Barang::with(['batches' => function($query) {
            $query->orderBy('tanggal_masuk', 'asc');
        }])->findOrFail($id);

        // Get batch yang akan kadaluarsa dalam 30 hari
        $batchService = new BatchInventoryService();
        $expiringBatches = $barang->batches()
            ->whereNotNull('tanggal_kadaluarsa')
            ->where('tanggal_kadaluarsa', '<=', now()->addDays(30))
            ->where('tanggal_kadaluarsa', '>=', now())
            ->where('stok_tersedia', '>', 0)
            ->orderBy('tanggal_kadaluarsa', 'asc')
            ->get();

        $batches = $barang->batches;

        return view('barang.batches', compact('barang', 'batches', 'expiringBatches'));
    }
}