<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pembelian', function (Blueprint $table) {
            $table->decimal('diskon', 15, 2)->default(0)->after('total_harga');
            $table->enum('status_pembayaran', ['lunas', 'belum_lunas'])->default('lunas')->after('diskon');
        });
    }

    public function down(): void
    {
        Schema::table('pembelian', function (Blueprint $table) {
            $table->dropColumn(['diskon', 'status_pembayaran']);
        });
    }
};
