<?php

namespace App\Models;

use App\Enums\PublicationCategory;
use App\Enums\PublicationStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['member_id', 'judul', 'deskripsi', 'kategori', 'file', 'status', 'reviewer_id'])]
class Publication extends Model
{
    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'kategori' => PublicationCategory::class,
            'status' => PublicationStatus::class,
        ];
    }

    /**
     * The member (user) who uploaded this publication.
     */
    public function member(): BelongsTo
    {
        return $this->belongsTo(User::class, 'member_id');
    }

    /**
     * The admin who reviewed this publication.
     */
    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }
}
