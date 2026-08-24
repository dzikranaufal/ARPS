<?php

namespace Tests\Feature;

use App\Enums\AccountStatus;
use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    private function makeUser(UserRole $role): User
    {
        $user = User::factory()->create();
        $user->forceFill(['role' => $role])->save();

        return $user;
    }

    public function test_guest_redirected_from_admin(): void
    {
        $this->get('/admin')->assertRedirect(route('login'));
    }

    public function test_guest_redirected_from_dashboard(): void
    {
        $this->get('/dashboard')->assertRedirect(route('login'));
    }

    public function test_member_forbidden_from_admin(): void
    {
        $member = $this->makeUser(UserRole::Member);

        $this->actingAs($member)->get('/admin')->assertForbidden();
    }

    public function test_admin_forbidden_from_dashboard(): void
    {
        $admin = $this->makeUser(UserRole::SuperAdmin);

        $this->actingAs($admin)->get('/dashboard')->assertForbidden();
    }

    public function test_admin_can_access_admin_dashboard(): void
    {
        $admin = $this->makeUser(UserRole::SuperAdmin);

        $this->actingAs($admin)->get('/admin')->assertOk();
    }

    public function test_member_can_access_dashboard(): void
    {
        $member = $this->makeUser(UserRole::Member);

        $this->actingAs($member)->get('/dashboard')->assertOk();
    }

    public function test_role_not_mass_assignable(): void
    {
        // role/status tidak fillable (A2): create dengan role superadmin tetap member.
        $user = User::create([
            'nama' => 'Hacker',
            'email' => 'hacker@example.com',
            'password' => 'secretpass',
            'telepon' => '081234567890',
            'role' => UserRole::SuperAdmin,
        ]);

        $user->refresh();
        $this->assertEquals(UserRole::Member, $user->role);

        // update(['status' => ...]) juga harus no-op (A2) — status default 'aktif' tidak berubah.
        $user->update(['status' => AccountStatus::Nonaktif]);
        $user->refresh();
        $this->assertEquals(AccountStatus::Aktif, $user->status);
    }
}