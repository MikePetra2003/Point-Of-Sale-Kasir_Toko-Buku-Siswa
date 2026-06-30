<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('barang_satuan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('barang_id')->constrained('barang')->cascadeOnDelete();
            $table->foreignId('satuan_id')->constrained('satuan')->restrictOnDelete();
            $table->unsignedInteger('konversi_ke_satuan_dasar')->default(1);
            $table->decimal('harga_beli', 15, 2)->default(0);
            $table->decimal('harga_jual', 15, 2)->default(0);
            $table->boolean('is_satuan_dasar')->default(false);
            $table->timestamps();

            $table->unique(['barang_id', 'satuan_id']);
        });

        DB::table('barang')
            ->select(['id', 'satuan_id', 'harga_beli', 'harga_jual', 'created_at', 'updated_at'])
            ->orderBy('id')
            ->each(function ($barang) {
                DB::table('barang_satuan')->insert([
                    'barang_id' => $barang->id,
                    'satuan_id' => $barang->satuan_id,
                    'konversi_ke_satuan_dasar' => 1,
                    'harga_beli' => $barang->harga_beli,
                    'harga_jual' => $barang->harga_jual,
                    'is_satuan_dasar' => true,
                    'created_at' => $barang->created_at ?? now(),
                    'updated_at' => $barang->updated_at ?? now(),
                ]);
            });

        Schema::table('detail_pembelian', function (Blueprint $table) {
            $table->foreignId('barang_satuan_id')->nullable()->after('barang_id')->constrained('barang_satuan')->nullOnDelete();
            $table->foreignId('satuan_id')->nullable()->after('barang_satuan_id')->constrained('satuan')->nullOnDelete();
            $table->unsignedInteger('jumlah_satuan')->default(0)->after('jumlah');
            $table->unsignedInteger('konversi_satuan')->default(1)->after('jumlah_satuan');
        });

        Schema::table('detail_penjualan', function (Blueprint $table) {
            $table->foreignId('barang_satuan_id')->nullable()->after('barang_id')->constrained('barang_satuan')->nullOnDelete();
            $table->foreignId('satuan_id')->nullable()->after('barang_satuan_id')->constrained('satuan')->nullOnDelete();
            $table->unsignedInteger('jumlah_satuan')->default(0)->after('jumlah');
            $table->unsignedInteger('konversi_satuan')->default(1)->after('jumlah_satuan');
        });
    }

    public function down(): void
    {
        Schema::table('detail_penjualan', function (Blueprint $table) {
            $table->dropConstrainedForeignId('barang_satuan_id');
            $table->dropConstrainedForeignId('satuan_id');
            $table->dropColumn(['jumlah_satuan', 'konversi_satuan']);
        });

        Schema::table('detail_pembelian', function (Blueprint $table) {
            $table->dropConstrainedForeignId('barang_satuan_id');
            $table->dropConstrainedForeignId('satuan_id');
            $table->dropColumn(['jumlah_satuan', 'konversi_satuan']);
        });

        Schema::dropIfExists('barang_satuan');
    }
};
