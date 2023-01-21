<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTakingOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('taking_orders', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->boolean('is_taken')->default(0);  // 1 for confirm, 0 for cancel
            $table->timestamp('taken_at')->nullable();
            $table->unsignedInteger('confirmer_id')->nullable();    // who is confirming, can be merchant / customer
            $table->string('confirmer_type')->nullable();       // can be Customer / Merchant as well
            $table->unsignedInteger('merchant_id');
            $table->unsignedInteger('order_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('taking_orders');
    }
}
