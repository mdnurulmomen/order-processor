<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRestaurantDealsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('restaurant_deals', function (Blueprint $table) {
            $table->mediumIncrements('id');
            $table->unsignedTinyInteger('sale_percentage')->default(0);
            $table->unsignedTinyInteger('restaurant_promotional_discount')->default(0);
            $table->unsignedTinyInteger('native_discount')->default(0);
            $table->unsignedTinyInteger('discount_id')->default(0);
            $table->boolean('delivery_fee_addition')->default(true);
            $table->unsignedMediumInteger('restaurant_id')->unique();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('restaurant_deals');
    }
}
