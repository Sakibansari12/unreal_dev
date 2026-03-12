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
        Schema::create('tbl_features', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('home_id');
            $table->string('title', 100)->nullable();
            $table->text('detail');
            $table->integer('position')->default(0);
            $table->tinyInteger('status')->default(1);
            $table->string('add_ip', 20);
            $table->string('add_by', 50);
            $table->string('update_ip', 20)->nullable();
            $table->string('update_by', 50)->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->enum('pType', ['standalone', 'unit', 'multiunit'])->nullable();
            $table->integer('unit_id')->nullable();
            $table->integer('multi_unit_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_features');
    }
};
