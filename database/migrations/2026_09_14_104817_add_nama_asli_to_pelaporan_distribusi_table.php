<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pelaporan_distribusi', function (Blueprint $table) {
            $table->string('scan_surat_nama_asli', 255)->nullable()->after('scan_surat');
            $table->string('bukti_foto_nama_asli', 255)->nullable()->after('bukti_foto');
        });
    }

    public function down(): void
    {
        Schema::table('pelaporan_distribusi', function (Blueprint $table) {
            $table->dropColumn(['scan_surat_nama_asli', 'bukti_foto_nama_asli']);
        });
    }
};