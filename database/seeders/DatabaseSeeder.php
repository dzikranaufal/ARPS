<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            OrganizationProfileSeeder::class,
            JournalSeeder::class,
            CategorySeeder::class,
            TechnologyInnovationSeeder::class,
            ContentSeeder::class,
            HeroSeeder::class,
            FocusAreaSeeder::class,
            OrganizationStructureSeeder::class,
            PublicationSeeder::class,
        ]);
    }
}
