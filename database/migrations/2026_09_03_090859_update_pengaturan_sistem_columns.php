<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pengaturan_sistem', function (Blueprint $table) {
            $table->dropColumn([
                'nama_penanggung_jawab',
                'hp_penanggung_jawab',
                'nama_kepala_pelaksana_bpbd',
                'hp_kepala_pelaksana',
                'nama_penggung_jawab_barang',
                'hp_penggung_jawab_barang',
            ]);
        });

        Schema::table('pengaturan_sistem', function (Blueprint $table) {
            $table->string('nama_pengurus_barang', 150)->nullable()->after('id');
            $table->string('nip_pengurus_barang', 30)->nullable()->after('nama_pengurus_barang');
            $table->string('nama_kabid_kedaruratan_logistik', 150)->nullable()->after('nip_pengurus_barang');
            $table->string('nip_kabid_kedaruratan_logistik', 30)->nullable()->after('nama_kabid_kedaruratan_logistik');
            $table->string('nama_kepala_pelaksana_bpbd', 150)->nullable()->after('nip_kabid_kedaruratan_logistik');
            $table->string('nip_kepala_pelaksana_bpbd', 30)->nullable()->after('nama_kepala_pelaksana_bpbd');
        });
    }

    public function down(): void
    {
        Schema::table('pengaturan_sistem', function (Blueprint $table) {
            $table->dropColumn([
                'nama_pengurus_barang',
                'nip_pengurus_barang',
                'nama_kabid_kedaruratan_logistik',
                'nip_kabid_kedaruratan_logistik',
                'nama_kepala_pelaksana_bpbd',
                'nip_kepala_pelaksana_bpbd',
            ]);
        });

        Schema::table('pengaturan_sistem', function (Blueprint $table) {
            $table->string('nama_penanggung_jawab', 150)->nullable();
            $table->string('hp_penanggung_jawab', 30)->nullable();
            $table->string('nama_kepala_pelaksana_bpbd', 150)->nullable();
            $table->string('hp_kepala_pelaksana', 30)->nullable();
            $table->string('nama_penggung_jawab_barang', 150)->nullable();
            $table->string('hp_penggung_jawab_barang', 30)->nullable();
        });
    }
};