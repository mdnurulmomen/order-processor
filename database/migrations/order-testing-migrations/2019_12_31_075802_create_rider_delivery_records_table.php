<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRiderDeliveryRecordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // If it is delivery order (after/when restaurant accepted the order, for all orders)
        // May be multiple row for single order untill any rider accepts the order
        Schema::create('rider_delivery_records', function (Blueprint $table) {
            $table->increments('id');
            $table->tinyInteger('delivery_order_acceptance')->default(0); // initially ringing (-1 for ringing, 1 for confirm, 0 for cancel, default 0 as may not receive)
            $table->unsignedInteger('order_id');
            $table->unsignedSmallInteger('rider_id');
            // $table->string('updater_type')->nullable();     // App/Models/Admin
            // $table->unsignedSmallInteger('updater_id')->nullable(); // Admin ID
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
        Schema::dropIfExists('rider_delivery_records');
    }
}
