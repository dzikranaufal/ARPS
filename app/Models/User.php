<?php

namespace App\Models;

use App\Enums\AccountStatus;
use App\Enums\UserRole;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

// Risk A2: 'role' & 'status' sengaja TIDAK di-fillable — tidak boleh diisi dari
// input form. Admin mengubahnya via method khusus / forceFill, bukan update().
#[Fillable(['nama', 'email', 'password', 'telepon', 'organisasi', 'foto'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => UserRole::class,
            'status' => AccountStatus::class,
        ];
    }

    /**
     * Publications uploaded by this user (member).
     */
    public function publications(): HasMany
    {
        return $this->hasMany(Publication::class, 'member_id');
    }

    /**
     * Publications reviewed by this user (admin).
     */
    public function reviewedPublications(): HasMany
    {
        return $this->hasMany(Publication::class, 'reviewer_id');
    }

    /**
     * Set account status. SAFE PATH for admins (Phase 4).
     * status is intentionally NOT mass-assignable (risk A2), so this
     * uses forceFill instead of update(). Never pass user input here.
     */
    public function setAccountStatus(AccountStatus $status): void
    {
        $this->forceFill(['status' => $status])->save();
    }
}
