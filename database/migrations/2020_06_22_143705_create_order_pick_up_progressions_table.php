<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderPickUpProgressionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_pick_up_progressions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->boolean('rider_food_pick_confirmation')->default(false);
            $table->unsignedInteger('restaurant_id');
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
        Schema::dropIfExists('order_pick_up_progressions');
    }
}
