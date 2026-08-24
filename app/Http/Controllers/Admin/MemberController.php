<?php

namespace App\Http\Controllers\Admin;

use App\Enums\AccountStatus;
use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MemberController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->string('search')->toString();

        $members = User::where('role', UserRole::Member)
            ->when($search, function ($query) use ($search) {
                $like = '%' . addcslashes($search, '%_\\') . '%';
                $query->where(function ($q) use ($like) {
                    $q->where('nama', 'like', $like)
                        ->orWhere('email', 'like', $like);
                });
            })
            ->withCount('publications')
            ->orderBy('nama')
            ->paginate(15)
            ->withQueryString();

        return view('admin.members.index', compact('members', 'search'));
    }

    public function show(User $member): View
    {
        abort_unless($member->role === UserRole::Member, 404);

        $member->loadCount('publications');
        $publications = $member->publications()->latest()->paginate(10);

        return view('admin.members.show', compact('member', 'publications'));
    }

    public function updateStatus(Request $request, User $member): RedirectResponse
    {
        abort_unless($member->role === UserRole::Member, 404);

        if ($member->id === auth()->id()) {
            return back()->with('error', 'Tidak dapat menonaktifkan akun sendiri.');
        }

        $new = $member->status === AccountStatus::Aktif ? AccountStatus::Nonaktif : AccountStatus::Aktif;
        $member->setAccountStatus($new);

        return back()->with('success', 'Status member berhasil diubah menjadi ' . $new->value . '.');
    }
}
