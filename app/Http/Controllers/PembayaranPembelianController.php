<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\PembayaranPembelian;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PembayaranPembelianController extends Controller
{
    public function index()
    {
        $pembayarans = PembayaranPembelian::with('barang')
            ->orderBy('tanggal_pembayaran', 'desc')
            ->get();
        
        $barangs = Barang::all();
        
        return view('transaksi.pembayaranPembelian', compact('pembayarans', 'barangs'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'barang_id' => 'required|exists:barangs,id',
            'jumlah_bayar' => 'required|integer|min:1',
            'bukti_bayar' => 'nullable|file|mimes:pdf|max:5120',
            'tanggal_pembayaran' => 'nullable|date',
        ]);

        $barang = Barang::findOrFail($request->barang_id);

        // Handle file upload
        if ($request->hasFile('bukti_bayar')) {
            $file = $request->file('bukti_bayar');
            $filename = time() . '_' . str_replace(' ', '_', $barang->nama) . '.pdf';
            $path = $file->storeAs('bukti_bayar', $filename, 'public');
            $validated['bukti_bayar'] = $path;
        }

        // Set default tanggal_pembayaran to today if not provided
        if (empty($validated['tanggal_pembayaran'])) {
            $validated['tanggal_pembayaran'] = now()->toDateString();
        }

        // Create payment record
        $pembayaran = PembayaranPembelian::create($validated);

        return redirect()->route('pembayaran-pembelian.index')
            ->with('success', 'Pembayaran pembelian berhasil dicatat!');
    }

    public function destroy(PembayaranPembelian $pembayaranPembelian)
    {
        // Delete file if exists
        if ($pembayaranPembelian->bukti_bayar) {
            Storage::disk('public')->delete($pembayaranPembelian->bukti_bayar);
        }

        $pembayaranPembelian->delete();

        return redirect()->route('pembayaran-pembelian.index')
            ->with('success', 'Pembayaran pembelian berhasil dihapus!');
    }
}
