<?php

namespace App\Http\Controllers\Admin;

use App\Enums\AccountStatus;
use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AdminUserController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->string('search')->toString();
        $users = User::whereIn('role', [UserRole::SuperAdmin, UserRole::AdminManager])
            ->when($search, function ($q) use ($search) {
                $like = '%' . addcslashes($search, '%_\\') . '%';
                $q->where(function ($qq) use ($like) {
                    $qq->where('nama', 'like', $like)->orWhere('email', 'like', $like);
                });
            })
            ->orderBy('nama')
            ->paginate(15)
            ->withQueryString();

        return view('admin.admin-users.index', compact('users', 'search'));
    }

    public function create(): View
    {
        return view('admin.admin-users.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role' => ['required', Rule::in([UserRole::SuperAdmin->value, UserRole::AdminManager->value])],
        ]);

        $user = User::create([
            'nama' => $validated['nama'],
            'email' => $validated['email'],
            'password' => $validated['password'],
        ]);
        $user->forceFill(['role' => $validated['role'], 'status' => AccountStatus::Aktif])->save();

        return redirect()->route('admin.admin-users.index')->with('success', 'Admin berhasil ditambahkan.');
    }

    public function edit(User $adminUser): View
    {
        return view('admin.admin-users.edit', compact('adminUser'));
    }

    public function update(Request $request, User $adminUser): RedirectResponse
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($adminUser->id)],
            'role' => ['required', Rule::in([UserRole::SuperAdmin->value, UserRole::AdminManager->value])],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);

        if ($adminUser->id === auth()->id() && $validated['role'] !== UserRole::SuperAdmin->value) {
            return back()->withErrors(['role' => 'Tidak bisa mengubah role diri sendiri dari superadmin.'])->withInput();
        }

        $data = [
            'nama' => $validated['nama'],
            'email' => $validated['email'],
        ];
        if (! empty($validated['password'])) {
            $data['password'] = $validated['password'];
        }

        $adminUser->update($data);
        $adminUser->forceFill(['role' => $validated['role']])->save();

        return redirect()->route('admin.admin-users.index')->with('success', 'Admin berhasil diperbarui.');
    }

    public function destroy(User $adminUser): RedirectResponse
    {
        if ($adminUser->id === auth()->id()) {
            return back()->with('error', 'Tidak bisa menghapus akun sendiri.');
        }

        $adminUser->delete();

        return redirect()->route('admin.admin-users.index')->with('success', 'Admin berhasil dihapus.');
    }

    public function updateStatus(User $user): RedirectResponse
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Tidak bisa menonaktifkan akun sendiri.');
        }

        if (! in_array($user->role, [UserRole::SuperAdmin, UserRole::AdminManager], true)) {
            abort(404);
        }

        $new = $user->status === AccountStatus::Aktif ? AccountStatus::Nonaktif : AccountStatus::Aktif;
        $user->setAccountStatus($new);

        return back()->with('success', 'Status admin diubah menjadi ' . $new->value . '.');
    }
}
