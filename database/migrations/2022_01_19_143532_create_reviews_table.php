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
            $table->unsignedTinyInteger('rating')->nullable()->default(3);
            $table->string('reviewable_type', 50); // Restaurnat / Rider
            $table->unsignedMediumInteger('reviewable_id'); // Restaurnat / Rider
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
