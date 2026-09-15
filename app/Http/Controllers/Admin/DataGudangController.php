<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Gudang;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class DataGudangController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');

        $query = Gudang::with('operator')->latest();

        if ($search) {
            $query->where('nama_gudang', 'like', "%{$search}%");
        }

        $gudangList = $query->paginate(10)->withQueryString();

        return view('admin.data-gudang.index', compact('gudangList', 'search'));
    }

    public function create()
    {
        $operatorList = $this->operatorOptions();

        return view('admin.data-gudang.create', compact('operatorList'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_gudang'        => 'required|string|max:150',
            'lokasi'              => 'nullable|string|max:255',
            'operator_gudang_id' => 'nullable|exists:user,id',
        ]);

        $gudang = Gudang::create([
            'nama_gudang'        => $validated['nama_gudang'],
            'lokasi'              => $validated['lokasi'] ?? null,
            'operator_gudang_id' => $validated['operator_gudang_id'] ?? null,
        ]);

        $this->syncOperator($gudang, $validated['operator_gudang_id'] ?? null);

        return redirect()
            ->route('admin.data-gudang.index')
            ->with('success', 'Gudang berhasil ditambahkan.');
    }

    public function edit(Gudang $dataGudang)
    {
        $operatorList = $this->operatorOptions($dataGudang->id);

        return view('admin.data-gudang.edit', ['gudang' => $dataGudang, 'operatorList' => $operatorList]);
    }

    public function update(Request $request, Gudang $dataGudang)
    {
        $validated = $request->validate([
            'nama_gudang'        => 'required|string|max:150',
            'lokasi'              => 'nullable|string|max:255',
            'operator_gudang_id' => 'nullable|exists:user,id',
        ]);

        $dataGudang->update([
            'nama_gudang'        => $validated['nama_gudang'],
            'lokasi'              => $validated['lokasi'] ?? null,
            'operator_gudang_id' => $validated['operator_gudang_id'] ?? null,
        ]);

        $this->syncOperator($dataGudang, $validated['operator_gudang_id'] ?? null);

        return redirect()
            ->route('admin.data-gudang.index')
            ->with('success', 'Gudang berhasil diperbarui.');
    }

    public function destroy(Gudang $dataGudang)
    {
        $masihDipakai = $dataGudang->stokBarang()->exists()
            || $dataGudang->mutasiBarang()->exists()
            || $dataGudang->user()->exists();

        if ($masihDipakai) {
            return back()->with('error', 'Gudang tidak bisa dihapus karena masih memiliki data terkait (stok/mutasi/user).');
        }

        $dataGudang->delete();

        return redirect()
            ->route('admin.data-gudang.index')
            ->with('success', 'Gudang berhasil dihapus.');
    }

    /**
     * Daftar user role upt yang bisa dipilih sebagai operator gudang
     * (yang belum jadi operator gudang lain, ditambah operator gudang saat ini kalau edit).
     */
    protected function operatorOptions(?int $currentGudangId = null)
    {
        return User::where('role', 'upt')
            ->where(function ($q) use ($currentGudangId) {
                $q->whereNull('gudang_id');
                if ($currentGudangId) {
                    $q->orWhere('gudang_id', $currentGudangId);
                }
            })
            ->orderBy('nama')
            ->get();
    }

    /**
     * Samakan user.gudang_id dengan gudang yang baru dipilih sebagai operator,
     * dan lepaskan operator lama kalau diganti.
     */
    protected function syncOperator(Gudang $gudang, ?int $newOperatorId): void
    {
        User::where('gudang_id', $gudang->id)
            ->when($newOperatorId, fn ($q) => $q->where('id', '!=', $newOperatorId))
            ->update(['gudang_id' => null]);

        if ($newOperatorId) {
            User::whereKey($newOperatorId)->update(['gudang_id' => $gudang->id]);
        }
    }
}