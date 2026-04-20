<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('meeting_agenda_memos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('meeting_id')->constrained()->cascadeOnDelete();
            $table->foreignId('section_id')
                ->constrained('meeting_agenda_sections')
                ->cascadeOnDelete();
            $table->string('title', 255);
            $table->text('description')->nullable();
            $table->enum('vote_required', ['none', 'optional', 'required'])->default('none');
            $table->unsignedSmallInteger('order')->default(0);
            $table->text('minutes_note')->nullable();   // filled during minutes phase
            $table->timestamps();

            $table->index(['section_id', 'order']);
            $table->index('meeting_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('meeting_agenda_memos');
    }
};
