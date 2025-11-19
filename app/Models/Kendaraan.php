<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kendaraan extends Model
{
    use HasFactory;

    protected $fillable = [
        'sopir_id',
        'nama',
        'plat_nomor',
        'jenis',
        'kapasitas',
        'status',
    ];

    public function sopir()
    {
        return $this->belongsTo(Sopir::class);
    }

    public function pesanans()
    {
        return $this->hasMany(Pesanan::class);
    }
}
