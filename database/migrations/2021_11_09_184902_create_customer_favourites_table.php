<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomerFavouritesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customer_favourites', function (Blueprint $table) {
            $table->increments('id');
            // $table->string('lat'); // from suggested area name / pin pointing map
            // $table->string('lang'); // from suggested area name / pin pointing map
            $table->unsignedMediumInteger('restaurant_id');
            $table->string('customer_address_id'); // work,home,other
            // $table->unsignedInteger('customer_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('customer_favourites');
    }
}
