<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pesanan extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'sopir_id',
        'kendaraan_id',
        'jadwal_id',
        'rute_id',
        'tanggal_keberangkatan',
        'jam_keberangkatan',
        'status',
        'alasan_pembatalan',
        'catatan',
    ];

    public function penumpang()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function sopir()
    {
        return $this->belongsTo(Sopir::class);
    }

    public function kendaraan()
    {
        return $this->belongsTo(Kendaraan::class);
    }

    public function rute()
    {
        return $this->belongsTo(Rute::class);
    }

    public function jadwal()
    {
        return $this->belongsTo(JadwalSopir::class, 'jadwal_id');
    }
}
