<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('hutang_supplier')) {
            return;
        }

        Schema::create('hutang_supplier', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pembelian_id')->constrained('pembelian')->cascadeOnDelete();
            $table->foreignId('supplier_id')->constrained('supplier')->restrictOnDelete();
            $table->decimal('total_hutang', 15, 2);
            $table->decimal('total_dibayar', 15, 2)->default(0);
            $table->decimal('sisa_hutang', 15, 2);
            $table->enum('status', ['belum_lunas', 'lunas'])->default('belum_lunas');
            $table->date('tanggal_jatuh_tempo')->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hutang_supplier');
    }
};
