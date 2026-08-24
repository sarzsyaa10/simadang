<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('user')->updateOrInsert(
            ['username' => 'admin'],
            [
                'nama' => 'Administrator',
                'password' => Hash::make('admin123456'), // ganti sesuai password yang kamu mau
                'alamat' => 'Cilacap',
                'jabatan' => 'Admin Sistem',
                'role' => 'admin',
                'gudang_id' => null,
                'updated_at' => now(),
                'created_at' => now(),
            ]
        );
        
        $uptList = [
            ['username' => 'upt_cilacap', 'nama' => 'UPT Cilacap', 'gudang' => 'Gudang UPT Cilacap'],
            ['username' => 'upt_majenang', 'nama' => 'UPT Majenang', 'gudang' => 'Gudang UPT Majenang'],
            ['username' => 'upt_kroya', 'nama' => 'UPT Kroya', 'gudang' => 'Gudang UPT Kroya'],
            ['username' => 'upt_sidareja', 'nama' => 'UPT Sidareja', 'gudang' => 'Gudang UPT Sidareja'],
        ];

        foreach ($uptList as $upt) {
            $gudangId = DB::table('gudang')->where('nama_gudang', $upt['gudang'])->value('id');

            DB::table('user')->updateOrInsert(
                ['username' => $upt['username']],
                [
                    'nama' => $upt['nama'],
                    'password' => Hash::make('upt123'),
                    'alamat' => null,
                    'jabatan' => 'Operator Gudang',
                    'role' => 'upt',
                    'gudang_id' => $gudangId,
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );
        }
    }
}