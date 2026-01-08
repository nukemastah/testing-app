<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Barang;
use App\Models\DetailHjual;
use App\Models\NotaHjual;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;

class DashboardController extends Controller
{
    /**
     * Menampilkan dashboard dengan data yang dipaginasi dan difilter.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $tanggal = $request->input('tanggal');
        $tanggalBarang = $request->input('tanggal_barang');

        // 1. Query untuk Penjualan (dari detail_hjuals) dengan filter tanggal
        $penjualanQuery = DetailHjual::with(['barang', 'notaHjual'])
            ->whereHas('barang') // Hanya tampilkan penjualan yang barangnya masih ada
            ->when($tanggal, function ($query) use ($tanggal) {
                return $query->whereHas('notaHjual', function ($q) use ($tanggal) {
                    $q->whereDate('tanggal', $tanggal);
                });
            })
            ->latest();

        // Paginate data penjualan (5 item per halaman)
        $penjualans = $penjualanQuery->paginate(5, ['*'], 'penjualan_page')->withQueryString();

        // Transform untuk compatibility dengan view existing
        $penjualans->getCollection()->transform(function ($detail) {
            return (object)[
                'tanggal' => $detail->notaHjual->tanggal,
                'barang' => $detail->barang,
                'jumlah' => $detail->quantity,
                'total_harga' => $detail->subtotal,
                'harga_jual' => $detail->harga_jual,
            ];
        });

        // 2. Membuat data Laporan Laba Rugi dari hasil paginasi penjualan
        $laporanData = $penjualans->map(function ($p) {
            // Harga beli dari barang (harga master) atau bisa dari batch
            $hargaBeli = $p->barang->harga ?? 0;
            
            // Harga jual per item
            $hargaJual = $p->harga_jual ?? ($p->jumlah > 0 ? $p->total_harga / $p->jumlah : 0);
            
            // Laba per item dikalikan jumlah
            $laba = ($hargaJual - $hargaBeli) * $p->jumlah;

            return [
                'tanggal' => optional($p->tanggal)->format('l, d/m/Y'),
                'nama_barang' => $p->barang->nama ?? 'N/A',
                'harga_beli' => $hargaBeli,
                'harga_jual' => $hargaJual,
                'laba' => $laba,
            ];
        });

        // Membuat instance Paginator baru untuk Laporan Laba Rugi
        $laporan = new LengthAwarePaginator(
            $laporanData,
            $penjualans->total(),
            $penjualans->perPage(),
            $penjualans->currentPage(),
            [
                'path' => Paginator::resolveCurrentPath(),
                'pageName' => 'penjualan_page', // Menggunakan pageName yang sama agar sinkron dengan tabel Penjualan
            ]
        );
        
        // Agar filter tetap aktif saat paginasi
        $laporan->withQueryString();

        // 3. Query untuk Barang dengan filter tanggal
        $barangs = Barang::when($tanggalBarang, function ($query) use ($tanggalBarang) {
                return $query->whereDate('created_at', $tanggalBarang);
            })
            ->latest()
            ->paginate(5, ['*'], 'barang_page')
            ->withQueryString();

        return view('dashboard.index', compact(
            'penjualans', 
            'barangs', 
            'tanggal', 
            'tanggalBarang', 
            'laporan'
        ));
    }

    /**
     * Method untuk print semua data (opsional - jika ingin handling khusus untuk print)
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function printAll(Request $request)
    {
        $tanggal = $request->input('tanggal');
        $tanggalBarang = $request->input('tanggal_barang');

        // Ambil semua data tanpa paginasi untuk print (dari detail_hjuals)
        $penjualanDetails = DetailHjual::with(['barang', 'notaHjual'])
            ->whereHas('barang')
            ->when($tanggal, function ($query) use ($tanggal) {
                return $query->whereHas('notaHjual', function ($q) use ($tanggal) {
                    $q->whereDate('tanggal', $tanggal);
                });
            })
            ->latest()
            ->get();

        // Transform ke format yang compatible
        $penjualans = $penjualanDetails->map(function ($detail) {
            return (object)[
                'tanggal' => $detail->notaHjual->tanggal,
                'barang' => $detail->barang,
                'jumlah' => $detail->quantity,
                'total_harga' => $detail->subtotal,
                'harga_jual' => $detail->harga_jual,
            ];
        });

        $barangs = Barang::when($tanggalBarang, function ($query) use ($tanggalBarang) {
                return $query->whereDate('created_at', $tanggalBarang);
            })
            ->latest()
            ->get();

        // Buat laporan laba rugi dari semua data penjualan
        $laporanData = $penjualans->map(function ($p) {
            $hargaBeli = $p->barang->harga ?? 0;
            $hargaJual = $p->harga_jual ?? ($p->jumlah > 0 ? $p->total_harga / $p->jumlah : 0);
            $laba = ($hargaJual - $hargaBeli) * $p->jumlah;

            return [
                'tanggal' => optional($p->tanggal)->format('l, d/m/Y'),
                'nama_barang' => $p->barang->nama ?? 'N/A',
                'harga_beli' => $hargaBeli,
                'harga_jual' => $hargaJual,
                'laba' => $laba,
            ];
        });

        return view('dashboard.print-all', compact(
            'penjualans', 
            'barangs', 
            'tanggal', 
            'tanggalBarang', 
            'laporanData'
        ));
    }
}