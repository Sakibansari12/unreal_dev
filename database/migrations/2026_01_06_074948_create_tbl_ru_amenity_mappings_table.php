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
        Schema::create('tbl_ru_amenity_mappings', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('home_id')->nullable();
            $table->integer('unit_id')->nullable();
            $table->integer('multi_unit_id')->nullable();
            $table->integer('floor_id');
            $table->integer('bedroom_no')->nullable();
            $table->integer('bathroom_no')->nullable();
            $table->string('amenity_type', 100);
            $table->integer('ru_amenity_id');
            $table->dateTime('deleted_at')->nullable();
            $table->dateTime('updated_at')->useCurrentOnUpdate()->useCurrent();
            $table->dateTime('created_at')->useCurrent();
            $table->enum('pType', ['standalone', 'unit', 'multiunit'])->default('standalone');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_ru_amenity_mappings');
    }
};
