<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('penjualans', function (Blueprint $table) {
            $table->foreignId('pelanggan_id')->nullable()->after('barang_id')->constrained('pelanggans')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('penjualans', function (Blueprint $table) {
            $table->dropConstrainedForeignId('pelanggan_id');
        });
    }
};
