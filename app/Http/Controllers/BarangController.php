<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Pemasok;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

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
            'kuantitas' => 'required|integer|min:1',
            'pemasok_id' => 'nullable|exists:pemasoks,id',
        ]);

        Barang::create($request->only('nama', 'harga', 'kuantitas', 'pemasok_id'));

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
            'kuantitas' => 'required|integer|min:1',
            'pemasok_id' => 'nullable|exists:pemasoks,id',
        ]);

        $barang = Barang::findOrFail($id);
        $barang->update($request->only('nama', 'harga', 'kuantitas', 'pemasok_id'));

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
}