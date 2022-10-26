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
            $table->unsignedMediumInteger('min_order')->default(100);
            $table->boolean('is_post_paid')->default(false);
            $table->boolean('is_self_service')->default(true);
            $table->boolean('is_sponsored')->default(false);
            $table->boolean('has_parking')->default(false);
            $table->boolean('has_free_delivery')->default(true);
            $table->boolean('has_self_delivery_support')->default(true);
            $table->json('service_schedule')->nullable(); // service hour for whole week
            $table->json('booking_break_schedule')->nullable(); // break hour for whole week
            $table->float('supported_delivery_order_sale_percentage', 5, 2)->default(0);
            $table->float('general_order_sale_percentage', 5, 2)->default(0);   // all other orders except supported delivery order
            $table->float('discount', 5, 2)->default(0);            // global discount
            $table->unsignedTinyInteger('order_acceptance_percentage')->default(0);
            $table->unsignedTinyInteger('mean_rating')->default(0);     // From Customers
            $table->boolean('is_open')->default(false);
            $table->boolean('is_approved')->default(false);
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
