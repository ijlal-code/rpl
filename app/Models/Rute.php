<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rute extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_rute',
        'asal',
        'tujuan',
        'jarak_km',
        'perkiraan_waktu',
    ];

    public function pesanans()
    {
        return $this->hasMany(Pesanan::class);
    }
}
