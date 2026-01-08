<?php
namespace App\Http\Controllers;

use App\Models\NotaHjual;
use App\Models\DetailHjual;
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

        // Get nota hjual in date range
        $notaHjuals = NotaHjual::with(['pelanggan', 'details.barang' => function($query) {
                $query->withTrashed();
            }])
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->get();

        $total = $notaHjuals->sum('total_harga');
        $count = $notaHjuals->count();
        $avg = $count ? ($total / $count) : 0;
        $itemsSold = $notaHjuals->sum(function($nota) {
            return $nota->details->sum('quantity');
        });

        // Top items from DetailHjual
        $topItems = DetailHjual::select('barang_id', DB::raw('SUM(quantity) as total_sold'))
            ->whereHas('notaHjual', function($q) use ($startDate, $endDate) {
                $q->whereBetween('tanggal', [$startDate, $endDate]);
            })
            ->groupBy('barang_id')
            ->orderByDesc('total_sold')
            ->limit(5)
            ->get()
            ->map(function ($r) {
                $barang = Barang::withTrashed()->find($r->barang_id);
                $nama = $barang ? $barang->nama : 'Barang Dihapus';
                if ($barang && $barang->trashed()) {
                    $nama .= ' (Dihapus)';
                }
                return [
                    'nama' => $nama,
                    'total' => $r->total_sold,
                ];
            });

        // Prepare chart data from NotaHjual
        $daily = NotaHjual::select(DB::raw('DATE(tanggal) as date'), DB::raw('SUM(total_harga) as total'))
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->groupBy(DB::raw('DATE(tanggal)'))
            ->orderBy('date')
            ->get();

        $labels = $daily->pluck('date')->map(function ($d) { return Carbon::parse($d)->format('d M'); })->toArray();
        $data = $daily->pluck('total')->toArray();

        // Get all nota hjual for table
        $penjualanList = NotaHjual::with(['pelanggan', 'details.barang' => function($query) {
                $query->withTrashed();
            }])
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->orderBy('tanggal', 'desc')
            ->get();

        return view('laporan.penjualan', compact('total', 'count', 'avg', 'itemsSold', 'topItems', 'labels', 'data', 'startDate', 'endDate', 'penjualanList'));
    }
}
