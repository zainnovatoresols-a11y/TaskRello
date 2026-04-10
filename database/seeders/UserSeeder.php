<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // ── Main test account ─────────────────────────────────
        // Use this to log in: admin@test.com / password
        User::firstOrCreate(
            ['email' => 'admin@test.com'],
            [
                'name'              => 'Admin User',
                'password'          => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        // ── Team members for testing collaboration ────────────
        $members = [
            ['name' => 'Sara Khan',    'email' => 'sara@test.com'],
            ['name' => 'Ali Hassan',   'email' => 'ali@test.com'],
            ['name' => 'Maria Younas', 'email' => 'maria@test.com'],
            ['name' => 'Bilal Ahmed',  'email' => 'bilal@test.com'],
        ];

        foreach ($members as $member) {
            User::firstOrCreate(
                ['email' => $member['email']],
                [
                    'name'              => $member['name'],
                    'password'          => Hash::make('password'),
                    'email_verified_at' => now(),
                ]
            );
        }

        $this->command->info('✓ 5 users created. Login: admin@test.com / password');
    }
}
