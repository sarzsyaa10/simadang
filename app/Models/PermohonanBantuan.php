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

    /**
     * Hitung ulang status header berdasarkan status semua barang di
     * permohonan ini. Dipanggil setelah admin setuju/tolak salah satu barang.
     *
     * - Masih ada barang yang belum diputuskan -> tetap 'pending' (Diajukan)
     * - Semua barang statusnya sama (semua disetujui / semua ditolak) -> ikut itu
     * - Campuran (sebagian disetujui, sebagian ditolak) -> 'sebagian'
     */
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
