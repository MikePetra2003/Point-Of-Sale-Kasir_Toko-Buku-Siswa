<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pembelian', function (Blueprint $table) {
            $table->decimal('diskon_persen', 5, 2)->default(0)->after('diskon');
        });

        DB::table('pembelian')
            ->where('diskon', '>', 0)
            ->where('total_harga', '>', 0)
            ->update([
                'diskon_persen' => DB::raw('ROUND((diskon / total_harga) * 100, 2)'),
            ]);
    }

    public function down(): void
    {
        Schema::table('pembelian', function (Blueprint $table) {
            $table->dropColumn('diskon_persen');
        });
    }
};
