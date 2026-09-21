<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('surat_distribusi', function (Blueprint $table) {
            $table->string('tingkat_posko', 100)->nullable()->after('tujuan');
        });
    }

    public function down(): void
    {
        Schema::table('surat_distribusi', function (Blueprint $table) {
            $table->dropColumn('tingkat_posko');
        });
    }
};