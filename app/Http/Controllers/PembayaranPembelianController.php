<?php

namespace App\Http\Controllers;

use App\Models\PembelianBarang;
use App\Models\PembayaranPembelian;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PembayaranPembelianController extends Controller
{
    public function index()
    {
        $pembayarans = PembayaranPembelian::with('pembelian.barang', 'pembelian.pemasok')
            ->orderBy('tanggal_pembayaran', 'desc')
            ->get();
        
        $pembelians = PembelianBarang::with('barang', 'pemasok')
            ->whereIn('status_pembayaran', ['belum bayar', 'sebagian'])
            ->orderBy('tanggal_pembelian', 'desc')
            ->get();
        
        return view('transaksi.pembayaranPembelian', compact('pembayarans', 'pembelians'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'pembelian_id' => 'required|exists:pembelian_barangs,id',
            'jumlah_bayar' => 'required|integer|min:1',
            'bukti_bayar' => 'nullable|file|mimes:pdf|max:5120',
            'tanggal_pembayaran' => 'nullable|date',
        ]);

        $pembelian = PembelianBarang::findOrFail($request->pembelian_id);

        // Handle file upload
        if ($request->hasFile('bukti_bayar')) {
            $file = $request->file('bukti_bayar');
            $filename = time() . '_' . str_replace(' ', '_', $pembelian->barang->nama) . '.pdf';
            $path = $file->storeAs('bukti_bayar', $filename, 'public');
            $validated['bukti_bayar'] = $path;
        }

        // Set default tanggal_pembayaran to today if not provided
        if (empty($validated['tanggal_pembayaran'])) {
            $validated['tanggal_pembayaran'] = now()->toDateString();
        }

        // Create payment record
        $pembayaran = PembayaranPembelian::create($validated);

        // Update status pembayaran
        $this->updatePaymentStatus($pembelian);

        return redirect()->route('pembayaran-pembelian.index')
            ->with('success', 'Pembayaran pembelian berhasil dicatat!');
    }

    public function destroy(PembayaranPembelian $pembayaranPembelian)
    {
        $pembelian = $pembayaranPembelian->pembelian;

        // Delete file if exists
        if ($pembayaranPembelian->bukti_bayar) {
            Storage::disk('public')->delete($pembayaranPembelian->bukti_bayar);
        }

        $pembayaranPembelian->delete();

        // Recalculate payment status
        if ($pembelian) {
            $this->updatePaymentStatus($pembelian);
        }

        return redirect()->route('pembayaran-pembelian.index')
            ->with('success', 'Pembayaran pembelian berhasil dihapus!');
    }

    private function updatePaymentStatus(PembelianBarang $pembelian)
    {
        // Calculate total paid
        $totalPaid = PembayaranPembelian::where('pembelian_id', $pembelian->id)
            ->sum('jumlah_bayar');
        $totalHarga = $pembelian->total_harga;

        // Determine status based on payment
        if ($totalPaid >= $totalHarga) {
            $status = 'lunas';
        } elseif ($totalPaid > 0) {
            $status = 'sebagian';
        } else {
            $status = 'belum bayar';
        }

        $pembelian->update(['status_pembayaran' => $status]);
    }

    public function getDetailPembelian($id)
    {
        $pembelian = PembelianBarang::with('barang', 'pemasok')
            ->findOrFail($id);
        
        $totalPaid = PembayaranPembelian::where('pembelian_id', $id)
            ->sum('jumlah_bayar');
        
        return response()->json([
            'barang_nama' => $pembelian->barang->nama,
            'jumlah' => $pembelian->jumlah,
            'harga_satuan' => $pembelian->harga_satuan,
            'total_harga' => $pembelian->total_harga,
            'total_paid' => $totalPaid,
            'outstanding' => $pembelian->total_harga - $totalPaid,
            'status' => $pembelian->status_pembayaran,
        ]);
    }
}
