<?php

namespace App\Http\Controllers;

use App\Models\Penjualan;
use Illuminate\Http\Request;

class HutangController extends Controller
{
    public function index(Request $request)
    {
        // Get all penjualan with payment status
        $hutangs = Penjualan::with('pelanggan', 'pembayarans')
            ->orderBy('created_at', 'desc')
            ->get();

        // Calculate outstanding debt for each
        $hutangList = [];
        $totalHutang = 0;
        $totalLunas = 0;
        $totalBelumBayar = 0;
        $totalKurangBayar = 0;

        foreach ($hutangs as $h) {
            $totalPaid = $h->pembayarans->sum('jumlah_bayar');
            $outstanding = $h->total_harga - $totalPaid;

            $status = 'lunas';
            if ($outstanding >= $h->total_harga) {
                $status = 'belum bayar';
                $totalBelumBayar += $outstanding;
            } elseif ($outstanding > 0) {
                $status = 'kurang bayar';
                $totalKurangBayar += $outstanding;
            } else {
                $totalLunas += $h->total_harga;
            }

            $hutangList[] = [
                'id' => $h->id,
                'tanggal' => $h->tanggal,
                'pelanggan' => $h->pelanggan->nama_pelanggan ?? 'Walk-in',
                'total_harga' => $h->total_harga,
                'total_bayar' => $totalPaid,
                'outstanding' => max(0, $outstanding),
                'status' => $status,
                'status_pembayaran' => $h->status_pembayaran,
            ];

            $totalHutang += $h->total_harga;
        }

        return view('laporan.hutang', compact(
            'hutangList',
            'totalHutang',
            'totalLunas',
            'totalBelumBayar',
            'totalKurangBayar'
        ));
    }
}
