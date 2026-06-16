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
        Schema::create('pembayaran', function (Blueprint $table) {
            $table->id();

            $table->foreignId('penjualan_id')
                ->constrained('penjualan')
                ->cascadeOnDelete();

            $table->datetime('tanggal_pembayaran');
            $table->enum('metode_pembayaran', ['tunai', 'qris', 'transfer']);
            $table->decimal('jumlah_bayar', 15, 2);
            $table->string('bukti_pembayaran', 255)->nullable();
            $table->enum('status_pembayaran', ['pending', 'berhasil', 'gagal'])->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pembayaran');
    }
};
