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
        Schema::create('tbl_home_image_video', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('home_id')->nullable();
            $table->string('type')->default('image')->comment('Type of content: "image" or "video"');
            $table->string('title')->nullable();
            $table->string('filename');
            $table->tinyInteger('default')->default(0)->comment('default image, position first');
            $table->integer('position')->default(0)->comment('order wise positions set');
            $table->boolean('status')->default(true);
            $table->string('add_ip', 20);
            $table->string('add_by', 50);
            $table->string('update_ip', 20)->nullable();
            $table->string('update_by', 50)->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->integer('tbl_ru_image_type_id')->nullable();
            $table->enum('pType', ['standalone', 'unit', 'multiunit'])->default('standalone');
            $table->integer('unit_id')->nullable();
            $table->integer('multi_unit_id')->nullable();
            $table->longText('base64_image')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_home_image_video');
    }
};
