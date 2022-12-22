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
            $table->string('type');     // delivery / take-away / serving / reservation
            $table->boolean('is_asap_order')->default(true);
            $table->string('payment_method');       // cash / bkash
            $table->boolean('has_cutlery')->default(false);
            $table->string('orderer_type'); // Merchant / Waiter / Customer
            $table->unsignedInteger('orderer_id');
            $table->tinyInteger('customer_confirmation')->default(-1);
            $table->tinyInteger('in_progress')->default(-1);    // after customer confirmation turns to 1
            $table->float('is_completed', 6, 2)->default(-1);    // after customer confirmation turns to 0
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
