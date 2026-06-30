<?php

namespace App\Models;
use App\Models\BarangSatuan;
use App\Models\DetailPembelian;
use App\Models\DetailPenjualan;

use Illuminate\Database\Eloquent\Model;

class Barang extends Model
{
    protected $table = 'barang';

    protected $fillable = [
        'kode_barang',
        'nama_barang',
        'kategori_id',
        'satuan_id',
        'supplier_id',
        'harga_beli',
        'harga_jual',
        'stok',
        'is_active',
    ];

    protected static function booted(): void
    {
        static::created(function (Barang $barang) {
            if ($barang->satuan_id && ! $barang->barangSatuan()->exists()) {
                BarangSatuan::create([
                    'barang_id' => $barang->id,
                    'satuan_id' => $barang->satuan_id,
                    'konversi_ke_satuan_dasar' => 1,
                    'harga_beli' => $barang->harga_beli,
                    'harga_jual' => $barang->harga_jual,
                    'is_satuan_dasar' => true,
                ]);
            }
        });
    }

    // RELASI BELONGSTO
    public function kategori()
    {
        return $this->belongsTo(Kategori::class, 'kategori_id');
    }

    public function satuan()
    {
        return $this->belongsTo(Satuan::class, 'satuan_id');
    }

    public function barangSatuan()
    {
        return $this->hasMany(BarangSatuan::class, 'barang_id');
    }

    public function satuanDasar()
    {
        return $this->hasOne(BarangSatuan::class, 'barang_id')->where('is_satuan_dasar', true);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }

    public function detailPembelian()
{
    return $this->hasMany(DetailPembelian::class, 'barang_id');
}

    public function detailPenjualan()
{
    return $this->hasMany(DetailPenjualan::class, 'barang_id');
}
}
