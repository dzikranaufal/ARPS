<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\OrganizationStructureRequest;
use App\Models\OrganizationStructure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class OrganizationStructureController extends Controller
{
    public function index(): View
    {
        $structures = OrganizationStructure::orderBy('nama_pengurus')->paginate(10);

        return view('admin.organization.structure.index', compact('structures'));
    }

    public function create(): View
    {
        return view('admin.organization.structure.create');
    }

    public function store(OrganizationStructureRequest $request): RedirectResponse
    {
        $data = $request->validated();
        if ($request->hasFile('foto')) {
            $data['foto'] = $request->file('foto')->store('organization', 'public');
        }
        OrganizationStructure::create($data);

        return redirect()->route('admin.structure.index')->with('success', 'Pengurus berhasil ditambahkan.');
    }

    public function edit(OrganizationStructure $structure): View
    {
        return view('admin.organization.structure.edit', compact('structure'));
    }

    public function update(OrganizationStructureRequest $request, OrganizationStructure $structure): RedirectResponse
    {
        $data = $request->validated();
        if ($request->hasFile('foto')) {
            if ($structure->foto) {
                Storage::disk('public')->delete($structure->foto);
            }
            $data['foto'] = $request->file('foto')->store('organization', 'public');
        }
        $structure->update($data);

        return redirect()->route('admin.structure.index')->with('success', 'Pengurus berhasil diperbarui.');
    }

    public function destroy(OrganizationStructure $structure): RedirectResponse
    {
        if ($structure->foto) {
            Storage::disk('public')->delete($structure->foto);
        }
        $structure->delete();

        return redirect()->route('admin.structure.index')->with('success', 'Pengurus berhasil dihapus.');
    }
}
