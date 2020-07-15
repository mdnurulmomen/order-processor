<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRestaurantEvaluationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('restaurant_evaluations', function (Blueprint $table) {
            $table->mediumIncrements('id');
            $table->unsignedTinyInteger('order_acceptance_percentage')->default(0);
            $table->unsignedTinyInteger('mean_rating')->default(0);
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
        Schema::dropIfExists('restaurant_evaluations');
    }
}
