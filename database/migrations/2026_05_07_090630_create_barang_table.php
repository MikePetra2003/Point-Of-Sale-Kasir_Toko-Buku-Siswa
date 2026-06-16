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
        Schema::create('barang', function (Blueprint $table) {
            $table->id();
            $table->string('kode_barang', 20)->unique();
            $table->string('nama_barang', 100);

            $table->foreignId('kategori_id')
                ->constrained('kategori')
                ->restrictOnDelete();

            $table->foreignId('satuan_id')
                ->constrained('satuan')
                ->restrictOnDelete();

            $table->foreignId('supplier_id')
                ->nullable()
                ->constrained('supplier')
                ->nullOnDelete();

            $table->decimal('harga_beli', 15, 2)->default(0);
            $table->decimal('harga_jual', 15, 2)->default(0);
            $table->integer('stok')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('barang');
    }
};
