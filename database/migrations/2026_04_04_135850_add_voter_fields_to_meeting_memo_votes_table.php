<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('meeting_memo_votes', function (Blueprint $table) {
            // Make user_id nullable for guest voters
            $table->foreignId('user_id')->nullable()->change();

            $table->string('voter_name')->nullable()->after('user_id');
            $table->string('voter_email')->nullable()->after('voter_name');
            $table->enum('voter_type', ['user', 'guest'])->default('user')->after('voter_email');
            $table->foreignId('vote_link_id')
                  ->nullable()->after('voter_type')
                  ->constrained('meeting_vote_links')->nullOnDelete();
            $table->string('ip_address', 45)->nullable()->after('vote_link_id');
        });
    }

    public function down(): void
    {
        Schema::table('meeting_memo_votes', function (Blueprint $table) {
            $table->dropColumn(['voter_name', 'voter_email', 'voter_type', 'vote_link_id', 'ip_address']);
            $table->foreignId('user_id')->nullable(false)->change();
        });
    }
};
