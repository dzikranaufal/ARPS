<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['nama', 'deskripsi', 'visi', 'misi', 'logo'])]
class OrganizationProfile extends Model
{
    /**
     * Table name stays singular per schema.md (single-row settings table).
     */
    protected $table = 'organization_profile';
}
