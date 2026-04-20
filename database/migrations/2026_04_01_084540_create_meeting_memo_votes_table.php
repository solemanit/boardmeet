<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('meeting_memo_votes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('memo_id')
                ->constrained('meeting_agenda_memos')
                ->cascadeOnDelete();
            $table->foreignId('meeting_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->enum('vote', ['approve', 'reject', 'abstain']);
            $table->text('comment')->nullable();
            $table->timestamp('voted_at')->nullable();
            $table->timestamps();

            // One vote per user per memo
            $table->unique(['memo_id', 'user_id']);
            $table->index(['meeting_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('meeting_memo_votes');
    }
};
