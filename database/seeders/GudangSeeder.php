<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GudangSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('gudang')
            ->where('nama_gudang', 'Gudang Utama BPBD Cilacap')
            ->update([
                'nama_gudang' => 'Gudang Induk',
                'lokasi' => 'Kantor BPBD Cilacap',
                'updated_at' => now(),
            ]);

        $gudangList = [
            ['nama_gudang' => 'Gudang Radjiman', 'lokasi' => 'Jl. Radjiman'],
            ['nama_gudang' => 'Gudang UPT Cilacap', 'lokasi' => 'Cilacap'],
            ['nama_gudang' => 'Gudang UPT Majenang', 'lokasi' => 'Majenang'],
            ['nama_gudang' => 'Gudang UPT Kroya', 'lokasi' => 'Kroya'],
            ['nama_gudang' => 'Gudang UPT Sidareja', 'lokasi' => 'Sidareja'],
        ];

        foreach ($gudangList as $gudang) {
            DB::table('gudang')->updateOrInsert(
                ['nama_gudang' => $gudang['nama_gudang']],
                [
                    'lokasi' => $gudang['lokasi'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}