<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rutes', function (Blueprint $table) {
            $table->id();
            $table->string('nama_rute');
            $table->string('asal');
            $table->string('tujuan');
            $table->decimal('jarak_km', 8, 2)->nullable();
            $table->time('perkiraan_waktu')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rutes');
    }
};
