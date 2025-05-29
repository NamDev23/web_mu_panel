<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWithdrawRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('withdraw_requests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('game_account_id');
            $table->string('game_username'); // Tên tài khoản game nhận
            $table->integer('amount'); // Số coin rút
            $table->enum('status', ['pending', 'processing', 'completed', 'failed', 'cancelled'])->default('pending');
            $table->integer('web_coins_before'); // Số coin web trước khi rút
            $table->integer('web_coins_after')->nullable(); // Số coin web sau khi rút
            $table->integer('game_coins_before'); // Số coin game trước khi nhận
            $table->integer('game_coins_after')->nullable(); // Số coin game sau khi nhận
            $table->decimal('exchange_rate', 8, 4)->default(1.0000); // Tỷ lệ quy đổi (nếu có)
            $table->text('notes')->nullable(); // Ghi chú
            $table->text('error_message')->nullable(); // Lỗi nếu có
            $table->unsignedBigInteger('processed_by')->nullable(); // Admin xử lý
            $table->timestamp('processed_at')->nullable(); // Thời gian xử lý
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamps();

            // Indexes
            $table->index(['user_id']);
            $table->index(['game_account_id']);
            $table->index(['status']);
            $table->index(['created_at']);
            $table->index(['processed_at']);

            // Foreign key constraints
            $table->foreign('user_id')->references('id')->on('user_accounts')->onDelete('cascade');
            $table->foreign('game_account_id')->references('id')->on('game_accounts')->onDelete('cascade');
            $table->foreign('processed_by')->references('id')->on('admin_users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('withdraw_requests');
    }
}
