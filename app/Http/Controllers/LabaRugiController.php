<?php

namespace App\Http\Controllers;

use App\Models\DetailHjual;
use App\Models\NotaHjual;
use App\Models\PembelianBarang;
use Carbon\Carbon;
use Illuminate\Http\Request;

class LabaRugiController extends Controller
{
    public function index(Request $request)
    {
        $startDate = $request->input('start_date') ? Carbon::parse($request->input('start_date')) : Carbon::now()->startOfMonth();
        $endDate = $request->input('end_date') ? Carbon::parse($request->input('end_date')) : Carbon::now()->endOfMonth();

        // Revenue (Penjualan dari NotaHjual)
        $revenue = NotaHjual::whereBetween('tanggal', [$startDate, $endDate])
            ->whereIn('status', ['selesai', 'sebagian', 'lunas'])
            ->sum('total_harga');

        // Cost of Goods Sold (Pembelian)
        $cogs = PembelianBarang::whereBetween('tanggal_pembelian', [$startDate, $endDate])
            ->sum('total_harga');

        // Gross Profit
        $grossProfit = $revenue - $cogs;

        // Net Profit (simplified - no other expenses for now)
        $netProfit = $grossProfit;

        $profitMargin = $revenue > 0 ? round(($netProfit / $revenue) * 100, 2) : 0;

        return view('laporan.labaRugi', compact(
            'startDate',
            'endDate',
            'revenue',
            'cogs',
            'grossProfit',
            'netProfit',
            'profitMargin'
        ));
    }
}
