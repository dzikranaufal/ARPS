<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class MemberProfileController extends Controller
{
    public function edit(): View
    {
        $member = auth()->user();

        return view('member.profile', compact('member'));
    }

    public function update(Request $request): RedirectResponse
    {
        $member = auth()->user();

        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'telepon' => ['required', 'string', 'max:20'],
            'organisasi' => ['nullable', 'string', 'max:255'],
            'foto' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
            'current_password' => ['nullable', 'string', 'current_password'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);

        // If password baru diisi, current_password wajib
        if (! empty($validated['password']) && empty($validated['current_password'])) {
            return back()->withErrors(['current_password' => 'Password saat ini wajib diisi untuk mengganti password.'])->withInput();
        }

        $data = [
            'nama' => $validated['nama'],
            'telepon' => $validated['telepon'],
            'organisasi' => $validated['organisasi'] ?? null,
        ];

        if ($request->hasFile('foto')) {
            if ($member->foto) {
                Storage::disk('public')->delete($member->foto);
            }
            $data['foto'] = $request->file('foto')->store('profiles', 'public');
        }

        if (! empty($validated['password'])) {
            $data['password'] = $validated['password'];
        }

        $member->update($data);

        return redirect()->route('member.profile.edit')->with('success', 'Profil berhasil diperbarui.');
    }
}
