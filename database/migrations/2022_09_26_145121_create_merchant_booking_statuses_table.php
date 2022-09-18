<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMerchantBookingStatusesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('merchant_booking_statuses', function (Blueprint $table) {
            $table->mediumIncrements('id');
            $table->unsignedSmallInteger('total_seat');
            $table->unsignedSmallInteger('engaged_seat')->default(0);
            $table->unsignedMediumInteger('merchant_id')->unique();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('merchant_booking_statuses');
    }
}
