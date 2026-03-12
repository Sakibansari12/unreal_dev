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
        Schema::create('booking_quotation_properties', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('booking_quotation_id');
            $table->integer('property_id');
            $table->string('property_name')->nullable();
            $table->enum('pType', ['standalone', 'unit', 'multiunit'])->default('standalone');
            $table->float('basePrice', 10)->nullable();
            $table->integer('price');
            $table->double('total_amount')->nullable();
            $table->double('website_markup_price')->nullable();
            $table->double('tax_amount')->nullable();
            $table->double('taxable_amount');
            $table->double('addon_total_amount')->nullable();
            $table->double('addon_discount_amount')->nullable();
            $table->text('additional_charges_detail')->nullable();
            $table->integer('gst')->nullable();
            $table->double('gst_amount');
            $table->double('per_night_price')->nullable();
            $table->double('payable_amount')->nullable();
            $table->double('discountAmount')->nullable();
            $table->double('adOnsDiscountAmount')->nullable();
            $table->double('extra_guest_charge')->nullable();
            $table->enum('booking_status', ['Booked', 'Not Booked', 'Cancelled']);
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
        Schema::dropIfExists('booking_quotation_properties');
    }
};
