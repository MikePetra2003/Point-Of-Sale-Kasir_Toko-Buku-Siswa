<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('detail_pembelian', function (Blueprint $table) {
            $table->integer('jumlah_pack')->default(0)->after('jumlah');
            $table->integer('isi_per_pack')->default(12)->after('jumlah_pack');
        });

        DB::table('detail_pembelian')->orderBy('id')->chunkById(100, function ($details) {
            foreach ($details as $detail) {
                DB::table('detail_pembelian')
                    ->where('id', $detail->id)
                    ->update([
                        'jumlah_pack' => (int) ceil(((int) $detail->jumlah) / 12),
                        'isi_per_pack' => 12,
                    ]);
            }
        });
    }

    public function down(): void
    {
        Schema::table('detail_pembelian', function (Blueprint $table) {
            $table->dropColumn(['jumlah_pack', 'isi_per_pack']);
        });
    }
};
