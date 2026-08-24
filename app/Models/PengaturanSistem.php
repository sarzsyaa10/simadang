<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PengaturanSistem extends Model
{
    protected $table = 'pengaturan_sistem';

    protected $fillable = [
        'nama_penanggung_jawab',
        'hp_penanggung_jawab',
        'nama_kepala_pelaksana_bpbd',
        'hp_kepala_pelaksana',
        'nama_penggung_jawab_barang',
        'hp_penggung_jawab_barang',
    ];

    public static function current(): self
    {
        return static::first() ?? static::create([]);
    }
}