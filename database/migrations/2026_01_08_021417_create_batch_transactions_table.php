<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('batch_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('barang_batch_id')->constrained('barang_batches')->onDelete('cascade');
            $table->foreignId('penjualan_id')->nullable()->constrained('penjualans')->onDelete('cascade');
            $table->enum('tipe', ['masuk', 'keluar', 'adjustment']); // tipe transaksi
            $table->integer('jumlah'); // jumlah yang keluar/masuk
            $table->integer('stok_sebelum'); // stok sebelum transaksi
            $table->integer('stok_sesudah'); // stok setelah transaksi
            $table->text('keterangan')->nullable();
            $table->timestamps();

            // Index untuk performa
            $table->index(['barang_batch_id', 'tipe']);
            $table->index('penjualan_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('batch_transactions');
    }
};
