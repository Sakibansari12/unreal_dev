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
        Schema::create('services', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('image')->nullable();
            $table->string('customer_service_title')->nullable();
            $table->string('customer_service_icon')->nullable();
            $table->longText('customer_service_short_description')->nullable();
            $table->string('privacy_flexibility_title')->nullable();
            $table->text('privacy_flexibility_icon')->nullable();
            $table->longText('privacy_flexibility_short_description')->nullable();
            $table->string('professionally_managed_title')->nullable();
            $table->string('professionally_managed_icon');
            $table->longText('professionally_managed_description');
            $table->string('best_feature_title');
            $table->string('best_feature_icon');
            $table->longText('best_feature_description');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
