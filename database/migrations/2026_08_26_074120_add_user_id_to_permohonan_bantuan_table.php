<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('permohonan_bantuan', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->after('diverifikasi_oleh')
            ->constrained('user')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('permohonan_bantuan', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColoumn('user_id');
        });
    }
};
