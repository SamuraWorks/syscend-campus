<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        // ── Super Admin (platform owner) ──────────────────────────
        $superAdmin = User::firstOrCreate(
            ['email' => 'syscend@gmail.com'],
            [
                'name'     => 'Super Admin',
                'password' => bcrypt('password'),
                'phone'    => '+23279630777',
                'status'   => 'active',
            ]
        );
        $superAdmin->assignRole('super-admin');

        // ── Ministry Admin ────────────────────────────────────────
        $ministryAdmin = User::firstOrCreate(
            ['email' => 'ministry@syscend.com'],
            [
                'name'     => 'Ministry Admin',
                'password' => bcrypt('password'),
                'phone'    => '+23279630777',
                'status'   => 'active',
            ]
        );
        $ministryAdmin->assignRole('ministry-admin');

        // ── District Officer ──────────────────────────────────────
        $districtOfficer = User::firstOrCreate(
            ['email' => 'district@syscend.com'],
            [
                'name'     => 'District Officer',
                'password' => bcrypt('password'),
                'phone'    => '+23279630777',
                'status'   => 'active',
            ]
        );
        $districtOfficer->assignRole('district-officer');

        $this->command->info('Platform administrators seeded:');
        $this->command->info('  Super Admin       : syscend@gmail.com');
        $this->command->info('  Ministry Admin    : ministry@syscend.com');
        $this->command->info('  District Officer  : district@syscend.com');
        $this->command->info('  Password for all  : password');
    }
}
