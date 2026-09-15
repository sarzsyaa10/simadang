<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DistribusiDetail extends Model
{
    protected $table = 'distribusi_detail';

    protected $fillable = [
        'surat_distribusi_id',
        'barang_id',
        'gudang_id',
        'jumlah',
        'sumber',
        'keterangan',
    ];

    public function suratDistribusi()
    {
        return $this->belongsTo(SuratDistribusi::class, 'surat_distribusi_id');
    }

    public function barang()
    {
        return $this->belongsTo(Barang::class, 'barang_id');
    }

    public function gudang()
    {
        return $this->belongsTo(Gudang::class, 'gudang_id');
    }
}