<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('surat_distribusi', function (Blueprint $table) {
            $table->string('kecamatan', 100)->nullable()->after('tujuan');
            $table->string('perihal', 255)->nullable()->after('kecamatan');
        });
    }

    public function down(): void
    {
        Schema::table('surat_distribusi', function (Blueprint $table) {
            $table->dropColumn(['kecamatan', 'perihal']);
        });
    }
};
