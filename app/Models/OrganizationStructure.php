<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['nama_pengurus', 'jabatan', 'afiliasi', 'foto'])]
class OrganizationStructure extends Model
{
    /**
     * Table name stays singular per schema.md.
     */
    protected $table = 'organization_structure';
}
