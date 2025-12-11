<?php
namespace App\Http\Controllers;

use App\Models\NotaHjual;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PiutangController extends Controller
{
    public function index(Request $request)
    {
        $startDate = $request->input('start_date') ? Carbon::parse($request->input('start_date'))->startOfDay() : Carbon::now()->startOfMonth();
        $endDate = $request->input('end_date') ? Carbon::parse($request->input('end_date'))->endOfDay() : Carbon::now()->endOfMonth();

        $notaHjuals = NotaHjual::with('pelanggan', 'pembayarans')
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->whereIn('status', ['selesai', 'sebagian', 'lunas'])
            ->orderBy('tanggal', 'desc')
            ->get();

        $piutangList = [];
        $totalPiutang = 0;
        $totalPaid = 0;
        $totalOutstanding = 0;

        foreach ($notaHjuals as $nota) {
            $paid = $nota->pembayarans->sum('jumlah_bayar');
            $outstanding = $nota->total_harga - $paid;

            // Only include if there's outstanding balance
            if ($outstanding > 0) {
                $piutangList[] = [
                    'tanggal' => $nota->tanggal,
                    'pelanggan' => $nota->pelanggan ? $nota->pelanggan->nama_pelanggan : 'Walk-in',
                    'no_nota' => $nota->no_nota,
                    'total_harga' => $nota->total_harga,
                    'total_bayar' => $paid,
                    'outstanding' => $outstanding,
                    'status' => $nota->status,
                ];

                $totalOutstanding += $outstanding;
            }

            $totalPiutang += $nota->total_harga;
            $totalPaid += $paid;
        }

        return view('laporan.piutang', compact('piutangList', 'totalPiutang', 'totalPaid', 'totalOutstanding', 'startDate', 'endDate'));
    }
}
