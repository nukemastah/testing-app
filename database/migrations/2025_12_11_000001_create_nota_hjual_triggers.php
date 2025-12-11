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
        // Trigger untuk delete detail saat header nota dihapus
        DB::statement('
            CREATE TRIGGER IF NOT EXISTS delete_detail_hjual_trigger
            BEFORE DELETE ON nota_hjuals
            FOR EACH ROW
            BEGIN
                DELETE FROM detail_hjuals WHERE no_nota = OLD.no_nota;
            END;
        ');

        // Trigger untuk update total_item dan total_harga saat insert detail
        DB::statement('
            CREATE TRIGGER IF NOT EXISTS update_nota_total_insert_trigger
            AFTER INSERT ON detail_hjuals
            FOR EACH ROW
            BEGIN
                UPDATE nota_hjuals 
                SET total_item = (SELECT COUNT(*) FROM detail_hjuals WHERE no_nota = NEW.no_nota),
                    total_harga = (SELECT COALESCE(SUM(subtotal), 0) FROM detail_hjuals WHERE no_nota = NEW.no_nota)
                WHERE no_nota = NEW.no_nota;
            END;
        ');

        // Trigger untuk update total saat delete detail
        DB::statement('
            CREATE TRIGGER IF NOT EXISTS update_nota_total_delete_trigger
            AFTER DELETE ON detail_hjuals
            FOR EACH ROW
            BEGIN
                UPDATE nota_hjuals 
                SET total_item = (SELECT COUNT(*) FROM detail_hjuals WHERE no_nota = OLD.no_nota),
                    total_harga = (SELECT COALESCE(SUM(subtotal), 0) FROM detail_hjuals WHERE no_nota = OLD.no_nota)
                WHERE no_nota = OLD.no_nota;
            END;
        ');

        // Trigger untuk update total saat update detail
        DB::statement('
            CREATE TRIGGER IF NOT EXISTS update_nota_total_update_trigger
            AFTER UPDATE ON detail_hjuals
            FOR EACH ROW
            BEGIN
                UPDATE nota_hjuals 
                SET total_item = (SELECT COUNT(*) FROM detail_hjuals WHERE no_nota = NEW.no_nota),
                    total_harga = (SELECT COALESCE(SUM(subtotal), 0) FROM detail_hjuals WHERE no_nota = NEW.no_nota)
                WHERE no_nota = NEW.no_nota;
            END;
        ');

        // Trigger untuk DECREASE stok barang saat insert detail (kurangi stok saat barang dijual)
        DB::statement('
            CREATE TRIGGER IF NOT EXISTS decrease_barang_stock_trigger
            AFTER INSERT ON detail_hjuals
            FOR EACH ROW
            BEGIN
                UPDATE barangs SET kuantitas = kuantitas - NEW.quantity WHERE id = NEW.barang_id;
            END;
        ');

        // Trigger untuk INCREASE stok barang saat delete detail (KEMBALIKAN STOK saat nota dihapus)
        DB::statement('
            CREATE TRIGGER IF NOT EXISTS increase_barang_stock_trigger
            AFTER DELETE ON detail_hjuals
            FOR EACH ROW
            BEGIN
                UPDATE barangs SET kuantitas = kuantitas + OLD.quantity WHERE id = OLD.barang_id;
            END;
        ');

        // Trigger untuk update stok saat quantity berubah (saat detail diupdate)
        DB::statement('
            CREATE TRIGGER IF NOT EXISTS update_barang_stock_trigger
            AFTER UPDATE OF quantity ON detail_hjuals
            FOR EACH ROW
            BEGIN
                UPDATE barangs SET kuantitas = kuantitas + (OLD.quantity - NEW.quantity) WHERE id = NEW.barang_id;
            END;
        ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop triggers
        DB::statement('DROP TRIGGER IF EXISTS delete_detail_hjual_trigger');
        DB::statement('DROP TRIGGER IF EXISTS update_nota_total_insert_trigger');
        DB::statement('DROP TRIGGER IF EXISTS update_nota_total_delete_trigger');
        DB::statement('DROP TRIGGER IF EXISTS update_nota_total_update_trigger');
        DB::statement('DROP TRIGGER IF EXISTS decrease_barang_stock_trigger');
        DB::statement('DROP TRIGGER IF EXISTS increase_barang_stock_trigger');
        DB::statement('DROP TRIGGER IF EXISTS update_barang_stock_trigger');
    }
};
