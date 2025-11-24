<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pelanggan;

class PelangganController extends Controller
{
    public function index()
    {
        $pelanggans = Pelanggan::latest()->get();
        return view('master.pelanggan', compact('pelanggans'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_pelanggan' => 'required|string|max:255',
            'alamat' => 'nullable|string',
        ]);

        Pelanggan::create($request->only('nama_pelanggan', 'alamat'));

        return redirect()->route('pelanggan.index')->with('success', 'Pelanggan berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_pelanggan' => 'required|string|max:255',
            'alamat' => 'nullable|string',
        ]);

        $pelanggan = Pelanggan::findOrFail($id);
        $pelanggan->update($request->only('nama_pelanggan', 'alamat'));

        return redirect()->route('pelanggan.index')->with('success', 'Pelanggan berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $pelanggan = Pelanggan::findOrFail($id);
        $pelanggan->delete();
        return redirect()->route('pelanggan.index')->with('success', 'Pelanggan berhasil dihapus.');
    }
}
