<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    protected $table = 'supplier';

    protected $fillable = [
        'nama_supplier',
        'no_telepon',
        'email',
        'alamat',
    ];

    public function barang()
    {
        return $this->hasMany(Barang::class, 'supplier_id');
    }
}
