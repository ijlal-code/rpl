<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sopir extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'nama', 'no_hp', 'pengalaman'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function kendaraans()
    {
        return $this->hasMany(Kendaraan::class);
    }

    public function pesanan()
    {
        return $this->hasMany(Pesanan::class);
    }
}
