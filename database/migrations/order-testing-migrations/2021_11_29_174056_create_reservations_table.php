<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReservationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reservations', function (Blueprint $table) {
            $table->mediumIncrements('id');
            $table->unsignedTinyInteger('guest_number')->default(1);
            // $table->timestamp('arriving_time')->useCurrent();
            // $table->string('mobile', 13);
            // $table->boolean('booking_confirmation')->default(false);
            $table->timestamp('max_payment_time')->useCurrent();
            $table->unsignedInteger('order_id');
            $table->unsignedMediumInteger('merchant_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('reservations');
    }
}
