<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('card_time_logs', function (Blueprint $table) {

            $table->id();
            $table->foreignId('card_id')
                  ->constrained()
                  ->cascadeOnDelete();
            $table->foreignId('user_id')
                  ->constrained()
                  ->cascadeOnDelete();
                  
            $table->timestamp('started_at');
            $table->timestamp('ended_at')->nullable();
            $table->unsignedInteger('duration')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('card_id');
            $table->index('user_id');
            $table->index(['card_id', 'user_id']);
            $table->index(['user_id', 'ended_at']);
            $table->index('started_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('card_time_logs');
    }
};