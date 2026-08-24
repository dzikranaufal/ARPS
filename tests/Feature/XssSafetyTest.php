<?php

namespace Tests\Feature;

use App\Enums\AccountStatus;
use App\Enums\UserRole;
use App\Models\News;
use App\Models\Publication;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class XssSafetyTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(PreventRequestForgery::class);
        Storage::fake('public');
    }

    private function member(array $over = []): User
    {
        $u = User::factory()->create(array_merge(['telepon' => '081234567890'], $over));
        $u->forceFill(['role' => UserRole::Member, 'status' => AccountStatus::Aktif])->save();
        return $u->refresh();
    }

    public function test_member_publication_deskripsi_is_escaped_on_public_page(): void
    {
        $member = $this->member();
        $pub = Publication::create([
            'member_id' => $member->id,
            'judul' => 'XSS Pub',
            'deskripsi' => '<script>alert(1)</script>Halo <b>dunia</b>',
            'kategori' => 'tulisan',
            'file' => 'publications/fake.pdf',
            'status' => 'approved',
        ]);

        $resp = $this->get(route('publications.show', $pub));
        $resp->assertOk();
        // raw tag must NOT appear
        $resp->assertDontSee('<script>alert(1)</script>', false);
        // escaped version appears? atau at least tidak dirender sebagai tag
        // we assert that bold tag juga tidak dirender mentah (karena escape, bukan purify)
        $resp->assertDontSee('<b>dunia</b>', false);
        // but text content remains (dunia)
        $resp->assertSee('dunia', false);
    }

    public function test_member_publication_deskripsi_is_escaped_on_admin_page(): void
    {
        $member = $this->member();
        $super = User::factory()->create();
        $super->forceFill(['role' => UserRole::SuperAdmin, 'status' => AccountStatus::Aktif])->save();
        $super = $super->refresh();

        $pub = Publication::create([
            'member_id' => $member->id,
            'judul' => 'XSS Admin',
            'deskripsi' => '<script>alert(1)</script>Halo',
            'kategori' => 'tulisan',
            'file' => null,
            'status' => 'pending',
        ]);

        $resp = $this->actingAs($super)->get(route('admin.publications.show', $pub));
        $resp->assertOk();
        $resp->assertDontSee('<script>alert(1)</script>', false);
    }

    public function test_admin_news_isi_sanitized_on_public_page(): void
    {
        $news = News::create([
            'judul' => 'News XSS',
            'isi' => '<script>alert(2)</script><p>aman</p><p onclick="x()">klik</p><strong>bold</strong>',
            'tanggal_publish' => now(),
        ]);

        $resp = $this->get(route('news.show', $news));
        $resp->assertOk();
        $resp->assertDontSee('<script>alert(2)</script>', false);
        $resp->assertDontSee('onclick', false);
        // safe tag tetap tampil
        $resp->assertSee('<p>aman</p>', false);
        $resp->assertSee('<strong>bold</strong>', false);
    }

    public function test_admin_rich_text_keeps_inline_style_but_strips_js(): void
    {
        $news = News::create([
            'judul' => 'Style XSS',
            'isi' => '<p style="color: red; text-align: center;">warna</p><a href="javascript:alert(1)">js</a><a href="https://example.com" target="_blank">ok</a>',
            'tanggal_publish' => now(),
        ]);

        $resp = $this->get(route('news.show', $news));
        $resp->assertOk();
        $resp->assertDontSee('javascript:', false);
        // inline style tetap (color, text-align) — Purifier keeps them
        $resp->assertSee('color', false);
        $resp->assertSee('warna', false);
        $resp->assertSee('https://example.com', false);
    }

    public function test_admin_description_via_programs_sanitized(): void
    {
        $program = \App\Models\Program::create([
            'judul' => 'Prog XSS',
            'deskripsi' => '<script>alert(3)</script><p>program aman</p>',
            'kategori_id' => null,
        ]);
        $resp = $this->get(route('programs.show', $program));
        $resp->assertOk();
        $resp->assertDontSee('<script>alert(3)</script>', false);
        $resp->assertSee('program aman', false);
    }
}
