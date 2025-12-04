<?php

namespace App\Http\Controllers;

use App\Models\Penjualan;
use App\Models\PembayaranPembelian;
use Carbon\Carbon;
use Illuminate\Http\Request;

class LabaRugiController extends Controller
{
    public function index(Request $request)
    {
        $startDate = $request->input('start_date') ? Carbon::parse($request->input('start_date')) : Carbon::now()->startOfMonth();
        $endDate = $request->input('end_date') ? Carbon::parse($request->input('end_date')) : Carbon::now()->endOfMonth();

        // Revenue (Penjualan)
        $revenue = Penjualan::whereBetween('tanggal', [$startDate, $endDate])
            ->sum('total_harga');

        // Cost of Goods Sold (Pembelian)
        $cogs = PembayaranPembelian::whereBetween('created_at', [$startDate, $endDate])
            ->sum('jumlah_bayar');

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
