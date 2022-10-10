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
            $table->string('type');
            $table->boolean('is_asap_order')->default(true);
            $table->unsignedMediumInteger('price');
            $table->unsignedTinyInteger('vat');
            $table->unsignedTinyInteger('discount')->default(0);
            $table->unsignedTinyInteger('delivery_fee')->default(0);
            $table->unsignedMediumInteger('net_payable');
            $table->string('payment_method');
            $table->boolean('has_cutlery')->default(false);
            $table->string('orderer_type'); // Merchant / Waiter / Customer
            $table->unsignedInteger('orderer_id');
            $table->tinyInteger('customer_confirmation')->default(-1);
            $table->tinyInteger('in_progress')->default(-1);    // after customer confirmation turns to 1
            $table->tinyInteger('is_completed')->default(-1);    // after customer confirmation turns to 0
            // $table->string('canceller_type')->nullable();     // App/Models/Admin
            // $table->unsignedSmallInteger('canceller_id')->nullable(); // Admin 1
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
