<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGiftcodeUsageLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('giftcode_usage_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('giftcode_id')->nullable(); // Link to giftcodes table
            $table->string('giftcode'); // Mã giftcode
            $table->string('giftcode_name')->nullable(); // Tên giftcode
            $table->enum('status', ['success', 'failed', 'expired', 'used', 'invalid'])->default('success');
            $table->json('rewards_received')->nullable(); // Phần thưởng nhận được
            $table->text('error_message')->nullable(); // Lỗi nếu có
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamps();

            // Indexes
            $table->index(['user_id']);
            $table->index(['giftcode']);
            $table->index(['status']);
            $table->index(['created_at']);

            // Foreign key constraints
            $table->foreign('user_id')->references('id')->on('user_accounts')->onDelete('cascade');
            $table->foreign('giftcode_id')->references('id')->on('giftcodes')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('giftcode_usage_logs');
    }
}
