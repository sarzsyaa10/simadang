<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('permohonan_bantuan_detail', function (Blueprint $table) {
            $table->enum('status', ['pending', 'disetujui', 'ditolak'])->default('pending')->after('jumlah');
            $table->text('keterangan')->nullable()->after('status');
        });

        DB::statement("ALTER TABLE permohonan_bantuan MODIFY COLUMN status ENUM('pending','disetujui','ditolak','sebagian') NOT NULL DEFAULT 'pending'");
    }

    public function down(): void
    {
        Schema::table('permohonan_bantuan_detail', function (Blueprint $table) {
            $table->dropColumn(['status', 'keterangan']);
        });

        DB::statement("ALTER TABLE permohonan_bantuan MODIFY COLUMN status ENUM('pending','disetujui','ditolak') NOT NULL DEFAULT 'pending'");
    }
};