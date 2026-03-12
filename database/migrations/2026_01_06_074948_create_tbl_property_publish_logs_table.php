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
        Schema::create('tbl_property_publish_logs', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('property_id')->default(0);
            $table->string('pType')->nullable();
            $table->integer('status')->default(0);
            $table->date('adate1')->nullable();
            $table->date('adate2')->nullable();
            $table->date('activity_date')->nullable();
            $table->timestamp('updated_at')->useCurrent();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_property_publish_logs');
    }
};
