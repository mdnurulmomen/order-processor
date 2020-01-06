<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderServeProgressionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // If serve on table for both insight/outside, take-away 
        // (After restaurant accepted the request)
        Schema::create('order_serve_progressions', function (Blueprint $table) {
            $table->increments('id');
            $table->boolean('waiter_confirmation')->default(false);
            $table->unsignedInteger('order_id')->unique();
            $table->unsignedMediumInteger('restaurant_id');
            $table->unsignedMediumInteger('waiter_id');
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
        Schema::dropIfExists('order_serve_progressions');
    }
}
