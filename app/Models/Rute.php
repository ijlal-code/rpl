<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rute extends Model
{
    use HasFactory;

    protected $table = 'rute';

    protected $fillable = ['nama_rute', 'titik_berangkat', 'titik_tujuan', 'estimasi_waktu'];

    public function kendaraans()
    {
        return $this->hasMany(Kendaraan::class);
    }

    public function pesanan()
    {
        return $this->hasMany(Pesanan::class);
    }
}
