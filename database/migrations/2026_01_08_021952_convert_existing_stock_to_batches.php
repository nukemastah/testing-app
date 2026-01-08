<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Models\Barang;
use App\Models\BarangBatch;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Convert existing stock menjadi batch untuk barang yang sudah ada
     */
    public function up(): void
    {
        // Get all barang yang memiliki stok
        $barangs = Barang::where('kuantitas', '>', 0)->get();

        foreach ($barangs as $barang) {
            // Buat batch untuk stok existing
            // Menggunakan harga dari field 'harga' sebagai harga_beli default
            BarangBatch::create([
                'barang_id' => $barang->id,
                'pembelian_barang_id' => null, // Tidak ada pembelian terkait untuk stok existing
                'batch_number' => 'BTH-EXISTING-' . $barang->id . '-' . now()->format('YmdHis'),
                'harga_beli' => $barang->harga ?? 0,
                'stok_awal' => $barang->kuantitas,
                'stok_tersedia' => $barang->kuantitas,
                'tanggal_masuk' => now(),
                'tanggal_kadaluarsa' => null, // Tidak ada tanggal kadaluarsa untuk stok existing
                'keterangan' => 'Converted from existing stock - Initial batch',
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Hapus batch yang dibuat dari konversi
        BarangBatch::where('batch_number', 'like', 'BTH-EXISTING-%')->delete();
    }
};
