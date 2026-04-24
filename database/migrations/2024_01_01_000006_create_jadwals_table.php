<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jadwals', function (Blueprint $table) {
            $table->id();
            $table->string('jam', 10);
            $table->string('judul');
            $table->string('keterangan')->nullable();
            $table->enum('kategori', ['Sekolah', 'S2', 'Mandiri', 'Lainnya'])->default('Lainnya');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jadwals');
    }
};
