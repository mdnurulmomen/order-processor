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
        // table reservation (with food / without food)
        Schema::create('table_booking_details', function (Blueprint $table) {
            $table->mediumIncrements('id');
            $table->unsignedTinyInteger('guest_number')->default(1);
            // $table->timestamp('arriving_time')->useCurrent();
            $table->string('mobile', 13);
            $table->boolean('booking_confirmation')->default(false);
            $table->unsignedInteger('order_id')->nullable();
            $table->unsignedMediumInteger('restaurant_id');
            $table->timestamp('max_payment_time')->useCurrent();
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
