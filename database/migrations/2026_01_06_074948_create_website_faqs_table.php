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
        Schema::create('website_faqs', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('unit_id')->default(0);
            $table->integer('multi_unit_id')->default(0);
            $table->string('pType')->nullable();
            $table->text('question')->nullable();
            $table->text('answer')->nullable();
            $table->softDeletes();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('website_faqs');
    }
};
