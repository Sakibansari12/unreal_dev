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
        Schema::create('tbl_ru_amenities', function (Blueprint $table) {
            $table->integer('amenities_id', true);
            $table->integer('ru_amenities_id')->nullable();
            $table->string('amenities_type', 100)->nullable()->default('')->index('newindex1');
            $table->string('amenities_name', 200)->default('');
            $table->string('amenities_image', 250)->nullable()->default('');
            $table->boolean('status')->default(true);
            $table->integer('position')->default(0);
            $table->string('add_ip', 50)->nullable();
            $table->dateTime('add_time')->nullable();
            $table->integer('add_by')->nullable()->default(0);
            $table->string('update_ip', 50)->nullable();
            $table->dateTime('update_time')->nullable();
            $table->integer('update_by')->nullable()->default(0);
            $table->dateTime('deleted_at')->nullable();
            $table->integer('active')->default(1);
            $table->dateTime('updated_at')->useCurrentOnUpdate()->useCurrent();
            $table->dateTime('created_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_ru_amenities');
    }
};
