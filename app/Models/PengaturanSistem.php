<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PengaturanSistem extends Model
{
    protected $table = 'pengaturan_sistem';

    protected $fillable = [
        'nama_pengurus_barang',
        'nip_pengurus_barang',
        'nama_kabid_kedaruratan_logistik',
        'nip_kabid_kedaruratan_logistik',
        'nama_kepala_pelaksana_bpbd',
        'nip_kepala_pelaksana_bpbd',
    ];

    /**
     * Ambil satu-satunya baris pengaturan sistem (bikin baris kosong
     * kalau belum ada sama sekali).
     */
    public static function current(): self
    {
        return static::first() ?? static::create([]);
    }
}
