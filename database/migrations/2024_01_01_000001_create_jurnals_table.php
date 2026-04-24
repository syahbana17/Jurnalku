<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jurnals', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal');
            $table->string('hari', 20);
            $table->string('materi');
            $table->string('kelas', 50);
            $table->string('metode')->nullable();
            $table->text('kendala')->nullable();
            $table->text('evaluasi')->nullable();
            $table->string('matkul_s2')->nullable();
            $table->string('tugas_s2')->nullable();
            $table->text('insight')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jurnals');
    }
};
