<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['nama'])]
class Category extends Model
{
    public function programs(): HasMany
    {
        return $this->hasMany(Program::class, 'kategori_id');
    }
}
