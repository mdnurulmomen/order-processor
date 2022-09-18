<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMyRegularProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('my_regular_products', function (Blueprint $table) {
            $table->mediumIncrements('id');
            $table->unsignedMediumInteger('merchant_product_id');
            $table->unsignedTinyInteger('quantity')->default(1);
            $table->unsignedInteger('my_regular_merchant_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('my_regular_products');
    }
}
