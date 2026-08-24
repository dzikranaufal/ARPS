<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\Publication;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PublicationController extends Controller
{
    public function index(): View
    {
        $publications = auth()->user()->publications()->latest()->paginate(10);

        return view('member.publications.index', compact('publications'));
    }

    public function create(): View
    {
        return view('member.publications.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'judul' => ['required', 'string', 'max:255'],
            'deskripsi' => ['nullable', 'string', 'max:2000'],
            'kategori' => ['required', 'in:tulisan,prestasi,produk,pkm'],
            'file' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png,docx', 'max:10240'],
        ]);

        $path = $request->file('file')->store('publications', 'public');

        Publication::create([
            'member_id' => auth()->id(),
            'judul' => $validated['judul'],
            'deskripsi' => isset($validated['deskripsi']) ? strip_tags($validated['deskripsi']) : null,
            'kategori' => $validated['kategori'],
            'file' => $path,
            'status' => 'pending',
            'reviewer_id' => null,
        ]);

        return redirect()->route('member.publications.index')->with('success', 'Karya terkirim, menunggu review.');
    }

    public function download(Publication $publication): StreamedResponse
    {
        abort_unless(auth()->id() === $publication->member_id, 403);

        abort_unless($publication->file && Storage::disk('public')->exists($publication->file), 404);

        $ext = pathinfo($publication->file, PATHINFO_EXTENSION);
        $name = 'publication-' . $publication->id . '.' . $ext;

        return Storage::disk('public')->download($publication->file, $name);
    }
}
