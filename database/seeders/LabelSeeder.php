<?php

namespace Database\Seeders;

use App\Models\Board;
use App\Models\Label;
use Illuminate\Database\Seeder;

class LabelSeeder extends Seeder
{
    public function run(): void
    {
        // ── Board 1: Development Sprint labels ────────────────
        $board1 = Board::where('name', 'Development Sprint')->first();

        $devLabels = [
            ['name' => 'Bug',         'color' => '#EB5A46'],
            ['name' => 'Feature',     'color' => '#61BD4F'],
            ['name' => 'Improvement', 'color' => '#0079BF'],
            ['name' => 'Urgent',      'color' => '#F2D600'],
            ['name' => 'Blocked',     'color' => '#C377E0'],
            ['name' => 'Backend',     'color' => '#FF9F1A'],
            ['name' => 'Frontend',    'color' => '#00C2E0'],
        ];

        foreach ($devLabels as $label) {
            Label::create([
                'board_id' => $board1->id,
                'name'     => $label['name'],
                'color'    => $label['color'],
            ]);
        }

        // ── Board 2: Marketing Campaigns labels ───────────────
        $board2 = Board::where('name', 'Marketing Campaigns')->first();

        $marketingLabels = [
            ['name' => 'Social Media', 'color' => '#0079BF'],
            ['name' => 'Email',        'color' => '#61BD4F'],
            ['name' => 'Paid Ads',     'color' => '#EB5A46'],
            ['name' => 'Content',      'color' => '#FF9F1A'],
            ['name' => 'SEO',          'color' => '#C377E0'],
        ];

        foreach ($marketingLabels as $label) {
            Label::create([
                'board_id' => $board2->id,
                'name'     => $label['name'],
                'color'    => $label['color'],
            ]);
        }

        // ── Board 3: Personal Tasks labels ────────────────────
        $board3 = Board::where('name', 'Personal Tasks')->first();

        $personalLabels = [
            ['name' => 'Important', 'color' => '#EB5A46'],
            ['name' => 'Later',     'color' => '#61BD4F'],
            ['name' => 'Personal',  'color' => '#C377E0'],
        ];

        foreach ($personalLabels as $label) {
            Label::create([
                'board_id' => $board3->id,
                'name'     => $label['name'],
                'color'    => $label['color'],
            ]);
        }

        $this->command->info('✓ Labels created for all 3 boards.');
    }
}
