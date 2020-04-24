<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdditionalOrderedAddonsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('additional_ordered_addons', function (Blueprint $table) {
            $table->mediumIncrements('id');
            $table->unsignedSmallInteger('restaurant_menu_item_addon_id');
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
        Schema::dropIfExists('additional_ordered_addons');
    }
}
