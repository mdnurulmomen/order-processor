<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMyRegularMerchantsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('my_regular_merchants', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('merchant_id');
            $table->unsignedInteger('my_regular_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('my_regular_merchants');
    }
}
