<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\PenjualanController;
use App\Http\Controllers\PemasokController;
use App\Http\Controllers\PelangganController;
use App\Http\Controllers\PembayaranPenjualanController;
use App\Http\Controllers\PembayaranPembelianController;
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

    // Master - Pemasok routes
    Route::resource('pemasok', PemasokController::class);
    Route::get('/master/pemasok', [PemasokController::class, 'index'])->name('pemasok.index');
    
    // Master - Pelanggan routes (CRUD)
    Route::resource('pelanggan', PelangganController::class)->only(['index','store','update','destroy']);
    Route::get('/master/pelanggan', [PelangganController::class, 'index'])->name('pelanggan.index');

    // Transaksi routes
    Route::get('/penjualan', [PenjualanController::class, 'index'])->name('penjualan.index');
    Route::post('/penjualan', [PenjualanController::class, 'store'])->name('penjualan.store');
    Route::delete('/penjualan/{id}', [PenjualanController::class, 'destroy'])->name('penjualan.destroy');
    Route::post('/penjualan/undo', [PenjualanController::class, 'undo'])->name('penjualan.undo');

    // Transaksi - Pembayaran Penjualan routes
    Route::get('/transaksi/pembayaran-penjualan', [PembayaranPenjualanController::class, 'index'])->name('pembayaran-penjualan.index');
    Route::post('/pembayaran-penjualan', [PembayaranPenjualanController::class, 'store'])->name('pembayaran-penjualan.store');
    Route::delete('/pembayaran-penjualan/{pembayaranPenjualan}', [PembayaranPenjualanController::class, 'destroy'])->name('pembayaran-penjualan.destroy');

    // Transaksi - Pembayaran Pembelian routes
    Route::get('/transaksi/pembayaran-pembelian', [PembayaranPembelianController::class, 'index'])->name('pembayaran-pembelian.index');
    Route::post('/pembayaran-pembelian', [PembayaranPembelianController::class, 'store'])->name('pembayaran-pembelian.store');
    Route::delete('/pembayaran-pembelian/{pembayaranPembelian}', [PembayaranPembelianController::class, 'destroy'])->name('pembayaran-pembelian.destroy');

    // Laporan routes
    Route::get('/laporan/mutasi-rekening', function () { return view('laporan.mutasiRekening'); })->name('laporan.mutasiRekening');
    Route::get('/laporan/mutasi-stok', function () { return view('laporan.mutasiStok'); })->name('laporan.mutasiStok');
    Route::get('/laporan/kas', function () { return view('laporan.kas'); })->name('laporan.kas');
    Route::get('/laporan/piutang', function () { return view('laporan.piutang'); })->name('laporan.piutang');
    Route::get('/laporan/penjualan', function () { return view('laporan.penjualan'); })->name('laporan.penjualan');
});

require __DIR__.'/auth.php';
require __DIR__.'/profile.php';