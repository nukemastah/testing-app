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
        // When a penjualan (sale) is deleted, return the sold quantity back to the barang stock
        DB::statement(
            "CREATE TRIGGER IF NOT EXISTS restore_stock_on_penjualan_delete\n            AFTER DELETE ON penjualans\n            BEGIN\n                UPDATE barangs SET kuantitas = COALESCE(kuantitas, 0) + OLD.jumlah WHERE id = OLD.barang_id;\n            END;"
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('DROP TRIGGER IF EXISTS restore_stock_on_penjualan_delete');
    }
};
