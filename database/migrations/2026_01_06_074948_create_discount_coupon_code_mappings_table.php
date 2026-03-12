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
        Schema::create('discount_coupon_code_mappings', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('code', 100)->nullable();
            $table->integer('discount_coupon_id');
            $table->integer('is_used')->default(0);
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
        Schema::dropIfExists('discount_coupon_code_mappings');
    }
};
