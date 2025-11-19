<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kendaraans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sopir_id')->nullable()->constrained('sopirs')->nullOnDelete();
            $table->string('nama');
            $table->string('plat_nomor');
            $table->string('jenis');
            $table->integer('kapasitas');
            $table->enum('status', ['siap', 'jalan', 'selesai'])->default('siap');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kendaraans');
    }
};
