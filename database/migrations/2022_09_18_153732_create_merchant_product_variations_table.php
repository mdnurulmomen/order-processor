<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMerchantProductVariationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('merchant_product_variations', function (Blueprint $table) {
            $table->mediumIncrements('id');
            $table->string('sku');
            $table->unsignedSmallInteger('variation_id');
            $table->unsignedSmallInteger('price');
            $table->unsignedMediumInteger('merchant_product_id');
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
        Schema::dropIfExists('merchant_product_variations');
    }
}
