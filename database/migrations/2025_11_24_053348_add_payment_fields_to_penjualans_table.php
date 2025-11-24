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
        Schema::table('penjualans', function (Blueprint $table) {
            $table->date('tenggat_pembayaran')->nullable()->after('tanggal');
            $table->enum('status_pembayaran', ['belum bayar', 'kurang bayar', 'lunas', 'telat bayar'])->default('belum bayar')->after('tenggat_pembayaran');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('penjualans', function (Blueprint $table) {
            $table->dropColumn(['tenggat_pembayaran', 'status_pembayaran']);
        });
    }
};
