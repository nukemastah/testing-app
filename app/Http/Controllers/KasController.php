<?php
namespace App\Http\Controllers;

use App\Models\PembayaranPenjualan;
use App\Models\PembayaranPembelian;
use Carbon\Carbon;
use Illuminate\Http\Request;

class KasController extends Controller
{
    public function index(Request $request)
    {
        $startDate = $request->input('start_date') ? Carbon::parse($request->input('start_date'))->startOfDay() : Carbon::now()->startOfMonth();
        $endDate = $request->input('end_date') ? Carbon::parse($request->input('end_date'))->endOfDay() : Carbon::now()->endOfMonth();

        $entries = [];

        $incomes = PembayaranPenjualan::with('penjualan.pelanggan')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get();

        foreach ($incomes as $inc) {
            $entries[] = [
                'date' => $inc->created_at->toDateString(),
                'description' => 'Pembayaran Penjualan #' . ($inc->penjualan ? $inc->penjualan->id : '-'),
                'debit' => $inc->jumlah_bayar,
                'credit' => 0,
            ];
        }

        $expenses = PembayaranPembelian::with('barang')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get();

        foreach ($expenses as $exp) {
            $entries[] = [
                'date' => $exp->created_at->toDateString(),
                'description' => 'Pembelian ' . ($exp->barang ? $exp->barang->nama : '-'),
                'debit' => 0,
                'credit' => $exp->jumlah_bayar,
            ];
        }

        usort($entries, function ($a, $b) {
            return strtotime($a['date']) - strtotime($b['date']);
        });

        $running = 0;
        foreach ($entries as &$e) {
            $running += ($e['debit'] - $e['credit']);
            $e['saldo'] = $running;
        }

        return view('laporan.kas', compact('entries', 'startDate', 'endDate'));
    }
}
