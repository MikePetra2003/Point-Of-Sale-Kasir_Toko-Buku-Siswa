<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BarangSatuan extends Model
{
    protected $table = 'barang_satuan';

    protected $fillable = [
        'barang_id',
        'satuan_id',
        'konversi_ke_satuan_dasar',
        'harga_beli',
        'harga_jual',
        'is_satuan_dasar',
    ];

    protected $casts = [
        'konversi_ke_satuan_dasar' => 'integer',
        'harga_beli' => 'decimal:2',
        'harga_jual' => 'decimal:2',
        'is_satuan_dasar' => 'boolean',
    ];

    public function barang()
    {
        return $this->belongsTo(Barang::class, 'barang_id');
    }

    public function satuan()
    {
        return $this->belongsTo(Satuan::class, 'satuan_id');
    }
}
