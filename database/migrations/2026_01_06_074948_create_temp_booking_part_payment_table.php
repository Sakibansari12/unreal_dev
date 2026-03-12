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
        Schema::create('temp_booking_part_payment', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('property_booking_payment_request_id');
            $table->string('txnid')->nullable();
            $table->text('payuDetail')->nullable();
            $table->dateTime('update_at')->useCurrentOnUpdate()->useCurrent();
            $table->dateTime('created_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('temp_booking_part_payment');
    }
};
