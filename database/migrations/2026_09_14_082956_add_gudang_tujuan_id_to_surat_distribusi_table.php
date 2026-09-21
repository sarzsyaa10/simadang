<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('surat_distribusi', function (Blueprint $table) {
            $table->foreignId('gudang_tujuan_id')->nullable()->after('permohonan_bantuan_id')
                ->constrained('gudang')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('surat_distribusi', function (Blueprint $table) {
            $table->dropForeign(['gudang_tujuan_id']);
            $table->dropColumn('gudang_tujuan_id');
        });
    }
};