<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserTransactionLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_transaction_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->enum('type', [
                'coin_add',           // Cộng coin (nạp tiền, admin cộng)
                'coin_deduct',        // Trừ coin (mua dịch vụ)
                'service_purchase',   // Mua dịch vụ
                'character_rename',   // Đổi tên nhân vật
                'character_reset',    // Reset stats
                'giftcode_redeem',    // Nhập giftcode
                'transfer_to_game',   // Chuyển coin sang game
                'admin_adjustment'    // Admin điều chỉnh
            ]);
            $table->string('description'); // Mô tả giao dịch
            $table->integer('coin_amount')->default(0); // Số coin thay đổi (+/-)
            $table->integer('coin_before'); // Số coin trước giao dịch
            $table->integer('coin_after'); // Số coin sau giao dịch
            $table->json('metadata')->nullable(); // Dữ liệu chi tiết (tên cũ/mới, character_id, etc.)
            $table->string('reference_type')->nullable(); // Model liên quan (UserPaymentRequest, etc.)
            $table->unsignedBigInteger('reference_id')->nullable(); // ID của model liên quan
            $table->unsignedBigInteger('processed_by')->nullable(); // Admin xử lý (nếu có)
            $table->string('ip_address', 45)->nullable(); // IP thực hiện
            $table->string('user_agent')->nullable(); // User agent
            $table->timestamps();

            // Indexes
            $table->index(['user_id']);
            $table->index(['type']);
            $table->index(['created_at']);
            $table->index(['reference_type', 'reference_id']);

            // Foreign key constraints
            $table->foreign('user_id')->references('id')->on('user_accounts')->onDelete('cascade');
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
        Schema::dropIfExists('user_transaction_logs');
    }
}
