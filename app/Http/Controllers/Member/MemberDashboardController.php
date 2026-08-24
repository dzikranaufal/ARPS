<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class MemberDashboardController extends Controller
{
    public function index(): View
    {
        $member = auth()->user();
        $publications = $member->publications()->latest()->paginate(5);

        return view('member.dashboard', compact('member', 'publications'));
    }
}
