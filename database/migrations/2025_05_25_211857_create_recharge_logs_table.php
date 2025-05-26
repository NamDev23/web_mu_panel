<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRechargeLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('recharge_logs', function (Blueprint $table) {
            $table->id();
            $table->string('username');
            $table->string('character_name')->nullable();
            $table->decimal('amount', 10, 2);
            $table->integer('coins_added');
            $table->enum('type', ['manual', 'card', 'bank', 'paypal'])->default('manual');
            $table->enum('status', ['pending', 'completed', 'failed'])->default('completed');
            $table->text('note')->nullable();
            $table->string('transaction_id')->nullable();
            $table->unsignedBigInteger('admin_id');
            $table->string('admin_ip');
            $table->timestamps();

            $table->foreign('admin_id')->references('id')->on('admin_users');
            $table->index(['username', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('recharge_logs');
    }
}
