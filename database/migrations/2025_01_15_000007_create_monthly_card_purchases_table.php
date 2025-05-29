<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMonthlyCardPurchasesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('monthly_card_purchases', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('package_name'); // Tên gói thẻ tháng
            $table->enum('package_type', ['basic', 'premium', 'vip']); // Loại gói
            $table->integer('duration_days'); // Số ngày hiệu lực
            $table->integer('cost_coins'); // Chi phí coin
            $table->decimal('daily_reward_coins', 8, 2); // Coin nhận mỗi ngày
            $table->json('bonus_items')->nullable(); // Items bonus khi mua
            $table->json('daily_items')->nullable(); // Items nhận mỗi ngày
            $table->enum('status', ['active', 'expired', 'cancelled'])->default('active');
            $table->timestamp('activated_at'); // Thời gian kích hoạt
            $table->timestamp('expires_at'); // Thời gian hết hạn
            $table->timestamp('last_claimed_at')->nullable(); // Lần cuối nhận thưởng
            $table->integer('days_claimed')->default(0); // Số ngày đã nhận thưởng
            $table->text('notes')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->timestamps();

            // Indexes
            $table->index(['user_id']);
            $table->index(['package_type']);
            $table->index(['status']);
            $table->index(['expires_at']);

            // Foreign key constraints
            $table->foreign('user_id')->references('id')->on('user_accounts')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('monthly_card_purchases');
    }
}
