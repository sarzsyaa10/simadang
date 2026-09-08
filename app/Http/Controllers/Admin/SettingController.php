<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PengaturanSistem;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function edit()
    {
        $pengaturan = PengaturanSistem::current();

        return view('admin.setting.edit', compact('pengaturan'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'nama_pengurus_barang'             => 'nullable|string|max:150',
            'nip_pengurus_barang'               => 'nullable|string|max:30',
            'nama_kabid_kedaruratan_logistik'   => 'nullable|string|max:150',
            'nip_kabid_kedaruratan_logistik'    => 'nullable|string|max:30',
            'nama_kepala_pelaksana_bpbd'        => 'nullable|string|max:150',
            'nip_kepala_pelaksana_bpbd'         => 'nullable|string|max:30',
        ]);

        $pengaturan = PengaturanSistem::current();
        $pengaturan->update($validated);

        return redirect()
            ->route('admin.setting')
            ->with('success', 'Pengaturan sistem berhasil diperbarui.');
    }
}
