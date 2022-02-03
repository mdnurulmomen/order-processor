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
        // After any rider accepts the order
        // Multple row for multiple restaurants in a single order
        Schema::create('order_pick_up_progressions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->tinyInteger('rider_food_pick_confirmation')->default(-1); // initially pending (-1 for pending, 1 for confirm, 0 for cancel)
            $table->unsignedInteger('order_id');
            $table->unsignedInteger('restaurant_id');
            $table->unsignedMediumInteger('rider_id');
            // $table->string('updater_type')->nullable();     // App/Models/Admin / App/Models/Restaurant
            // $table->unsignedSmallInteger('updater_id')->nullable(); // Admin 1
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
