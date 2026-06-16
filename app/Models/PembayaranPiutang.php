<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PembayaranPiutang extends Model
{
    protected $table = 'pembayaran_piutang';

    protected $fillable = [
        'piutang_id',
        'tanggal_bayar',
        'jumlah_bayar',
        'metode_pembayaran',
        'keterangan',
    ];

    protected $casts = [
        'tanggal_bayar' => 'datetime',
        'jumlah_bayar' => 'decimal:2',
    ];

    // RELASI
    public function piutang()
    {
        return $this->belongsTo(PiutangPelanggan::class, 'piutang_id');
    }
}
