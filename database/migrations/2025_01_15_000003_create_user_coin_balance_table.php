<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserCoinBalanceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_coin_balance', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->primary();
            $table->integer('web_coins')->default(0); // Coins for web services
            $table->integer('game_coins')->default(0); // Coins transferred to game
            $table->decimal('total_recharged', 10, 2)->default(0);
            $table->timestamp('last_recharge_at')->nullable();
            $table->timestamps();

            // Foreign key constraint
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
        Schema::dropIfExists('user_coin_balance');
    }
}
