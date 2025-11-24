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
        Schema::create('pembayaran_pembelians', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('barang_id')->nullable();
            $table->integer('jumlah_bayar');
            $table->string('bukti_bayar')->nullable();
            $table->date('tanggal_pembayaran');
            $table->timestamps();
            $table->foreign('barang_id')->references('id')->on('barangs')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pembayaran_pembelians');
    }
};
