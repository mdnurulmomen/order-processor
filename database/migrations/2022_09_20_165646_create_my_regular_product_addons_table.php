<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMyRegularProductAddonsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('my_regular_product_addons', function (Blueprint $table) {
            $table->mediumIncrements('id');
            $table->unsignedSmallInteger('merchant_product_addon_id');
            $table->unsignedTinyInteger('quantity')->default(1);
            $table->unsignedMediumInteger('my_regular_product_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('my_regular_product_addons');
    }
}
