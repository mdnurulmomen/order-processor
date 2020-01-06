<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRiderReviewsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rider_reviews', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedTinyInteger('rating')->nullable()->default(2);
            $table->unsignedSmallInteger('rider_id');
            $table->unsignedInteger('customer_id');
            $table->timestamp('created_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('rider_reviews');
    }
}
