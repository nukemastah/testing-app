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
        // Drop old trigger if exists (references old penjualan_id column)
        DB::statement('DROP TRIGGER IF EXISTS delete_pembayaran_penjualan_trigger');
        
        // SQLite doesn't support ALTER COLUMN for enum, so we need to recreate the table
        DB::statement('PRAGMA foreign_keys = OFF');
        
        // Check if table exists before attempting to rename
        if (Schema::hasTable('nota_hjuals')) {
            // Rename the old table
            Schema::rename('nota_hjuals', 'nota_hjuals_old');
            
            // Create new table with updated enum
            Schema::create('nota_hjuals', function (Blueprint $table) {
                $table->string('no_nota')->primary();
                $table->date('tanggal')->useCurrent();
                $table->foreignId('pelanggan_id')->constrained('pelanggans')->onDelete('cascade');
                $table->integer('total_item')->default(0);
                $table->bigInteger('total_harga')->default(0);
                $table->enum('status', ['draft', 'selesai', 'sebagian', 'lunas', 'cancel'])->default('draft');
                $table->text('keterangan')->nullable();
                $table->timestamps();
            });
            
            // Copy data from old table
            DB::statement('INSERT INTO nota_hjuals (no_nota, tanggal, pelanggan_id, total_item, total_harga, status, keterangan, created_at, updated_at) 
                          SELECT no_nota, tanggal, pelanggan_id, total_item, total_harga, status, keterangan, created_at, updated_at 
                          FROM nota_hjuals_old');
            
            // Drop old table
            Schema::drop('nota_hjuals_old');
        }
        
        DB::statement('PRAGMA foreign_keys = ON');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('PRAGMA foreign_keys = OFF');
        
        if (Schema::hasTable('nota_hjuals')) {
            Schema::rename('nota_hjuals', 'nota_hjuals_old');
            
            Schema::create('nota_hjuals', function (Blueprint $table) {
                $table->string('no_nota')->primary();
                $table->date('tanggal')->useCurrent();
                $table->foreignId('pelanggan_id')->constrained('pelanggans')->onDelete('cascade');
                $table->integer('total_item')->default(0);
                $table->bigInteger('total_harga')->default(0);
                $table->enum('status', ['draft', 'selesai', 'cancel'])->default('draft');
                $table->text('keterangan')->nullable();
                $table->timestamps();
            });
            
            DB::statement('INSERT INTO nota_hjuals (no_nota, tanggal, pelanggan_id, total_item, total_harga, status, keterangan, created_at, updated_at) 
                          SELECT no_nota, tanggal, pelanggan_id, total_item, total_harga, 
                                 CASE status 
                                    WHEN "sebagian" THEN "selesai"
                                    WHEN "lunas" THEN "selesai"
                                    ELSE status 
                                 END as status, 
                                 keterangan, created_at, updated_at 
                          FROM nota_hjuals_old');
            
            Schema::drop('nota_hjuals_old');
        }
        
        DB::statement('PRAGMA foreign_keys = ON');
    }
};
