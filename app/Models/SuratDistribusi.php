<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SuratDistribusi extends Model
{
    protected $table = 'surat_distribusi';

    protected $fillable = [
        'nomor_surat',
        'tanggal',
        'jam',
        'kendaraan',
        'tujuan',
        'petugas',
        'user_id',
        'permohonan_bantuan_id',
    ];

    protected function casts(): array
    {
        return [
            'tanggal' => 'date',
        ];
    }

    public function distribusiDetail()
    {
        return $this->hasMany(DistribusiDetail::class, 'surat_distribusi_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function pelaporanDistribusi()
    {
        return $this->hasOne(PelaporanDistribusi::class, 'surat_distribusi_id');
    }

    public function permohonanBantuan()
    {
        return $this->belongsTo(PermohonanBantuan::class, 'permohonan_bantuan_id');
    }
}