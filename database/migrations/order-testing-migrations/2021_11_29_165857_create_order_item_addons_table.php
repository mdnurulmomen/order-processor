<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderItemAddonsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_item_addons', function (Blueprint $table) {
            $table->mediumIncrements('id');
            $table->unsignedSmallInteger('restaurant_menu_item_addon_id');
            $table->unsignedTinyInteger('quantity')->default(1);
            $table->unsignedMediumInteger('order_item_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('order_item_addons');
    }
}
