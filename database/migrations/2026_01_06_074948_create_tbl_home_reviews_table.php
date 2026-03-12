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
        Schema::create('tbl_home_reviews', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('home_id')->nullable();
            $table->integer('unit_id')->default(0);
            $table->integer('multi_unit_id')->nullable();
            $table->string('pType')->nullable();
            $table->string('file')->nullable();
            $table->string('banner_image')->nullable();
            $table->string('file_type')->nullable();
            $table->string('guest_name');
            $table->date('review_date');
            $table->integer('review_type')->default(0);
            $table->text('link')->nullable();
            $table->double('rating')->nullable();
            $table->text('comment');
            $table->integer('position')->default(0);
            $table->tinyInteger('display_on_home_page')->default(0);
            $table->string('img', 500)->nullable();
            $table->tinyInteger('status')->default(1);
            $table->string('add_ip', 20);
            $table->string('add_by', 50);
            $table->string('update_ip', 20)->nullable();
            $table->string('update_by', 50)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_home_reviews');
    }
};
