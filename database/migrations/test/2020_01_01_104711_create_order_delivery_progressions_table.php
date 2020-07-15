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
            $table->tinyInteger('rider_delivery_confirmation')->default(-1); // initially pending (-1 for pending, 1 for dropped, 2 for dropped at office, 0 for cancel)
            $table->unsignedMediumInteger('rider_id');
            $table->unsignedInteger('order_id')->unique();
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
