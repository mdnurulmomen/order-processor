<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_orders', function (Blueprint $table) {
            $table->mediumIncrements('id');
            $table->unsignedMediumInteger('merchant_product_id');
            $table->unsignedMediumInteger('price');     // regular price for each at the time of ordering
            $table->unsignedTinyInteger('discount')->default(0);      // % at the time of ordering
            // $table->unsignedMediumInteger('discount_price');     // each price applying discount
            // $table->float('vat_rate', 5, 2)->default(0);
            $table->unsignedTinyInteger('quantity')->default(1);
            $table->boolean('is_addon_added')->default(0);      // if addon is added with this product-order
            $table->unsignedInteger('merchant_order_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_orders');
    }
}
