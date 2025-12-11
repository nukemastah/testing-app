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

        // Pembayaran Penjualan (Income - Debit)
        $incomes = PembayaranPenjualan::with('notaHjual.pelanggan')
            ->whereBetween('tanggal_pembayaran', [$startDate, $endDate])
            ->get();

        foreach ($incomes as $inc) {
            $pelangganNama = $inc->notaHjual && $inc->notaHjual->pelanggan 
                ? $inc->notaHjual->pelanggan->nama_pelanggan 
                : 'Pelanggan';
            
            $entries[] = [
                'date' => $inc->tanggal_pembayaran,
                'description' => 'Pembayaran Penjualan ' . $inc->no_nota . ' - ' . $pelangganNama,
                'debit' => $inc->jumlah_bayar,
                'credit' => 0,
            ];
        }

        // Pembayaran Pembelian (Expense - Kredit)
        $expenses = PembayaranPembelian::with('pembelian.barang', 'pembelian.pemasok')
            ->whereBetween('tanggal_pembayaran', [$startDate, $endDate])
            ->get();

        foreach ($expenses as $exp) {
            $barangNama = $exp->pembelian && $exp->pembelian->barang 
                ? $exp->pembelian->barang->nama 
                : 'Barang';
            $pemasokNama = $exp->pembelian && $exp->pembelian->pemasok 
                ? ' dari ' . $exp->pembelian->pemasok->nama_pemasok 
                : '';
            
            $entries[] = [
                'date' => $exp->tanggal_pembayaran,
                'description' => 'Pembayaran Pembelian ' . $barangNama . $pemasokNama,
                'debit' => 0,
                'credit' => $exp->jumlah_bayar,
            ];
        }

        // Sort by date
        usort($entries, function ($a, $b) {
            $dateA = $a['date'] instanceof Carbon ? $a['date']->timestamp : strtotime($a['date']);
            $dateB = $b['date'] instanceof Carbon ? $b['date']->timestamp : strtotime($b['date']);
            return $dateA - $dateB;
        });

        // Calculate running balance
        $running = 0;
        foreach ($entries as &$e) {
            $running += ($e['debit'] - $e['credit']);
            $e['saldo'] = $running;
        }

        return view('laporan.kas', compact('entries', 'startDate', 'endDate'));
    }
}
