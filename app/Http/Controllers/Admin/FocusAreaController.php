<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\FocusAreaRequest;
use App\Models\FocusArea;

class FocusAreaController extends Controller
{
    public function index()
    {
        $areas = FocusArea::orderBy('urutan')->paginate(10);
        return view('admin.focus-areas.index', compact('areas'));
    }

    public function create()
    {
        return view('admin.focus-areas.create');
    }

    public function store(FocusAreaRequest $request)
    {
        $data = $request->validated();
        $data['urutan'] = $data['urutan'] ?? 0;
        FocusArea::create($data);
        return redirect()->route('admin.focus-areas.index')->with('success','Focus area berhasil ditambahkan.');
    }

    public function edit(FocusArea $focusArea)
    {
        return view('admin.focus-areas.edit', compact('focusArea'));
    }

    public function update(FocusAreaRequest $request, FocusArea $focusArea)
    {
        $data = $request->validated();
        $focusArea->update($data);
        return redirect()->route('admin.focus-areas.index')->with('success','Focus area berhasil diperbarui.');
    }

    public function destroy(FocusArea $focusArea)
    {
        $focusArea->delete();
        return redirect()->route('admin.focus-areas.index')->with('success','Focus area berhasil dihapus.');
    }
}
