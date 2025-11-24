<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Penjualan;
use App\Models\Pelanggan;
use Illuminate\Http\Request;
use Carbon\Carbon;

class PenjualanController extends Controller
{
    public function index(Request $request)
    {
        // Ambil filter dari query string
        $filter = $request->query('filter', 'all');

        $query = Penjualan::with(['barang', 'pelanggan']);

        switch ($filter) {
            case 'daily':
                $query->whereDate('tanggal', Carbon::today());
                break;
            case 'weekly':
                $query->whereBetween('tanggal', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
                break;
            case 'monthly':
                $query->whereMonth('tanggal', Carbon::now()->month);
                break;
            case 'yearly':
                $query->whereYear('tanggal', Carbon::now()->year);
                break;
            default:
                // all data
                break;
        }

        $penjualans = $query->latest()->get();
        $barangs = Barang::all();
        $pelanggans = Pelanggan::all();

        return view('penjualan.index', compact('penjualans', 'barangs', 'pelanggans'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'barang_id' => 'required|exists:barangs,id',
            'jumlah' => 'required|integer|min:1',
            'harga_jual' => 'nullable|numeric|min:0',
            'pelanggan_id' => 'nullable|exists:pelanggans,id',
            'tenggat_pembayaran' => 'nullable|date',
        ]);

        $barang = Barang::findOrFail($request->barang_id);

        if ($barang->kuantitas < $request->jumlah) {
            return back()->with('error', 'Stok tidak mencukupi.');
        }

        $hargaSatuan = $request->harga_jual ?? $barang->harga;
        $totalHarga = $hargaSatuan * $request->jumlah;

        $penjualan = Penjualan::create([
            'barang_id' => $barang->id,
            'jumlah' => $request->jumlah,
            'total_harga' => $totalHarga,
            'tanggal' => now(),
            'pelanggan_id' => $request->pelanggan_id ?? null,
            'tenggat_pembayaran' => $request->tenggat_pembayaran ?? null,
            'status_pembayaran' => 'belum bayar',
        ]);

        // Kurangi stok barang
        $barang->kuantitas -= $request->jumlah;
        $barang->save();

        return redirect()->route('penjualan.index')->with('success', 'Penjualan berhasil.');
    }

public function destroy($id)
{
    $penjualan = Penjualan::with('barang')->findOrFail($id);

    // Cek apakah relasi barang tersedia
    if ($penjualan->barang) {
        $barang = $penjualan->barang;
        $barang->kuantitas += $penjualan->jumlah;
        $barang->save();
    }

    $penjualan->delete();

    return redirect()->route('penjualan.index')->with('success', 'Penjualan berhasil dibatalkan.');
}
}
