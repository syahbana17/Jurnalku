<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tugas', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->enum('kategori', ['Sekolah', 'S2', 'Pribadi']);
            $table->date('deadline');
            $table->enum('status', ['Belum', 'Sedang Dikerjakan', 'Selesai'])->default('Belum');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tugas');
    }
};
