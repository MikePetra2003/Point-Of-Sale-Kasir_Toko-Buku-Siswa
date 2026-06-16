<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetailPembelian extends Model
{
    protected $table = 'detail_pembelian';

    protected $fillable = [
        'pembelian_id',
        'barang_id',
        'jumlah',
        'jumlah_pack',
        'isi_per_pack',
        'harga_beli',
        'subtotal',
    ];

    protected $casts = [
        'harga_beli' => 'decimal:2',
        'subtotal' => 'decimal:2',
    ];

    // RELASI
    public function pembelian()
    {
        return $this->belongsTo(Pembelian::class, 'pembelian_id');
    }

    public function barang()
    {
        return $this->belongsTo(Barang::class, 'barang_id');
    }
}
