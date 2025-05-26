<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMonthlyCardRewardsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('monthly_card_rewards', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('card_id');
            $table->string('username', 50);
            $table->enum('reward_type', ['daily', 'bonus', 'milestone']);
            $table->text('reward_data'); // JSON format
            $table->timestamp('claimed_at');
            $table->integer('day_number')->nullable(); // For daily rewards
            $table->timestamps();

            // Indexes
            $table->index(['card_id']);
            $table->index(['username']);
            $table->index(['claimed_at']);
            $table->index(['reward_type']);

            // Foreign key constraint
            $table->foreign('card_id')->references('id')->on('monthly_cards')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('monthly_card_rewards');
    }
}
