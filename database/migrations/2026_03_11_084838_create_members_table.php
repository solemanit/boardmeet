<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('members', function (Blueprint $table) {
            $table->id();

            // ── Personal Info ─────────────────────────────────────────────
            $table->string('name');
            $table->string('email')->unique()->nullable();
            $table->string('phone')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->enum('gender', ['male', 'female', 'other'])->nullable();
            $table->string('blood_group')->nullable();
            $table->text('address')->nullable();
            $table->string('photo')->nullable(); // stored path

            // ── Professional Info ─────────────────────────────────────────
            $table->string('employee_id')->unique()->nullable();
            $table->string('designation')->nullable();
            $table->string('department')->nullable();
            $table->string('organization')->nullable();
            $table->date('joining_date')->nullable();

            // ── Educational Info ──────────────────────────────────────────
            $table->string('degree')->nullable();
            $table->string('institution')->nullable();
            $table->string('passing_year', 4)->nullable();

            // ── Access Control ────────────────────────────────────────────
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('members');
    }
};
