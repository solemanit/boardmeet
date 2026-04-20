<?php
// database/migrations/xxxx_create_meetings_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('meetings', function (Blueprint $table) {
            $table->id();
            $table->string('meeting_number')->unique();          // MTG-2024-001
            $table->string('title');
            $table->enum('type', ['board', 'committee', 'agm', 'egm', 'executive', 'other'])->default('board');
            $table->enum('status', ['scheduled', 'ongoing', 'completed', 'cancelled', 'postponed'])->default('scheduled');
            $table->date('date');
            $table->time('start_time');
            $table->time('end_time')->nullable();
            $table->string('venue')->nullable();
            $table->enum('venue_type', ['physical', 'virtual', 'hybrid'])->default('physical');
            $table->string('meeting_link')->nullable();          // Zoom/Teams link
            $table->text('description')->nullable();
            $table->foreignId('chairperson_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('secretary_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('previous_meeting_id')->nullable()->constrained('meetings')->onDelete('set null');
            $table->foreignId('rescheduled_from_id')->nullable()->constrained('meetings')->onDelete('set null');
            $table->timestamp('rescheduled_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->text('cancellation_reason')->nullable();
            $table->timestamp('notification_sent_at')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('meeting_attendees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('meeting_id')->constrained('meetings')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->enum('role', ['chairperson', 'secretary', 'member', 'guest'])->default('member');
            $table->enum('status', ['invited', 'confirmed', 'declined', 'attended', 'absent'])->default('invited');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->unique(['meeting_id', 'user_id']);
        });

        // Pivot: meeting <-> agenda (many-to-many)
        Schema::create('meeting_agendas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('meeting_id')->constrained('meetings')->onDelete('cascade');
            $table->foreignId('agenda_id')->constrained('agendas')->onDelete('cascade');
            $table->unsignedSmallInteger('order')->default(0);
            $table->text('discussion_notes')->nullable();
            $table->text('resolution')->nullable();
            $table->enum('status', ['pending', 'discussed', 'deferred'])->default('pending');
            $table->timestamps();
            $table->unique(['meeting_id', 'agenda_id']);
        });

        Schema::create('meeting_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('meeting_id')->constrained('meetings')->onDelete('cascade');
            $table->string('file_name');
            $table->string('original_name');
            $table->string('file_path');
            $table->string('file_type');
            $table->unsignedBigInteger('file_size');
            $table->enum('document_type', ['minutes', 'notice', 'supporting'])->default('supporting');
            $table->foreignId('uploaded_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('meeting_documents');
        Schema::dropIfExists('meeting_agendas');
        Schema::dropIfExists('meeting_attendees');
        Schema::dropIfExists('meetings');
    }
};
