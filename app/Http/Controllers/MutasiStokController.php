<?php
namespace App\Http\Controllers;

use App\Models\PembelianBarang;
use App\Models\DetailHjual;
use App\Models\NotaHjual;
use App\Models\Barang;
use Carbon\Carbon;
use Illuminate\Http\Request;

class MutasiStokController extends Controller
{
    public function index(Request $request)
    {
        $startDate = $request->input('start_date') ? Carbon::parse($request->input('start_date'))->startOfDay() : Carbon::now()->startOfMonth();
        $endDate = $request->input('end_date') ? Carbon::parse($request->input('end_date'))->endOfDay() : Carbon::now()->endOfMonth();

        $mutasi = [];

        // Get pembelian (stock in)
        $pembelians = PembelianBarang::with(['barang' => function($query) {
                $query->withTrashed();
            }])
            ->whereBetween('tanggal_pembelian', [$startDate, $endDate])
            ->get();

        foreach ($pembelians as $p) {
            $barangNama = $p->barang ? $p->barang->nama : 'Barang Dihapus';
            if ($p->barang && $p->barang->trashed()) {
                $barangNama .= ' (Dihapus)';
            }
            
            $mutasi[] = [
                'tanggal' => $p->tanggal_pembelian,
                'kode' => $p->barang ? $p->barang->id : '-',
                'nama' => $barangNama,
                'masuk' => $p->jumlah,
                'keluar' => 0,
                'stok_akhir' => $p->barang ? $p->barang->kuantitas : 0,
                'keterangan' => 'Pembelian',
            ];
        }

        // Get penjualan (stock out)
        $sales = DetailHjual::with(['barang' => function($query) {
                $query->withTrashed();
            }, 'notaHjual'])
            ->whereHas('notaHjual', function($q) use ($startDate, $endDate) {
                $q->whereBetween('tanggal', [$startDate, $endDate]);
            })
            ->get();

        foreach ($sales as $s) {
            $barangNama = $s->barang ? $s->barang->nama : 'Barang Dihapus';
            if ($s->barang && $s->barang->trashed()) {
                $barangNama .= ' (Dihapus)';
            }
            
            $mutasi[] = [
                'tanggal' => $s->notaHjual->tanggal,
                'kode' => $s->barang ? $s->barang->id : '-',
                'nama' => $barangNama,
                'masuk' => 0,
                'keluar' => $s->quantity,
                'stok_akhir' => $s->barang ? $s->barang->kuantitas : 0,
                'keterangan' => 'Penjualan - ' . $s->notaHjual->no_nota,
            ];
        }

        // Sort by date
        usort($mutasi, function($a, $b) {
            return strtotime($a['tanggal']) - strtotime($b['tanggal']);
        });

        return view('laporan.mutasiStok', ['mutasiList' => $mutasi, 'startDate' => $startDate, 'endDate' => $endDate]);
    }
}
