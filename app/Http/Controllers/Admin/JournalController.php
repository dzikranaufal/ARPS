<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\JournalRequest;
use App\Models\Journal;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class JournalController extends Controller
{
    public function index(): View
    {
        $journals = Journal::orderBy('nama')->paginate(10);

        return view('admin.journals.index', compact('journals'));
    }

    public function create(): View
    {
        return view('admin.journals.create');
    }

    public function store(JournalRequest $request): RedirectResponse
    {
        $data = $request->validated();

        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['nama']);
        }

        if ($request->hasFile('cover')) {
            $data['cover'] = $request->file('cover')->store('journals', 'public');
        }

        try {
            Journal::create($data);
        } catch (\Illuminate\Database\QueryException $e) {
            return back()->withInput()->withErrors(['slug' => 'Slug sudah terpakai. Gunakan slug lain.']);
        }

        return redirect()->route('admin.journals.index')->with('success', 'Jurnal berhasil ditambahkan.');
    }

    public function edit(Journal $journal): View
    {
        return view('admin.journals.edit', compact('journal'));
    }

    public function update(JournalRequest $request, Journal $journal): RedirectResponse
    {
        $data = $request->validated();

        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['nama']);
        }

        if ($request->hasFile('cover')) {
            if ($journal->cover) {
                Storage::disk('public')->delete($journal->cover);
            }
            $data['cover'] = $request->file('cover')->store('journals', 'public');
        }

        try {
            $journal->update($data);
        } catch (\Illuminate\Database\QueryException $e) {
            return back()->withInput()->withErrors(['slug' => 'Slug sudah terpakai. Gunakan slug lain.']);
        }

        return redirect()->route('admin.journals.index')->with('success', 'Jurnal berhasil diperbarui.');
    }

    public function destroy(Journal $journal): RedirectResponse
    {
        if ($journal->cover) {
            Storage::disk('public')->delete($journal->cover);
        }
        $journal->delete();

        return redirect()->route('admin.journals.index')->with('success', 'Jurnal berhasil dihapus.');
    }
}
