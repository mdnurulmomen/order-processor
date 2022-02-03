<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRiderDeliveryReturnsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rider_delivery_returns', function (Blueprint $table) {
            $table->increments('id');
            $table->tinyInteger('rider_return_confirmation')->default(0); // initially -1 for food return, 1 for confirm, 0 for not return, default 0 as may not returned)
            $table->unsignedInteger('order_id');
            $table->unsignedSmallInteger('rider_id');
            // $table->string('updater_type')->nullable();     // App/Models/Admin / App/Models/Restaurant
            // $table->unsignedSmallInteger('updater_id')->nullable(); // Admin 1
            $table->timestamp('created_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('rider_delivery_returns');
    }
}
