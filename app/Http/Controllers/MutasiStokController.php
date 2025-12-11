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
        // Use withTrashed() to include deleted barang in historical reports
        $sales = Penjualan::with(['barang' => function($query) {
                $query->withTrashed();
            }])
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->get();

        $mutasi = [];

        foreach ($sales as $s) {
            $barangNama = $s->barang ? $s->barang->nama : 'Barang Dihapus';
            if ($s->barang && $s->barang->trashed()) {
                $barangNama .= ' (Dihapus)';
            }
            
            $mutasi[] = [
                'tanggal' => $s->tanggal,
                'kode' => $s->barang ? $s->barang->id : '-',
                'nama' => $barangNama,
                'masuk' => 0,
                'keluar' => $s->jumlah,
                'stok_akhir' => $s->barang ? $s->barang->kuantitas : 0,
            ];
        }

        return view('laporan.mutasiStok', ['mutasiList' => $mutasi, 'startDate' => $startDate, 'endDate' => $endDate]);
    }
}
