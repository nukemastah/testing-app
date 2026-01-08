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
        Schema::create('barang_batches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('barang_id')->constrained('barangs')->onDelete('cascade');
            $table->foreignId('pembelian_barang_id')->nullable()->constrained('pembelian_barangs')->onDelete('set null');
            $table->string('batch_number')->unique(); // Auto-generated: BTH-{timestamp}-{id}
            $table->bigInteger('harga_beli'); // Harga beli per unit dari batch ini
            $table->integer('stok_awal'); // Stok awal saat batch dibuat
            $table->integer('stok_tersedia'); // Stok yang masih tersedia
            $table->date('tanggal_masuk'); // Tanggal batch masuk
            $table->date('tanggal_kadaluarsa')->nullable(); // Tanggal kadaluarsa (untuk FEFO)
            $table->text('keterangan')->nullable();
            $table->timestamps();

            // Index untuk performa
            $table->index(['barang_id', 'stok_tersedia']);
            $table->index(['barang_id', 'tanggal_kadaluarsa']);
            $table->index(['barang_id', 'tanggal_masuk']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('barang_batches');
    }
};
