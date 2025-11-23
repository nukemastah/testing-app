<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pelanggan;
use App\Models\Rekening;

class PelangganController extends Controller
{
    public function index()
    {
        $pelanggans = Pelanggan::with('rekening')->latest()->get();
        $rekenings = Rekening::all();
        return view('master.pelanggan', compact('pelanggans', 'rekenings'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_pelanggan' => 'required|string|max:255',
            'alamat' => 'nullable|string',
            'rekening_id' => 'nullable|exists:rekenings,id',
        ]);

        $pelanggan = Pelanggan::create([
            'nama_pelanggan' => $request->nama_pelanggan,
            'alamat' => $request->alamat,
            'rekening_id' => $request->rekening_id ?? null,
        ]);

        return redirect()->route('pelanggan.index')->with('success', 'Pelanggan berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_pelanggan' => 'required|string|max:255',
            'alamat' => 'nullable|string',
            'rekening_id' => 'nullable|exists:rekenings,id',
        ]);

        $pelanggan = Pelanggan::findOrFail($id);
        $pelanggan->update($request->only('nama_pelanggan', 'alamat', 'rekening_id'));

        return redirect()->route('pelanggan.index')->with('success', 'Pelanggan berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $pelanggan = Pelanggan::findOrFail($id);
        $pelanggan->delete();
        return redirect()->route('pelanggan.index')->with('success', 'Pelanggan berhasil dihapus.');
    }
}
