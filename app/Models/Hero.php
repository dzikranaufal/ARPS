<?php

namespace App\Models;

use App\Enums\JournalStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['judul', 'deskripsi', 'gambar', 'link', 'urutan', 'status'])]
class Hero extends Model
{
    protected function casts(): array
    {
        return ['status' => JournalStatus::class];
    }
}
