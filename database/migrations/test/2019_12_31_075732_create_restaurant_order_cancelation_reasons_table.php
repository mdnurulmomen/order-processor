<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRestaurantOrderCancelationReasonsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('restaurant_order_cancelation_reasons', function (Blueprint $table) {
            $table->mediumIncrements('id');
            $table->unsignedTinyInteger('reason_id');
            $table->unsignedInteger('order_id');
            $table->unsignedMediumInteger('restaurant_id');
            $table->timestamp('created_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('restaurant_order_cancelation_reasons');
    }
}
