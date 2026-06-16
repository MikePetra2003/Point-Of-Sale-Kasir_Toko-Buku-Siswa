<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pembayaran extends Model
{
    protected $table = 'pembayaran';

    protected $fillable = [
        'penjualan_id',
        'tanggal_pembayaran',
        'metode_pembayaran',
        'jumlah_bayar',
        'bukti_pembayaran',
        'status_pembayaran',
    ];

    protected $casts = [
        'tanggal_pembayaran' => 'datetime',
        'jumlah_bayar' => 'decimal:2',
    ];

    // RELASI
    public function penjualan()
    {
        return $this->belongsTo(Penjualan::class, 'penjualan_id');
    }
}
