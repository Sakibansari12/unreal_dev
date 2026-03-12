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
        Schema::create('tbl_layout_images', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('home_id')->default(0);
            $table->string('type')->nullable();
            $table->string('filename')->nullable();
            $table->string('title')->nullable();
            $table->integer('default')->default(0);
            $table->integer('position')->default(0);
            $table->integer('status')->default(0);
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
            $table->softDeletes();
            $table->string('pType')->nullable();
            $table->integer('unit_id')->default(0);
            $table->integer('multi_unit_id')->nullable()->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_layout_images');
    }
};
