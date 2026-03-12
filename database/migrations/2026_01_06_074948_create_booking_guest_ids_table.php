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
        Schema::create('booking_guest_ids', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('property_booking_id')->nullable();
            $table->string('property_name')->nullable();
            $table->integer('user_id')->nullable();
            $table->integer('sub_user_id')->nullable();
            $table->string('name')->nullable();
            $table->string('email')->nullable();
            $table->string('country_code')->nullable();
            $table->string('mobile_no')->nullable();
            $table->string('id_proof_img')->nullable();
            $table->date('checkin_date')->nullable();
            $table->date('checkout_date')->nullable();
            $table->string('otp_verified')->default('No');
            $table->tinyInteger('status')->default(1);
            $table->softDeletes();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->useCurrent();
            $table->timestamp('created_at')->useCurrent();
            $table->date('dob')->nullable();
            $table->date('anniversary')->nullable();
            $table->longText('id_type')->nullable();
            $table->longText('id_proof_front')->nullable();
            $table->longText('id_proof_back')->nullable();
            $table->integer('is_checkin')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('booking_guest_ids');
    }
};
