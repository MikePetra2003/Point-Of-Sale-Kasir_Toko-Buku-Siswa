<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('penjualan', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained('users')
                ->restrictOnDelete();

            $table->foreignId('pelanggan_id')
                ->nullable()
                ->constrained('pelanggan')
                ->nullOnDelete();

            $table->string('nomor_invoice', 30)->unique();
            $table->datetime('tanggal_penjualan');
            $table->decimal('total_harga', 15, 2)->default(0);
            $table->decimal('diskon', 15, 2)->default(0);
            $table->decimal('total_akhir', 15, 2)->default(0);
            $table->enum('status_pembayaran', ['belum_lunas', 'lunas', 'sebagian'])->default('belum_lunas');
            $table->string('jenis_struk', 50)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penjualan');
    }
};
