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
        Schema::create('ru_property_minstay', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('ru_property_id')->nullable();
            $table->integer('home_id')->nullable();
            $table->date('minstay_date')->nullable();
            $table->integer('is_minstay_count');
            $table->string('type', 100)->default('standalone');
            $table->integer('status')->default(1);
            $table->dateTime('deleted_at')->nullable();
            $table->dateTime('updated_at')->useCurrentOnUpdate()->useCurrent();
            $table->dateTime('created_at')->useCurrent();

            $table->index(['home_id', 'minstay_date'], 'idx_home_id_minstay_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ru_property_minstay');
    }
};
