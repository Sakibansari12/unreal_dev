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
        Schema::create('cancellation_slabs', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('home_id')->nullable();
            $table->integer('slab_from');
            $table->integer('slab_to');
            $table->float('slab', 10)->nullable();
            $table->dateTime('deleted_at')->nullable();
            $table->dateTime('updated_at')->useCurrentOnUpdate()->useCurrent();
            $table->dateTime('created_at')->useCurrent();
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
        Schema::dropIfExists('cancellation_slabs');
    }
};
