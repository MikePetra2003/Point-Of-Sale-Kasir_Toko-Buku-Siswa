<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PiutangPelanggan extends Model
{
    protected $table = 'piutang_pelanggan';

    protected $fillable = [
        'penjualan_id',
        'pelanggan_id',
        'total_piutang',
        'total_dibayar',
        'sisa_piutang',
        'status',
        'tanggal_jatuh_tempo',
        'keterangan',
    ];

    protected $casts = [
        'total_piutang' => 'decimal:2',
        'total_dibayar' => 'decimal:2',
        'sisa_piutang' => 'decimal:2',
        'tanggal_jatuh_tempo' => 'date',
    ];

    // RELASI
    public function penjualan()
    {
        return $this->belongsTo(Penjualan::class, 'penjualan_id');
    }

    public function pelanggan()
    {
        return $this->belongsTo(Pelanggan::class, 'pelanggan_id');
    }

    public function pembayaranPiutang()
    {
        return $this->hasMany(PembayaranPiutang::class, 'piutang_id');
    }
}
