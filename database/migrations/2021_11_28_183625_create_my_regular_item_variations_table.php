<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMyRegularItemVariationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('my_regular_item_variations', function (Blueprint $table) {
            $table->mediumIncrements('id');
            $table->unsignedSmallInteger('restaurant_menu_item_variation_id');
            $table->unsignedInteger('my_regular_item_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('my_regular_item_variations');
    }
}