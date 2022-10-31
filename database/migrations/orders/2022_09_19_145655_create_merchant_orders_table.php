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
            $table->timestamp('accepted_at')->nullable();
            $table->boolean('is_ready')->nullable();  // 1 for confirm, 0 for cancel
            $table->timestamp('ready_at')->nullable();
            $table->boolean('is_free_delivery')->nullable();
            $table->boolean('is_self_delivery')->nullable();        // if self-delivery or system delivery
            $table->boolean('is_rider_available')->nullable();        // if self-delivery or system delivery
            $table->unsignedMediumInteger('net_price')->default(0);     // total payable price including addons and applying discounts
            // $table->unsignedTinyInteger('total_vat');         // applied vat % when ordering
            $table->float('applied_sale_percentage', 5, 2)->default(0);     // applied on price (excluding vat)
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
