<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGameAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('game_accounts', function (Blueprint $table) {
            $table->id();
            $table->string('username')->unique();
            $table->string('password');
            $table->string('email')->unique();
            $table->string('phone')->nullable();
            $table->string('full_name')->nullable();
            $table->enum('status', ['active', 'banned', 'suspended'])->default('active');
            $table->text('ban_reason')->nullable();
            $table->timestamp('banned_at')->nullable();
            $table->unsignedBigInteger('banned_by')->nullable();
            $table->integer('vip_level')->default(0);
            $table->decimal('total_recharge', 15, 2)->default(0);
            $table->decimal('current_balance', 15, 2)->default(0);
            $table->integer('characters_count')->default(0);
            $table->timestamp('last_login_at')->nullable();
            $table->string('last_login_ip')->nullable();
            $table->json('security_questions')->nullable();
            $table->boolean('is_verified')->default(false);
            $table->timestamps();

            $table->foreign('banned_by')->references('id')->on('admin_users');
            $table->index(['username', 'email']);
            $table->index(['status', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('game_accounts');
    }
}
