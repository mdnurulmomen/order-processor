<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRiderCollectionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rider_collections', function (Blueprint $table) {
            $table->increments('id');
            $table->tinyInteger('is_collected')->default(-1); // -1 for pending, 1 for confirm, 0 for cancel
            $table->timestamp('collected_at')->nullable();
            $table->unsignedInteger('order_id');
            $table->unsignedInteger('merchant_id');
            $table->unsignedSmallInteger('rider_id');
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
        Schema::dropIfExists('rider_collections');
    }
}
