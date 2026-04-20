<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('agendas', function (Blueprint $table) {
            $table->id();
            $table->string('agenda_number')->unique(); // Auto-generated: AGD-2024-001
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('status', [
                'draft',
                'pending_review',
                'approved',
                'rejected',
                'revised',
            ])->default('draft');
            $table->enum('priority', ['low', 'medium', 'high', 'urgent'])->default('medium');
            $table->string('category')->nullable(); // financial, operational, strategic, etc.
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('assigned_to')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->date('meeting_date')->nullable();
            $table->integer('version')->default(1);
            $table->text('rejection_reason')->nullable();
            $table->text('revision_notes')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('agenda_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('agenda_id')->constrained('agendas')->onDelete('cascade');
            $table->string('file_name');
            $table->string('original_name');
            $table->string('file_path');
            $table->string('file_type'); // pdf, doc, docx, etc.
            $table->unsignedBigInteger('file_size'); // in bytes
            $table->enum('document_type', ['main', 'supporting'])->default('supporting');
            $table->string('description')->nullable();
            $table->foreignId('uploaded_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });

        Schema::create('agenda_approvals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('agenda_id')->constrained('agendas')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->enum('action', ['submitted', 'approved', 'rejected', 'revision_requested', 'revised']);
            $table->text('comment')->nullable();
            $table->integer('version')->default(1);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('agenda_approvals');
        Schema::dropIfExists('agenda_documents');
        Schema::dropIfExists('agendas');
    }
};
