<?php

namespace App\Http\Controllers;

use App\Models\NotaHjual;
use App\Models\PembayaranPenjualan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PembayaranPenjualanController extends Controller
{
    public function index()
    {
        $pembayarans = PembayaranPenjualan::with('notaHjual.pelanggan')
            ->orderBy('tanggal_pembayaran', 'desc')
            ->get();
        
        // Ambil nota yang belum lunas (status 'selesai' atau 'sebagian', tapi tidak 'lunas')
        $notaHjuals = NotaHjual::with('pelanggan', 'details')
            ->whereIn('status', ['selesai', 'sebagian'])
            ->orderBy('tanggal', 'desc')
            ->get();
        
        return view('transaksi.pembayaranPenjualan', compact('pembayarans', 'notaHjuals'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'no_nota' => 'required|exists:nota_hjuals,no_nota',
            'jumlah_bayar' => 'required|integer|min:1',
            'bukti_bayar' => 'nullable|file|mimes:pdf|max:5120',
            'tanggal_pembayaran' => 'nullable|date',
        ]);

        $notaHjual = NotaHjual::with('pelanggan')->findOrFail($request->no_nota);

        // Handle file upload
        if ($request->hasFile('bukti_bayar')) {
            $file = $request->file('bukti_bayar');
            $filename = time() . '_' . str_replace(' ', '_', $notaHjual->pelanggan->nama_pelanggan ?? 'pelanggan') . '.pdf';
            $path = $file->storeAs('bukti_bayar', $filename, 'public');
            $validated['bukti_bayar'] = $path;
        }

        // Set default tanggal_pembayaran to today if not provided
        if (empty($validated['tanggal_pembayaran'])) {
            $validated['tanggal_pembayaran'] = now()->toDateString();
        }

        // Create payment record
        PembayaranPenjualan::create($validated);

        // Update payment status
        $this->updatePaymentStatus($notaHjual);

        return redirect()->route('pembayaran-penjualan.index')
            ->with('success', 'Pembayaran untuk nota ' . $notaHjual->no_nota . ' berhasil dicatat!');
    }

    public function destroy(PembayaranPenjualan $pembayaranPenjualan)
    {
        $notaHjual = $pembayaranPenjualan->notaHjual;
        
        // Delete file if exists
        if ($pembayaranPenjualan->bukti_bayar) {
            Storage::disk('public')->delete($pembayaranPenjualan->bukti_bayar);
        }

        $pembayaranPenjualan->delete();

        // Recalculate payment status
        if ($notaHjual) {
            $this->updatePaymentStatus($notaHjual);
        }

        return redirect()->route('pembayaran-penjualan.index')
            ->with('success', 'Pembayaran berhasil dihapus!');
    }

    public function getDetailNota($no_nota)
    {
        $notaHjual = NotaHjual::with('pelanggan')->findOrFail($no_nota);
        
        // Calculate total paid so far
        $totalPaid = PembayaranPenjualan::where('no_nota', $no_nota)
            ->sum('jumlah_bayar');
        
        $outstanding = $notaHjual->total_harga - $totalPaid;
        
        return response()->json([
            'pelanggan_nama' => $notaHjual->pelanggan->nama_pelanggan ?? 'Guest',
            'total_harga' => $notaHjual->total_harga,
            'total_paid' => $totalPaid,
            'outstanding' => max(0, $outstanding),
            'status' => $notaHjual->status,
        ]);
    }

    private function updatePaymentStatus(NotaHjual $notaHjual)
    {
        // Calculate total paid
        $totalPaid = PembayaranPenjualan::where('no_nota', $notaHjual->no_nota)
            ->sum('jumlah_bayar');
        $totalHarga = $notaHjual->total_harga;

        // Determine status based on payment
        if ($totalPaid >= $totalHarga) {
            $status = 'lunas';
        } elseif ($totalPaid > 0) {
            $status = 'sebagian';
        } else {
            $status = 'selesai'; // No payment yet, but transaction is complete
        }

        $notaHjual->update(['status' => $status]);
    }
}
