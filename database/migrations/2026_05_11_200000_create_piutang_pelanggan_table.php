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
        Schema::create('piutang_pelanggan', function (Blueprint $table) {
            $table->id();

            $table->foreignId('penjualan_id')
                ->constrained('penjualan')
                ->cascadeOnDelete();

            $table->foreignId('pelanggan_id')
                ->nullable()
                ->constrained('pelanggan')
                ->restrictOnDelete();

            $table->decimal('total_piutang', 15, 2);
            $table->decimal('total_dibayar', 15, 2)->default(0);
            $table->decimal('sisa_piutang', 15, 2);
            $table->enum('status', ['belum_lunas', 'lunas', 'sebagian'])->default('belum_lunas');
            $table->date('tanggal_jatuh_tempo')->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('piutang_pelanggan');
    }
};
