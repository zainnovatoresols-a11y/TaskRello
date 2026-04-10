<?php

namespace Database\Seeders;

use App\Models\Board;
use App\Models\BoardList;
use Illuminate\Database\Seeder;

class ListSeeder extends Seeder
{
    public function run(): void
    {
        // ── Board 1: Development Sprint lists ─────────────────
        $board1 = Board::where('name', 'Development Sprint')->first();

        $devLists = [
            'Backlog',
            'To Do',
            'In Progress',
            'Code Review',
            'Testing',
            'Done',
        ];

        foreach ($devLists as $position => $name) {
            BoardList::create([
                'board_id' => $board1->id,
                'name'     => $name,
                'position' => $position,
            ]);
        }

        // ── Board 2: Marketing Campaigns lists ────────────────
        $board2 = Board::where('name', 'Marketing Campaigns')->first();

        $marketingLists = [
            'Ideas',
            'Planning',
            'In Progress',
            'Review',
            'Published',
        ];

        foreach ($marketingLists as $position => $name) {
            BoardList::create([
                'board_id' => $board2->id,
                'name'     => $name,
                'position' => $position,
            ]);
        }

        // ── Board 3: Personal Tasks lists ─────────────────────
        $board3 = Board::where('name', 'Personal Tasks')->first();

        $personalLists = [
            'To Do',
            'In Progress',
            'Done',
        ];

        foreach ($personalLists as $position => $name) {
            BoardList::create([
                'board_id' => $board3->id,
                'name'     => $name,
                'position' => $position,
            ]);
        }

        $this->command->info('✓ Lists created for all 3 boards.');
    }
}
