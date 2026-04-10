<?php

namespace Database\Seeders;

use App\Models\Board;
use App\Models\User;
use Illuminate\Database\Seeder;

class BoardSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('email', 'admin@test.com')->first();
        $sara  = User::where('email', 'sara@test.com')->first();
        $ali   = User::where('email', 'ali@test.com')->first();
        $maria = User::where('email', 'maria@test.com')->first();
        $bilal = User::where('email', 'bilal@test.com')->first();

        // ── Board 1: Development Sprint ───────────────────────
        $board1 = Board::create([
            'user_id'          => $admin->id,
            'name'             => 'Development Sprint',
            'description'      => 'Main sprint board for the development team.',
            'background_color' => '#0052CC',
        ]);

        // Owner + 3 members
        $board1->members()->attach([
            $admin->id => ['role' => 'owner'],
            $sara->id  => ['role' => 'member'],
            $ali->id   => ['role' => 'member'],
            $maria->id => ['role' => 'member'],
        ]);

        // ── Board 2: Marketing Campaigns ──────────────────────
        $board2 = Board::create([
            'user_id'          => $admin->id,
            'name'             => 'Marketing Campaigns',
            'description'      => 'Track all ongoing and upcoming marketing campaigns.',
            'background_color' => '#00875A',
        ]);

        // Owner + 2 members
        $board2->members()->attach([
            $admin->id => ['role' => 'owner'],
            $bilal->id => ['role' => 'member'],
            $sara->id  => ['role' => 'member'],
        ]);

        // ── Board 3: Personal Tasks ───────────────────────────
        $board3 = Board::create([
            'user_id'          => $admin->id,
            'name'             => 'Personal Tasks',
            'description'      => 'My own personal task board.',
            'background_color' => '#6554C0',
        ]);

        // Owner only — private board
        $board3->members()->attach([
            $admin->id => ['role' => 'owner'],
        ]);

        $this->command->info('✓ 3 boards created with members attached.');
    }
}
