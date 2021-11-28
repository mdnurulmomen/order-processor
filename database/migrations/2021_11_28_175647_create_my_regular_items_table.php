<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMyRegularItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('my_regular_items', function (Blueprint $table) {
            $table->mediumIncrements('id');
            $table->unsignedMediumInteger('restaurant_menu_item_id');
            $table->unsignedTinyInteger('quantity')->default(1);
            $table->unsignedInteger('my_regular_restaurant_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('my_regular_items');
    }
}
