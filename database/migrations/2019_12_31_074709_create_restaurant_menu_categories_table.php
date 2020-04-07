<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRestaurantMenuCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('restaurant_menu_categories', function (Blueprint $table) {
            $table->mediumIncrements('id');
            $table->unsignedMediumInteger('menu_category_id');
            $table->string('serving_from')->nullable();
            $table->string('serving_to')->nullable();
            $table->unsignedMediumInteger('restaurant_id');
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
        Schema::dropIfExists('restaurant_menu_categories');
    }
}
