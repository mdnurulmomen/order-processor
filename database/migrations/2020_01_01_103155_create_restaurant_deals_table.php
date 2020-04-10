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
            $table->float('sale_percentage', 5, 2)->default(0);
            $table->float('restaurant_promotional_discount', 5, 2)->default(0);
            $table->float('native_discount', 5, 2)->default(0);
            $table->unsignedTinyInteger('discount_id');
            $table->boolean('delivery_fee_addition')->default(true);
            $table->unsignedMediumInteger('restaurant_id')->unique();
            $table->softDeletes();
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
