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
            $table->tinyInteger('is_accepted')->nullable();  // -1 for ringing, 1 for confirm, 0 for cancel
            $table->timestamp('accepted_at')->nullable();
            $table->tinyInteger('is_ready')->nullable();  // 1 for confirm, 0 for cancel
            $table->timestamp('ready_at')->nullable();
            $table->boolean('has_delivery_support')->default(false);
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
