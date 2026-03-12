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
        Schema::create('ru_logs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->text('api_request')->nullable();
            $table->string('response_id')->nullable();
            $table->string('api_status')->nullable();
            $table->string('message')->nullable();
            $table->text('log')->nullable();
            $table->string('add_ip', 800)->nullable();
            $table->tinyInteger('status')->default(1);
            $table->softDeletes();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->useCurrent();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ru_logs');
    }
};
