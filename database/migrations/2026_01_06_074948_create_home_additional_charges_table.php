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
        Schema::create('home_additional_charges', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('name', 100)->nullable();
            $table->float('price')->nullable();
            $table->integer('home_id')->nullable();
            $table->string('type_option', 200)->nullable();
            $table->integer('gst')->nullable();
            $table->integer('display_on_website')->default(0);
            $table->integer('status')->default(1);
            $table->dateTime('deleted_at')->nullable();
            $table->dateTime('updated_at')->useCurrentOnUpdate()->useCurrent();
            $table->dateTime('created_at')->nullable()->useCurrent();
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
        Schema::dropIfExists('home_additional_charges');
    }
};
