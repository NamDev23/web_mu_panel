<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCoinSpendLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('coin_spend_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('account_id');
            $table->string('username');
            $table->string('transaction_id')->unique();
            $table->integer('coins_spent');
            $table->string('item_type'); // withdraw, purchase, etc.
            $table->string('item_name');
            $table->json('item_data')->nullable();
            $table->text('description')->nullable();
            $table->string('ip_address')->nullable();
            $table->timestamps();

            $table->index(['account_id', 'created_at']);
            $table->index('transaction_id');
            $table->index('item_type');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('coin_spend_logs');
    }
}
