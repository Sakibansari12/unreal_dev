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
        Schema::create('ru_property_availabilities', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('ru_property_id', 100);
            $table->integer('property_id')->nullable();
            $table->date('availability_date')->nullable();
            $table->enum('is_available', ['yes', 'no'])->nullable()->default('yes');
            $table->string('type', 100)->default('standalone');
            $table->tinyInteger('status')->default(1);
            $table->softDeletes();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->useCurrent();
            $table->timestamp('created_at')->useCurrent();
            $table->text('reason')->nullable();

            $table->index(['ru_property_id', 'availability_date', 'is_available', 'type'], 'idx_property_availability');
            $table->index(['ru_property_id', 'availability_date', 'is_available', 'type'], 'idx_ru_property_id_availability_daste');
            $table->index(['ru_property_id', 'availability_date', 'is_available'], 'idx_ru_property_id_availability_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ru_property_availabilities');
    }
};
