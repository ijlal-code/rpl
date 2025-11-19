<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kendaraan extends Model
{
    use HasFactory;

    protected $table = 'kendaraan';

    protected $fillable = ['sopir_id', 'rute_id', 'nama_kendaraan', 'kapasitas', 'status'];

    public function sopir()
    {
        return $this->belongsTo(Sopir::class);
    }

    public function rute()
    {
        return $this->belongsTo(Rute::class);
    }

    public function pesanan()
    {
        return $this->hasMany(Pesanan::class);
    }
}
