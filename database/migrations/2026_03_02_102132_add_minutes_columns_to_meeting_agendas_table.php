<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ── meeting_agendas pivot ─────────────────────────────────────────────
        Schema::table('meeting_agendas', function (Blueprint $table) {
            if (!Schema::hasColumn('meeting_agendas', 'presenter')) {
                $table->string('presenter', 100)->nullable()->after('order');
            }
            if (!Schema::hasColumn('meeting_agendas', 'time_allocated')) {
                $table->unsignedSmallInteger('time_allocated')->nullable()->after('presenter')
                      ->comment('Allocated time in minutes');
            }
        });

        // ── meeting_attendees ─────────────────────────────────────────────────
        Schema::table('meeting_attendees', function (Blueprint $table) {
            if (!Schema::hasColumn('meeting_attendees', 'notes')) {
                $table->string('notes', 500)->nullable()->after('status');
            }
        });
    }

    public function down(): void
    {
        Schema::table('meeting_agendas', function (Blueprint $table) {
            $cols = array_filter(['presenter', 'time_allocated'], fn($c) => Schema::hasColumn('meeting_agendas', $c));
            if ($cols) $table->dropColumn(array_values($cols));
        });

        Schema::table('meeting_attendees', function (Blueprint $table) {
            if (Schema::hasColumn('meeting_attendees', 'notes')) {
                $table->dropColumn('notes');
            }
        });
    }
};
