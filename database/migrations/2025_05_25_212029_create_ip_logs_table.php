<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIpLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ip_logs', function (Blueprint $table) {
            $table->id();
            $table->string('ip_address');
            $table->string('username')->nullable();
            $table->string('character_name')->nullable();
            $table->enum('action', ['login', 'logout', 'register', 'failed_login']);
            $table->string('user_agent')->nullable();
            $table->string('country')->nullable();
            $table->string('city')->nullable();
            $table->boolean('is_banned')->default(false);
            $table->timestamp('banned_at')->nullable();
            $table->unsignedBigInteger('banned_by')->nullable();
            $table->text('ban_reason')->nullable();
            $table->timestamps();

            $table->foreign('banned_by')->references('id')->on('admin_users');
            $table->index(['ip_address', 'created_at']);
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
        Schema::dropIfExists('ip_logs');
    }
}
