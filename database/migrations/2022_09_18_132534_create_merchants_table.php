<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMerchantsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('merchants', function (Blueprint $table) {
            $table->increments('id');
            $table->string('type')->default('restaurant');     // restaurant / shop / mart
            $table->string('name')->unique();
            $table->string('user_name')->unique(); // Auto Generated
            $table->string('email')->nullable();
            $table->char('mobile')->unique();
            $table->string('password'); // Login with username / mobile
            $table->string('address'); // floor, house, road and other landmark  
            $table->float('lat', 10, 6); // from area name / map
            $table->float('lng', 10, 6); // from area name / map
            $table->string('website')->nullable();
            $table->string('banner_preview')->nullable();
            $table->unsignedMediumInteger('min_order')->default(150);
            $table->boolean('is_post_paid')->default(false);
            $table->boolean('is_self_service')->default(true);
            $table->boolean('has_parking')->default(false);
            $table->boolean('sponsored')->default(false);
            $table->unsignedTinyInteger('delivery_charge_per_kilometer');
            $table->unsignedTinyInteger('min_delivery_charge');
            $table->unsignedTinyInteger('max_delivery_charge');
            $table->unsignedTinyInteger('order_acceptance_percentage')->default(0);
            $table->unsignedTinyInteger('mean_rating')->default(0);     // From Customers
            $table->json('service_schedule')->nullable(); // service hour for whole week
            $table->json('booking_break_schedule')->nullable(); // break hour for whole week
            $table->boolean('taking_order')->default(false);
            $table->boolean('admin_approval')->default(false);
            $table->unsignedInteger('merchant_owner_id');

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
        Schema::dropIfExists('merchants');
    }
}
