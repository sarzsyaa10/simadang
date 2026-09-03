<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PelaporanDistribusi extends Model
{
    protected $table = 'pelaporan_distribusi';

    protected $fillable = [
        'surat_distribusi_id',
        'tanggal_lapor',
        'nama_upt',
        'scan_surat',
        'bukti_foto',
        'koordinat',
        'keterangan',
        'user_id',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_lapor' => 'date',
        ];
    }

    public function suratDistribusi()
    {
        return $this->belongsTo(SuratDistribusi::class, 'surat_distribusi_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}