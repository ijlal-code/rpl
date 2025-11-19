<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sopir extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'nama',
        'nomor_sim',
        'telepon',
        'pengalaman',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function kendaraans()
    {
        return $this->hasMany(Kendaraan::class);
    }

    public function pesanans()
    {
        return $this->hasMany(Pesanan::class);
    }

    public function jadwal()
    {
        return $this->hasMany(JadwalSopir::class);
    }
}
