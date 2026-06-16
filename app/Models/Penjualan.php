<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Penjualan extends Model
{
    protected $table = 'penjualan';

    protected $fillable = [
        'user_id',
        'pelanggan_id',
        'nomor_invoice',
        'tanggal_penjualan',
        'total_harga',
        'diskon',
        'total_akhir',
        'status_pembayaran',
        'jenis_struk',
    ];

    protected $casts = [
        'tanggal_penjualan' => 'datetime',
        'total_harga' => 'decimal:2',
        'diskon' => 'decimal:2',
        'total_akhir' => 'decimal:2',
    ];

    // RELASI
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function pelanggan()
    {
        return $this->belongsTo(Pelanggan::class, 'pelanggan_id');
    }

    public function getNamaPelangganDisplayAttribute(): string
    {
        return Pelanggan::displayNamaPelanggan($this->pelanggan->nama_pelanggan ?? null);
    }

    public function detailPenjualan()
    {
        return $this->hasMany(DetailPenjualan::class, 'penjualan_id');
    }

    public function pembayaran()
    {
        return $this->hasMany(Pembayaran::class, 'penjualan_id');
    }

    public function piutang()
    {
        return $this->hasOne(PiutangPelanggan::class, 'penjualan_id');
    }
}
