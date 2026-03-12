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
        Schema::create('tbl_ru_specific_room_ammenities', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('tbl_ru_specific_room_ammenity_id');
            $table->integer('ru_room_ammenity_id');
            $table->string('ru_room_ammenity_name');
            $table->tinyInteger('status')->default(1);
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
        Schema::dropIfExists('tbl_ru_specific_room_ammenities');
    }
};
