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
        // Bảng log IP của user
        if (!Schema::hasTable('ip_logs')) {
            Schema::create('ip_logs', function (Blueprint $table) {
                $table->id();
                $table->string('ip_address', 45)->index();
                $table->integer('account_id')->nullable()->index();
                $table->string('username', 50)->nullable()->index();
                $table->string('character_name', 50)->nullable();
                $table->enum('action', ['login', 'logout', 'register', 'failed_login', 'admin_login'])->default('login');
                $table->enum('status', ['success', 'failed', 'blocked'])->default('success');
                $table->string('user_agent')->nullable();
                $table->string('country', 10)->nullable(); // Country code
                $table->string('city', 100)->nullable();
                $table->text('location_data')->nullable(); // Thông tin địa lý (JSON string)
                $table->text('notes')->nullable();
                $table->timestamps();

                $table->index(['ip_address', 'created_at']);
                $table->index(['username', 'created_at']);
            });
        }

        // Bảng IP bị cấm
        if (!Schema::hasTable('banned_ips')) {
            Schema::create('banned_ips', function (Blueprint $table) {
                $table->id();
                $table->string('ip_address', 45)->unique();
                $table->text('reason');
                $table->enum('type', ['permanent', 'temporary'])->default('permanent');
                $table->integer('banned_by')->index(); // Admin ID
                $table->string('banned_by_username', 50);
                $table->timestamp('banned_at');
                $table->timestamp('expires_at')->nullable();
                $table->boolean('is_active')->default(true);
                $table->text('notes')->nullable();
                $table->timestamps();
            });
        }

        // Bảng whitelist IP (IP được phép)
        if (!Schema::hasTable('ip_whitelist')) {
            Schema::create('ip_whitelist', function (Blueprint $table) {
                $table->id();
                $table->string('ip_address', 45)->unique();
                $table->string('description');
                $table->integer('added_by')->index(); // Admin ID
                $table->string('added_by_username', 50);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        // Bảng cảnh báo IP đáng ngờ
        if (!Schema::hasTable('ip_alerts')) {
            Schema::create('ip_alerts', function (Blueprint $table) {
                $table->id();
                $table->string('ip_address', 45)->index();
                $table->enum('alert_type', ['multiple_accounts', 'rapid_login', 'suspicious_location', 'failed_attempts'])->index();
                $table->string('title');
                $table->text('description');
                $table->integer('severity')->default(1); // 1=low, 2=medium, 3=high, 4=critical
                $table->text('alert_data')->nullable(); // Chi tiết cảnh báo (JSON string)
                $table->enum('status', ['new', 'investigating', 'resolved', 'false_positive'])->default('new');
                $table->integer('resolved_by')->nullable();
                $table->timestamp('resolved_at')->nullable();
                $table->text('resolution_notes')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ip_alerts');
        Schema::dropIfExists('ip_whitelist');
        Schema::dropIfExists('banned_ips');
        Schema::dropIfExists('ip_logs');
    }
};
