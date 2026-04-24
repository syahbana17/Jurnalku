<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('semesters', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('nama'); // "Semester 2", "Semester 3", dst
            $table->boolean('aktif')->default(false);
            $table->timestamps();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        // Tambah semester_id ke progres_s2
        Schema::table('progres_s2', function (Blueprint $table) {
            $table->unsignedBigInteger('semester_id')->nullable()->after('user_id');
            $table->foreign('semester_id')->references('id')->on('semesters')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('progres_s2', function (Blueprint $table) {
            $table->dropForeign(['semester_id']);
            $table->dropColumn('semester_id');
        });
        Schema::dropIfExists('semesters');
    }
};
