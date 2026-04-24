<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('refleksis', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal');
            $table->text('berhasil');
            $table->text('gagal');
            $table->text('perbaikan');
            $table->text('target');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('refleksis');
    }
};
