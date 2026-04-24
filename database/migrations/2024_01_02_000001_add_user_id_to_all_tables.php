<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $tables = ['jurnals', 'tugas', 'materis', 'refleksis', 'jadwals', 'progres_s2', 'notes'];
        foreach ($tables as $table) {
            Schema::table($table, function (Blueprint $t) {
                $t->unsignedBigInteger('user_id')->nullable()->after('id');
                $t->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            });
        }
    }

    public function down(): void
    {
        $tables = ['jurnals', 'tugas', 'materis', 'refleksis', 'jadwals', 'progres_s2', 'notes'];
        foreach ($tables as $table) {
            Schema::table($table, function (Blueprint $t) use ($table) {
                $t->dropForeign([$table === 'notes' ? 'notes_user_id_foreign' : "{$table}_user_id_foreign"]);
                $t->dropColumn('user_id');
            });
        }
    }
};
