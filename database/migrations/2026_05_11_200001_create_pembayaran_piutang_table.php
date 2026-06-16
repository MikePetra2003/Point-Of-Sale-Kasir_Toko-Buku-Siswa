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
        Schema::create('pembayaran_piutang', function (Blueprint $table) {
            $table->id();

            $table->foreignId('piutang_id')
                ->constrained('piutang_pelanggan')
                ->cascadeOnDelete();

            $table->datetime('tanggal_bayar');
            $table->decimal('jumlah_bayar', 15, 2);
            $table->enum('metode_pembayaran', ['tunai', 'qris', 'transfer']);
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pembayaran_piutang');
    }
};
