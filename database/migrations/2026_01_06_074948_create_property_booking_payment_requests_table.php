<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('property_booking_payment_requests', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('booking_request_id')->nullable();
            $table->integer('property_booking_id');
            $table->string('name')->nullable();
            $table->string('email')->nullable();
            $table->string('mobile_no', 30)->nullable();
            $table->string('country_code', 10)->nullable();
            $table->double('amount')->nullable();
            $table->enum('payment_mode', ['Razorpay', 'Cash', 'NEFT', 'Other'])->nullable();
            $table->text('note')->nullable();
            $table->enum('booking_request_status', ['Pending', 'Declined', 'Payment Received'])->nullable();
            $table->tinyInteger('status')->default(1);
            $table->string('payment_link_id')->nullable();
            $table->string('payment_link')->nullable();
            $table->string('link_status')->nullable();
            $table->softDeletes();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->useCurrent();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('property_booking_payment_requests');
    }
};
