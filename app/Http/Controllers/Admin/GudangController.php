<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Gudang;
use App\Models\User;
use Illuminate\Http\Request;

class GudangController extends Controller
{
    public function index(Request $request)
    {
        $gudang = Gudang::with('operator')
            ->when($request->filled('q'), fn ($q) => $q->where('nama_gudang', 'like', '%' . $request->q . '%'))
            ->orderBy('nama_gudang')
            ->paginate(10)
            ->withQueryString();

        return view('admin.gudang.index', compact('gudang'));
    }

    public function create()
    {
        $operatorList = User::where('role', 'upt')->orderBy('nama')->get();

        return view('admin.gudang.create', compact('operatorList'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_gudang' => 'required|string|max:150',
            'lokasi' => 'nullable|string|max:255',
            'operator_gudang_id' => 'nullable|exists:user,id',
        ]);

        Gudang::create($validated);

        return redirect()
            ->route('admin.gudang.index')
            ->with('success', 'Gudang baru berhasil ditambahkan.');
    }

    public function edit(Gudang $gudang)
    {
        $operatorList = User::where('role', 'upt')->orderBy('nama')->get();

        return view('admin.gudang.edit', compact('gudang', 'operatorList'));
    }

    public function update(Request $request, Gudang $gudang)
    {
        $validated = $request->validate([
            'nama_gudang' => 'required|string|max:150',
            'lokasi' => 'nullable|string|max:255',
            'operator_gudang_id' => 'nullable|exists:user,id',
        ]);

        $gudang->update($validated);

        return redirect()
            ->route('admin.gudang.index')
            ->with('success', 'Data gudang berhasil diperbarui.');
    }

    public function destroy(Gudang $gudang)
    {
        $gudang->delete();

        return redirect()
            ->route('admin.gudang.index')
            ->with('success', 'Gudang berhasil dihapus.');
    }
}
