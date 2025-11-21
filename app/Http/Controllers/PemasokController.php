<?php

namespace App\Http\Controllers;

use App\Models\Pemasok;
use Illuminate\Http\Request;

class PemasokController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $pemasoks = Pemasok::all();
        return view('master.pemasok', compact('pemasoks'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_pemasok' => 'required',
            'alamat_pemasok' => 'required',
        ]);

        Pemasok::create($validated);
        return response()->json(['success' => true, 'message' => 'Pemasok berhasil ditambahkan']);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Pemasok $pemasok)
    {
        $validated = $request->validate([
            'nama_pemasok' => 'required',
            'alamat_pemasok' => 'required',
        ]);

        $pemasok->update($validated);
        return response()->json(['success' => true, 'message' => 'Pemasok berhasil diubah']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Pemasok $pemasok)
    {
        $pemasok->delete();
        return response()->json(['success' => true, 'message' => 'Pemasok berhasil dihapus']);
    }
}
