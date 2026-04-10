<?php

namespace Database\Seeders;

use App\Models\Attachment;
use App\Models\Card;
use App\Models\User;
use Illuminate\Database\Seeder;

class AttachmentSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('email', 'admin@test.com')->first();
        $sara  = User::where('email', 'sara@test.com')->first();
        $ali   = User::where('email', 'ali@test.com')->first();
        $maria = User::where('email', 'maria@test.com')->first();
        $bilal = User::where('email', 'bilal@test.com')->first();

        // ── Card: Build Kanban board drag and drop ────────────
        $card = Card::where('title', 'Build Kanban board drag and drop')->first();

        Attachment::create([
            'card_id'   => $card->id,
            'user_id'   => $admin->id,
            'filename'  => 'sortablejs-integration-notes.pdf',
            'file_path' => 'attachments/card-' . $card->id . '/sortablejs-integration-notes.pdf',
            'file_size' => 204800, // 200 KB
            'mime_type' => 'application/pdf',
        ]);

        Attachment::create([
            'card_id'   => $card->id,
            'user_id'   => $sara->id,
            'filename'  => 'drag-drop-wireframe.png',
            'file_path' => 'attachments/card-' . $card->id . '/drag-drop-wireframe.png',
            'file_size' => 512000, // 500 KB
            'mime_type' => 'image/png',
        ]);

        // ── Card: Fix login page on mobile Safari ─────────────
        $card = Card::where('title', 'Fix login page on mobile Safari')->first();

        Attachment::create([
            'card_id'   => $card->id,
            'user_id'   => $sara->id,
            'filename'  => 'mobile-safari-screenshot.png',
            'file_path' => 'attachments/card-' . $card->id . '/mobile-safari-screenshot.png',
            'file_size' => 318000, // ~310 KB
            'mime_type' => 'image/png',
        ]);

        Attachment::create([
            'card_id'   => $card->id,
            'user_id'   => $sara->id,
            'filename'  => 'iphone-se-bug-recording.mp4',
            'file_path' => 'attachments/card-' . $card->id . '/iphone-se-bug-recording.mp4',
            'file_size' => 2048000, // 2 MB
            'mime_type' => 'video/mp4',
        ]);

        // ── Card: Set up CI/CD pipeline ───────────────────────
        $card = Card::where('title', 'Set up CI/CD pipeline')->first();

        Attachment::create([
            'card_id'   => $card->id,
            'user_id'   => $ali->id,
            'filename'  => 'github-actions-workflow.yml',
            'file_path' => 'attachments/card-' . $card->id . '/github-actions-workflow.yml',
            'file_size' => 4096, // 4 KB
            'mime_type' => 'text/plain',
        ]);

        // ── Card: PR #42 — Add label management feature ───────
        $card = Card::where('title', 'PR #42 — Add label management feature')->first();

        Attachment::create([
            'card_id'   => $card->id,
            'user_id'   => $ali->id,
            'filename'  => 'label-feature-test-results.pdf',
            'file_path' => 'attachments/card-' . $card->id . '/label-feature-test-results.pdf',
            'file_size' => 153600, // 150 KB
            'mime_type' => 'application/pdf',
        ]);

        // ── Card: Google Ads campaign setup ───────────────────
        $card = Card::where('title', 'Google Ads campaign setup for product launch')
            ->first();

        Attachment::create([
            'card_id'   => $card->id,
            'user_id'   => $bilal->id,
            'filename'  => 'keyword-research-sheet.xlsx',
            'file_path' => 'attachments/card-' . $card->id . '/keyword-research-sheet.xlsx',
            'file_size' => 98304, // 96 KB
            'mime_type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);

        Attachment::create([
            'card_id'   => $card->id,
            'user_id'   => $bilal->id,
            'filename'  => 'ads-budget-breakdown.pdf',
            'file_path' => 'attachments/card-' . $card->id . '/ads-budget-breakdown.pdf',
            'file_size' => 77824, // 76 KB
            'mime_type' => 'application/pdf',
        ]);

        // ── Card: Write blog post ─────────────────────────────
        $card = Card::where('title', 'Write blog post: Top 10 productivity tips')
            ->first();

        Attachment::create([
            'card_id'   => $card->id,
            'user_id'   => $sara->id,
            'filename'  => 'blog-post-draft-v1.docx',
            'file_path' => 'attachments/card-' . $card->id . '/blog-post-draft-v1.docx',
            'file_size' => 45056, // 44 KB
            'mime_type' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        ]);

        // ── Card: Design database schema for notifications ─────
        $card = Card::where('title', 'Design database schema for notifications')
            ->first();

        Attachment::create([
            'card_id'   => $card->id,
            'user_id'   => $admin->id,
            'filename'  => 'notifications-erd-diagram.png',
            'file_path' => 'attachments/card-' . $card->id . '/notifications-erd-diagram.png',
            'file_size' => 256000, // 250 KB
            'mime_type' => 'image/png',
        ]);

        // ── Card: Renew passport ──────────────────────────────
        $card = Card::where('title', 'Renew passport')->first();

        Attachment::create([
            'card_id'   => $card->id,
            'user_id'   => $admin->id,
            'filename'  => 'passport-requirements-checklist.pdf',
            'file_path' => 'attachments/card-' . $card->id . '/passport-requirements-checklist.pdf',
            'file_size' => 61440, // 60 KB
            'mime_type' => 'application/pdf',
        ]);

        $this->command->info('✓ Attachment records created across all boards.');
    }
}
