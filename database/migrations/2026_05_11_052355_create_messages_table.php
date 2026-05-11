<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->id();

            $table->foreignId('conversation_id')
                  ->constrained()
                  ->cascadeOnDelete();

            $table->foreignId('user_id')
                  ->constrained()
                  ->cascadeOnDelete();
            // the sender

            $table->text('body')->nullable();
            // nullable because message could be attachment only

            $table->enum('type', ['text', 'image', 'file', 'system'])
                  ->default('text');
            // system = "Ali added Sara to the group"
            // these are auto-generated, not user typed

            $table->string('attachment_path', 500)->nullable();
            $table->string('attachment_name', 255)->nullable();
            // original filename shown in UI

            $table->unsignedBigInteger('attachment_size')->nullable();
            // in bytes

            $table->foreignId('reply_to_id')
                  ->nullable()
                  ->constrained('messages')
                  ->nullOnDelete();
            // reply/thread feature — points to parent message

            $table->boolean('is_edited')->default(false);
            // shows "edited" label in UI

            $table->softDeletes();
            // deleted messages show "This message was deleted"
            // we never hard delete so reply context is preserved

            $table->timestamps();

            // Most critical index — loads messages in order fast
            $table->index(['conversation_id', 'created_at']);
            $table->index('user_id');
            $table->index('reply_to_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};