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
        // Tabel header nota penjualan
        Schema::create('nota_hjuals', function (Blueprint $table) {
            $table->string('no_nota')->primary(); // No nota sebagai primary key (auto increment format)
            $table->date('tanggal')->useCurrent();
            $table->foreignId('pelanggan_id')->constrained('pelanggans')->onDelete('cascade');
            $table->integer('total_item')->default(0);
            $table->bigInteger('total_harga')->default(0);
            $table->enum('status', ['draft', 'selesai', 'cancel'])->default('draft');
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });

        // Tabel detail nota penjualan
        Schema::create('detail_hjuals', function (Blueprint $table) {
            $table->id();
            $table->string('no_nota'); // FK ke nota_hjuals
            $table->foreignId('barang_id')->constrained('barangs')->onDelete('cascade');
            $table->integer('quantity');
            $table->bigInteger('harga_jual'); // Harga per unit
            $table->bigInteger('subtotal'); // quantity * harga_jual
            $table->timestamps();
            
            // Foreign key untuk no_nota
            $table->foreign('no_nota')->references('no_nota')->on('nota_hjuals')->onDelete('cascade');
            
            // Composite index untuk performance
            $table->index(['no_nota', 'barang_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_hjuals');
        Schema::dropIfExists('nota_hjuals');
    }
};
