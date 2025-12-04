<?php
namespace App\Http\Controllers;

use App\Models\Penjualan;
use App\Models\Barang;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LaporanPenjualanController extends Controller
{
    public function index(Request $request)
    {
        $startDate = $request->input('start_date') ? Carbon::parse($request->input('start_date'))->startOfDay() : Carbon::now()->startOfMonth();
        $endDate = $request->input('end_date') ? Carbon::parse($request->input('end_date'))->endOfDay() : Carbon::now()->endOfMonth();

        $penjualans = Penjualan::with('barang', 'pelanggan')
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->get();

        $total = $penjualans->sum('total_harga');
        $count = $penjualans->count();
        $avg = $count ? ($total / $count) : 0;
        $itemsSold = $penjualans->sum('jumlah');

        // Top items
        $topItems = Penjualan::select('barang_id', DB::raw('SUM(jumlah) as total_sold'))
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->groupBy('barang_id')
            ->orderByDesc('total_sold')
            ->limit(5)
            ->get()
            ->map(function ($r) {
                $barang = Barang::find($r->barang_id);
                return [
                    'nama' => $barang ? $barang->nama : 'Unknown',
                    'total' => $r->total_sold,
                ];
            });

        // Prepare chart data
        $daily = Penjualan::select(DB::raw('DATE(tanggal) as date'), DB::raw('SUM(total_harga) as total'))
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->groupBy(DB::raw('DATE(tanggal)'))
            ->orderBy('date')
            ->get();

        $labels = $daily->pluck('date')->map(function ($d) { return Carbon::parse($d)->format('d M'); })->toArray();
        $data = $daily->pluck('total')->toArray();

        // Get all penjualan for table
        $penjualanList = Penjualan::with('barang', 'pelanggan')
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->orderBy('tanggal', 'desc')
            ->get();

        return view('laporan.penjualan', compact('total', 'count', 'avg', 'itemsSold', 'topItems', 'labels', 'data', 'startDate', 'endDate', 'penjualanList'));
    }
}
