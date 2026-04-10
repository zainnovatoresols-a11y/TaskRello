<?php

namespace Database\Seeders;

use App\Models\Board;
use App\Models\BoardList;
use App\Models\Card;
use App\Models\Comment;
use App\Models\User;
use Illuminate\Database\Seeder;

class CommentSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('email', 'admin@test.com')->first();
        $sara  = User::where('email', 'sara@test.com')->first();
        $ali   = User::where('email', 'ali@test.com')->first();
        $maria = User::where('email', 'maria@test.com')->first();
        $bilal = User::where('email', 'bilal@test.com')->first();

        // ── Board 1: Development Sprint comments ──────────────
        $board1 = Board::where('name', 'Development Sprint')->first();

        // Card: Fix login page on mobile Safari
        $card = Card::where('title', 'Fix login page on mobile Safari')->first();

        Comment::create([
            'card_id'    => $card->id,
            'user_id'    => $sara->id,
            'body'       => 'I can reproduce this on iPhone 13 as well. '
                . 'The issue is with the pb-safe-area class missing '
                . 'from the guest layout.',
            'created_at' => now()->subHours(5),
            'updated_at' => now()->subHours(5),
        ]);

        Comment::create([
            'card_id'    => $card->id,
            'user_id'    => $admin->id,
            'body'       => 'Good catch Sara. We need to add '
                . 'pb-[env(safe-area-inset-bottom)] to the body tag '
                . 'in the guest layout. Can you take this one?',
            'created_at' => now()->subHours(4),
            'updated_at' => now()->subHours(4),
        ]);

        Comment::create([
            'card_id'    => $card->id,
            'user_id'    => $sara->id,
            'body'       => 'On it. Will push a fix by end of day.',
            'created_at' => now()->subHours(3),
            'updated_at' => now()->subHours(3),
        ]);

        // Card: Build Kanban board drag and drop
        $card = Card::where('title', 'Build Kanban board drag and drop')->first();

        Comment::create([
            'card_id'    => $card->id,
            'user_id'    => $admin->id,
            'body'       => 'Using SortableJS for this. The group option '
                . 'allows cards to move between lists. '
                . 'Tested locally and it works great.',
            'created_at' => now()->subHours(10),
            'updated_at' => now()->subHours(10),
        ]);

        Comment::create([
            'card_id'    => $card->id,
            'user_id'    => $ali->id,
            'body'       => 'Make sure the onEnd callback handles the case '
                . 'where the card is dropped back in the same '
                . 'position. No API call should be made in that case.',
            'created_at' => now()->subHours(8),
            'updated_at' => now()->subHours(8),
        ]);

        Comment::create([
            'card_id'    => $card->id,
            'user_id'    => $admin->id,
            'body'       => 'Good point Ali. Already handled that with the '
                . 'evt.from === evt.to && evt.oldIndex === evt.newIndex check.',
            'created_at' => now()->subHours(6),
            'updated_at' => now()->subHours(6),
        ]);

        Comment::create([
            'card_id'    => $card->id,
            'user_id'    => $sara->id,
            'body'       => 'The ghost class opacity looks great. '
                . 'Very smooth UX. Nice work!',
            'created_at' => now()->subHours(2),
            'updated_at' => now()->subHours(2),
        ]);

        // Card: Card detail modal — comments section
        $card = Card::where('title', 'Card detail modal — comments section')->first();

        Comment::create([
            'card_id'    => $card->id,
            'user_id'    => $maria->id,
            'body'       => 'Started working on this. The comment form '
                . 'posts via fetch and appends to the DOM without reload. '
                . 'Should be done by tomorrow.',
            'created_at' => now()->subDays(1),
            'updated_at' => now()->subDays(1),
        ]);

        Comment::create([
            'card_id'    => $card->id,
            'user_id'    => $admin->id,
            'body'       => 'Remember to handle the empty state when there '
                . 'are no comments yet. Show a placeholder message.',
            'created_at' => now()->subHours(20),
            'updated_at' => now()->subHours(20),
        ]);

        // Card: PR #42 — Add label management feature
        $card = Card::where('title', 'PR #42 — Add label management feature')->first();

        Comment::create([
            'card_id'    => $card->id,
            'user_id'    => $ali->id,
            'body'       => 'Left two comments on the PR. The syncWithoutDetaching '
                . 'call is correct. One small thing — the label board_id '
                . 'validation in LabelController should return 422 '
                . 'not 403 when label does not belong to board.',
            'created_at' => now()->subHours(3),
            'updated_at' => now()->subHours(3),
        ]);

        Comment::create([
            'card_id'    => $card->id,
            'user_id'    => $admin->id,
            'body'       => 'Fixed. Updated the status code to 422. '
                . 'Ready for re-review.',
            'created_at' => now()->subHours(1),
            'updated_at' => now()->subHours(1),
        ]);

        // Card: Set up CI/CD pipeline
        $card = Card::where('title', 'Set up CI/CD pipeline')->first();

        Comment::create([
            'card_id'    => $card->id,
            'user_id'    => $ali->id,
            'body'       => 'I will use GitHub Actions with a Laravel workflow. '
                . 'Will set up separate jobs for tests, code style, '
                . 'and deployment.',
            'created_at' => now()->subDays(2),
            'updated_at' => now()->subDays(2),
        ]);

        // ── Board 2: Marketing Campaigns comments ─────────────
        $board2 = Board::where('name', 'Marketing Campaigns')->first();

        // Card: Google Ads campaign setup for product launch
        $card = Card::where('title', 'Google Ads campaign setup for product launch')
            ->first();

        Comment::create([
            'card_id'    => $card->id,
            'user_id'    => $bilal->id,
            'body'       => 'Keywords research done. Found 3 high-intent '
                . 'keywords with good search volume and low competition. '
                . 'Will share the sheet tomorrow.',
            'created_at' => now()->subDays(1),
            'updated_at' => now()->subDays(1),
        ]);

        Comment::create([
            'card_id'    => $card->id,
            'user_id'    => $sara->id,
            'body'       => 'Make sure we set up conversion tracking before '
                . 'launching. Last time we missed that and had no data '
                . 'for the first week.',
            'created_at' => now()->subHours(18),
            'updated_at' => now()->subHours(18),
        ]);

        Comment::create([
            'card_id'    => $card->id,
            'user_id'    => $bilal->id,
            'body'       => 'Good reminder Sara. Already added conversion '
                . 'tracking setup as a subtask checklist item.',
            'created_at' => now()->subHours(16),
            'updated_at' => now()->subHours(16),
        ]);

        // Card: Write blog post
        $card = Card::where('title', 'Write blog post: Top 10 productivity tips')
            ->first();

        Comment::create([
            'card_id'    => $card->id,
            'user_id'    => $sara->id,
            'body'       => 'First draft is done at 1200 words. '
                . 'Need to expand the remote work section '
                . 'and add a proper conclusion.',
            'created_at' => now()->subHours(6),
            'updated_at' => now()->subHours(6),
        ]);

        // ── Board 3: Personal Tasks comments ──────────────────
        $board3 = Board::where('name', 'Personal Tasks')->first();

        // Card: Renew passport
        $card = Card::where('title', 'Renew passport')->first();

        Comment::create([
            'card_id'    => $card->id,
            'user_id'    => $admin->id,
            'body'       => 'Checked the website — appointment slots '
                . 'are available next Thursday. Will book today.',
            'created_at' => now()->subDays(1),
            'updated_at' => now()->subDays(1),
        ]);

        $this->command->info('✓ Comments created across all boards.');
    }
}
