<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTRolesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('t_roles', function (Blueprint $table) {
            $table->bigIncrements('rid'); // Primary key
            $table->string('rname', 50); // Character name
            $table->unsignedBigInteger('userid'); // User ID (foreign key to game_accounts)
            $table->integer('serverid')->default(1); // Server ID
            $table->integer('level')->default(1); // Character level
            $table->bigInteger('experience')->default(0); // Experience points
            $table->bigInteger('money')->default(0); // In-game money
            $table->integer('occupation')->default(0); // Character class/occupation
            $table->timestamp('regtime')->nullable(); // Registration time
            $table->timestamp('lasttime')->nullable(); // Last login time
            $table->timestamp('logofftime')->nullable(); // Last logout time
            $table->tinyInteger('isdel')->default(0); // 0 = active, 1 = banned/deleted
            $table->timestamps();

            // Indexes
            $table->index(['rname']);
            $table->index(['userid']);
            $table->index(['serverid']);
            $table->index(['level']);
            $table->index(['isdel']);

            // Foreign key constraint
            $table->foreign('userid')->references('id')->on('game_accounts')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('t_roles');
    }
}
