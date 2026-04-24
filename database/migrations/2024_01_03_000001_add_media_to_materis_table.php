<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('materis', function (Blueprint $table) {
            $table->string('link_url')->nullable()->after('catatan');
            $table->enum('link_type', ['youtube', 'canva', 'drive', 'pdf', 'other'])->nullable()->after('link_url');
            $table->string('file_pdf')->nullable()->after('link_type'); // path file upload
        });
    }

    public function down(): void
    {
        Schema::table('materis', function (Blueprint $table) {
            $table->dropColumn(['link_url', 'link_type', 'file_pdf']);
        });
    }
};
