<?php

namespace Database\Seeders;

use App\Models\ActivityLog;
use App\Models\Board;
use App\Models\Card;
use App\Models\User;
use Illuminate\Database\Seeder;

class ActivityLogSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('email', 'admin@test.com')->first();
        $sara  = User::where('email', 'sara@test.com')->first();
        $ali   = User::where('email', 'ali@test.com')->first();
        $maria = User::where('email', 'maria@test.com')->first();
        $bilal = User::where('email', 'bilal@test.com')->first();

        // ── Board 1: Development Sprint ───────────────────────
        $board1 = Board::where('name', 'Development Sprint')->first();

        // Board-level logs
        $this->log(
            $admin,
            'created_board',
            'Admin User created board "Development Sprint"',
            $board1->id,
            null,
            now()->subDays(10)
        );

        $this->log(
            $admin,
            'added_member',
            'Admin User added Sara Khan to the board',
            $board1->id,
            null,
            now()->subDays(10)
        );

        $this->log(
            $admin,
            'added_member',
            'Admin User added Ali Hassan to the board',
            $board1->id,
            null,
            now()->subDays(10)
        );

        $this->log(
            $admin,
            'added_member',
            'Admin User added Maria Younas to the board',
            $board1->id,
            null,
            now()->subDays(9)
        );

        // Card-level logs — Fix login page on mobile Safari
        $card = Card::where('title', 'Fix login page on mobile Safari')->first();

        $this->log(
            $admin,
            'created_card',
            'Admin User created card "Fix login page on mobile Safari"',
            $board1->id,
            $card->id,
            now()->subDays(3)
        );

        $this->log(
            $admin,
            'assigned_user',
            'Admin User assigned Sara Khan to "Fix login page on mobile Safari"',
            $board1->id,
            $card->id,
            now()->subDays(3)
        );

        $this->log(
            $admin,
            'added_label',
            'Admin User added label "Bug" to "Fix login page on mobile Safari"',
            $board1->id,
            $card->id,
            now()->subDays(3)
        );

        $this->log(
            $admin,
            'added_label',
            'Admin User added label "Urgent" to "Fix login page on mobile Safari"',
            $board1->id,
            $card->id,
            now()->subDays(3)
        );

        $this->log(
            $sara,
            'added_comment',
            'Sara Khan commented on "Fix login page on mobile Safari"',
            $board1->id,
            $card->id,
            now()->subHours(5)
        );

        $this->log(
            $admin,
            'added_comment',
            'Admin User commented on "Fix login page on mobile Safari"',
            $board1->id,
            $card->id,
            now()->subHours(4)
        );

        $this->log(
            $sara,
            'added_attachment',
            'Sara Khan attached "mobile-safari-screenshot.png" to '
                . '"Fix login page on mobile Safari"',
            $board1->id,
            $card->id,
            now()->subHours(3)
        );

        // Card-level logs — Build Kanban board drag and drop
        $card = Card::where('title', 'Build Kanban board drag and drop')->first();

        $this->log(
            $admin,
            'created_card',
            'Admin User created card "Build Kanban board drag and drop"',
            $board1->id,
            $card->id,
            now()->subDays(5)
        );

        $this->log(
            $admin,
            'moved_card',
            'Admin User moved "Build Kanban board drag and drop" from "To Do" to "In Progress"',
            $board1->id,
            $card->id,
            now()->subDays(2)
        );

        $this->log(
            $admin,
            'assigned_user',
            'Admin User assigned Sara Khan to "Build Kanban board drag and drop"',
            $board1->id,
            $card->id,
            now()->subDays(2)
        );

        $this->log(
            $admin,
            'added_attachment',
            'Admin User attached "sortablejs-integration-notes.pdf" to '
                . '"Build Kanban board drag and drop"',
            $board1->id,
            $card->id,
            now()->subDays(1)
        );

        $this->log(
            $sara,
            'added_attachment',
            'Sara Khan attached "drag-drop-wireframe.png" to '
                . '"Build Kanban board drag and drop"',
            $board1->id,
            $card->id,
            now()->subHours(12)
        );

        $this->log(
            $sara,
            'added_comment',
            'Sara Khan commented on "Build Kanban board drag and drop"',
            $board1->id,
            $card->id,
            now()->subHours(2)
        );

        // Card-level logs — Card detail modal comments section
        $card = Card::where('title', 'Card detail modal — comments section')->first();

        $this->log(
            $admin,
            'created_card',
            'Admin User created card "Card detail modal — comments section"',
            $board1->id,
            $card->id,
            now()->subDays(4)
        );

        $this->log(
            $admin,
            'assigned_user',
            'Admin User assigned Maria Younas to "Card detail modal — comments section"',
            $board1->id,
            $card->id,
            now()->subDays(4)
        );

        $this->log(
            $admin,
            'set_due_date',
            'Admin User set due date on "Card detail modal — comments section" to '
                . now()->subDays(1)->format('M d, Y'),
            $board1->id,
            $card->id,
            now()->subDays(4)
        );

        $this->log(
            $maria,
            'added_comment',
            'Maria Younas commented on "Card detail modal — comments section"',
            $board1->id,
            $card->id,
            now()->subDays(1)
        );

        // Card-level logs — PR #42
        $card = Card::where('title', 'PR #42 — Add label management feature')->first();

        $this->log(
            $admin,
            'created_card',
            'Admin User created card "PR #42 — Add label management feature"',
            $board1->id,
            $card->id,
            now()->subDays(2)
        );

        $this->log(
            $admin,
            'moved_card',
            'Admin User moved "PR #42 — Add label management feature" '
                . 'from "In Progress" to "Code Review"',
            $board1->id,
            $card->id,
            now()->subDays(1)
        );

        $this->log(
            $ali,
            'added_comment',
            'Ali Hassan commented on "PR #42 — Add label management feature"',
            $board1->id,
            $card->id,
            now()->subHours(3)
        );

        $this->log(
            $admin,
            'added_comment',
            'Admin User commented on "PR #42 — Add label management feature"',
            $board1->id,
            $card->id,
            now()->subHours(1)
        );

        // Card-level logs — Set up CI/CD pipeline
        $card = Card::where('title', 'Set up CI/CD pipeline')->first();

        $this->log(
            $admin,
            'created_card',
            'Admin User created card "Set up CI/CD pipeline"',
            $board1->id,
            $card->id,
            now()->subDays(7)
        );

        $this->log(
            $admin,
            'assigned_user',
            'Admin User assigned Ali Hassan to "Set up CI/CD pipeline"',
            $board1->id,
            $card->id,
            now()->subDays(7)
        );

        $this->log(
            $ali,
            'added_comment',
            'Ali Hassan commented on "Set up CI/CD pipeline"',
            $board1->id,
            $card->id,
            now()->subDays(2)
        );

        $this->log(
            $ali,
            'added_attachment',
            'Ali Hassan attached "github-actions-workflow.yml" to "Set up CI/CD pipeline"',
            $board1->id,
            $card->id,
            now()->subDays(1)
        );

        // Card-level logs — completed cards
        $card = Card::where('title', 'Install Laravel Breeze with dark mode')->first();

        $this->log(
            $admin,
            'created_card',
            'Admin User created card "Install Laravel Breeze with dark mode"',
            $board1->id,
            $card->id,
            now()->subDays(10)
        );

        $this->log(
            $admin,
            'moved_card',
            'Admin User moved "Install Laravel Breeze with dark mode" to "Done"',
            $board1->id,
            $card->id,
            now()->subDays(9)
        );

        $card = Card::where('title', 'Write all database migrations')->first();

        $this->log(
            $admin,
            'created_card',
            'Admin User created card "Write all database migrations"',
            $board1->id,
            $card->id,
            now()->subDays(8)
        );

        $this->log(
            $admin,
            'moved_card',
            'Admin User moved "Write all database migrations" to "Done"',
            $board1->id,
            $card->id,
            now()->subDays(7)
        );

        $card = Card::where('title', 'Define all Eloquent models and relationships')->first();

        $this->log(
            $admin,
            'created_card',
            'Admin User created card "Define all Eloquent models and relationships"',
            $board1->id,
            $card->id,
            now()->subDays(6)
        );

        $this->log(
            $admin,
            'moved_card',
            'Admin User moved "Define all Eloquent models and relationships" to "Done"',
            $board1->id,
            $card->id,
            now()->subDays(5)
        );

        // ── Board 2: Marketing Campaigns ──────────────────────
        $board2 = Board::where('name', 'Marketing Campaigns')->first();

        $this->log(
            $admin,
            'created_board',
            'Admin User created board "Marketing Campaigns"',
            $board2->id,
            null,
            now()->subDays(8)
        );

        $this->log(
            $admin,
            'added_member',
            'Admin User added Bilal Ahmed to the board',
            $board2->id,
            null,
            now()->subDays(8)
        );

        $this->log(
            $admin,
            'added_member',
            'Admin User added Sara Khan to the board',
            $board2->id,
            null,
            now()->subDays(8)
        );

        $card = Card::where('title', 'Google Ads campaign setup for product launch')
            ->first();

        $this->log(
            $admin,
            'created_card',
            'Admin User created card "Google Ads campaign setup for product launch"',
            $board2->id,
            $card->id,
            now()->subDays(4)
        );

        $this->log(
            $admin,
            'assigned_user',
            'Admin User assigned Bilal Ahmed to "Google Ads campaign setup for product launch"',
            $board2->id,
            $card->id,
            now()->subDays(4)
        );

        $this->log(
            $admin,
            'moved_card',
            'Admin User moved "Google Ads campaign setup for product launch" '
                . 'from "Ideas" to "Planning"',
            $board2->id,
            $card->id,
            now()->subDays(3)
        );

        $this->log(
            $bilal,
            'added_comment',
            'Bilal Ahmed commented on "Google Ads campaign setup for product launch"',
            $board2->id,
            $card->id,
            now()->subDays(1)
        );

        $this->log(
            $bilal,
            'added_attachment',
            'Bilal Ahmed attached "keyword-research-sheet.xlsx" to '
                . '"Google Ads campaign setup for product launch"',
            $board2->id,
            $card->id,
            now()->subHours(20)
        );

        $card = Card::where('title', 'Write blog post: Top 10 productivity tips')
            ->first();

        $this->log(
            $admin,
            'created_card',
            'Admin User created card "Write blog post: Top 10 productivity tips"',
            $board2->id,
            $card->id,
            now()->subDays(3)
        );

        $this->log(
            $admin,
            'assigned_user',
            'Admin User assigned Sara Khan to '
                . '"Write blog post: Top 10 productivity tips"',
            $board2->id,
            $card->id,
            now()->subDays(3)
        );

        $this->log(
            $sara,
            'added_comment',
            'Sara Khan commented on "Write blog post: Top 10 productivity tips"',
            $board2->id,
            $card->id,
            now()->subHours(6)
        );

        // ── Board 3: Personal Tasks ───────────────────────────
        $board3 = Board::where('name', 'Personal Tasks')->first();

        $this->log(
            $admin,
            'created_board',
            'Admin User created board "Personal Tasks"',
            $board3->id,
            null,
            now()->subDays(6)
        );

        $card = Card::where('title', 'Renew passport')->first();

        $this->log(
            $admin,
            'created_card',
            'Admin User created card "Renew passport"',
            $board3->id,
            $card->id,
            now()->subDays(5)
        );

        $this->log(
            $admin,
            'set_due_date',
            'Admin User set due date on "Renew passport" to '
                . now()->addDays(30)->format('M d, Y'),
            $board3->id,
            $card->id,
            now()->subDays(5)
        );

        $this->log(
            $admin,
            'added_comment',
            'Admin User commented on "Renew passport"',
            $board3->id,
            $card->id,
            now()->subDays(1)
        );

        $card = Card::where('title', 'Set up Laravel development environment')
            ->first();

        $this->log(
            $admin,
            'created_card',
            'Admin User created card "Set up Laravel development environment"',
            $board3->id,
            $card->id,
            now()->subDays(6)
        );

        $this->log(
            $admin,
            'moved_card',
            'Admin User moved "Set up Laravel development environment" to "Done"',
            $board3->id,
            $card->id,
            now()->subDays(5)
        );

        $this->command->info('✓ Activity logs created across all boards.');
    }

    // ── Private helper to reduce repetition ───────────────────
    private function log(
        User    $user,
        string  $action,
        string  $description,
        int     $boardId,
        ?int    $cardId,
        $createdAt
    ): void {
        ActivityLog::create([
            'user_id'     => $user->id,
            'board_id'    => $boardId,
            'card_id'     => $cardId,
            'action'      => $action,
            'description' => $description,
            'created_at'  => $createdAt,
        ]);
    }
}
