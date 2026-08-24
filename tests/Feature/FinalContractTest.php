<?php

namespace Tests\Feature;

use App\Enums\AccountStatus;
use App\Enums\UserRole;
use App\Models\Publication;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * Kontrak penting Fase 7.2 — melindungi dari regresi.
 * Bukan plumbing: menguji perilaku yang bisa rusak.
 */
class FinalContractTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(PreventRequestForgery::class);
        Storage::fake('public');
    }

    private function super(): User { $u = User::factory()->create(); $u->forceFill(['role'=>UserRole::SuperAdmin,'status'=>AccountStatus::Aktif])->save(); return $u->refresh(); }
    private function manager(): User { $u = User::factory()->create(); $u->forceFill(['role'=>UserRole::AdminManager,'status'=>AccountStatus::Aktif])->save(); return $u->refresh(); }
    private function member(array $over=[]): User { $u = User::factory()->create(array_merge(['telepon'=>'081234567890'], $over)); $u->forceFill(['role'=>UserRole::Member,'status'=>AccountStatus::Aktif])->save(); return $u->refresh(); }

    public function test_auth_register_and_rate_limit_and_nonaktif(): void
    {
        // register
        $this->post('/register', ['nama'=>'Budi','email'=>'budi@test.com','telepon'=>'0812','password'=>'secret123','password_confirmation'=>'secret123'])->assertRedirect(route('member.dashboard'));
        $this->assertDatabaseHas('users',['email'=>'budi@test.com','role'=>'member','status'=>'aktif']);
        $this->assertAuthenticated();
        // nonaktif
        $m=$this->member(['email'=>'non@test.com']); $m->setAccountStatus(AccountStatus::Nonaktif);
        $this->post('/logout'); $this->post('/login',['email'=>'non@test.com','password'=>'password'])->assertSessionHasErrors('email'); $this->assertGuest();
        // rate limit 5x
        $u=User::factory()->create(['email'=>'rate@test.com']);
        for($i=0;$i<5;$i++) $this->post('/login',['email'=>'rate@test.com','password'=>'wrong']);
        $this->withHeaders(['Accept'=>'application/json'])->post('/login',['email'=>'rate@test.com','password'=>'wrong'])->assertStatus(429);
    }

    public function test_role_middleware(): void
    {
        $m=$this->member(); $a=$this->super(); $man=$this->manager();
        $this->actingAs($m)->get('/admin')->assertStatus(403);
        $this->actingAs($a)->get('/admin')->assertOk();
        $this->actingAs($man)->get(route('admin.admin-users.index'))->assertStatus(403);
        $this->actingAs($a)->get(route('admin.admin-users.index'))->assertOk();
        $this->actingAs($m)->get('/dashboard')->assertOk();
        $this->actingAs($a)->get('/dashboard')->assertStatus(403);
    }

    public function test_publication_approval_and_public(): void
    {
        $m=$this->member(); $a=$this->super();
        $file=UploadedFile::fake()->create('doc.pdf', 500, 'application/pdf');
        $this->actingAs($m)->post(route('member.publications.store'), ['judul'=>'K','kategori'=>'tulisan','file'=>$file])->assertRedirect(route('member.publications.index'));
        $pub=Publication::first(); $this->assertEquals('pending',$pub->status->value);
        $this->actingAs($a)->put(route('admin.publications.approve',$pub))->assertRedirect(); $this->assertEquals('approved',$pub->refresh()->status->value); $this->assertEquals($a->id,$pub->reviewer_id);
        // race: second approve should not change (0 row)
        $a2=$this->manager(); $this->actingAs($a2)->put(route('admin.publications.approve',$pub))->assertRedirect(); $this->assertEquals('approved',$pub->refresh()->status->value);
        $this->get('/publications')->assertSee('K');
        // pending not in public
        $file2=UploadedFile::fake()->create('doc2.pdf', 100, 'application/pdf');
        $this->actingAs($m)->post(route('member.publications.store'), ['judul'=>'Pending','kategori'=>'produk','file'=>$file2]);
        $this->get('/publications')->assertDontSee('Pending');
    }

    public function test_upload_validation(): void
    {
        $m=$this->member();
        $txt=UploadedFile::fake()->create('bad.txt', 100, 'text/plain');
        $this->actingAs($m)->post(route('member.publications.store'), ['judul'=>'Bad','kategori'=>'tulisan','file'=>$txt])->assertSessionHasErrors('file');
        $big=UploadedFile::fake()->create('big.pdf', 11000, 'application/pdf');
        $this->actingAs($m)->post(route('member.publications.store'), ['judul'=>'Big','kategori'=>'tulisan','file'=>$big])->assertSessionHasErrors('file');
    }

    public function test_idor_member_cannot_download_other(): void
    {
        $m1=$this->member(); $m2=$this->member(['email'=>'b@test.com']);
        $file=UploadedFile::fake()->create('doc.pdf', 100, 'application/pdf');
        $this->actingAs($m1)->post(route('member.publications.store'), ['judul'=>'K','kategori'=>'tulisan','file'=>$file]);
        $pub=Publication::first();
        $this->actingAs($m2)->get(route('member.publications.download',$pub))->assertStatus(403);
        $this->actingAs($m1)->get(route('member.publications.download',$pub))->assertOk();
        $a=$this->super(); $this->actingAs($a)->get(route('admin.publications.download',$pub))->assertOk();
    }

    public function test_profile_does_not_change_role(): void
    {
        $m=$this->member(['email'=>'keep@test.com']); $origRole=$m->role; $origStatus=$m->status;
        $this->actingAs($m)->put(route('member.profile.update'), ['nama'=>'Hacked','telepon'=>'0812','organisasi'=>'X','role'=>'superadmin','status'=>'nonaktif','email'=>'hacked@test.com']);
        $m->refresh(); $this->assertEquals($origRole,$m->role); $this->assertEquals($origStatus,$m->status); $this->assertEquals('keep@test.com',$m->email);
    }

    public function test_seo_and_sitemap(): void
    {
        $this->get('/')->assertSee('<h1',false)->assertSee('ARPS',false);
        $this->get('/sitemap.xml')->assertOk()->assertHeader('Content-Type','text/xml; charset=utf-8');
        $this->get('/sitemap.xml')->assertSee(route('home'),false);
        // robots not blocked
        $this->assertTrue(file_exists(public_path('robots.txt')));
        $this->assertStringContainsString('Allow: /', file_get_contents(public_path('robots.txt')));
    }
}
