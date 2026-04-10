<?php

namespace Database\Seeders;

use App\Models\Board;
use App\Models\BoardList;
use App\Models\Card;
use App\Models\Label;
use App\Models\User;
use Illuminate\Database\Seeder;

class CardSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('email', 'admin@test.com')->first();
        $sara  = User::where('email', 'sara@test.com')->first();
        $ali   = User::where('email', 'ali@test.com')->first();
        $maria = User::where('email', 'maria@test.com')->first();
        $bilal = User::where('email', 'bilal@test.com')->first();

        // ════════════════════════════════════════════════════════
        // BOARD 1 — Development Sprint
        // ════════════════════════════════════════════════════════
        $board1 = Board::where('name', 'Development Sprint')->first();

        // Fetch labels for board 1
        $bugLabel         = Label::where('board_id', $board1->id)->where('name', 'Bug')->first();
        $featureLabel     = Label::where('board_id', $board1->id)->where('name', 'Feature')->first();
        $urgentLabel      = Label::where('board_id', $board1->id)->where('name', 'Urgent')->first();
        $backendLabel     = Label::where('board_id', $board1->id)->where('name', 'Backend')->first();
        $frontendLabel    = Label::where('board_id', $board1->id)->where('name', 'Frontend')->first();
        $blockedLabel     = Label::where('board_id', $board1->id)->where('name', 'Blocked')->first();
        $improvementLabel = Label::where('board_id', $board1->id)->where('name', 'Improvement')->first();

        // ── Backlog list ───────────────────────────────────────
        $backlog = BoardList::where('board_id', $board1->id)
            ->where('name', 'Backlog')->first();

        $card = Card::create([
            'list_id'     => $backlog->id,
            'user_id'     => $admin->id,
            'title'       => 'Set up CI/CD pipeline',
            'description' => 'Configure GitHub Actions to run tests on every push '
                . 'and auto-deploy to staging on merge to main.',
            'position'    => 0,
            'due_date'    => now()->addDays(14),
        ]);
        $card->assignees()->attach([$ali->id]);
        $card->labels()->attach([$backendLabel->id, $featureLabel->id]);

        $card = Card::create([
            'list_id'     => $backlog->id,
            'user_id'     => $admin->id,
            'title'       => 'Design database schema for notifications',
            'description' => 'Plan tables for real-time notifications. '
                . 'Consider using polymorphic relationships.',
            'position'    => 1,
            'due_date'    => now()->addDays(10),
        ]);
        $card->assignees()->attach([$admin->id]);
        $card->labels()->attach([$backendLabel->id]);

        $card = Card::create([
            'list_id'  => $backlog->id,
            'user_id'  => $admin->id,
            'title'    => 'Write API documentation',
            'position' => 2,
        ]);
        $card->labels()->attach([$improvementLabel->id]);

        // ── To Do list ────────────────────────────────────────
        $todo = BoardList::where('board_id', $board1->id)
            ->where('name', 'To Do')->first();

        $card = Card::create([
            'list_id'     => $todo->id,
            'user_id'     => $admin->id,
            'title'       => 'Fix login page on mobile Safari',
            'description' => 'The login button is partially hidden behind the '
                . 'iOS bottom bar on iPhone SE. Needs a fix in '
                . 'the guest layout padding.',
            'position'    => 0,
            'due_date'    => now()->addDays(2),
            'cover_color' => '#EB5A46',
        ]);
        $card->assignees()->attach([$sara->id]);
        $card->labels()->attach([$bugLabel->id, $urgentLabel->id, $frontendLabel->id]);

        $card = Card::create([
            'list_id'     => $todo->id,
            'user_id'     => $admin->id,
            'title'       => 'Add pagination to boards index',
            'description' => 'When a user has more than 20 boards the dashboard '
                . 'becomes slow. Add simple pagination or infinite scroll.',
            'position'    => 1,
            'due_date'    => now()->addDays(5),
        ]);
        $card->assignees()->attach([$admin->id, $sara->id]);
        $card->labels()->attach([$featureLabel->id, $frontendLabel->id]);

        $card = Card::create([
            'list_id'     => $todo->id,
            'user_id'     => $admin->id,
            'title'       => 'Implement email notification for card assignment',
            'description' => 'Send an email to the user when they are assigned '
                . 'to a card. Use Laravel Mail with a Blade email template.',
            'position'    => 2,
            'due_date'    => now()->addDays(7),
        ]);
        $card->assignees()->attach([$ali->id]);
        $card->labels()->attach([$featureLabel->id, $backendLabel->id]);

        $card = Card::create([
            'list_id'  => $todo->id,
            'user_id'  => $admin->id,
            'title'    => 'Refactor BoardController to use Service class',
            'position' => 3,
        ]);
        $card->labels()->attach([$improvementLabel->id, $backendLabel->id]);

        // ── In Progress list ──────────────────────────────────
        $inProgress = BoardList::where('board_id', $board1->id)
            ->where('name', 'In Progress')->first();

        $card = Card::create([
            'list_id'     => $inProgress->id,
            'user_id'     => $admin->id,
            'title'       => 'Build Kanban board drag and drop',
            'description' => 'Integrate SortableJS for card and list drag-and-drop. '
                . 'Cards must sync position to the database on drop.',
            'position'    => 0,
            'due_date'    => now()->addDays(1),
            'cover_color' => '#0079BF',
        ]);
        $card->assignees()->attach([$admin->id, $sara->id]);
        $card->labels()->attach([$featureLabel->id, $frontendLabel->id]);

        $card = Card::create([
            'list_id'     => $inProgress->id,
            'user_id'     => $admin->id,
            'title'       => 'Card detail modal — comments section',
            'description' => 'Build the comments UI inside the card modal. '
                . 'Post and delete comments without page reload.',
            'position'    => 1,
            'due_date'    => now()->subDays(1), // overdue on purpose
        ]);
        $card->assignees()->attach([$maria->id]);
        $card->labels()->attach([$featureLabel->id, $frontendLabel->id]);

        // ── Code Review list ──────────────────────────────────
        $codeReview = BoardList::where('board_id', $board1->id)
            ->where('name', 'Code Review')->first();

        $card = Card::create([
            'list_id'     => $codeReview->id,
            'user_id'     => $admin->id,
            'title'       => 'PR #42 — Add label management feature',
            'description' => 'Review the pull request for label CRUD and '
                . 'the attach/detach endpoints.',
            'position'    => 0,
            'due_date'    => now()->addDays(1),
        ]);
        $card->assignees()->attach([$ali->id, $admin->id]);
        $card->labels()->attach([$featureLabel->id]);

        $card = Card::create([
            'list_id'     => $codeReview->id,
            'user_id'     => $admin->id,
            'title'       => 'PR #39 — Fix n+1 queries on board show',
            'description' => 'Eager loading was missing on lists → cards → assignees. '
                . 'Review and approve the fix.',
            'position'    => 1,
        ]);
        $card->assignees()->attach([$admin->id]);
        $card->labels()->attach([$bugLabel->id, $backendLabel->id]);

        // ── Testing list ──────────────────────────────────────
        $testing = BoardList::where('board_id', $board1->id)
            ->where('name', 'Testing')->first();

        $card = Card::create([
            'list_id'     => $testing->id,
            'user_id'     => $admin->id,
            'title'       => 'Test file upload and attachment download',
            'description' => 'Upload multiple file types (image, PDF, docx). '
                . 'Verify storage path, public URL, and delete flow.',
            'position'    => 0,
            'due_date'    => now()->addDays(3),
        ]);
        $card->assignees()->attach([$maria->id]);
        $card->labels()->attach([$featureLabel->id]);

        // ── Done list ─────────────────────────────────────────
        $done = BoardList::where('board_id', $board1->id)
            ->where('name', 'Done')->first();

        $card = Card::create([
            'list_id'     => $done->id,
            'user_id'     => $admin->id,
            'title'       => 'Install Laravel Breeze with dark mode',
            'description' => 'Auth scaffolding installed and verified. '
                . 'Login, register, profile, and password reset all working.',
            'position'    => 0,
        ]);
        $card->assignees()->attach([$admin->id]);
        $card->labels()->attach([$featureLabel->id, $backendLabel->id]);

        $card = Card::create([
            'list_id'     => $done->id,
            'user_id'     => $admin->id,
            'title'       => 'Write all database migrations',
            'description' => 'All 10 migration files written and verified. '
                . '16 tables created successfully.',
            'position'    => 1,
        ]);
        $card->assignees()->attach([$admin->id]);
        $card->labels()->attach([$backendLabel->id]);

        $card = Card::create([
            'list_id'     => $done->id,
            'user_id'     => $admin->id,
            'title'       => 'Define all Eloquent models and relationships',
            'description' => 'All 8 models written with correct relationships, '
                . 'fillable fields, casts, and helper methods.',
            'position'    => 2,
        ]);
        $card->assignees()->attach([$admin->id]);
        $card->labels()->attach([$backendLabel->id]);

        // ════════════════════════════════════════════════════════
        // BOARD 2 — Marketing Campaigns
        // ════════════════════════════════════════════════════════
        $board2 = Board::where('name', 'Marketing Campaigns')->first();

        $socialLabel  = Label::where('board_id', $board2->id)
            ->where('name', 'Social Media')->first();
        $emailLabel   = Label::where('board_id', $board2->id)
            ->where('name', 'Email')->first();
        $contentLabel = Label::where('board_id', $board2->id)
            ->where('name', 'Content')->first();
        $paidLabel    = Label::where('board_id', $board2->id)
            ->where('name', 'Paid Ads')->first();

        $ideas = BoardList::where('board_id', $board2->id)
            ->where('name', 'Ideas')->first();

        $card = Card::create([
            'list_id'  => $ideas->id,
            'user_id'  => $admin->id,
            'title'    => 'Summer sale email campaign',
            'position' => 0,
            'due_date' => now()->addDays(20),
        ]);
        $card->assignees()->attach([$bilal->id]);
        $card->labels()->attach([$emailLabel->id]);

        $card = Card::create([
            'list_id'  => $ideas->id,
            'user_id'  => $admin->id,
            'title'    => 'Instagram reels strategy for Q3',
            'position' => 1,
        ]);
        $card->labels()->attach([$socialLabel->id, $contentLabel->id]);

        $planning = BoardList::where('board_id', $board2->id)
            ->where('name', 'Planning')->first();

        $card = Card::create([
            'list_id'     => $planning->id,
            'user_id'     => $admin->id,
            'title'       => 'Google Ads campaign setup for product launch',
            'description' => 'Set up search and display campaigns. '
                . 'Budget: $2000/month. Target: 25-40 age group.',
            'position'    => 0,
            'due_date'    => now()->addDays(8),
            'cover_color' => '#00875A',
        ]);
        $card->assignees()->attach([$bilal->id, $sara->id]);
        $card->labels()->attach([$paidLabel->id]);

        $inProgressM = BoardList::where('board_id', $board2->id)
            ->where('name', 'In Progress')->first();

        $card = Card::create([
            'list_id'     => $inProgressM->id,
            'user_id'     => $admin->id,
            'title'       => 'Write blog post: Top 10 productivity tips',
            'description' => 'Target keyword: productivity tips for remote teams. '
                . 'Minimum 1500 words. Include CTA at the end.',
            'position'    => 0,
            'due_date'    => now()->addDays(3),
        ]);
        $card->assignees()->attach([$sara->id]);
        $card->labels()->attach([$contentLabel->id]);

        // ════════════════════════════════════════════════════════
        // BOARD 3 — Personal Tasks
        // ════════════════════════════════════════════════════════
        $board3 = Board::where('name', 'Personal Tasks')->first();

        $importantLabel = Label::where('board_id', $board3->id)
            ->where('name', 'Important')->first();
        $laterLabel     = Label::where('board_id', $board3->id)
            ->where('name', 'Later')->first();

        $personalTodo = BoardList::where('board_id', $board3->id)
            ->where('name', 'To Do')->first();

        $card = Card::create([
            'list_id'     => $personalTodo->id,
            'user_id'     => $admin->id,
            'title'       => 'Renew passport',
            'description' => 'Book appointment at the passport office. '
                . 'Bring 2 photos and ID card.',
            'position'    => 0,
            'due_date'    => now()->addDays(30),
        ]);
        $card->assignees()->attach([$admin->id]);
        $card->labels()->attach([$importantLabel->id]);

        $card = Card::create([
            'list_id'  => $personalTodo->id,
            'user_id'  => $admin->id,
            'title'    => 'Read Clean Code by Robert Martin',
            'position' => 1,
        ]);
        $card->labels()->attach([$laterLabel->id]);

        $card = Card::create([
            'list_id'     => $personalTodo->id,
            'user_id'     => $admin->id,
            'title'       => 'Set up home office desk properly',
            'description' => 'Buy monitor arm, cable management tray, '
                . 'and a proper desk lamp.',
            'position'    => 2,
            'due_date'    => now()->addDays(7),
        ]);
        $card->labels()->attach([$laterLabel->id]);

        $personalDone = BoardList::where('board_id', $board3->id)
            ->where('name', 'Done')->first();

        $card = Card::create([
            'list_id'  => $personalDone->id,
            'user_id'  => $admin->id,
            'title'    => 'Set up Laravel development environment',
            'position' => 0,
        ]);
        $card->assignees()->attach([$admin->id]);
        $card->labels()->attach([$importantLabel->id]);

        $this->command->info('✓ Cards created with assignees and labels for all 3 boards.');
    }
}
