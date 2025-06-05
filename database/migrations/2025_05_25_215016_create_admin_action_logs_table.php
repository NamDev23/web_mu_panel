<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdminActionLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('admin_action_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('admin_id');
            $table->string('admin_username');
            $table->string('action'); // ban_account, unban_account, edit_account, etc.
            $table->string('target_type'); // account, character, giftcode, etc.
            $table->string('target_id');
            $table->string('target_name')->nullable();
            $table->text('old_data')->nullable(); // Data before change (JSON string)
            $table->text('new_data')->nullable(); // Data after change (JSON string)
            $table->text('reason')->nullable();
            $table->string('ip_address');
            $table->string('user_agent')->nullable();
            $table->timestamps();

            $table->foreign('admin_id')->references('id')->on('admin_users');
            $table->index(['admin_id', 'created_at']);
            $table->index(['action', 'created_at']);
            $table->index(['target_type', 'target_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('admin_action_logs');
    }
}
