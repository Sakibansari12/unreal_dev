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
        Schema::create('tbl_home_tags', function (Blueprint $table) {
            $table->id(); // int(11) AUTO_INCREMENT

            $table->integer('home_id')->default(0);
            
            $table->integer('tags_id')->default(0);

            $table->string('tags_name', 255)->nullable();
            $table->string('pType', 255)->nullable();
            $table->integer('unit_id')->default(0);
            $table->integer('multi_unit_id')->default(0);
            $table->integer('position')->default(0);
            $table->integer('status')->default(1);

            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();

            $table->timestamp('deleted_at')->nullable();

            $table->integer('tags_number')->default(1);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_home_tags');
    }
};
