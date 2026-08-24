<?php

namespace Tests\Feature;

use App\Enums\JournalStatus;
use App\Models\Journal;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class JournalsPublicTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_journals_only_shows_aktif(): void
    {
        Journal::create([
            'nama' => 'Jurnal Aktif',
            'slug' => 'jurnal-aktif',
            'link_eksternal' => 'https://aktif.example.com',
            'status' => JournalStatus::Aktif,
        ]);

        Journal::create([
            'nama' => 'Jurnal Arsip',
            'slug' => 'jurnal-arsip',
            'link_eksternal' => 'https://arsip.example.com',
            'status' => JournalStatus::Arsip,
        ]);

        $response = $this->get('/journals');

        $response->assertOk();
        $response->assertSee('Jurnal Aktif');
        $response->assertDontSee('Jurnal Arsip');
    }

    public function test_public_journals_sorted_by_name(): void
    {
        Journal::create([
            'nama' => 'Zeta Journal',
            'slug' => 'zeta-journal',
            'link_eksternal' => 'https://zeta.example.com',
            'status' => JournalStatus::Aktif,
        ]);
        Journal::create([
            'nama' => 'Alpha Journal',
            'slug' => 'alpha-journal',
            'link_eksternal' => 'https://alpha.example.com',
            'status' => JournalStatus::Aktif,
        ]);

        $response = $this->get('/journals');

        $response->assertOk();
        $this->assertTrue(
            strpos($response->getContent(), 'Alpha Journal') < strpos($response->getContent(), 'Zeta Journal')
        );
    }

    public function test_public_journals_card_links_to_external_url(): void
    {
        Journal::create([
            'nama' => 'External Journal',
            'slug' => 'external-journal',
            'link_eksternal' => 'https://external.example.com',
            'status' => JournalStatus::Aktif,
        ]);

        $response = $this->get('/journals');

        $response->assertOk();
        $response->assertSee('https://external.example.com');
    }
}