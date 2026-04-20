<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('meeting_memo_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('memo_id')
                ->constrained('meeting_agenda_memos')
                ->cascadeOnDelete();
            $table->string('file_name');            // UUID-based stored name
            $table->string('original_name');        // original upload name
            $table->string('file_path');            // storage path
            $table->string('file_type', 20);        // pdf, docx, xlsx …
            $table->unsignedBigInteger('file_size');
            $table->foreignId('uploaded_by')->constrained('users');
            $table->timestamps();

            $table->index('memo_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('meeting_memo_documents');
    }
};
