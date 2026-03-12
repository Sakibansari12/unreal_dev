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
        Schema::create('tbl_location', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('state_id');
            $table->integer('ru_location_id')->nullable();
            $table->string('location_name');
            $table->text('title')->nullable();
            $table->text('sub_title')->nullable();
            $table->string('image')->nullable();
            $table->integer('show_on_location_page')->default(0);
            $table->integer('tax')->nullable()->comment('In percentage');
            $table->timestamps();
            $table->softDeletes();
            $table->tinyInteger('status')->default(1);
            $table->string('slug_name');
            $table->string('meta_title');
            $table->string('meta_keyword');
            $table->string('meta_description');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_location');
    }
};
