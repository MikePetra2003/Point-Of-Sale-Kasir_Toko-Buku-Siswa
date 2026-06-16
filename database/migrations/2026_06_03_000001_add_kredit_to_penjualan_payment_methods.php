<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::getConnection()->getDriverName() === 'sqlite') {
            $this->rebuildPembayaranTable();
            $this->rebuildPembayaranPiutangTable();

            return;
        }

        DB::statement("ALTER TABLE pembayaran MODIFY metode_pembayaran ENUM('tunai', 'qris', 'transfer', 'kredit')");
        DB::statement("ALTER TABLE pembayaran_piutang MODIFY metode_pembayaran ENUM('tunai', 'qris', 'transfer', 'kredit')");
    }

    public function down(): void
    {
        DB::table('pembayaran')->where('metode_pembayaran', 'kredit')->update([
            'metode_pembayaran' => 'tunai',
        ]);

        DB::table('pembayaran_piutang')->where('metode_pembayaran', 'kredit')->update([
            'metode_pembayaran' => 'tunai',
        ]);

        if (Schema::getConnection()->getDriverName() === 'sqlite') {
            $this->rebuildPembayaranTableWithoutKredit();
            $this->rebuildPembayaranPiutangTableWithoutKredit();

            return;
        }

        DB::statement("ALTER TABLE pembayaran MODIFY metode_pembayaran ENUM('tunai', 'qris', 'transfer')");
        DB::statement("ALTER TABLE pembayaran_piutang MODIFY metode_pembayaran ENUM('tunai', 'qris', 'transfer')");
    }

    private function rebuildPembayaranTable(): void
    {
        Schema::disableForeignKeyConstraints();

        Schema::create('pembayaran_new', function (Blueprint $table) {
            $table->id();
            $table->foreignId('penjualan_id')->constrained('penjualan')->cascadeOnDelete();
            $table->dateTime('tanggal_pembayaran');
            $table->string('metode_pembayaran', 20);
            $table->decimal('jumlah_bayar', 15, 2);
            $table->string('bukti_pembayaran', 255)->nullable();
            $table->enum('status_pembayaran', ['pending', 'berhasil', 'gagal'])->default('pending');
            $table->timestamps();
        });

        DB::table('pembayaran_new')->insertUsing(
            ['id', 'penjualan_id', 'tanggal_pembayaran', 'metode_pembayaran', 'jumlah_bayar', 'bukti_pembayaran', 'status_pembayaran', 'created_at', 'updated_at'],
            DB::table('pembayaran')->select('id', 'penjualan_id', 'tanggal_pembayaran', 'metode_pembayaran', 'jumlah_bayar', 'bukti_pembayaran', 'status_pembayaran', 'created_at', 'updated_at')
        );

        Schema::drop('pembayaran');
        Schema::rename('pembayaran_new', 'pembayaran');

        Schema::enableForeignKeyConstraints();
    }

    private function rebuildPembayaranPiutangTable(): void
    {
        Schema::disableForeignKeyConstraints();

        Schema::create('pembayaran_piutang_new', function (Blueprint $table) {
            $table->id();
            $table->foreignId('piutang_id')->constrained('piutang_pelanggan')->cascadeOnDelete();
            $table->dateTime('tanggal_bayar');
            $table->decimal('jumlah_bayar', 15, 2);
            $table->string('metode_pembayaran', 20);
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });

        DB::table('pembayaran_piutang_new')->insertUsing(
            ['id', 'piutang_id', 'tanggal_bayar', 'jumlah_bayar', 'metode_pembayaran', 'keterangan', 'created_at', 'updated_at'],
            DB::table('pembayaran_piutang')->select('id', 'piutang_id', 'tanggal_bayar', 'jumlah_bayar', 'metode_pembayaran', 'keterangan', 'created_at', 'updated_at')
        );

        Schema::drop('pembayaran_piutang');
        Schema::rename('pembayaran_piutang_new', 'pembayaran_piutang');

        Schema::enableForeignKeyConstraints();
    }

    private function rebuildPembayaranTableWithoutKredit(): void
    {
        Schema::disableForeignKeyConstraints();

        Schema::create('pembayaran_new', function (Blueprint $table) {
            $table->id();
            $table->foreignId('penjualan_id')->constrained('penjualan')->cascadeOnDelete();
            $table->dateTime('tanggal_pembayaran');
            $table->enum('metode_pembayaran', ['tunai', 'qris', 'transfer']);
            $table->decimal('jumlah_bayar', 15, 2);
            $table->string('bukti_pembayaran', 255)->nullable();
            $table->enum('status_pembayaran', ['pending', 'berhasil', 'gagal'])->default('pending');
            $table->timestamps();
        });

        DB::table('pembayaran_new')->insertUsing(
            ['id', 'penjualan_id', 'tanggal_pembayaran', 'metode_pembayaran', 'jumlah_bayar', 'bukti_pembayaran', 'status_pembayaran', 'created_at', 'updated_at'],
            DB::table('pembayaran')->select('id', 'penjualan_id', 'tanggal_pembayaran', 'metode_pembayaran', 'jumlah_bayar', 'bukti_pembayaran', 'status_pembayaran', 'created_at', 'updated_at')
        );

        Schema::drop('pembayaran');
        Schema::rename('pembayaran_new', 'pembayaran');

        Schema::enableForeignKeyConstraints();
    }

    private function rebuildPembayaranPiutangTableWithoutKredit(): void
    {
        Schema::disableForeignKeyConstraints();

        Schema::create('pembayaran_piutang_new', function (Blueprint $table) {
            $table->id();
            $table->foreignId('piutang_id')->constrained('piutang_pelanggan')->cascadeOnDelete();
            $table->dateTime('tanggal_bayar');
            $table->decimal('jumlah_bayar', 15, 2);
            $table->enum('metode_pembayaran', ['tunai', 'qris', 'transfer']);
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });

        DB::table('pembayaran_piutang_new')->insertUsing(
            ['id', 'piutang_id', 'tanggal_bayar', 'jumlah_bayar', 'metode_pembayaran', 'keterangan', 'created_at', 'updated_at'],
            DB::table('pembayaran_piutang')->select('id', 'piutang_id', 'tanggal_bayar', 'jumlah_bayar', 'metode_pembayaran', 'keterangan', 'created_at', 'updated_at')
        );

        Schema::drop('pembayaran_piutang');
        Schema::rename('pembayaran_piutang_new', 'pembayaran_piutang');

        Schema::enableForeignKeyConstraints();
    }
};
