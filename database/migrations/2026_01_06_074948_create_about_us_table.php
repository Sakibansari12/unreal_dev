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
        Schema::create('about_us', function (Blueprint $table) {
            $table->integer('id', true);
            $table->text('title')->nullable();
            $table->longText('short_description')->nullable();
            $table->string('image')->nullable();
            $table->string('properties')->nullable();
            $table->string('properties_icon');
            $table->string('properties_count');
            $table->string('happy_guests');
            $table->string('happy_guests_icon')->nullable();
            $table->string('happy_guests_count')->nullable();
            $table->string('hosting_experience')->nullable();
            $table->string('hosting_experience_icon')->nullable();
            $table->string('hosting_experience_count')->nullable();
            $table->longText('description')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('about_us');
    }
};
