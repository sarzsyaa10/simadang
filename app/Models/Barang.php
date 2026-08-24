<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Barang extends Model
{
    protected $table = 'barang';

    protected $fillable = [
        'nama_barang',
        'kategori',
        'satuan',
        'foto',
        'deskripsi',
    ];

    public function stokBarang()
    {
        return $this->hasMany(StokBarang::class, 'barang_id');
    }

    public function mutasiBarang()
    {
        return $this->hasMany(MutasiBarang::class, 'barang_id');
    }

    public function getTotalStokAttribute()
    {
        return $this->stokBarang()->sum('jumlah');
    }

    public function scopeLogistik($query)
    {
        return $query->where('kategori', 'logistik_non_permakanan');
    }

    public function scopePeralatan($query)
    {
        return $query->where('kategori', 'peralatan');
    }
}