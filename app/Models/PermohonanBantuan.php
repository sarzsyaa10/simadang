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
        'user_id',
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

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function suratDistribusi()
    {
        return $this->hasMany(SuratDistribusi::class, 'permohonan_bantuan_id');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function refreshStatus(): void
    {
        $statuses = $this->permohonanBantuanDetail()->pluck('status');

        if ($statuses->isEmpty() || $statuses->contains('pending')) {
            $status = 'pending';
        } elseif ($statuses->unique()->count() === 1) {
            $status = $statuses->first();
        } else {
            $status = 'sebagian';
        }

        $this->update([
            'status' => $status,
            'diverifikasi_oleh' => auth()->id(),
        ]);
    }
}