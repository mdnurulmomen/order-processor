<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateServingOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('serving_orders', function (Blueprint $table) {
            $table->increments('id');
            $table->tinyInteger('is_served')->default(-1); // -1 for pending, 1 for confirm, 0 for cancel
            $table->unsignedInteger('confirmer_id');
            $table->string('confirmer_type')->default('App\Models\Merchant');       // can be Waiter as well
            $table->unsignedInteger('order_id')->unique();
            // $table->unsignedMediumInteger('restaurant_id');
            // $table->unsignedMediumInteger('waiter_id');
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
        Schema::dropIfExists('serving_orders');
    }
}
