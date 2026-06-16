<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pembelian extends Model
{
    protected $table = 'pembelian';

    protected $fillable = [
        'user_id',
        'supplier_id',
        'nomor_faktur',
        'tanggal_pembelian',
        'total_harga',
        'diskon',
        'diskon_persen',
        'status_pembayaran',
        'status',
        'keterangan',
    ];

    protected $casts = [
        'tanggal_pembelian' => 'datetime',
        'total_harga' => 'decimal:2',
        'diskon' => 'decimal:2',
        'diskon_persen' => 'decimal:2',
    ];

    public function getTotalAkhirAttribute(): float
    {
        return max(0, (float) $this->total_harga - (float) $this->diskon);
    }

    public function getTotalDibayarSupplierAttribute(): float
    {
        if ($this->hutangSupplier) {
            return (float) $this->hutangSupplier->total_dibayar;
        }

        return $this->status_pembayaran === 'lunas' ? $this->total_akhir : 0;
    }

    public function getSisaHutangSupplierAttribute(): float
    {
        if ($this->hutangSupplier) {
            return (float) $this->hutangSupplier->sisa_hutang;
        }

        return $this->status_pembayaran === 'lunas' ? 0 : $this->total_akhir;
    }

    // RELASI
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }

    public function detailPembelian()
    {
        return $this->hasMany(DetailPembelian::class, 'pembelian_id');
    }

    public function hutangSupplier()
    {
        return $this->hasOne(HutangSupplier::class, 'pembelian_id');
    }
}
