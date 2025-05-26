<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMonthlyCardsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('monthly_cards', function (Blueprint $table) {
            $table->id();
            $table->string('username', 50);
            $table->enum('type', ['monthly_card', 'battle_pass']);
            $table->string('package_name', 100);
            $table->decimal('price', 10, 2);
            $table->integer('duration_days');
            $table->text('daily_rewards'); // JSON format
            $table->text('bonus_rewards')->nullable(); // JSON format
            $table->text('description')->nullable();
            $table->enum('status', ['active', 'expired', 'cancelled'])->default('active');
            $table->timestamp('purchased_at');
            $table->timestamp('expires_at');
            $table->timestamp('cancelled_at')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('cancelled_by')->nullable();
            $table->text('cancel_reason')->nullable();
            $table->timestamps();

            // Indexes
            $table->index(['username']);
            $table->index(['type']);
            $table->index(['status']);
            $table->index(['expires_at']);
            $table->index(['purchased_at']);

            // Foreign key constraints
            $table->foreign('created_by')->references('id')->on('admin_users')->onDelete('cascade');
            $table->foreign('cancelled_by')->references('id')->on('admin_users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('monthly_cards');
    }
}
