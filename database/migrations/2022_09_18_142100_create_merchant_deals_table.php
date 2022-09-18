<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMerchantDealsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('merchant_deals', function (Blueprint $table) {
            $table->mediumIncrements('id');
            $table->float('sale_percentage', 5, 2)->default(0);
            $table->float('promotional_discount', 5, 2)->default(0);
            $table->float('native_discount', 5, 2)->default(0);
            $table->unsignedTinyInteger('net_discount')->default(0);
            $table->boolean('delivery_fee_addition')->default(true);
            $table->unsignedMediumInteger('merchant_id')->unique();
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
        Schema::dropIfExists('merchant_deals');
    }
}
