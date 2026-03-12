<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('home_important_information', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->text('sub_title')->nullable();
            $table->integer('icon_id')->default(0)->nullable();
            $table->integer('display_on_website')->default(0);
            $table->integer('status')->default(1);
            $table->string('icon_image')->nullable();
            $table->integer('home_id')->default(0);
            $table->string('pType', 255)->nullable();
            $table->integer('unit_id')->default(0);
            $table->integer('multi_unit_id')->default(0);
            $table->string('type_option')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('home_important_information');
    }
};
