<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBannedIpsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('banned_ips', function (Blueprint $table) {
            $table->id();
            $table->string('ip_address', 45); // Support IPv6
            $table->text('reason');
            $table->unsignedBigInteger('banned_by');
            $table->timestamp('banned_at');
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();

            // Indexes
            $table->unique('ip_address');
            $table->index(['banned_at']);
            $table->index(['expires_at']);

            // Foreign key constraint
            $table->foreign('banned_by')->references('id')->on('admin_users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('banned_ips');
    }
}
