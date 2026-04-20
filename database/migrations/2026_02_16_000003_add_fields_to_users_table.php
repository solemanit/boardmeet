<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Add role relationship
            $table->foreignId('role_id')->nullable()->constrained('roles')->onDelete('set null')->after('id');

            // Add activation status
            $table->boolean('is_active')->default(false)->after('password');
            $table->timestamp('activated_at')->nullable()->after('is_active');

            // Add password policy fields
            $table->timestamp('last_password_changed_at')->nullable()->after('activated_at');
            $table->integer('failed_login_attempts')->default(0)->after('last_password_changed_at');
            $table->timestamp('last_login_at')->nullable()->after('failed_login_attempts');
            $table->timestamp('locked_until')->nullable()->after('last_login_at');

            // Add MFA fields
            $table->boolean('mfa_enabled')->default(false)->after('locked_until');
            $table->string('mfa_method')->nullable()->comment('totp, sms, email')->after('mfa_enabled');
            $table->string('mfa_secret')->nullable()->after('mfa_method');
            $table->string('phone_number')->nullable()->after('mfa_secret');
            $table->boolean('phone_verified')->default(false)->after('phone_number');

            // Add audit fields
            $table->timestamp('deleted_at')->nullable()->after('phone_verified');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeignKeyIfExists(['role_id']);
            $table->dropColumn([
                'role_id',
                'is_active',
                'activated_at',
                'last_password_changed_at',
                'failed_login_attempts',
                'last_login_at',
                'locked_until',
                'mfa_enabled',
                'mfa_method',
                'mfa_secret',
                'phone_number',
                'phone_verified',
                'deleted_at',
            ]);
        });
    }
};
