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
        Schema::create('tbl_homes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('user_id')->nullable()->index('idx_home_user_id');
            $table->integer('parent_user_id')->nullable();
            $table->integer('sub_user_id')->nullable();
            $table->string('home_name', 100)->index('idx_home_name');
            $table->string('slug')->nullable();
            $table->string('features_heading', 100)->nullable();
            $table->integer('home_type_id')->nullable();
            $table->string('home_type', 100)->nullable();
            $table->integer('state_id');
            $table->string('state', 100);
            $table->integer('area_id')->default(0);
            $table->string('area', 250)->nullable();
            $table->integer('location_id');
            $table->string('location');
            $table->text('short_description')->nullable();
            $table->text('description')->nullable();
            $table->text('short_direction')->nullable();
            $table->text('direction_how_to_get_there')->nullable();
            $table->text('house_rules')->nullable();
            $table->text('cancellation_policy')->nullable();
            $table->text('things_to_know')->nullable();
            $table->integer('max_number_of_nights')->nullable();
            $table->integer('booking_window')->nullable();
            $table->string('checkin_time', 10)->nullable();
            $table->string('checkout_time', 10)->nullable();
            $table->integer('maximum_number_of_guests')->nullable();
            $table->integer('guests_included')->nullable();
            $table->double('extra_guest_charges')->nullable();
            $table->integer('no_of_staff')->nullable();
            $table->integer('no_of_bedrooms')->nullable();
            $table->integer('no_of_bathrooms')->nullable();
            $table->string('map_latitude', 50)->nullable()->default('28.7041');
            $table->string('map_longitude', 50)->nullable()->default('77.1025');
            $table->string('map_text', 1000)->nullable();
            $table->string('googlelocation_url', 250)->nullable();
            $table->boolean('pet_friendly')->default(false);
            $table->boolean('stags_allowed')->default(false);
            $table->boolean('private_pool')->default(false);
            $table->double('weekly_discounts')->nullable();
            $table->double('monthly_discounts')->nullable();
            $table->string('owner_name', 150)->nullable();
            $table->string('owner_email', 150)->nullable();
            $table->string('owner_alternate_email', 150)->nullable();
            $table->string('owner_mobile', 50)->nullable();
            $table->string('owner_pan', 100)->nullable();
            $table->string('owner_company_name', 150)->nullable();
            $table->string('owner_gst_number', 50)->nullable();
            $table->string('owner_country', 100)->nullable();
            $table->string('owner_state', 100)->nullable();
            $table->string('owner_city', 100)->nullable();
            $table->string('owner_address', 120)->nullable();
            $table->string('tourism_license_number', 50)->nullable();
            $table->date('license_expiry_date')->nullable();
            $table->double('owner_share')->nullable();
            $table->boolean('status')->default(true);
            $table->integer('position')->default(0);
            $table->string('add_ip', 50)->nullable();
            $table->string('add_by')->nullable();
            $table->string('update_ip', 50)->nullable();
            $table->string('update_by')->nullable();
            $table->string('url_key', 260)->nullable();
            $table->string('meta_title', 200)->nullable();
            $table->string('meta_keyword', 550)->nullable();
            $table->string('meta_description', 500)->nullable();
            $table->integer('ru_property_id')->nullable();
            $table->string('ru_response_id', 200)->nullable();
            $table->text('comms')->nullable();
            $table->string('ru_response_message', 200)->nullable();
            $table->double('pl_price')->nullable();
            $table->string('pricelab_response_message', 250)->nullable();
            $table->text('additional_charges')->nullable();
            $table->enum('available_for_rent', ['Yes', 'No'])->default('Yes');
            $table->integer('contact_number')->nullable();
            $table->float('per_night_price', 10)->nullable();
            $table->string('address', 500)->nullable();
            $table->string('postal_code', 30)->nullable();
            $table->string('type', 30)->nullable();
            $table->integer('min_no_of_nights')->default(1);
            $table->integer('is_published')->default(0);
            $table->integer('min_stay')->nullable();
            $table->text('ru_description')->nullable();
            $table->integer('allow_building')->default(0);
            $table->string('ru_building_id', 100)->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->index(['location_id', 'status'], 'idx_location_id_status');
            $table->index(['location_id', 'status'], 'idx_location_id_statusss');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_homes');
    }
};
