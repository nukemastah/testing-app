<?php

namespace App\Http\Controllers;

use App\Models\PembelianBarang;
use App\Models\PembayaranPembelian;
use Carbon\Carbon;
use Illuminate\Http\Request;

class HutangController extends Controller
{
    public function index(Request $request)
    {
        $startDate = $request->input('start_date') ? Carbon::parse($request->input('start_date'))->startOfDay() : Carbon::now()->startOfMonth();
        $endDate = $request->input('end_date') ? Carbon::parse($request->input('end_date'))->endOfDay() : Carbon::now()->endOfMonth();

        // Get all pembelian with payment status (Hutang = Pembelian yang belum dibayar)
        $pembelians = PembelianBarang::with('pemasok', 'barang', 'pembayarans')
            ->whereBetween('tanggal_pembelian', [$startDate, $endDate])
            ->orderBy('tanggal_pembelian', 'desc')
            ->get();

        // Calculate outstanding hutang for each pembelian
        $hutangList = [];
        $totalHutang = 0;
        $totalLunas = 0;
        $totalBelumBayar = 0;
        $totalKurangBayar = 0;

        foreach ($pembelians as $pembelian) {
            $totalPaid = $pembelian->pembayarans->sum('jumlah_bayar');
            $outstanding = $pembelian->total_harga - $totalPaid;

            $statusLabel = $pembelian->status_pembayaran;
            if ($pembelian->status_pembayaran == 'lunas') {
                $totalLunas += $pembelian->total_harga;
            } elseif ($pembelian->status_pembayaran == 'belum bayar') {
                $totalBelumBayar += $outstanding;
            } else { // sebagian
                $totalKurangBayar += $outstanding;
            }

            $hutangList[] = [
                'id' => $pembelian->id,
                'tanggal' => $pembelian->tanggal_pembelian,
                'pemasok' => $pembelian->pemasok->nama_pemasok ?? '-',
                'barang' => $pembelian->barang->nama ?? '-',
                'jumlah' => $pembelian->jumlah,
                'total_harga' => $pembelian->total_harga,
                'total_bayar' => $totalPaid,
                'outstanding' => max(0, $outstanding),
                'status_label' => ucfirst($statusLabel),
                'status' => $pembelian->status_pembayaran,
            ];

            $totalHutang += $pembelian->total_harga;
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
