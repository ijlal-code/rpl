<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jadwal_sopirs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sopir_id')->constrained('sopirs')->cascadeOnDelete();
            $table->foreignId('rute_id')->constrained('rutes')->cascadeOnDelete();
            $table->date('tanggal_keberangkatan');
            $table->time('jam_keberangkatan');
            $table->enum('status', ['aktif', 'sedang_jalan', 'tidak_aktif'])->default('tidak_aktif');
            $table->string('catatan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jadwal_sopirs');
    }
};
