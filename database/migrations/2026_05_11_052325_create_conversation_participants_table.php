<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('conversation_participants', function (Blueprint $table) {
            $table->id();

            $table->foreignId('conversation_id')
                  ->constrained()
                  ->cascadeOnDelete();

            $table->foreignId('user_id')
                  ->constrained()
                  ->cascadeOnDelete();

            $table->enum('role', ['admin', 'member'])
                  ->default('member');
            // admin can add/remove members in group chats

            $table->timestamp('last_read_at')->nullable();
            // tracks when this user last read messages
            // used to calculate unread count badge

            $table->timestamp('joined_at')->nullable();
            // when they were added to the conversation

            $table->boolean('is_muted')->default(false);
            // mute notifications for this conversation

            $table->timestamps();

            // Prevent duplicate participants
            $table->unique(['conversation_id', 'user_id']);

            // Indexes
            $table->index('user_id');
            $table->index(['conversation_id', 'last_read_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('conversation_participants');
    }
};