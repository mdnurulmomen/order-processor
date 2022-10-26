<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMerchantProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('merchant_products', function (Blueprint $table) {
            $table->mediumIncrements('id');
            $table->string('name');
            $table->string('sku');
            $table->text('detail')->nullable(); // A short description / quantity / half, full
            $table->float('discount', 5, 2)->default(0);            // individual discount
            $table->unsignedSmallInteger('price'); // price / if variation exists, then price of minimum-one
            $table->boolean('has_variation')->default(false);
            $table->boolean('has_addon')->default(false);
            $table->boolean('is_sponsored')->default(false);
            $table->boolean('is_customizable')->default(true);
            $table->boolean('is_promoted')->default(false);
            $table->boolean('is_available')->default(true);
            $table->unsignedMediumInteger('merchant_product_category_id');
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
        Schema::dropIfExists('merchant_products');
    }
}
