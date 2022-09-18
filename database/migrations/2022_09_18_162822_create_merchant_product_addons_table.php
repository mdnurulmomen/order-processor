<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMerchantProductAddonsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('merchant_product_addons', function (Blueprint $table) {
            $table->mediumIncrements('id');
            $table->unsignedSmallInteger('addon_id');
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
        Schema::dropIfExists('merchant_product_addons');
    }
}
