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
        // Bảng lưu số coin của user
        if (!Schema::hasTable('user_coins')) {
            Schema::create('user_coins', function (Blueprint $table) {
                $table->id();
                $table->integer('account_id')->index(); // ID từ t_account
                $table->string('username', 50)->index(); // Username từ t_account
                $table->bigInteger('coins')->default(0); // Số coin hiện tại
                $table->bigInteger('total_recharged')->default(0); // Tổng đã nạp
                $table->bigInteger('total_spent')->default(0); // Tổng đã tiêu
                $table->timestamps();

                $table->unique('account_id');
                $table->unique('username');
            });
        }

        // Bảng lịch sử nạp coin
        if (!Schema::hasTable('coin_recharge_logs')) {
            Schema::create('coin_recharge_logs', function (Blueprint $table) {
                $table->id();
                $table->integer('account_id')->index();
                $table->string('username', 50)->index();
                $table->string('transaction_id', 100)->unique();
                $table->bigInteger('amount_vnd'); // Số tiền VNĐ
                $table->bigInteger('coins_added'); // Số coin được cộng
                $table->enum('type', ['manual', 'card', 'bank', 'paypal', 'momo'])->default('manual');
                $table->enum('status', ['pending', 'completed', 'failed', 'cancelled'])->default('pending');
                $table->text('note')->nullable();
                $table->string('payment_method')->nullable();
                $table->text('payment_data')->nullable(); // Thông tin thanh toán (JSON string)
                $table->integer('admin_id')->nullable(); // Admin thực hiện (nếu manual)
                $table->string('admin_username')->nullable();
                $table->string('ip_address', 45)->nullable();
                $table->text('user_agent')->nullable();
                $table->timestamp('completed_at')->nullable();
                $table->timestamps();
            });
        }

        // Bảng lịch sử tiêu coin
        if (!Schema::hasTable('coin_spend_logs')) {
            Schema::create('coin_spend_logs', function (Blueprint $table) {
                $table->id();
                $table->integer('account_id')->index();
                $table->string('username', 50)->index();
                $table->string('transaction_id', 100)->unique();
                $table->bigInteger('coins_spent'); // Số coin đã tiêu
                $table->string('item_type', 50); // Loại item: vip, item, service, etc.
                $table->string('item_name'); // Tên item/dịch vụ
                $table->text('item_data')->nullable(); // Chi tiết item (JSON string)
                $table->text('description')->nullable();
                $table->string('ip_address', 45)->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coin_spend_logs');
        Schema::dropIfExists('coin_recharge_logs');
        Schema::dropIfExists('user_coins');
    }
};
