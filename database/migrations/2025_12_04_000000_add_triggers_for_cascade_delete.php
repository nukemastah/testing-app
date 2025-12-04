<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Enable foreign keys di SQLite
        DB::statement('PRAGMA foreign_keys = ON');

        // Trigger untuk Cascade delete pembayaran saat penjualan dihapus
        DB::statement('
            CREATE TRIGGER IF NOT EXISTS delete_pembayaran_penjualan_trigger
            BEFORE DELETE ON penjualans
            BEGIN
                DELETE FROM pembayaran_penjualans WHERE penjualan_id = OLD.id;
            END;
        ');

        // Trigger untuk Cascade delete penjualans saat barang dihapus
        DB::statement('
            CREATE TRIGGER IF NOT EXISTS delete_penjualan_cascade_trigger
            BEFORE DELETE ON barangs
            BEGIN
                DELETE FROM penjualans WHERE barang_id = OLD.id;
            END;
        ');

        // Trigger untuk Cascade delete pembayaran pembelian saat barang dihapus
        DB::statement('
            CREATE TRIGGER IF NOT EXISTS delete_pembayaran_pembelian_trigger
            BEFORE DELETE ON barangs
            BEGIN
                DELETE FROM pembayaran_pembelians WHERE barang_id = OLD.id;
            END;
        ');

        // Trigger untuk Cascade delete penjualans saat pelanggan dihapus
        DB::statement('
            CREATE TRIGGER IF NOT EXISTS delete_penjualan_by_pelanggan_trigger
            BEFORE DELETE ON pelanggans
            BEGIN
                DELETE FROM penjualans WHERE pelanggan_id = OLD.id;
            END;
        ');

        // Trigger untuk Set barang.pemasok_id = NULL saat pemasok dihapus
        DB::statement('
            CREATE TRIGGER IF NOT EXISTS update_barang_pemasok_trigger
            BEFORE DELETE ON pemasoks
            BEGIN
                UPDATE barangs SET pemasok_id = NULL WHERE pemasok_id = OLD.id;
            END;
        ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop triggers
        DB::statement('DROP TRIGGER IF EXISTS delete_pembayaran_penjualan_trigger');
        DB::statement('DROP TRIGGER IF EXISTS delete_penjualan_cascade_trigger');
        DB::statement('DROP TRIGGER IF EXISTS delete_pembayaran_pembelian_trigger');
        DB::statement('DROP TRIGGER IF EXISTS delete_penjualan_by_pelanggan_trigger');
        DB::statement('DROP TRIGGER IF EXISTS update_barang_pemasok_trigger');
    }
};
