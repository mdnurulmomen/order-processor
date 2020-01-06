<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->increments('id');
            $table->string('order_type');
            $table->boolean('is_asap_order')->default(true);
            $table->timestamp('delivery_datetime')->nullable();
            $table->unsignedMediumInteger('order_price');
            $table->tinyInteger('vat');
            $table->unsignedTinyInteger('discount')->default(0);
            $table->unsignedTinyInteger('delivery_fee')->default(0);
            $table->unsignedMediumInteger('net_payable');
            $table->string('payment_method', 20);
            $table->boolean('cutlery_addition')->default(false);
            $table->string('orderer_type'); // Counter / Waiter / Customer
            $table->unsignedInteger('orderer_id');
            $table->boolean('call_confirmation')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('orders');
    }
}
