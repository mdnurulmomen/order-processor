<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderDeliveryProgressionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_delivery_progressions', function (Blueprint $table) {
            $table->increments('id');
            $table->boolean('rider_food_pick_confirmation')->default(false);
            $table->boolean('rider_delivery_confirmation')->default(false);
            $table->unsignedInteger('order_id')->unique();
            $table->unsignedMediumInteger('rider_id');
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
        Schema::dropIfExists('order_delivery_progressions');
    }
}
