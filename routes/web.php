<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HutangController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\PelangganController;
use App\Http\Controllers\PembelianController;
use App\Http\Controllers\PenjualanController;
use App\Http\Controllers\PiutangController;
use App\Http\Controllers\SupplierController;
use Illuminate\Support\Facades\Route;

Route::get('/', [AuthenticatedSessionController::class, 'create'])
    ->middleware('guest')  
    ->name('home');

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'role:owner'])
    ->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::middleware(['role:owner,karyawan_kasir'])->group(function () {
        Route::get('/barang', [BarangController::class, 'index'])->name('barang.index');

        // Transaksi Penjualan / Kasir
        Route::get('/penjualan', [PenjualanController::class, 'index'])->name('penjualan.index');
        Route::get('/penjualan/create', [PenjualanController::class, 'create'])->name('penjualan.create');
        Route::post('/penjualan', [PenjualanController::class, 'store'])->name('penjualan.store');
        Route::get('/penjualan/{penjualan}', [PenjualanController::class, 'show'])->name('penjualan.show');
        Route::post('/penjualan/{penjualan}/print-thermal', [PenjualanController::class, 'printThermal'])->name('penjualan.print-thermal');

        // Data pelanggan: kasir boleh tambah dan koreksi data, tetapi tidak boleh hapus.
        Route::resource('pelanggan', PelangganController::class)->except(['show', 'destroy']);

        Route::middleware(['role:owner'])->group(function () {
            Route::get('/piutang/kartu', [PiutangController::class, 'kartu'])->name('kartu.piutang.index');
            Route::get('/piutang/kartu/{pelanggan}', [PiutangController::class, 'kartuDetail'])->name('kartu.piutang.show');
        });

        // Detail piutang hasil transaksi kasir
        Route::get('/piutang/{piutang}/edit', [PiutangController::class, 'edit'])->name('piutang.edit');
        Route::patch('/piutang/{piutang}', [PiutangController::class, 'update'])->name('piutang.update');
        Route::get('/piutang/{piutang}', [PiutangController::class, 'show'])->name('piutang.show');
    });

    Route::middleware(['role:owner'])->group(function () {
        Route::resource('barang', BarangController::class)->except(['index', 'show']);
        Route::resource('supplier', SupplierController::class)->except(['show']);
        Route::delete('/pelanggan/{pelanggan}', [PelangganController::class, 'destroy'])->name('pelanggan.destroy');

        // Piutang Pelanggan
        Route::get('/piutang', [PiutangController::class, 'index'])->name('piutang.index');
        Route::post('/piutang/{piutang}/bayar', [PiutangController::class, 'bayar'])->name('piutang.bayar');
        Route::get('/piutang/{piutang}/edit-general', [PiutangController::class, 'editGeneral'])->name('piutang.edit-general');
        Route::put('/piutang/{piutang}/update-general', [PiutangController::class, 'updateGeneral'])->name('piutang.update-general');
        Route::delete('/piutang/{piutang}', [PiutangController::class, 'destroy'])->name('piutang.destroy');

        // Pembelian Supplier
        Route::get('/pembelian', [PembelianController::class, 'index'])->name('pembelian.index');
        Route::get('/pembelian/create', [PembelianController::class, 'create'])->name('pembelian.create');
        Route::post('/pembelian', [PembelianController::class, 'store'])->name('pembelian.store');
        Route::get('/pembelian/{pembelian}', [PembelianController::class, 'show'])->name('pembelian.show');
        Route::get('/pembelian/{pembelian}/export/pdf', [PembelianController::class, 'exportPdf'])->name('pembelian.export.pdf');
        Route::delete('/pembelian/{pembelian}', [PembelianController::class, 'destroy'])->name('pembelian.destroy');

        // Hutang Supplier
        Route::get('/hutang', [HutangController::class, 'index'])->name('hutang.index');
        Route::get('/hutang/kartu', [HutangController::class, 'kartu'])->name('kartu.hutang.index');
        Route::get('/hutang/kartu/{supplier}', [HutangController::class, 'kartuDetail'])->name('kartu.hutang.show');
        Route::get('/hutang/{hutang}', [HutangController::class, 'show'])->name('hutang.show');
        Route::post('/hutang/{hutang}/bayar', [HutangController::class, 'bayar'])->name('hutang.bayar');
        Route::get('/hutang/{hutang}/edit', [HutangController::class, 'edit'])->name('hutang.edit');
        Route::put('/hutang/{hutang}', [HutangController::class, 'update'])->name('hutang.update');
        Route::delete('/hutang/{hutang}', [HutangController::class, 'destroy'])->name('hutang.destroy');

        // Laporan
        Route::get('/laporan/penjualan', [LaporanController::class, 'penjualan'])->name('laporan.penjualan');
        Route::get('/laporan/penjualan/export/xlsx', [LaporanController::class, 'exportPenjualanXlsx'])->name('laporan.penjualan.export.xlsx');
        Route::get('/laporan/penjualan/export/pdf', [LaporanController::class, 'exportPenjualanPdf'])->name('laporan.penjualan.export.pdf');
        Route::get('/laporan/pembelian', [LaporanController::class, 'pembelian'])->name('laporan.pembelian');
    });
});

require __DIR__.'/auth.php';
