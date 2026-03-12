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
        Schema::create('tbl_home_units', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('ru_property_id')->nullable()->index('idx_homeunit_ru_property_id');
            $table->integer('is_published')->default(0);
            $table->integer('website_is_published')->default(0);
            $table->integer('ru_status')->default(0);
            $table->date('is_published_date')->nullable();
            $table->date('is_unpublished_date')->nullable();
            $table->string('pricelabs_unique_id',255)->nullable();
            $table->integer('user_id')->nullable()->index('idx_homeunit_user');
            $table->string('area_unit')->nullable();
            $table->integer('parent_user_id')->nullable();
            $table->string('brochure')->nullable();
            $table->integer('sub_user_id')->nullable()->index('idx_homeunit_sub_user');
            $table->integer('show_on_home')->default(0);
            $table->integer('only_for_enquiry')->default(0);
            $table->integer('home_id')->nullable()->index('idx_units_home_id');
            $table->integer('owner_id')->nullable();
            $table->string('unit_name', 100);
            $table->string('unit_name_website')->nullable();
            $table->double('per_night_price')->nullable();
            $table->string('slug')->nullable();
            $table->string('features_heading', 100)->nullable();
            $table->integer('home_type_id');
            $table->string('home_type', 100);
            $table->integer('state_id');
            $table->string('state', 100);
            $table->integer('area_id')->default(0);
            $table->string('area', 250)->nullable();
            $table->integer('location_id')->index('idx_homeunit_location');
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
            $table->integer('no_of_bedrooms')->default(0);
            $table->integer('no_of_bathrooms')->default(0);
            $table->string('property_size')->nullable();
            $table->string('map_latitude', 50)->nullable();
            $table->string('map_longitude', 50)->nullable();
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
            $table->string('ru_response_id', 200)->nullable();
            $table->text('comms')->nullable();
            $table->string('ru_response_message', 200)->nullable();
            $table->double('pl_price')->nullable();
            $table->string('pricelab_response_message', 250)->nullable();
            $table->text('additional_charges')->nullable();
            $table->enum('available_for_rent', ['Yes', 'No'])->default('Yes');
            $table->integer('contact_number')->nullable();
            $table->string('address', 500)->nullable();
            $table->string('postal_code', 30)->nullable();
            $table->text('ru_description')->nullable();
            $table->integer('min_stay')->default(1);
            $table->integer('allow_building')->default(0);
            $table->integer('no_of_rooms')->nullable();
            $table->string('ru_building_id', 100)->nullable();
            $table->timestamp('created_at')->nullable();
            $table->softDeletes();
            $table->timestamp('updated_at')->nullable();
            $table->text('website_property_rules')->nullable();
            $table->text('meals')->nullable();

            $table->index(['is_published', 'status'], 'idx_homeunit_status');
            $table->index(['user_id', 'sub_user_id'], 'idx_homeunit_user_sub');
            $table->index(['location_id', 'status', 'ru_property_id'], 'idx_location_aid_statuss');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_home_units');
    }
};
