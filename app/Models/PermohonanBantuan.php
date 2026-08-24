<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PermohonanBantuan extends Model
{
    protected $table = 'permohonan_bantuan';

    protected $fillable = [
        'tanggal',
        'jam',
        'nama_pemohon',
        'jabatan',
        'alamat',
        'status',
        'diverifikasi_oleh',
    ];

    protected function casts(): array
    {
        return [
            'tanggal' => 'date',
        ];
    }

    public function permohonanBantuanDetail()
    {
        return $this->hasMany(PermohonanBantuanDetail::class, 'permohonan_bantuan_id');
    }

    public function verifikator()
    {
        return $this->belongsTo(User::class, 'diverifikasi_oleh');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }
}