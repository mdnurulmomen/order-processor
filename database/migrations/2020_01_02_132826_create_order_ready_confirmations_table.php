<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderReadyConfirmationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // After restaurant accepted the order (for all order)
        Schema::create('order_ready_confirmations', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('order_id');
            $table->boolean('food_ready_confirmation')->default(false);
            $table->unsignedInteger('restaurant_id');   // Multiple row if multiple restaurants in single order, Same for both counter & kitchen
            
            //$table->string('order_confirmer_type')->nullable(); // Counter / Kitchen
            //$table->unsignedMediumInteger('order_confirmer_id')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('order_ready_confirmations');
    }
}
