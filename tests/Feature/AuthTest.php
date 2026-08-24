<?php

namespace Tests\Feature;

use App\Enums\AccountStatus;
use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // CSRF sudah diverifikasi tersendiri (risiko A5) & form memakai @csrf.
        // Nonaktifkan di feature test agar POST tidak gagal karena token.
        $this->withoutMiddleware(PreventRequestForgery::class);
    }

    public function test_register_creates_member_and_redirects_to_dashboard(): void
    {
        $response = $this->post('/register', [
            'nama' => 'Budi Santoso',
            'email' => 'budi@example.com',
            'telepon' => '081234567890',
            'organisasi' => 'Universitas Padjajaran',
            'password' => 'secretpass',
            'password_confirmation' => 'secretpass',
        ]);

        $response->assertRedirect(route('member.dashboard'));

        $this->assertAuthenticated();

        $user = User::where('email', 'budi@example.com')->first();
        $this->assertNotNull($user);
        $this->assertEquals(UserRole::Member, $user->role);
        $this->assertEquals(AccountStatus::Aktif, $user->status);
    }

    public function test_register_duplicate_email_returns_error(): void
    {
        User::factory()->create(['email' => 'duplicate@example.com']);

        $response = $this->from('/register')->post('/register', [
            'nama' => 'Dua',
            'email' => 'duplicate@example.com',
            'telepon' => '081234567890',
            'password' => 'secretpass',
            'password_confirmation' => 'secretpass',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertDatabaseCount('users', 1);
        $this->assertGuest();
    }

    public function test_register_validates_required_fields(): void
    {
        $response = $this->post('/register', [
            'nama' => '',
            'email' => '',
            'telepon' => '',
            'password' => '',
            'password_confirmation' => '',
        ]);

        $response->assertSessionHasErrors(['nama', 'email', 'telepon', 'password']);
    }

    public function test_register_requires_min_password_length(): void
    {
        $response = $this->post('/register', [
            'nama' => 'Budi',
            'email' => 'budi@example.com',
            'telepon' => '081234567890',
            'password' => 'short',
            'password_confirmation' => 'short',
        ]);

        $response->assertSessionHasErrors('password');
    }

    public function test_register_requires_password_confirmation(): void
    {
        $response = $this->post('/register', [
            'nama' => 'Budi',
            'email' => 'budi@example.com',
            'telepon' => '081234567890',
            'password' => 'secretpass',
            'password_confirmation' => 'differentpass',
        ]);

        $response->assertSessionHasErrors('password');
    }

    public function test_login_member_redirects_to_dashboard(): void
    {
        $member = User::factory()->create(['email' => 'member@example.com']);

        $response = $this->post('/login', [
            'email' => 'member@example.com',
            'password' => 'password',
        ]);

        $response->assertRedirect(route('member.dashboard'));
        $this->assertAuthenticatedAs($member);
    }

    public function test_login_admin_redirects_to_admin_dashboard(): void
    {
        $admin = User::factory()->create(['email' => 'admin@example.com']);
        $admin->forceFill(['role' => UserRole::SuperAdmin])->save();

        $response = $this->post('/login', [
            'email' => 'admin@example.com',
            'password' => 'password',
        ]);

        $response->assertRedirect(route('admin.dashboard'));
    }

    public function test_login_wrong_credentials_returns_error(): void
    {
        User::factory()->create(['email' => 'exist@example.com', 'password' => 'correctpass']);

        $response = $this->from('/login')->post('/login', [
            'email' => 'exist@example.com',
            'password' => 'wrongpass',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_login_rate_limit_blocks_after_five_attempts(): void
    {
        User::factory()->create(['email' => 'ratelimit@example.com']);

        for ($i = 0; $i < 5; $i++) {
            $this->post('/login', [
                'email' => 'ratelimit@example.com',
                'password' => 'wrongpass',
            ]);
        }

        $response = $this->withHeaders(['Accept' => 'application/json'])->post('/login', [
            'email' => 'ratelimit@example.com',
            'password' => 'wrongpass',
        ]);

        $response->assertStatus(429);
    }

    public function test_login_nonaktif_account_blocked(): void
    {
        $member = User::factory()->create(['email' => 'nonaktif@example.com']);
        $member->setAccountStatus(AccountStatus::Nonaktif);

        $response = $this->from('/login')->post('/login', [
            'email' => 'nonaktif@example.com',
            'password' => 'password',
        ]);

        $response->assertRedirect('/login');
        $response->assertSessionHasErrors('email');

        $this->assertGuest();

        $response = $this->get('/dashboard');
        $response->assertRedirect(route('login'));
    }

    public function test_logout_logs_out_and_redirects_home(): void
    {
        $member = User::factory()->create();

        $response = $this->actingAs($member)->post('/logout');

        $response->assertRedirect(route('home'));
        $this->assertGuest();
    }

    public function test_register_rate_limit_blocks_after_five_attempts(): void
    {
        // Semua percobaan gagal (konfirmasi password tidak sama), sehingga limiter menumpuk.
        for ($i = 0; $i < 5; $i++) {
            $this->post('/register', [
                'nama' => 'Budi',
                'email' => "budi$i@example.com",
                'telepon' => '081234567890',
                'password' => 'secretpass',
                'password_confirmation' => 'wrong-confirmation',
            ]);
        }

        $response = $this->withHeaders(['Accept' => 'application/json'])->post('/register', [
            'nama' => 'Budi',
            'email' => 'budi5@example.com',
            'telepon' => '081234567890',
            'password' => 'secretpass',
            'password_confirmation' => 'wrong-confirmation',
        ]);

        $response->assertStatus(429);
    }
}