<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\OrganizationProfileRequest;
use App\Models\OrganizationProfile;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class OrganizationProfileController extends Controller
{
    public function edit(): View
    {
        $organization = OrganizationProfile::first();
        if (! $organization) {
            $organization = OrganizationProfile::create(['nama' => 'ARPS']);
        }

        return view('admin.organization.profile', compact('organization'));
    }

    public function update(OrganizationProfileRequest $request): RedirectResponse
    {
        $organization = OrganizationProfile::first() ?? OrganizationProfile::create(['nama' => 'ARPS']);
        $data = $request->validated();

        if ($request->hasFile('logo')) {
            if ($organization->logo) {
                Storage::disk('public')->delete($organization->logo);
            }
            $data['logo'] = $request->file('logo')->store('organization', 'public');
        }

        $organization->update($data);

        return redirect()->route('admin.organization.edit')->with('success', 'Profil organisasi berhasil diperbarui.');
    }
}
