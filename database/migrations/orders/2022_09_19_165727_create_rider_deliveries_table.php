<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRiderDeliveriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rider_deliveries', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedTinyInteger('delivery_fee')->default(0);
            $table->unsignedTinyInteger('rider_call_receiving_time')->default(30);  // seconds
            $table->tinyInteger('is_accepted')->default(0); // -1 for ringing, 1 for confirm, 0 for cancel, default 0 as may not receive
            $table->timestamp('accepted_at')->nullable();
            $table->tinyInteger('is_delivered')->nullable(); // -1 for pending, 1 for dropped, 2 for dropped at office, 0 for cancel
            $table->timestamp('deliverd_at')->nullable();
            $table->unsignedInteger('order_id');
            $table->unsignedMediumInteger('rider_id');
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
        Schema::dropIfExists('rider_deliveries');
    }
}
