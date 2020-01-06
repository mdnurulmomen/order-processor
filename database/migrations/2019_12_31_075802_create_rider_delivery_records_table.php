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
        Schema::create('rider_delivery_records', function (Blueprint $table) {
            $table->increments('id');
            $table->boolean('delivery_order_acceptance')->default(1);
            $table->unsignedInteger('order_id');
            $table->unsignedSmallInteger('rider_id');
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
        Schema::dropIfExists('rider_delivery_records');
    }
}
