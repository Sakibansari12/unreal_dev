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
        Schema::create('tbl_ru_specific_room_ammenity_mappings', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('ru_room_id');
            $table->integer('ammenity_id');
            $table->dateTime('deleted_at')->nullable();
            $table->dateTime('updated_at')->useCurrentOnUpdate()->useCurrent();
            $table->dateTime('created_at')->useCurrent();
            $table->integer('unit_id')->nullable();
            $table->integer('multi_unit_id')->nullable();
            $table->string('pType', 100);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_ru_specific_room_ammenity_mappings');
    }
};
