<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('conversations', function (Blueprint $table) {
            $table->id();

            $table->enum('type', ['direct', 'group', 'board'])
                  ->default('direct');

            $table->string('name')->nullable();
            // null for direct chats
            // set for group chats and board chats

            $table->foreignId('board_id')
                  ->nullable()
                  ->constrained()
                  ->nullOnDelete();
            // only set when type = 'board'

            $table->foreignId('created_by')
                  ->constrained('users')
                  ->cascadeOnDelete();
            // who created this conversation

            $table->timestamp('last_message_at')->nullable();
            // updated every time a message is sent
            // used to sort inbox by recent activity

            $table->timestamps();

            // Indexes
            $table->index('type');
            $table->index('board_id');
            $table->index('last_message_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('conversations');
    }
};