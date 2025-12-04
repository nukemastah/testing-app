<?php
namespace App\Http\Controllers;

use App\Models\Penjualan;
use App\Models\Barang;
use Carbon\Carbon;
use Illuminate\Http\Request;

class MutasiStokController extends Controller
{
    public function index(Request $request)
    {
        $startDate = $request->input('start_date') ? Carbon::parse($request->input('start_date'))->startOfDay() : Carbon::now()->startOfMonth();
        $endDate = $request->input('end_date') ? Carbon::parse($request->input('end_date'))->endOfDay() : Carbon::now()->endOfMonth();

        // Since purchases aren't modeled, we show outgoing (sales) and current stock.
        $sales = Penjualan::with('barang')
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->get();

        $mutasi = [];

        foreach ($sales as $s) {
            $mutasi[] = [
                'tanggal' => $s->tanggal,
                'kode' => $s->barang ? $s->barang->id : '-',
                'nama' => $s->barang ? $s->barang->nama : 'Unknown',
                'masuk' => 0,
                'keluar' => $s->jumlah,
                'stok_akhir' => $s->barang ? $s->barang->kuantitas : 0,
            ];
        }

        return view('laporan.mutasiStok', ['mutasiList' => $mutasi, 'startDate' => $startDate, 'endDate' => $endDate]);
    }
}
