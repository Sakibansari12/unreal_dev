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
        Schema::create('tbl_home_amenities', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('home_id')->nullable();
            $table->integer('amenities_id');
            $table->string('amenities_name')->nullable();
            $table->integer('amenities_number')->nullable();
            $table->integer('position')->default(0)->comment('order wise positions set');
            $table->tinyInteger('status')->default(1);
            $table->string('add_ip', 20);
            $table->string('add_by', 50);
            $table->string('update_ip', 20)->nullable();
            $table->string('update_by', 50)->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->enum('pType', ['standalone', 'unit', 'multiunit'])->default('standalone');
            $table->integer('unit_id')->nullable();
            $table->integer('multi_unit_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_home_amenities');
    }
};
