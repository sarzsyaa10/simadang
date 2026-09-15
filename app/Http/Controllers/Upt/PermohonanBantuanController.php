<?php

namespace App\Http\Controllers\Upt;

use App\Http\Controllers\Controller;
use App\Models\Barang;
use App\Models\PermohonanBantuan;
use App\Models\PermohonanBantuanDetail;
use Illuminate\Http\Request;

class PermohonanBantuanController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 5);

        $sortableColumns = ['tanggal', 'nama_pemohon', 'jabatan', 'alamat', 'status'];
        $sort = in_array($request->input('sort'), $sortableColumns) ? $request->input('sort') : 'tanggal';
        $direction = $request->input('direction') === 'asc' ? 'asc' : 'desc';

        $permohonan = PermohonanBantuan::with('permohonanBantuanDetail.barang')
            ->where('user_id', auth()->id())
            ->when($request->search, fn ($q) => $q->where('nama_pemohon', 'like', '%' . $request->search . '%'))
            ->orderBy($sort, $direction)
            ->paginate($perPage)
            ->withQueryString();

        return view('upt.permohonan-bantuan.index', [
            'pageTitle' => 'Permohonan Bantuan',
            'permohonan' => $permohonan,
            'search' => $request->search,
            'perPage' => $perPage,
            'sort' => $sort,
            'direction' => $direction,
        ]);
    }

    private function getBarangList()
    {
        $adminGudangIds = \App\Models\Gudang::gudangUtama()->pluck('id');

        return Barang::whereHas('stokBarang', function ($q) use ($adminGudangIds) {
            $q->whereIn('gudang_id', $adminGudangIds)->where('jumlah', '>', 0);
        })
        ->with(['stokBarang' => function ($q) use ($adminGudangIds) {
            $q->whereIn('gudang_id', $adminGudangIds);
        }])
        ->orderBy('nama_barang')
        ->get();
    }

    public function create()
    {
        return view('upt.permohonan-bantuan.create', [
            'pageTitle' => 'Permohonan Bantuan',
            'barangList' => $this->getBarangList(),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'jam' => 'required',
            'nama_pemohon' => 'required|string|max:150',
            'jabatan' => 'nullable|string|max:150',
            'alamat' => 'nullable|string|max:255',
            'barang' => 'required|array|min:1',
            'barang.*.nama_barang' => 'required|string|max:150',
            'barang.*.jumlah' => 'required|integer|min:1',
        ]);

        $permohonan = PermohonanBantuan::create([
            'tanggal' => $request->tanggal,
            'jam' => $request->jam,
            'nama_pemohon' => $request->nama_pemohon,
            'jabatan' => $request->jabatan,
            'alamat' => $request->alamat,
            'status' => 'pending',
            'user_id' => auth()->id(),
        ]);

        foreach ($request->barang as $item) {
            // Cek dulu apakah barang dengan nama ini sudah ada (biar tidak duplikat)
            $barang = Barang::whereRaw('LOWER(nama_barang) = ?', [strtolower(trim($item['nama_barang']))])->first();

            if (! $barang) {
                // Beneran barang baru, belum pernah ada di sistem
                $barang = Barang::create([
                    'nama_barang' => trim($item['nama_barang']),
                    'kategori' => 'logistik_non_permakanan', // default, admin bisa perbaiki nanti
                    'satuan' => '-',
                ]);
            }

            PermohonanBantuanDetail::create([
                'permohonan_bantuan_id' => $permohonan->id,
                'barang_id' => $barang->id,
                'jumlah' => $item['jumlah'],
            ]);
        }

        return redirect()->route('upt.permohonan.index')->with('success', 'Permohonan bantuan berhasil diajukan.');
    }

    public function destroy(PermohonanBantuan $permohonan)
    {
        // Cuma boleh hapus punya sendiri, dan cuma kalau masih pending
        abort_if($permohonan->user_id !== auth()->id(), 403);
        abort_if($permohonan->status !== 'pending', 403, 'Permohonan yang sudah diproses tidak bisa dihapus.');

        $permohonan->permohonanBantuanDetail()->delete();
        $permohonan->delete();

        return redirect()->route('upt.permohonan.index')->with('success', 'Permohonan berhasil dihapus.');
    }

        public function show(PermohonanBantuan $permohonan)
    {
        abort_if($permohonan->user_id !== auth()->id(), 403);

        return view('upt.permohonan-bantuan.show', [
            'pageTitle' => 'Detail Permohonan Bantuan',
            'permohonan' => $permohonan->load('permohonanBantuanDetail.barang'),
        ]);
    }

    public function edit(PermohonanBantuan $permohonan)
    {
        abort_if($permohonan->user_id !== auth()->id(), 403);
        abort_if($permohonan->status !== 'pending', 403, 'Permohonan yang sudah diproses tidak bisa diedit.');

        return view('upt.permohonan-bantuan.edit', [
            'pageTitle' => 'Edit Permohonan Bantuan',
            'permohonan' => $permohonan->load('permohonanBantuanDetail'),
            'barangList' => $this->getBarangList(),
        ]);
    }

    public function update(Request $request, PermohonanBantuan $permohonan)
    {
        abort_if($permohonan->user_id !== auth()->id(), 403);
        abort_if($permohonan->status !== 'pending', 403);

        $request->validate([
            'tanggal' => 'required|date',
            'jam' => 'required',
            'nama_pemohon' => 'required|string|max:150',
            'jabatan' => 'nullable|string|max:150',
            'alamat' => 'nullable|string|max:255',
            'barang' => 'required|array|min:1',
            'barang.*.nama_barang' => 'required|string|max:150',
            'barang.*.jumlah' => 'required|integer|min:1',
        ]);

        $permohonan->update([
            'tanggal' => $request->tanggal,
            'jam' => $request->jam,
            'nama_pemohon' => $request->nama_pemohon,
            'jabatan' => $request->jabatan,
            'alamat' => $request->alamat,
        ]);

        // Hapus detail lama, ganti dengan yang baru (paling simpel & aman)
        $permohonan->permohonanBantuanDetail()->delete();

        foreach ($request->barang as $item) {
            $barang = Barang::whereRaw('LOWER(nama_barang) = ?', [strtolower(trim($item['nama_barang']))])->first();

            if (! $barang) {
                $barang = Barang::create([
                    'nama_barang' => trim($item['nama_barang']),
                    'kategori' => 'logistik_non_permakanan',
                    'satuan' => '-',
                ]);
            }

            PermohonanBantuanDetail::create([
                'permohonan_bantuan_id' => $permohonan->id,
                'barang_id' => $barang->id,
                'jumlah' => $item['jumlah'],
            ]);
        }

        return redirect()->route('upt.permohonan.index')->with('success', 'Permohonan berhasil diperbarui.');
    }
}