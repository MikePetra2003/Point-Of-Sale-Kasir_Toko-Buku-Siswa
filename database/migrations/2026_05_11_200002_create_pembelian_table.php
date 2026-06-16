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
        Schema::create('pembelian', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained('users')
                ->restrictOnDelete();

            $table->foreignId('supplier_id')
                ->constrained('supplier')
                ->restrictOnDelete();

            $table->string('nomor_faktur', 30)->unique();
            $table->datetime('tanggal_pembelian');
            $table->decimal('total_harga', 15, 2)->default(0);
            $table->enum('status', ['pending', 'selesai', 'batal'])->default('selesai');
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pembelian');
    }
};
