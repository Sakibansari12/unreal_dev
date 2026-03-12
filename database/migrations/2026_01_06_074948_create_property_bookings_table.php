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
        Schema::create('property_bookings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('booking_id')->nullable();
            $table->integer('user_id')->nullable();
            $table->integer('parent_user_id')->nullable();
            $table->integer('owner_id')->nullable();
            $table->integer('travelagent_id')->nullable();
            $table->integer('sub_user_id')->nullable();
            $table->integer('location_id');
            $table->string('property_name', 500)->nullable();
            $table->integer('property_id');
            $table->double('total_amount')->nullable();
            $table->double('website_markup_price')->nullable();
            $table->double('payable_amount')->nullable();
            $table->double('paid_amount')->default(0);
            $table->double('tax_amount')->nullable();
            $table->double('discount_amount')->nullable();
            $table->string('transcation_id', 800)->nullable();
            $table->text('customer_detail')->nullable();
            $table->text('company_detail')->nullable();
            $table->integer('no_of_adult')->nullable();
            $table->integer('no_of_children')->nullable();
            $table->enum('provider', ['stripe', 'razorpay', 'paypal', 'cashfee', 'payu'])->nullable();
            $table->enum('booking_status', ['pending', 'paid', 'declined'])->nullable();
            $table->integer('booking_quotation_id')->nullable();
            $table->enum('booking_created_by', ['admin', 'user', 'ru'])->nullable();
            $table->date('checkin_date')->nullable();
            $table->date('checkout_date')->nullable();
            $table->string('type')->nullable()->default('Property');
            $table->tinyInteger('status')->default(1);
            $table->softDeletes();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->useCurrent();
            $table->timestamp('created_at')->useCurrent();
            $table->string('razorpay_order_id', 200)->nullable();
            $table->string('booking_from', 30)->nullable();
            $table->string('ru_booking_status', 100)->nullable();
            $table->integer('is_blocking_hour')->default(1);
            $table->text('additional_charges')->nullable();
            $table->string('invoice', 500)->nullable();
            $table->string('channel', 100)->nullable();
            $table->double('per_night_price')->nullable();
            $table->integer('no_of_nights')->nullable();
            $table->integer('tax')->nullable();
            $table->text('additional_charges_detail')->nullable();
            $table->float('additional_charges_discount', 10)->nullable();
            $table->double('tot_additional_charge')->nullable();
            $table->double('base_price')->nullable();
            $table->double('extra_guest_charge')->nullable();
            $table->double('taxable_amount')->nullable();
            $table->text('customer_location_detail')->nullable();
            $table->integer('is_company_info')->default(0);
            $table->text('customer_company_info')->nullable();
            $table->string('applied_discount_coupon', 100)->nullable();
            $table->string('invoice_serial_no', 100)->nullable();
            $table->string('invoice_year', 100)->nullable();
            $table->string('invoice_no', 100)->nullable();
            $table->text('invoice_file')->nullable();
            $table->enum('property_booking_status', ['Requested', 'Confirmed', 'Not Confirmed', 'Canceled', 'Send Payment Link'])->default('Requested');
            $table->enum('payment_status', ['Pending', 'Paid'])->default('Pending');
            $table->string('booking_notes', 1000)->nullable();
            $table->string('customer_name', 100)->nullable();
            $table->string('customer_email')->nullable();
            $table->string('customer_number')->nullable();
            $table->string('checkin_time', 30)->nullable();
            $table->string('checkout_time', 30)->nullable();
            $table->string('ru_building_id', 100)->nullable();
            $table->string('ru_booking_id', 200)->nullable();
            $table->integer('room_no')->nullable();
            $table->string('pType', 20)->nullable()->default('unit');
            $table->string('otp', 10)->nullable();
            $table->string('otp_verified')->default('No');
            $table->integer('is_checkin')->default(0);

            $table->index(['property_id', 'checkin_date', 'checkout_date'], 'idx_property_booking');
            $table->index(['property_id', 'checkin_date'], 'idx_property_booking_checkin');
            $table->index(['property_id', 'checkin_date', 'checkout_date'], 'idx_property_id_checkin_checkout');
            $table->index(['property_id', 'checkin_date', 'checkout_date'], 'idx_property_id_checkin_checkouts');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('property_bookings');
    }
};
