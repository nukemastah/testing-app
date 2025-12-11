<?php

namespace App\Http\Controllers;

use App\Models\NotaHjual;
use App\Models\PembayaranPembelian;
use App\Models\PembayaranPenjualan;
use Carbon\Carbon;
use Illuminate\Http\Request;

class GeneralLedgerController extends Controller
{
    public function index(Request $request)
    {
        $startDate = $request->input('start_date') ? Carbon::parse($request->input('start_date'))->startOfDay() : Carbon::now()->startOfMonth();
        $endDate = $request->input('end_date') ? Carbon::parse($request->input('end_date'))->endOfDay() : Carbon::now()->endOfMonth();

        // Collect all transactions for GL posting
        $entries = [];

        // Sales transactions (from NotaHjual)
        $notaHjuals = NotaHjual::with('pelanggan')
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->whereIn('status', ['selesai', 'sebagian', 'lunas'])
            ->get();

        foreach ($notaHjuals as $nota) {
            $pelangganName = $nota->pelanggan ? $nota->pelanggan->nama_pelanggan : 'Walk-in';
            $entries[] = [
                'date' => $nota->tanggal,
                'account' => 'Piutang Dagang (A)',
                'debit' => $nota->total_harga,
                'credit' => 0,
                'description' => "Penjualan {$nota->no_nota} - {$pelangganName}",
                'type' => 'sales'
            ];
            $entries[] = [
                'date' => $nota->tanggal,
                'account' => 'Pendapatan Penjualan (I)',
                'debit' => 0,
                'credit' => $nota->total_harga,
                'description' => "Penjualan {$nota->no_nota}",
                'type' => 'sales'
            ];
        }

        // Purchase payment transactions (from PembayaranPembelian)
        // Only include actual payments (exclude jumlah_bayar = 0)
        $pembelians = PembayaranPembelian::with('pembelian.barang', 'pembelian.pemasok')
            ->whereBetween('tanggal_pembayaran', [$startDate, $endDate])
            ->where('jumlah_bayar', '>', 0)
            ->get();

        foreach ($pembelians as $pb) {
            $barangNama = $pb->pembelian && $pb->pembelian->barang 
                ? $pb->pembelian->barang->nama 
                : 'Barang';
            $pemasokNama = $pb->pembelian && $pb->pembelian->pemasok 
                ? ' dari ' . $pb->pembelian->pemasok->nama_pemasok 
                : '';
            
            $entries[] = [
                'date' => $pb->tanggal_pembayaran,
                'account' => 'Biaya Pembelian (E)',
                'debit' => $pb->jumlah_bayar,
                'credit' => 0,
                'description' => "Pembelian {$barangNama}{$pemasokNama} - Rp" . number_format($pb->jumlah_bayar, 0),
                'type' => 'purchase'
            ];
            $entries[] = [
                'date' => $pb->tanggal_pembayaran,
                'account' => 'Utang Dagang (L)',
                'debit' => 0,
                'credit' => $pb->jumlah_bayar,
                'description' => "Pembelian {$barangNama}{$pemasokNama}",
                'type' => 'purchase'
            ];
        }

        // Payment transactions (Sales)
        $payments = PembayaranPenjualan::with('notaHjual.pelanggan')
            ->whereBetween('tanggal_pembayaran', [$startDate, $endDate])
            ->get();

        foreach ($payments as $payment) {
            $pelangganName = $payment->notaHjual && $payment->notaHjual->pelanggan 
                ? $payment->notaHjual->pelanggan->nama_pelanggan 
                : 'Walk-in';
            
            $entries[] = [
                'date' => $payment->tanggal_pembayaran,
                'account' => 'Kas/Bank (A)',
                'debit' => $payment->jumlah_bayar,
                'credit' => 0,
                'description' => "Pembayaran {$payment->no_nota} - {$pelangganName}",
                'type' => 'payment'
            ];
            $entries[] = [
                'date' => $payment->tanggal_pembayaran,
                'account' => 'Piutang Dagang (A)',
                'debit' => 0,
                'credit' => $payment->jumlah_bayar,
                'description' => "Pembayaran {$payment->no_nota}",
                'type' => 'payment'
            ];
        }

        // Sort by date
        usort($entries, function ($a, $b) {
            return strtotime($a['date']) - strtotime($b['date']);
        });

        // Calculate totals
        $totalDebit = array_sum(array_column($entries, 'debit'));
        $totalCredit = array_sum(array_column($entries, 'credit'));

        return view('laporan.generalLedger', compact(
            'entries',
            'startDate',
            'endDate',
            'totalDebit',
            'totalCredit'
        ));
    }
}
