<?php

namespace App\Http\Controllers;

use App\Models\NotaHjual;
use Carbon\Carbon;
use Illuminate\Http\Request;

class HutangController extends Controller
{
    public function index(Request $request)
    {
        $startDate = $request->input('start_date') ? Carbon::parse($request->input('start_date'))->startOfDay() : Carbon::now()->startOfMonth();
        $endDate = $request->input('end_date') ? Carbon::parse($request->input('end_date'))->endOfDay() : Carbon::now()->endOfMonth();

        // Get all nota penjualan with payment status
        $notaHjuals = NotaHjual::with('pelanggan', 'pembayarans')
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->whereIn('status', ['selesai', 'sebagian', 'lunas'])
            ->orderBy('tanggal', 'desc')
            ->get();

        // Calculate outstanding debt for each
        $hutangList = [];
        $totalHutang = 0;
        $totalLunas = 0;
        $totalBelumBayar = 0;
        $totalKurangBayar = 0;

        foreach ($notaHjuals as $nota) {
            $totalPaid = $nota->pembayarans->sum('jumlah_bayar');
            $outstanding = $nota->total_harga - $totalPaid;

            $statusLabel = 'Lunas';
            if ($outstanding >= $nota->total_harga) {
                $statusLabel = 'Belum Bayar';
                $totalBelumBayar += $outstanding;
            } elseif ($outstanding > 0) {
                $statusLabel = 'Kurang Bayar';
                $totalKurangBayar += $outstanding;
            } else {
                $totalLunas += $nota->total_harga;
            }

            $hutangList[] = [
                'no_nota' => $nota->no_nota,
                'tanggal' => $nota->tanggal,
                'pelanggan' => $nota->pelanggan->nama_pelanggan ?? 'Walk-in',
                'total_harga' => $nota->total_harga,
                'total_bayar' => $totalPaid,
                'outstanding' => max(0, $outstanding),
                'status_label' => $statusLabel,
                'status' => $nota->status,
            ];

            $totalHutang += $nota->total_harga;
        }

        return view('laporan.hutang', compact(
            'hutangList',
            'totalHutang',
            'totalLunas',
            'totalBelumBayar',
            'totalKurangBayar',
            'startDate',
            'endDate'
        ));
    }
}
