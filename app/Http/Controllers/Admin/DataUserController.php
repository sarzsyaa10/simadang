<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class DataUserController extends Controller
{
    public function index(Request $request)
    {
        $role   = $request->get('role');
        $search = $request->get('search');

        $query = User::with('gudang')->latest();

        if ($role) {
            $query->where('role', $role);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('username', 'like', "%{$search}%");
            });
        }

        $userList = $query->paginate(10)->withQueryString();

        return view('admin.data-user.index', compact('userList', 'role', 'search'));
    }

    public function create()
    {
        return view('admin.data-user.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama'                  => 'required|string|max:150',
            'username'              => 'required|string|max:100|unique:user,username',
            'jabatan'               => 'nullable|string|max:150',
            'password'              => 'required|string|min:6|confirmed',
            'alamat'                => 'nullable|string|max:255',
            'role'                  => 'required|in:admin,upt',
        ]);

        User::create([
            'nama'     => $validated['nama'],
            'username' => $validated['username'],
            'jabatan'  => $validated['jabatan'] ?? null,
            'password' => $validated['password'],
            'alamat'   => $validated['alamat'] ?? null,
            'role'     => $validated['role'],
        ]);

        return redirect()
            ->route('admin.data-user.index')
            ->with('success', 'User berhasil ditambahkan.');
    }

    public function edit(User $dataUser)
    {
        return view('admin.data-user.edit', ['user' => $dataUser]);
    }

    public function update(Request $request, User $dataUser)
    {
        $validated = $request->validate([
            'nama'     => 'required|string|max:150',
            'username' => ['required', 'string', 'max:100', Rule::unique('user', 'username')->ignore($dataUser->id)],
            'jabatan'  => 'nullable|string|max:150',
            'alamat'   => 'nullable|string|max:255',
            'role'     => 'required|in:admin,upt',
            'password' => 'nullable|string|min:6|confirmed',
        ]);

        $dataUser->nama     = $validated['nama'];
        $dataUser->username = $validated['username'];
        $dataUser->jabatan  = $validated['jabatan'] ?? null;
        $dataUser->alamat   = $validated['alamat'] ?? null;
        $dataUser->role     = $validated['role'];

        if (!empty($validated['password'])) {
            $dataUser->password = $validated['password'];
        }

        $dataUser->save();

        return redirect()
            ->route('admin.data-user.index')
            ->with('success', 'User berhasil diperbarui.');
    }

    public function destroy(User $dataUser)
    {
        if ($dataUser->id === auth()->id()) {
            return back()->with('error', 'Anda tidak bisa menghapus akun Anda sendiri.');
        }

        // Lepaskan penautan sebagai operator gudang (jika ada) sebelum menghapus
        \App\Models\Gudang::where('operator_gudang_id', $dataUser->id)
            ->update(['operator_gudang_id' => null]);

        $dataUser->delete();

        return redirect()
            ->route('admin.data-user.index')
            ->with('success', 'User berhasil dihapus.');
    }
}
