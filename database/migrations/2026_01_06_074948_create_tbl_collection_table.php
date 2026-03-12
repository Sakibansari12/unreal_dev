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
        Schema::create('tbl_collection', function (Blueprint $table) {
            $table->integer('id')->primary();
            $table->string('collection_name')->nullable();
            $table->string('slug_name')->nullable();
            $table->text('collection_description')->nullable();
            $table->string('image')->nullable();
            $table->integer('status')->default(0);
            $table->integer('show_on_collection_page')->default(0);
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
        Schema::dropIfExists('tbl_collection');
    }
};
