<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        // Only add the column if it does not already exist (prevents duplicate column errors in sqlite/testing)
        if (!Schema::hasColumn('penjualans', 'tanggal')) {
            Schema::table('penjualans', function (Blueprint $table) {
                $table->date('tanggal')->nullable()->after('total_harga');
            });
        }
    }

    public function down()
    {
        if (Schema::hasColumn('penjualans', 'tanggal')) {
            Schema::table('penjualans', function (Blueprint $table) {
                $table->dropColumn('tanggal');
            });
        }
    }
};
