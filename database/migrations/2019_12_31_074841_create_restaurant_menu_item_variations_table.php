<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRestaurantMenuItemVariationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('restaurant_menu_item_variations', function (Blueprint $table) {
            $table->mediumIncrements('id');
            $table->unsignedSmallInteger('variation_id');
            $table->unsignedSmallInteger('price');
            $table->unsignedMediumInteger('restaurant_menu_item_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('restaurant_menu_item_variations');
    }
}
