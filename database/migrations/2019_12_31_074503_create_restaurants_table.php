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
            $table->string('user_name', 50)->unique(); // Auto Generated
            $table->char('mobile', 13)->unique();
            $table->string('email', 50)->nullable()->unique();
            $table->string('password'); // Login with username / mobile
            $table->rememberToken();
            $table->string('title', 50)->nullable();
            $table->string('address'); // floor, house, road and other landmark  
            $table->string('lat'); // from area name / map
            $table->string('lang'); // from area name / map
            $table->string('website')->nullable();
            $table->string('banner_preview')->nullable();
            $table->unsignedTinyInteger('min_order')->default(150);
            $table->string('meal_tags')->nullable();
            $table->string('food_tags')->nullable();
            $table->boolean('is_post_paid')->default(false);
            $table->boolean('is_self_service')->default(true);
            $table->boolean('has_parking')->default(false);
            $table->json('service_schedule')->nullable(); // service hour for whole week
            $table->json('booking_schedule_break')->nullable(); // break hour for whole week
            $table->boolean('taking_order')->default(false);
            $table->boolean('admin_approval')->default(false);
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
