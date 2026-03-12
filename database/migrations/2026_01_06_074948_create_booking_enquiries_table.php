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
        Schema::create('booking_enquiries', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('user_id')->nullable();
            $table->integer('parent_user_id')->nullable();
            $table->integer('location_id');
            $table->integer('property_id');
            $table->string('property_name')->nullable();
            $table->integer('no_of_guest');
            $table->integer('no_of_night');
            $table->string('name')->nullable();
            $table->string('phone_no')->nullable();
            $table->string('email')->nullable();
            $table->string('enquiry_message', 500)->nullable();
            $table->double('total_amount')->nullable();
            $table->date('checkin_date')->nullable();
            $table->date('checkout_date')->nullable();
            $table->enum('pType', ['standalone', 'unit', 'multiunit'])->default('standalone');
            $table->enum('enquiry_status', ['Requested', 'Available', 'Unavailable'])->default('Requested');
            $table->enum('email_status', ['Sent', 'Pending'])->default('Pending');
            $table->string('payment_link')->nullable();
            $table->enum('payment_status', ['Pending', 'Payment Received'])->default('Pending');
            $table->tinyInteger('status')->default(1);
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
        Schema::dropIfExists('booking_enquiries');
    }
};
