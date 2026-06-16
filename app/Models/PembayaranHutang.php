<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PembayaranHutang extends Model
{
    protected $table = 'pembayaran_hutang';

    protected $fillable = [
        'hutang_id',
        'tanggal_bayar',
        'jumlah_bayar',
        'bunga',
        'metode_pembayaran',
        'keterangan',
    ];

    protected $casts = [
        'tanggal_bayar' => 'datetime',
        'jumlah_bayar' => 'decimal:2',
        'bunga' => 'decimal:2',
    ];

    public function hutang()
    {
        return $this->belongsTo(HutangSupplier::class, 'hutang_id');
    }
}
