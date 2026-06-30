<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetailPenjualan extends Model
{
    protected $table = 'detail_penjualan';

    protected $fillable = [
        'penjualan_id',
        'barang_id',
        'barang_satuan_id',
        'satuan_id',
        'jumlah',
        'jumlah_satuan',
        'konversi_satuan',
        'harga_jual',
        'subtotal',
    ];

    protected $casts = [
        'harga_jual' => 'decimal:2',
        'subtotal' => 'decimal:2',
    ];

    // RELASI
    public function penjualan()
    {
        return $this->belongsTo(Penjualan::class, 'penjualan_id');
    }

    public function barang()
    {
        return $this->belongsTo(Barang::class, 'barang_id');
    }

    public function barangSatuan()
    {
        return $this->belongsTo(BarangSatuan::class, 'barang_satuan_id');
    }

    public function satuan()
    {
        return $this->belongsTo(Satuan::class, 'satuan_id');
    }
}
