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
        Schema::create('tbl_unit_multiunits', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('unit_id');
            $table->integer('multiunit_id');
            $table->boolean('status')->default(true);
            $table->string('add_ip', 20)->nullable();
            $table->dateTime('add_time')->nullable();
            $table->string('add_by', 50)->nullable()->default('');
            $table->string('update_ip', 20)->nullable()->default('');
            $table->dateTime('update_time')->nullable();
            $table->string('update_by', 50)->nullable()->default('');
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
        Schema::dropIfExists('tbl_unit_multiunits');
    }
};
