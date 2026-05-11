<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('conversations', function (Blueprint $table) {
            // Drop the old foreign key with nullOnDelete
            $table->dropForeign(['board_id']);
            
            // Re-add the foreign key with cascadeOnDelete
            // This ensures when a board is deleted, all its conversations and related data are also deleted
            $table->foreign('board_id')
                  ->references('id')
                  ->on('boards')
                  ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('conversations', function (Blueprint $table) {
            // Revert to nullOnDelete
            $table->dropForeign(['board_id']);
            
            $table->foreign('board_id')
                  ->references('id')
                  ->on('boards')
                  ->nullOnDelete();
        });
    }
};
