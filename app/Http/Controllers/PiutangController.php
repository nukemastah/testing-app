<?php
namespace App\Http\Controllers;

use App\Models\Penjualan;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PiutangController extends Controller
{
    public function index(Request $request)
    {
        $startDate = $request->input('start_date') ? Carbon::parse($request->input('start_date'))->startOfDay() : Carbon::now()->startOfMonth();
        $endDate = $request->input('end_date') ? Carbon::parse($request->input('end_date'))->endOfDay() : Carbon::now()->endOfMonth();

        $penjualans = Penjualan::with('pelanggan', 'pembayarans')
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->get();

        $piutangList = [];
        $totalPiutang = 0;
        $totalPaid = 0;
        $totalOutstanding = 0;

        foreach ($penjualans as $p) {
            $paid = $p->pembayarans->sum('jumlah_bayar');
            $outstanding = $p->total_harga - $paid;

            $piutangList[] = [
                'tanggal' => $p->tanggal,
                'pelanggan' => $p->pelanggan ? $p->pelanggan->nama_pelanggan : 'Walk-in',
                'nomor_invoice' => $p->id,
                'total_harga' => $p->total_harga,
                'total_bayar' => $paid,
                'outstanding' => $outstanding,
            ];

            $totalPiutang += $p->total_harga;
            $totalPaid += $paid;
            $totalOutstanding += $outstanding;
        }

        return view('laporan.piutang', compact('piutangList', 'totalPiutang', 'totalPaid', 'totalOutstanding', 'startDate', 'endDate'));
    }
}
