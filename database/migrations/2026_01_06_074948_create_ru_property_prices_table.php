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
        Schema::create('ru_property_prices', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('ru_property_id');
            $table->integer('property_id')->nullable();
            $table->integer('price')->nullable();
            $table->string('extra_price')->nullable();
            $table->date('price_date');
            $table->string('type', 100)->default('standalone');
            $table->tinyInteger('status')->default(1);
            $table->softDeletes();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->useCurrent();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['property_id', 'price_date', 'ru_property_id'], 'idx_property_id_price_date');
            $table->index(['property_id', 'price_date', 'type', 'ru_property_id'], 'idx_property_id_price_datsss');
            $table->index(['property_id', 'price_date', 'type'], 'idx_property_price');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ru_property_prices');
    }
};
