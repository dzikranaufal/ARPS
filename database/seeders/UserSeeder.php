<?php

namespace Database\Seeders;

use App\Enums\AccountStatus;
use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Seed admin & member accounts.
     *
     * NOTE (risk E3): passwords are random and printed to console — NOT
     * a weak default like admin/admin123. Change them before production.
     */
    public function run(): void
    {
        $superAdminPassword = Str::random(16);
        $adminManagerPassword = Str::random(16);

        $superAdmin = User::create([
            'nama' => 'Super Admin ARPS',
            'email' => 'superadmin@arps.org',
            'password' => Hash::make($superAdminPassword),
            'role' => UserRole::SuperAdmin,
            'status' => AccountStatus::Aktif,
        ]);

        $adminManager = User::create([
            'nama' => 'Admin Manager ARPS',
            'email' => 'admin@arps.org',
            'password' => Hash::make($adminManagerPassword),
            'role' => UserRole::AdminManager,
            'status' => AccountStatus::Aktif,
        ]);

        $this->command?->info('Super Admin   : superadmin@arps.org / ' . $superAdminPassword);
        $this->command?->info('Admin Manager : admin@arps.org / ' . $adminManagerPassword);

        // 8 sample members curated + 16 dummy via faker (total 24 members) — untuk uji pagination direktori (12/page)
        $members = [
            ['nama' => 'Ahmad Fauzi', 'email' => 'ahmad.fauzi@example.com', 'telepon' => '081234567801', 'organisasi' => 'Universitas Pendidikan Indonesia'],
            ['nama' => 'Bella Kusuma', 'email' => 'bella.kusuma@example.com', 'telepon' => '081234567802', 'organisasi' => 'Institut Teknologi Bandung'],
            ['nama' => 'Candra Wijaya', 'email' => 'candra.wijaya@example.com', 'telepon' => '081234567803', 'organisasi' => 'Universitas Gadjah Mada'],
            ['nama' => 'Dina Marlina', 'email' => 'dina.marlina@example.com', 'telepon' => '081234567804', 'organisasi' => 'Peneliti Independen'],
            ['nama' => 'Eko Prasetyo', 'email' => 'eko.prasetyo@example.com', 'telepon' => '081234567805', 'organisasi' => 'Politeknik Negeri Bandung'],
            ['nama' => 'Fitri Handayani', 'email' => 'fitri.handayani@example.com', 'telepon' => '081234567806', 'organisasi' => 'Universitas Negeri Jakarta'],
            ['nama' => 'Gilang Ramadhan', 'email' => 'gilang.ramadhan@example.com', 'telepon' => '081234567807', 'organisasi' => 'Praktisi Industri'],
            ['nama' => 'Hana Safitri', 'email' => 'hana.safitri@example.com', 'telepon' => '081234567808', 'organisasi' => 'Universitas Brawijaya'],
        ];

        foreach ($members as $member) {
            User::create([
                'nama' => $member['nama'],
                'email' => $member['email'],
                'telepon' => $member['telepon'],
                'organisasi' => $member['organisasi'],
                'password' => Hash::make('password'),
                'role' => UserRole::Member,
                'status' => AccountStatus::Aktif,
            ]);
        }

        // 16 member dummy tambahan via faker
        $orgs = ['Universitas Padjadjaran', 'Universitas Airlangga', 'Universitas Diponegoro', 'ITS Surabaya', 'Universitas Hasanuddin', 'Politeknik Negeri Jakarta', 'Praktisi Industri', 'Komunitas Riset Independen', 'Universitas Sriwijaya', 'Universitas Udayana'];
        for ($i = 9; $i <= 24; $i++) {
            User::create([
                'nama' => fake()->name(),
                'email' => 'member' . $i . '_' . fake()->unique()->safeEmail(),
                'telepon' => '0812' . fake()->numerify('########'),
                'organisasi' => fake()->randomElement($orgs),
                'password' => Hash::make('password'),
                'role' => UserRole::Member,
                'status' => fake()->randomElement([AccountStatus::Aktif, AccountStatus::Aktif, AccountStatus::Aktif, AccountStatus::Nonaktif]), // 75% aktif
            ]);
        }
    }
}
