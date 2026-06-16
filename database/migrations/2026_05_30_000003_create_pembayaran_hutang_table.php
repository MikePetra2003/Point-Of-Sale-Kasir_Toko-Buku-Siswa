<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('pembayaran_hutang')) {
            return;
        }

        Schema::create('pembayaran_hutang', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hutang_id')->constrained('hutang_supplier')->cascadeOnDelete();
            $table->datetime('tanggal_bayar');
            $table->decimal('jumlah_bayar', 15, 2);
            $table->decimal('bunga', 15, 2)->default(0);
            $table->enum('metode_pembayaran', ['tunai', 'qris', 'transfer']);
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pembayaran_hutang');
    }
};
