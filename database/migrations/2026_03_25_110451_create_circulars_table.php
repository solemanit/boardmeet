<?php
// database/migrations/xxxx_create_circulars_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('circulars', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('reference_number')->nullable()->unique();
            $table->longText('body');
            $table->enum('type', ['notice', 'circular', 'memo'])->default('circular');
            $table->enum('status', ['draft', 'published'])->default('draft');
            $table->date('issued_date');
            $table->timestamp('published_at')->nullable();
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete();
            $table->foreignId('updated_by')->constrained('users')->restrictOnDelete();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('circular_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('circular_id')->constrained()->cascadeOnDelete();
            $table->string('original_name');
            $table->string('path');
            $table->string('mime_type')->nullable();
            $table->unsignedBigInteger('size')->default(0); // bytes
            $table->timestamps();
        });

        Schema::create('circular_reminder_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('circular_id')->constrained()->cascadeOnDelete();
            $table->foreignId('sent_by')->constrained('users')->restrictOnDelete();
            $table->unsignedInteger('recipient_count')->default(0);
            $table->timestamp('sent_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('circular_reminder_logs');
        Schema::dropIfExists('circular_attachments');
        Schema::dropIfExists('circulars');
    }
};
