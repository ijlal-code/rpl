<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('kendaraan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sopir_id')->constrained('sopirs')->onDelete('cascade');
            $table->foreignId('rute_id')->constrained('rute')->onDelete('cascade');
            $table->string('nama_kendaraan');
            $table->integer('kapasitas');
            $table->enum('status', ['siap', 'jalan', 'selesai'])->default('siap');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kendaraan');
    }
};
