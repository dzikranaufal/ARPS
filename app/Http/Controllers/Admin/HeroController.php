<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\HeroRequest;
use App\Models\Hero;
use Illuminate\Support\Facades\Storage;

class HeroController extends Controller
{
    public function index()
    {
        $heroes = Hero::orderBy('urutan')->orderByDesc('created_at')->paginate(10);
        return view('admin.heroes.index', compact('heroes'));
    }

    public function create()
    {
        return view('admin.heroes.create');
    }

    public function store(HeroRequest $request)
    {
        $data = $request->validated();
        if ($request->hasFile('gambar')) {
            $data['gambar'] = $request->file('gambar')->store('heroes','public');
        }
        $data['urutan'] = $data['urutan'] ?? 0;
        Hero::create($data);
        return redirect()->route('admin.heroes.index')->with('success','Hero berhasil ditambahkan.');
    }

    public function edit(Hero $hero)
    {
        return view('admin.heroes.edit', compact('hero'));
    }

    public function update(HeroRequest $request, Hero $hero)
    {
        $data = $request->validated();
        if ($request->hasFile('gambar')) {
            if ($hero->gambar) Storage::disk('public')->delete($hero->gambar);
            $data['gambar'] = $request->file('gambar')->store('heroes','public');
        }
        $hero->update($data);
        return redirect()->route('admin.heroes.index')->with('success','Hero berhasil diperbarui.');
    }

    public function destroy(Hero $hero)
    {
        if ($hero->gambar) Storage::disk('public')->delete($hero->gambar);
        $hero->delete();
        return redirect()->route('admin.heroes.index')->with('success','Hero berhasil dihapus.');
    }
}
