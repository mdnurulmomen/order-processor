<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRestaurantOrderRecordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // order lifecycle starts after call confirmation
        Schema::create('restaurant_order_records', function (Blueprint $table) {
            $table->increments('id');
            $table->boolean('food_order_acceptance')->default(0);
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
        Schema::dropIfExists('restaurant_order_records');
    }
}
