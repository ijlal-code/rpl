<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JadwalSopir extends Model
{
    use HasFactory;

    protected $fillable = [
        'sopir_id',
        'rute_id',
        'tanggal_keberangkatan',
        'jam_keberangkatan',
        'status',
        'catatan',
    ];

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
        return $this->hasMany(Pesanan::class, 'jadwal_id');
    }
}
