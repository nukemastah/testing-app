<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\PenjualanController;
use App\Http\Controllers\PemasokController;
use App\Http\Controllers\PelangganController;
use App\Http\Controllers\PembayaranPenjualanController;
use App\Http\Controllers\PembayaranPembelianController;
use App\Http\Controllers\LabaRugiController;
use App\Http\Controllers\GeneralLedgerController;
use App\Http\Controllers\HutangController;
use App\Http\Controllers\MutasiStokController;
use App\Http\Controllers\KasController;
use App\Http\Controllers\PiutangController;
use App\Http\Controllers\LaporanPenjualanController;
use App\Http\Controllers\DashboardController;
use App\Models\Barang;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;


// Route utama - redirect ke dashboard jika sudah login, ke login jika belum
Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('dashboard');
    }

    // Langsung tampilkan halaman login
    return view('auth.login');
});
Route::get('/db-check', function () {
    try {
        DB::connection()->getPdo();
        return 'Database connection successful!';
    } catch (\Exception $e) {
        return 'Connection failed: ' . $e->getMessage();
    }
})->name('db-check');

// Route dashboard
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');
// routes/web.php

Route::get('/run-migrate', function () {
    try {
        Artisan::call('migrate', ['--force' => true]);
        return 'Migration ran successfully.';
    } catch (\Exception $e) {
        return 'Migration failed: ' . $e->getMessage();
    }
});

// Semua route yang perlu login
Route::middleware(['auth'])->group(function () {
    // Master routes
    Route::get('/barang', [BarangController::class, 'index'])->name('barang.index');
    Route::post('/barang', [BarangController::class, 'store'])->name('barang.store');
    Route::get('/barang/{id}/edit', [BarangController::class, 'edit'])->name('barang.edit'); 
    Route::put('/barang/{id}', [BarangController::class, 'update'])->name('barang.update'); 
    Route::delete('/barang/{id}', [BarangController::class, 'destroy'])->name('barang.destroy');
    Route::post('/barang/undo', [BarangController::class, 'undo'])->name('barang.undo');
    Route::get('/barang/{id}/add-stock', [BarangController::class, 'showAddStock'])->name('barang.showAddStock');
    Route::post('/barang/{id}/add-stock', [BarangController::class, 'addStock'])->name('barang.addStock');
    Route::get('/barang/{id}/batches', [BarangController::class, 'showBatches'])->name('barang.batches');

    // Master - Pemasok routes
    Route::resource('pemasok', PemasokController::class);
    Route::get('/master/pemasok', [PemasokController::class, 'index'])->name('pemasok.index');
    
    // Master - Pelanggan routes (CRUD)
    Route::resource('pelanggan', PelangganController::class)->only(['index','store','update','destroy']);
    Route::get('/master/pelanggan', [PelangganController::class, 'index'])->name('pelanggan.index');

    // Dashboard routes
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/print-all', [DashboardController::class, 'printAll'])->name('dashboard.print-all');

    // Transaksi routes
    Route::get('/penjualan', [PenjualanController::class, 'index'])->name('penjualan.index');
    Route::get('/penjualan/create', [PenjualanController::class, 'create'])->name('penjualan.create');
    Route::post('/penjualan', [PenjualanController::class, 'store'])->name('penjualan.store');
    Route::delete('/penjualan/{noNota}', [PenjualanController::class, 'destroy'])->name('penjualan.destroy');
    Route::post('/penjualan/undo', [PenjualanController::class, 'undo'])->name('penjualan.undo');

    // Transaksi - Pembayaran Penjualan routes
    Route::get('/transaksi/pembayaran-penjualan', [PembayaranPenjualanController::class, 'index'])->name('pembayaran-penjualan.index');
    Route::post('/pembayaran-penjualan', [PembayaranPenjualanController::class, 'store'])->name('pembayaran-penjualan.store');
    Route::delete('/pembayaran-penjualan/{pembayaranPenjualan}', [PembayaranPenjualanController::class, 'destroy'])->name('pembayaran-penjualan.destroy');
    Route::get('/pembayaran-penjualan/{no_nota}/detail', [PembayaranPenjualanController::class, 'getDetailNota'])->name('pembayaran-penjualan.detail');

    // Transaksi - Pembayaran Pembelian routes
    Route::get('/transaksi/pembayaran-pembelian', [PembayaranPembelianController::class, 'index'])->name('pembayaran-pembelian.index');
    Route::post('/pembayaran-pembelian', [PembayaranPembelianController::class, 'store'])->name('pembayaran-pembelian.store');
    Route::delete('/pembayaran-pembelian/{pembayaranPembelian}', [PembayaranPembelianController::class, 'destroy'])->name('pembayaran-pembelian.destroy');
    Route::get('/pembayaran-pembelian/{id}/detail', [PembayaranPembelianController::class, 'getDetailPembelian'])->name('pembayaran-pembelian.detail');

    // Laporan routes
    Route::get('/laporan/laba-rugi', [LabaRugiController::class, 'index'])->name('laporan.labaRugi');
    Route::get('/laporan/general-ledger', [GeneralLedgerController::class, 'index'])->name('laporan.generalLedger');
    Route::get('/laporan/hutang', [HutangController::class, 'index'])->name('laporan.hutang');
    Route::get('/laporan/mutasi-stok', [MutasiStokController::class, 'index'])->name('laporan.mutasiStok');
    Route::get('/laporan/kas', [KasController::class, 'index'])->name('laporan.kas');
    Route::get('/laporan/piutang', [PiutangController::class, 'index'])->name('laporan.piutang');
    Route::get('/laporan/penjualan', [LaporanPenjualanController::class, 'index'])->name('laporan.penjualan');
});

require __DIR__.'/auth.php';
require __DIR__.'/profile.php';