<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCharacterServiceLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('character_service_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('character_id'); // ID nhân vật trong game
            $table->string('character_name'); // Tên nhân vật hiện tại
            $table->enum('service_type', [
                'rename',           // Đổi tên
                'reset_stats',      // Reset điểm kỹ năng
                'reset_skills',     // Reset skill
                'change_class',     // Đổi class
                'teleport',         // Dịch chuyển
                'unbug',           // Sửa lỗi
                'item_recovery',    // Khôi phục item
                'level_adjustment'  // Điều chỉnh level
            ]);
            $table->integer('cost_coins'); // Chi phí coin
            $table->enum('status', ['pending', 'processing', 'completed', 'failed', 'cancelled'])->default('pending');
            $table->json('service_data'); // Dữ liệu dịch vụ chi tiết
            $table->json('before_data')->nullable(); // Dữ liệu trước khi thực hiện
            $table->json('after_data')->nullable(); // Dữ liệu sau khi thực hiện
            $table->text('notes')->nullable(); // Ghi chú
            $table->text('error_message')->nullable(); // Lỗi nếu có
            $table->unsignedBigInteger('processed_by')->nullable(); // Admin xử lý
            $table->timestamp('processed_at')->nullable(); // Thời gian xử lý
            $table->string('ip_address', 45)->nullable();
            $table->timestamps();

            // Indexes
            $table->index(['user_id']);
            $table->index(['character_id']);
            $table->index(['service_type']);
            $table->index(['status']);
            $table->index(['created_at']);

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
        Schema::dropIfExists('character_service_logs');
    }
}
