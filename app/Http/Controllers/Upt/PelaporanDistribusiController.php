<?php

namespace App\Http\Controllers\Upt;

use App\Http\Controllers\Controller;
use App\Models\PelaporanDistribusi;
use App\Models\SuratDistribusi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PelaporanDistribusiController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 5);

        $pelaporan = PelaporanDistribusi::with('suratDistribusi')
            ->where('user_id', auth()->id())
            ->when($request->search, fn ($q) => $q->where('nama_upt', 'like', '%' . $request->search . '%'))
            ->orderByDesc('tanggal_lapor')
            ->paginate($perPage)
            ->withQueryString();

        return view('upt.pelaporan-distribusi.index', [
            'pageTitle' => 'Pelaporan Distribusi',
            'pelaporan' => $pelaporan,
            'search' => $request->search,
            'perPage' => $perPage,
        ]);
    }

    public function create()
    {
        $gudangId = auth()->user()->gudang_id;

        // Surat distribusi yang ditujukan ke gudang UPT ini, dan BELUM pernah dilaporkan
        $suratList = SuratDistribusi::whereHas('distribusiDetail', function ($q) use ($gudangId) {
                $q->where('gudang_id', $gudangId);
            })
            ->whereDoesntHave('pelaporanDistribusi')
            ->orderByDesc('tanggal')
            ->get();

        return view('upt.pelaporan-distribusi.create', [
            'pageTitle' => 'Pelaporan Distribusi',
            'suratList' => $suratList,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'surat_distribusi_id' => 'required|exists:surat_distribusi,id',
            'tanggal_lapor' => 'required|date',
            'scan_surat' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'bukti_foto' => 'required|file|mimes:jpg,jpeg,png|max:5120',
            'koordinat' => 'nullable|string|max:100',
            'keterangan' => 'nullable|string',
        ]);

        $scanPath = $request->file('scan_surat')->store('pelaporan-distribusi/surat', 'public');
        $fotoPath = $request->file('bukti_foto')->store('pelaporan-distribusi/foto', 'public');

        PelaporanDistribusi::create([
            'surat_distribusi_id' => $request->surat_distribusi_id,
            'tanggal_lapor' => $request->tanggal_lapor,
            'nama_upt' => auth()->user()->gudang->nama_gudang ?? auth()->user()->nama,
            'scan_surat' => $scanPath,
            'bukti_foto' => $fotoPath,
            'koordinat' => $request->koordinat,
            'keterangan' => $request->keterangan,
            'user_id' => auth()->id(),
        ]);

        return redirect()->route('upt.distribusi.pelaporan.index')->with('success', 'Pelaporan distribusi berhasil dikirim.');
    }

    public function edit(PelaporanDistribusi $pelaporan)
    {
        abort_if($pelaporan->user_id !== auth()->id(), 403);

        $gudangId = auth()->user()->gudang_id;

        // Surat yang belum dilaporkan + suratnya sendiri (biar tetap muncul di dropdown pas edit)
        $suratList = SuratDistribusi::whereHas('distribusiDetail', function ($q) use ($gudangId) {
                $q->where('gudang_id', $gudangId);
            })
            ->where(function ($q) use ($pelaporan) {
                $q->whereDoesntHave('pelaporanDistribusi')
                ->orWhere('id', $pelaporan->surat_distribusi_id);
            })
            ->orderByDesc('tanggal')
            ->get();

        return view('upt.pelaporan-distribusi.edit', [
            'pageTitle' => 'Edit Pelaporan Distribusi',
            'pelaporan' => $pelaporan,
            'suratList' => $suratList,
        ]);
    }

    public function update(Request $request, PelaporanDistribusi $pelaporan)
    {
        abort_if($pelaporan->user_id !== auth()->id(), 403);

        $request->validate([
            'surat_distribusi_id' => 'required|exists:surat_distribusi,id',
            'tanggal_lapor' => 'required|date',
            'scan_surat' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'bukti_foto' => 'nullable|file|mimes:jpg,jpeg,png|max:5120',
            'koordinat' => 'nullable|string|max:100',
            'keterangan' => 'nullable|string',
        ]);

        $data = [
            'surat_distribusi_id' => $request->surat_distribusi_id,
            'tanggal_lapor' => $request->tanggal_lapor,
            'koordinat' => $request->koordinat,
            'keterangan' => $request->keterangan,
        ];

        if ($request->hasFile('scan_surat')) {
            if ($pelaporan->scan_surat) {
                Storage::disk('public')->delete($pelaporan->scan_surat);
            }
            $data['scan_surat'] = $request->file('scan_surat')->store('pelaporan-distribusi/surat', 'public');
        }

        if ($request->hasFile('bukti_foto')) {
            if ($pelaporan->bukti_foto) {
                Storage::disk('public')->delete($pelaporan->bukti_foto);
            }
            $data['bukti_foto'] = $request->file('bukti_foto')->store('pelaporan-distribusi/foto', 'public');
        }

        $pelaporan->update($data);

        return redirect()->route('upt.distribusi.pelaporan.index')->with('success', 'Pelaporan distribusi berhasil diperbarui.');
    }

    public function destroy(PelaporanDistribusi $pelaporan)
    {
        abort_if($pelaporan->user_id !== auth()->id(), 403);

        if ($pelaporan->scan_surat) {
            Storage::disk('public')->delete($pelaporan->scan_surat);
        }
        if ($pelaporan->bukti_foto) {
            Storage::disk('public')->delete($pelaporan->bukti_foto);
        }

        $pelaporan->delete();

        return redirect()->route('upt.distribusi.pelaporan.index')->with('success', 'Pelaporan distribusi berhasil dihapus.');
    }
}