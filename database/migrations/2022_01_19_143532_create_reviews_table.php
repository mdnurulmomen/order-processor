<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReviewsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedTinyInteger('rating')->default(3);
            $table->string('reviewable_type', 50); // Merchant / Rider
            $table->unsignedMediumInteger('reviewable_id'); // Merchant / Rider
            $table->unsignedMediumInteger('order_id');
            $table->unsignedInteger('customer_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('reviews');
    }
}
