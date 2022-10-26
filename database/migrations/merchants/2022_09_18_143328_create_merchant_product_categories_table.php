<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMerchantProductCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('merchant_product_categories', function (Blueprint $table) {
            $table->mediumIncrements('id');
            $table->unsignedMediumInteger('product_category_id');
            $table->string('serving_from')->nullable();
            $table->string('serving_to')->nullable();
            // $table->float('vat_rate', 5, 2)->default(0);
            $table->float('discount', 5, 2)->default(0);            // global discount
            $table->unsignedMediumInteger('merchant_id');
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
        Schema::dropIfExists('merchant_product_categories');
    }
}
