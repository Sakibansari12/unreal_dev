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
        Schema::create('discount_coupons', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('coupon_code', 100)->nullable();
            $table->string('code')->nullable();
            $table->string('title')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->tinyInteger('is_offer_valid_only_for_first_time')->default(0);
            $table->enum('user_type', ['single', 'multiple', 'early_bird', 'last_minute', 'long_stay'])->default('multiple');
            $table->integer('use_limit')->nullable();
            $table->enum('discount_type', ['percentage', 'flat'])->nullable();
            $table->integer('discount')->nullable();
            $table->enum('generated_coupon_code_by', ['self', 'auto'])->default('auto');
            $table->integer('coupon_valid_on_min_no_of_nights')->nullable();
            $table->double('coupon_valid_on_min_total_booking_amount')->nullable();
            $table->date('stay_date_from')->nullable();
            $table->date('stay_date_to')->nullable();
            $table->text('property_type_id')->nullable();
            $table->text('property_id')->nullable();
            $table->text('term_and_conditions')->nullable();
            $table->tinyInteger('status')->default(1);
            $table->integer('show_on_website')->default(0);
            $table->string('prefix')->nullable();
            $table->softDeletes();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->useCurrent();
            $table->timestamp('created_at')->useCurrent();
            $table->string('type', 100)->default('discount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('discount_coupons');
    }
};
