<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Create new table with no_nota instead of penjualan_id
        if (!Schema::hasTable('pembayaran_penjualans')) {
            Schema::create('pembayaran_penjualans', function (Blueprint $table) {
                $table->id();
                $table->string('no_nota');
                $table->bigInteger('jumlah_bayar');
                $table->string('bukti_bayar')->nullable();
                $table->date('tanggal_pembayaran');
                $table->timestamps();
                
                $table->foreign('no_nota')
                      ->references('no_nota')
                      ->on('nota_hjuals')
                      ->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('PRAGMA foreign_keys=OFF');
        
        Schema::dropIfExists('pembayaran_penjualans');
        
        // Recreate old structure
        Schema::create('pembayaran_penjualans', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('penjualan_id');
            $table->bigInteger('jumlah_bayar');
            $table->string('bukti_bayar')->nullable();
            $table->date('tanggal_pembayaran');
            $table->timestamps();
            
            $table->foreign('penjualan_id')
                  ->references('id')
                  ->on('penjualans')
                  ->onDelete('cascade');
        });
        
        DB::statement('PRAGMA foreign_keys=ON');
    }
};
