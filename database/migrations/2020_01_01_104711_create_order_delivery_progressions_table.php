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
            $table->bigIncrements('id');
            $table->tinyInteger('number_restaurant')->default(1);
            $table->boolean('restaurant_acceptance')->default(1);
            $table->boolean('restaurant_handover')->default(0);
            $table->boolean('rider_pick')->default(0);
            $table->boolean('rider_delivery')->default(0);
            $table->integer('order_id');
            $table->integer('rider_id');
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
