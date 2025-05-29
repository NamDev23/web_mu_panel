<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserPaymentRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_payment_requests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->enum('payment_method', ['card', 'bank_transfer', 'paypal', 'crypto'])->default('card');
            $table->decimal('amount', 10, 2);
            $table->integer('coins_requested');
            $table->enum('status', ['pending', 'processing', 'completed', 'rejected'])->default('pending');
            $table->string('transaction_ref', 100)->nullable();
            $table->string('proof_image', 255)->nullable(); // Upload proof for bank transfer
            $table->text('qr_code_data')->nullable(); // QR code info for bank transfer
            $table->text('admin_notes')->nullable();
            $table->unsignedBigInteger('processed_by')->nullable(); // admin_users.id
            $table->timestamp('processed_at')->nullable();
            $table->text('card_details')->nullable(); // JSON for card info (serial, code, etc.)
            $table->string('gateway_response')->nullable(); // Response from payment gateway
            $table->timestamps();

            // Indexes
            $table->index(['user_id']);
            $table->index(['payment_method']);
            $table->index(['status']);
            $table->index(['processed_by']);
            $table->index(['created_at']);

            // Foreign key constraints
            $table->foreign('user_id')->references('id')->on('user_accounts')->onDelete('cascade');
            $table->foreign('processed_by')->references('id')->on('admin_users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_payment_requests');
    }
}
