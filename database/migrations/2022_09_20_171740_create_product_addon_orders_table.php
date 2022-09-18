<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductAddonOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_addon_orders', function (Blueprint $table) {
            $table->mediumIncrements('id');
            $table->unsignedSmallInteger('merchant_product_addon_id');
            $table->unsignedTinyInteger('quantity')->default(1);
            $table->unsignedMediumInteger('product_order_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_addon_orders');
    }
}
