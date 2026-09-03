<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PermohonanBantuanDetail extends Model
{
    protected $table = 'permohonan_bantuan_detail';

    protected $fillable = [
        'permohonan_bantuan_id',
        'barang_id',
        'jumlah',
        'status',
        'keterangan',
    ];

    public function permohonanBantuan()
    {
        return $this->belongsTo(PermohonanBantuan::class, 'permohonan_bantuan_id');
    }

    public function barang()
    {
        return $this->belongsTo(Barang::class, 'barang_id');
    }
}