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
        Schema::create('booking_quotations', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('user_id')->nullable();
            $table->integer('parent_user_id')->nullable();
            $table->integer('location_id')->nullable();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('country_code')->nullable();
            $table->string('mobile_number')->nullable();
            $table->string('email')->nullable();
            $table->date('checkin_date');
            $table->date('checkout_date');
            $table->integer('no_of_nights');
            $table->integer('no_adults')->nullable();
            $table->integer('no_children')->nullable();
            $table->integer('guest_included_count');
            $table->string('validity', 100)->nullable();
            $table->integer('is_email_sent')->default(0);
            $table->enum('booking_status', ['Not Booked', 'Booked', 'Cancelled'])->default('Not Booked');
            $table->dateTime('deleted_at')->nullable();
            $table->dateTime('updated_at')->useCurrentOnUpdate()->useCurrent();
            $table->dateTime('created_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('booking_quotations');
    }
};
