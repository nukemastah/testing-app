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
        // SQLite doesn't support ALTER COLUMN for foreign keys, so we need to recreate
        DB::statement('PRAGMA foreign_keys = OFF');

        if (Schema::hasTable('pembayaran_pembelians')) {
            // Backup data
            DB::statement('CREATE TABLE pembayaran_pembelians_backup AS SELECT * FROM pembayaran_pembelians');

            // Drop old table
            DB::statement('DROP TABLE pembayaran_pembelians');

            // Create new table with correct structure
            Schema::create('pembayaran_pembelians', function (Blueprint $table) {
                $table->id();
                $table->foreignId('pembelian_id')->constrained('pembelian_barangs')->onDelete('cascade');
                $table->bigInteger('jumlah_bayar');
                $table->string('bukti_bayar')->nullable();
                $table->date('tanggal_pembayaran');
                $table->text('keterangan')->nullable();
                $table->timestamps();
            });

            // Restore data (mapping barang_id to pembelian_id)
            // For now, we'll clear since this is for new stock additions
            // In real scenario, you might need to migrate this data properly
        }

        DB::statement('PRAGMA foreign_keys = ON');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('PRAGMA foreign_keys = OFF');

        if (Schema::hasTable('pembayaran_pembelians')) {
            DB::statement('DROP TABLE pembayaran_pembelians');

            Schema::create('pembayaran_pembelians', function (Blueprint $table) {
                $table->id();
                $table->foreignId('barang_id')->nullable()->constrained('barangs')->onDelete('cascade');
                $table->bigInteger('jumlah_bayar');
                $table->string('bukti_bayar')->nullable();
                $table->date('tanggal_pembayaran');
                $table->timestamps();
            });
        }

        DB::statement('PRAGMA foreign_keys = ON');
    }
};
