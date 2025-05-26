<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGiftcodeUsageTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('giftcode_usage', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('giftcode_id');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('username');
            $table->string('character_name')->nullable();
            $table->timestamp('used_at');
            $table->string('ip_address');
            $table->string('user_agent')->nullable();
            $table->timestamps();

            // Indexes
            $table->index(['giftcode_id']);
            $table->index(['username']);
            $table->index(['used_at']);

            // Foreign key constraint
            $table->foreign('giftcode_id')->references('id')->on('giftcodes')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('giftcode_usage');
    }
}
