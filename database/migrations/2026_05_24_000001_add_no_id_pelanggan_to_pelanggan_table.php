<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('pelanggan', function (Blueprint $table) {
            $table->string('no_id_pelanggan', 10)->nullable()->unique()->after('id');
        });

        $counters = [];

        DB::table('pelanggan')
            ->select('id', 'nama_pelanggan')
            ->orderBy('id')
            ->get()
            ->each(function ($pelanggan) use (&$counters) {
                $prefix = strtoupper(substr(trim($pelanggan->nama_pelanggan), 0, 1));
                $prefix = preg_match('/[A-Z]/', $prefix) ? $prefix : 'X';
                $counters[$prefix] = ($counters[$prefix] ?? 0) + 1;

                DB::table('pelanggan')
                    ->where('id', $pelanggan->id)
                    ->update([
                        'no_id_pelanggan' => $prefix.str_pad($counters[$prefix], 3, '0', STR_PAD_LEFT),
                    ]);
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pelanggan', function (Blueprint $table) {
            $table->dropUnique(['no_id_pelanggan']);
            $table->dropColumn('no_id_pelanggan');
        });
    }
};
