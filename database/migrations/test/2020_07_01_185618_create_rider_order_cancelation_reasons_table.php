<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRiderOrderCancelationReasonsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rider_order_cancelation_reasons', function (Blueprint $table) {
            $table->mediumIncrements('id');
            $table->unsignedTinyInteger('reason_id');
            $table->unsignedInteger('order_id')->unique();
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
        Schema::dropIfExists('rider_order_cancelation_reasons');
    }
}
