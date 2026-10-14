<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('surat_distribusi', 'perihal')) {
            Schema::table('surat_distribusi', function (Blueprint $table) {
                $table->string('perihal', 255)->nullable()->after('kecamatan');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('surat_disribusi', 'perihal')) {
            Schema::table('surat_distribusi', function (Blueprint $table) {
                $table->dropColumn('perihal');
            });
        }
    }
};