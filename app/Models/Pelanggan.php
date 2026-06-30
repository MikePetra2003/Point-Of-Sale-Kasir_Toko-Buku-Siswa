<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pelanggan extends Model
{
    protected $table = 'pelanggan';

    protected $fillable = [
        'no_id_pelanggan',
        'nama_pelanggan',
        'no_telepon',
        'boleh_kredit',
    ];

    protected $casts = [
        'boleh_kredit' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::creating(function (Pelanggan $pelanggan) {
            if (blank($pelanggan->no_id_pelanggan)) {
                $pelanggan->no_id_pelanggan = static::generateNoIdPelanggan($pelanggan->nama_pelanggan);
            }
        });
    }

    public static function generateNoIdPelanggan(string $namaPelanggan): string
    {
        $prefix = static::prefixNoIdPelanggan($namaPelanggan);
        $lastNumber = static::where('no_id_pelanggan', 'like', "{$prefix}%")
            ->lockForUpdate()
            ->pluck('no_id_pelanggan')
            ->map(fn ($noId) => (int) substr($noId, 1))
            ->max() ?? 0;

        return $prefix.str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
    }

    public function getNamaPelangganDisplayAttribute(): string
    {
        return static::displayNamaPelanggan($this->nama_pelanggan);
    }

    public static function displayNamaPelanggan(?string $namaPelanggan): string
    {
        $namaPelanggan = trim((string) $namaPelanggan);

        if (
            $namaPelanggan === ''
            || strcasecmp($namaPelanggan, 'Umum') === 0
            || strcasecmp($namaPelanggan, 'Pelanggan Umum') === 0
        ) {
            return '-';
        }

        return $namaPelanggan;
    }

    private static function prefixNoIdPelanggan(string $namaPelanggan): string
    {
        $prefix = strtoupper(substr(trim($namaPelanggan), 0, 1));

        return preg_match('/[A-Z]/', $prefix) ? $prefix : 'X';
    }
}
