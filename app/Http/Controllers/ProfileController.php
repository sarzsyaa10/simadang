<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    public function show()
    {
        $user = auth()->user();

        return view('profile.show', compact('user'));
    }

    public function edit()
    {
        $user = auth()->user();

        return view('profile.edit', compact('user'));
    }

    public function update(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'nama'     => 'required|string|max:150',
            'username' => ['required', 'string', 'max:100', Rule::unique('user', 'username')->ignore($user->id)],
            'alamat'   => 'nullable|string|max:255',
            'jabatan'  => 'nullable|string|max:150',
            'password' => 'nullable|string|min:6|confirmed',
        ]);

        $user->nama     = $validated['nama'];
        $user->username = $validated['username'];
        $user->alamat   = $validated['alamat'] ?? null;
        $user->jabatan  = $validated['jabatan'] ?? null;

        if (!empty($validated['password'])) {
            $user->password = $validated['password'];
        }

        $user->save();

        return redirect()
            ->route('profile')
            ->with('success', 'Profil berhasil diperbarui.');
    }
}
