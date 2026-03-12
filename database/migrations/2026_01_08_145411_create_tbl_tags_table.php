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
        Schema::create('tbl_tags', function (Blueprint $table) {
            $table->id();
            $table->string('tags_name')->nullable();
            $table->string('tag_title')->nullable();
            $table->string('tab_sub_title')->nullable();
            $table->integer('tag_show_on_page')->default(0);
            $table->string('tags_image')->nullable();
            $table->integer('status')->default(1);
            $table->integer('position')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_tags');
    }
};
