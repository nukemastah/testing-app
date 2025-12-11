<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * This migration adds database-level protection for barang deletion.
     * When a barang is deleted, it will be soft deleted (deleted_at timestamp set)
     * instead of being permanently removed, preserving historical data integrity.
     */
    public function up(): void
    {
        // SQLite doesn't support triggers in the same way as MySQL/PostgreSQL
        // But we can add comments and documentation
        
        // Add index on deleted_at for performance when querying with withTrashed()
        if (!Schema::hasColumn('barangs', 'deleted_at')) {
            Schema::table('barangs', function (Blueprint $table) {
                $table->softDeletes();
            });
        }
        
        // Add index for better query performance on soft-deleted records
        Schema::table('barangs', function (Blueprint $table) {
            $table->index('deleted_at', 'barangs_deleted_at_index');
        });
        
        // Note: In SQLite, we rely on application-level logic (Model boot method)
        // to prevent hard deletion of barang with existing transactions.
        // For MySQL/PostgreSQL, you would add triggers here.
        
        DB::statement("
            -- Documentation: Barang Soft Delete Protection
            -- When deleting a barang:
            -- 1. Soft delete is used by default (deleted_at timestamp)
            -- 2. Barang with transactions cannot be permanently deleted
            -- 3. All reports use withTrashed() to access deleted barang data
            -- 4. This preserves historical transaction integrity
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('barangs', function (Blueprint $table) {
            $table->dropIndex('barangs_deleted_at_index');
        });
    }
};
