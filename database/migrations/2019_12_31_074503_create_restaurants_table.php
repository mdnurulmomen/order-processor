<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRestaurantsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('restaurants', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 50)->unique();
            $table->char('mobile', 13)->unique();
            $table->string('address'); // floor, house, road and other landmark  
            $table->float('lat', 10, 6); // from area name / map
            $table->float('lng', 10, 6); // from area name / map
            $table->string('website')->nullable();
            $table->string('banner_preview')->nullable();
            $table->unsignedMediumInteger('min_order')->default(150);
            $table->unsignedMediumInteger('max_booking')->default(100);
            // $table->string('meal_tags')->nullable();
            // $table->string('food_tags')->nullable();
            $table->boolean('is_post_paid')->default(false);
            $table->boolean('is_self_service')->default(true);
            $table->boolean('has_parking')->default(false);
            $table->json('service_schedule')->nullable(); // service hour for whole week
            $table->json('booking_break_schedule')->nullable(); // break hour for whole week
            $table->boolean('taking_order')->default(false);
            $table->boolean('admin_approval')->default(false);
            $table->unsignedInteger('restaurant_admins_id');

            $table->softDeletes();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('restaurants');
    }
}
