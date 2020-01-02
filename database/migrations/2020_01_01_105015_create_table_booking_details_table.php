<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableBookingDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('table_booking_details', function (Blueprint $table) {
            $table->increments('id');
            $table->tinyInteger('guest_number')->default(1);
            $table->timestamp('arriving_time')->useCurrent();
            $table->string('mobile', 11);
            $table->timestamp('max_payment_time')->useCurrent();
            $table->integer('restaurant_id');
            $table->boolean('booking_confirmation')->default(0);
            $table->integer('order_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('table_booking_details');
    }
}
