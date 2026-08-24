<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Gudang extends Model
{
    protected $table = 'gudang';

    protected $fillable = [
        'nama_gudang',
        'lokasi',
        'operator_gudang_id',
    ];

    public function operator()
    {
        return $this->belongsTo(User::class, 'operator_gudang_id');
    }

    public function user()
    {
        return $this->hasMany(User::class, 'gudang_id');
    }

    public function stokBarang()
    {
        return $this->hasMany(StokBarang::class, 'gudang_id');
    }

    public function mutasiBarang()
    {
        return $this->hasMany(MutasiBarang::class, 'gudang_id');
    }
}