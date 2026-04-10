<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('');
        $this->command->info('═══════════════════════════════════════════');
        $this->command->info('   Trello Clone — Database Seeder');
        $this->command->info('═══════════════════════════════════════════');
        $this->command->info('');

        // ── Step 1: Users ─────────────────────────────────────
        // Must run first — everything else depends on users
        $this->command->info('Seeding users...');
        $this->call(UserSeeder::class);

        // ── Step 2: Boards ────────────────────────────────────
        // Depends on: users (owner + members)
        $this->command->info('Seeding boards...');
        $this->call(BoardSeeder::class);

        // ── Step 3: Lists ─────────────────────────────────────
        // Depends on: boards
        $this->command->info('Seeding lists...');
        $this->call(ListSeeder::class);

        // ── Step 4: Labels ────────────────────────────────────
        // Depends on: boards
        $this->command->info('Seeding labels...');
        $this->call(LabelSeeder::class);

        // ── Step 5: Cards ─────────────────────────────────────
        // Depends on: lists, users, labels
        $this->command->info('Seeding cards...');
        $this->call(CardSeeder::class);

        // ── Step 6: Comments ──────────────────────────────────
        // Depends on: cards, users
        $this->command->info('Seeding comments...');
        $this->call(CommentSeeder::class);

        // ── Step 7: Attachments ───────────────────────────────
        // Depends on: cards, users
        $this->command->info('Seeding attachments...');
        $this->call(AttachmentSeeder::class);

        // ── Step 8: Activity logs ─────────────────────────────
        // Depends on: boards, cards, users
        $this->command->info('Seeding activity logs...');
        $this->call(ActivityLogSeeder::class);

        // ── Done ──────────────────────────────────────────────
        $this->command->info('');
        $this->command->info('═══════════════════════════════════════════');
        $this->command->info('   All seeders completed successfully!');
        $this->command->info('═══════════════════════════════════════════');
        $this->command->info('');
        $this->command->info('  Login credentials:');
        $this->command->info('  Email   : admin@test.com');
        $this->command->info('  Password: password');
        $this->command->info('');
        $this->command->info('  Other test users (same password):');
        $this->command->info('  sara@test.com');
        $this->command->info('  ali@test.com');
        $this->command->info('  maria@test.com');
        $this->command->info('  bilal@test.com');
        $this->command->info('');
    }
}
