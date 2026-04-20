<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // meeting_attendees table is already created inside
        // 2026_02_22_145600_create_meetings_table migration.
        // We only add missing columns if they don't exist.

        Schema::table('meeting_attendees', function (Blueprint $table) {
            if (!Schema::hasColumn('meeting_attendees', 'notes')) {
                $table->text('notes')->nullable()->after('status');
            }
        });
    }

    public function down(): void
    {
        Schema::table('meeting_attendees', function (Blueprint $table) {
            if (Schema::hasColumn('meeting_attendees', 'notes')) {
                $table->dropColumn('notes');
            }
        });
    }
};
