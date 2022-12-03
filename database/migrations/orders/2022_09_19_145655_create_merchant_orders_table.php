<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMerchantOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('merchant_orders', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('merchant_id');
            $table->boolean('is_accepted')->nullable();  // -1 for ringing, 1 for confirm, 0 for cancel
            $table->timestamp('ringing_started_at')->nullable();
            $table->timestamp('answered_at')->nullable();
            $table->boolean('is_ready')->nullable();  // 1 for confirm, 0 for cancel
            $table->timestamp('ready_at')->nullable();
            $table->boolean('is_self_delivery')->nullable();        // if self-delivery or rider delivery, null for non-delivery order
            $table->boolean('is_free_delivery')->nullable();    // nullable as default is non-delivery
            $table->boolean('is_rider_available')->nullable();        // nullable as default is non-delivery
            $table->unsignedMediumInteger('net_price')->default(0);     // total payable price including addons and applying discounts
            $table->float('applied_sale_percentage', 5, 2)->default(0);     // applied on price (excluding vat)
            $table->tinyInteger('in_progress')->default(-1);    // after customer confirmation turns to 1
            $table->tinyInteger('is_completed')->default(-1);    // after customer confirmation turns to 0
            $table->boolean('is_payment_settled')->nullable();      // if order succeeds, then turns to 0 and after settlement 1
            $table->unsignedInteger('order_id');
            $table->timestamp('created_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('merchant_orders');
    }
}
