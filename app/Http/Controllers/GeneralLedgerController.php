<?php

namespace App\Http\Controllers;

use App\Models\Penjualan;
use App\Models\PembayaranPembelian;
use App\Models\PembayaranPenjualan;
use Carbon\Carbon;
use Illuminate\Http\Request;

class GeneralLedgerController extends Controller
{
    public function index(Request $request)
    {
        $startDate = $request->input('start_date') ? Carbon::parse($request->input('start_date')) : Carbon::now()->startOfMonth();
        $endDate = $request->input('end_date') ? Carbon::parse($request->input('end_date')) : Carbon::now()->endOfMonth();

        // Collect all transactions for GL posting
        $entries = [];

        // Sales transactions
        $penjualans = Penjualan::with('barang', 'pelanggan')
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->get();

        foreach ($penjualans as $p) {
            $pelangganName = $p->pelanggan ? $p->pelanggan->nama_pelanggan : 'Walk-in';
            $entries[] = [
                'date' => $p->tanggal,
                'account' => 'Piutang Dagang (A)',
                'debit' => $p->total_harga,
                'credit' => 0,
                'description' => "Penjualan #{$p->id} - {$pelangganName}",
                'type' => 'sales'
            ];
            $entries[] = [
                'date' => $p->tanggal,
                'account' => 'Pendapatan Penjualan (I)',
                'debit' => 0,
                'credit' => $p->total_harga,
                'description' => "Penjualan #{$p->id}",
                'type' => 'sales'
            ];
        }

        // Purchase payment transactions
        $pembelians = PembayaranPembelian::with('barang')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get();

        foreach ($pembelians as $pb) {
            $entries[] = [
                'date' => $pb->created_at->toDateString(),
                'account' => 'Biaya Pembelian (E)',
                'debit' => $pb->jumlah_bayar,
                'credit' => 0,
                'description' => "Pembelian {$pb->barang->nama} - Rp" . number_format($pb->jumlah_bayar, 0),
                'type' => 'purchase'
            ];
            $entries[] = [
                'date' => $pb->created_at->toDateString(),
                'account' => 'Utang Dagang (L)',
                'debit' => 0,
                'credit' => $pb->jumlah_bayar,
                'description' => "Pembelian {$pb->barang->nama}",
                'type' => 'purchase'
            ];
        }

        // Payment transactions (Sales)
        $payments = PembayaranPenjualan::with('penjualan.pelanggan')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get();

        foreach ($payments as $payment) {
            $entries[] = [
                'date' => $payment->created_at->toDateString(),
                'account' => 'Kas/Bank (A)',
                'debit' => $payment->jumlah_bayar,
                'credit' => 0,
                'description' => "Pembayaran Penjualan #{$payment->penjualan->id}",
                'type' => 'payment'
            ];
            $entries[] = [
                'date' => $payment->created_at->toDateString(),
                'account' => 'Piutang Dagang (A)',
                'debit' => 0,
                'credit' => $payment->jumlah_bayar,
                'description' => "Pembayaran Penjualan #{$payment->penjualan->id}",
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
