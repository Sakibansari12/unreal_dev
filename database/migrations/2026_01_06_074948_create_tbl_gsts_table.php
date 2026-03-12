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
        Schema::create('tbl_gsts', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('slabs_start')->default(1);
            $table->integer('slabs_upto');
            $table->double('gst_percentage');
            $table->tinyInteger('status')->default(1);
            $table->integer('position')->default(0);
            $table->string('add_ip', 50)->nullable();
            $table->string('add_by')->nullable();
            $table->string('update_ip', 50)->nullable();
            $table->string('update_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_gsts');
    }
};
