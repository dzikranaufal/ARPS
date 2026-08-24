<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\TechnologyInnovationRequest;
use App\Models\TechnologyInnovation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class TechnologyInnovationController extends Controller
{
    public function index(): View
    {
        $innovations = TechnologyInnovation::orderByDesc('created_at')->paginate(10);

        return view('admin.technology-innovations.index', compact('innovations'));
    }

    public function create(): View
    {
        return view('admin.technology-innovations.create');
    }

    public function store(TechnologyInnovationRequest $request): RedirectResponse
    {
        $data = $request->validated();
        if ($request->hasFile('gambar')) {
            $data['gambar'] = $request->file('gambar')->store('technology-innovations', 'public');
        }
        TechnologyInnovation::create($data);

        return redirect()->route('admin.technology-innovations.index')->with('success', 'Inovasi teknologi berhasil ditambahkan.');
    }

    public function edit(TechnologyInnovation $technologyInnovation): View
    {
        return view('admin.technology-innovations.edit', compact('technologyInnovation'));
    }

    public function update(TechnologyInnovationRequest $request, TechnologyInnovation $technologyInnovation): RedirectResponse
    {
        $data = $request->validated();
        if ($request->hasFile('gambar')) {
            if ($technologyInnovation->gambar) {
                Storage::disk('public')->delete($technologyInnovation->gambar);
            }
            $data['gambar'] = $request->file('gambar')->store('technology-innovations', 'public');
        }
        $technologyInnovation->update($data);

        return redirect()->route('admin.technology-innovations.index')->with('success', 'Inovasi teknologi berhasil diperbarui.');
    }

    public function destroy(TechnologyInnovation $technologyInnovation): RedirectResponse
    {
        if ($technologyInnovation->gambar) {
            Storage::disk('public')->delete($technologyInnovation->gambar);
        }
        $technologyInnovation->delete();

        return redirect()->route('admin.technology-innovations.index')->with('success', 'Inovasi teknologi berhasil dihapus.');
    }
}
