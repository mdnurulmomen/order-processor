<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRestaurantMenuItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('restaurant_menu_items', function (Blueprint $table) {
            $table->mediumIncrements('id');
            $table->string('name');
            $table->text('detail')->nullable(); // A short description / quantity / half, full
            $table->boolean('has_variation')->default(false);
            $table->boolean('has_addon')->default(false);
            $table->boolean('sponsored')->default(false);
            $table->unsignedSmallInteger('price'); // price / if variation exists, then price of minimum-one
            $table->boolean('customizable')->default(true);
            $table->boolean('promoted')->default(false);
            $table->boolean('item_stock')->default(true);
            $table->unsignedMediumInteger('restaurant_menu_category_id');
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
        Schema::dropIfExists('restaurant_menu_items');
    }
}
