<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MutasiBarang extends Model
{
    protected $table = 'mutasi_barang';

    protected $fillable = [
        'barang_id',
        'gudang_id',
        'user_id',
        'tanggal',
        'jam',
        'area',
        'jumlah',
        'keterangan',
    ];

    protected function casts(): array
    {
        return [
            'tanggal' => 'date',
        ];
    }

    public function barang()
    {
        return $this->belongsTo(Barang::class, 'barang_id');
    }

    public function gudang()
    {
        return $this->belongsTo(Gudang::class, 'gudang_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function scopeMasuk($query)
    {
        return $query->where('area', 'masuk');
    }

    public function scopeKeluar($query)
    {
        return $query->where('area', 'keluar');
    }
}