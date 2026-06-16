<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HutangSupplier extends Model
{
    protected $table = 'hutang_supplier';

    /** Persentase bunga keterlambatan dari sisa hutang. */
    public const BUNGA_PERSEN = 5;

    protected $fillable = [
        'pembelian_id',
        'supplier_id',
        'total_hutang',
        'total_dibayar',
        'sisa_hutang',
        'status',
        'tanggal_jatuh_tempo',
        'keterangan',
    ];

    protected $casts = [
        'total_hutang' => 'decimal:2',
        'total_dibayar' => 'decimal:2',
        'sisa_hutang' => 'decimal:2',
        'tanggal_jatuh_tempo' => 'date',
    ];

    // RELASI
    public function pembelian()
    {
        return $this->belongsTo(Pembelian::class, 'pembelian_id');
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }

    public function pembayaranHutang()
    {
        return $this->hasMany(PembayaranHutang::class, 'hutang_id');
    }

    // Apakah hutang sudah mencapai/melewati jatuh tempo dan belum lunas.
    public function getIsOverdueAttribute(): bool
    {
        return $this->status !== 'lunas'
            && $this->tanggal_jatuh_tempo
            && today()->greaterThanOrEqualTo($this->tanggal_jatuh_tempo);
    }

    // Bunga 5% dari sisa hutang, berlaku mulai tanggal jatuh tempo.
    public function getBungaAttribute(): float
    {
        return $this->is_overdue
            ? round((float) $this->sisa_hutang * self::BUNGA_PERSEN / 100, 2)
            : 0;
    }

    // Total yang harus dibayar (sisa hutang + bunga keterlambatan).
    public function getTotalHarusBayarAttribute(): float
    {
        return (float) $this->sisa_hutang + $this->bunga;
    }
}
