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
        Schema::create('pembelian_barangs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('barang_id')->constrained('barangs')->onDelete('cascade');
            $table->foreignId('pemasok_id')->constrained('pemasoks')->onDelete('restrict');
            $table->integer('jumlah');
            $table->bigInteger('harga_satuan');
            $table->bigInteger('total_harga');
            $table->date('tanggal_pembelian');
            $table->enum('status_pembayaran', ['belum bayar', 'sebagian', 'lunas'])->default('belum bayar');
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pembelian_barangs');
    }
};
