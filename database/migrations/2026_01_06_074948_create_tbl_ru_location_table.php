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
        Schema::create('tbl_ru_location', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('ru_location_id');
            $table->integer('ru_location_type_id');
            $table->integer('ru_parent_location_id');
            $table->string('name', 200)->default('');
            $table->boolean('status')->default(true);
            $table->integer('position')->default(0);
            $table->string('add_ip', 50)->nullable();
            $table->dateTime('add_time')->nullable();
            $table->integer('add_by')->nullable()->default(0);
            $table->string('update_ip', 50)->nullable();
            $table->dateTime('update_time')->nullable();
            $table->integer('update_by')->nullable()->default(0);
            $table->string('url_key', 260)->nullable();
            $table->string('meta_title', 100)->nullable();
            $table->string('meta_keyword', 350)->nullable();
            $table->string('meta_description', 200)->nullable();
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
        Schema::dropIfExists('tbl_ru_location');
    }
};
