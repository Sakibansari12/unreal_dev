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
        Schema::create('tbl_ru_floor', function (Blueprint $table) {
            $table->increments('floor_id');
            $table->integer('ru_id')->nullable();
            $table->string('name', 150)->default('');
            $table->boolean('status')->default(true);
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
        Schema::dropIfExists('tbl_ru_floor');
    }
};
