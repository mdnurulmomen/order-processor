<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMerchantSelfDeliveriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('merchant_self_deliveries', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->boolean('is_delivered')->default(0);  // 1 for confirm, 0 for cancel
            $table->timestamp('delivered_at')->nullable();
            $table->unsignedInteger('confirmer_id')->nullable();    // who delivered can be merchant / merchant-agent
            $table->string('confirmer_type')->nullable();       // can be Waiter / Merchant as well
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
        Schema::dropIfExists('merchant_self_deliveries');
    }
}
