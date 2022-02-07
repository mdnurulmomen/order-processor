<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderCancelationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_cancelations', function (Blueprint $table) {
            $table->mediumIncrements('id');
            $table->unsignedTinyInteger('reason_id');
            $table->unsignedInteger('order_id');
            $table->string('canceller_type');     // App/Models/Admin, App/Models/Merchant App/Models/Rider
            $table->unsignedSmallInteger('canceller_id'); // id
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
        Schema::dropIfExists('order_cancelations');
    }
}
