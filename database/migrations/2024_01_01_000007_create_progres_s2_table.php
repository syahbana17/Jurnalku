<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('progres_s2', function (Blueprint $table) {
            $table->id();
            $table->string('label');
            $table->unsignedTinyInteger('persen'); // 0-100
            $table->enum('warna', ['blue', 'green', 'yellow', 'purple'])->default('blue');
            $table->unsignedSmallInteger('urutan')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('progres_s2');
    }
};
