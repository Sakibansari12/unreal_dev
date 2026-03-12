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
        Schema::create('tbl_ru_property_types', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('ru_property_type_id')->nullable();
            $table->string('name', 150)->default('');
            $table->integer('no_of_rooms')->nullable();
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
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_ru_property_types');
    }
};
