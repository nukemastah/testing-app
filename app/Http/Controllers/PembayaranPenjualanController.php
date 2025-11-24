<?php

namespace App\Http\Controllers;

use App\Models\Penjualan;
use App\Models\PembayaranPenjualan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PembayaranPenjualanController extends Controller
{
    public function index()
    {
        $pembayarans = PembayaranPenjualan::with('penjualan.pelanggan')
            ->orderBy('tanggal_pembayaran', 'desc')
            ->get();
        
        $penjualans = Penjualan::with('pelanggan')->get();
        
        return view('transaksi.pembayaranPenjualan', compact('pembayarans', 'penjualans'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'penjualan_id' => 'required|exists:penjualans,id',
            'jumlah_bayar' => 'required|integer|min:1',
            'bukti_bayar' => 'nullable|file|mimes:pdf|max:5120',
            'tanggal_pembayaran' => 'nullable|date',
        ]);

        $penjualan = Penjualan::findOrFail($request->penjualan_id);

        // Handle file upload
        if ($request->hasFile('bukti_bayar')) {
            $file = $request->file('bukti_bayar');
            $filename = time() . '_' . str_replace(' ', '_', $penjualan->pelanggan->nama_pelanggan) . '.pdf';
            $path = $file->storeAs('bukti_bayar', $filename, 'public');
            $validated['bukti_bayar'] = $path;
        }

        // Set default tanggal_pembayaran to today if not provided
        if (empty($validated['tanggal_pembayaran'])) {
            $validated['tanggal_pembayaran'] = now()->toDateString();
        }

        // Create payment record
        $pembayaran = $penjualan->pembayarans()->create($validated);

        // Update penjualan payment status
        $this->updatePaymentStatus($penjualan);

        return redirect()->route('pembayaran-penjualan.index')
            ->with('success', 'Pembayaran berhasil dicatat!');
    }

    public function destroy(PembayaranPenjualan $pembayaranPenjualan)
    {
        $penjualan = $pembayaranPenjualan->penjualan;
        
        // Delete file if exists
        if ($pembayaranPenjualan->bukti_bayar) {
            Storage::disk('public')->delete($pembayaranPenjualan->bukti_bayar);
        }

        $pembayaranPenjualan->delete();

        // Recalculate payment status
        $this->updatePaymentStatus($penjualan);

        return redirect()->route('pembayaran-penjualan.index')
            ->with('success', 'Pembayaran berhasil dihapus!');
    }

    private function updatePaymentStatus(Penjualan $penjualan)
    {
        // Calculate total paid
        $totalPaid = $penjualan->pembayarans()->sum('jumlah_bayar');
        $totalHarga = $penjualan->total_harga;

        // Determine status
        if ($totalPaid >= $totalHarga) {
            $status = 'lunas';
        } elseif ($totalPaid > 0) {
            $status = 'kurang bayar';
        } else {
            $status = 'belum bayar';
        }

        // Check if late (only if tenggat_pembayaran is set and payment is not yet complete)
        if ($penjualan->tenggat_pembayaran && $totalPaid > 0 && $totalPaid < $totalHarga) {
            if (now()->toDateString() > $penjualan->tenggat_pembayaran) {
                $status = 'telat bayar';
            }
        }

        $penjualan->update(['status_pembayaran' => $status]);
    }
}
