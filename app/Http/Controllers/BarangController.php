<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Pemasok;
use App\Models\PembelianBarang;
use App\Models\PembayaranPembelian;
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

        Barang::create($request->only('nama', 'harga', 'harga_jual', 'kuantitas', 'pemasok_id'));

        return redirect()->route('barang.index')->with('success', 'Barang berhasil ditambahkan!');
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
        ]);

        $barang = Barang::findOrFail($id);
        $totalHarga = $request->input('jumlah') * $request->input('harga_beli');

        try {
            DB::beginTransaction();

            // Update stok barang
            $barang->increment('kuantitas', $request->input('jumlah'));

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

            // Buat record pembayaran pembelian (belum bayar)
            PembayaranPembelian::create([
                'pembelian_id' => $pembelian->id,
                'jumlah_bayar' => 0,
                'tanggal_pembayaran' => now(),
                'keterangan' => 'Belum dibayar - Penambahan stok',
            ]);

            DB::commit();

            return redirect()->route('barang.index')
                ->with('success', "Stok barang {$barang->nama} berhasil ditambahkan sebanyak {$request->input('jumlah')} unit!");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menambahkan stok: ' . $e->getMessage());
        }
    }
}