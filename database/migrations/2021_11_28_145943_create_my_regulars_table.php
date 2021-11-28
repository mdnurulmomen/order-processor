<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMyRegularsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('my_regulars', function (Blueprint $table) {
            $table->increments('id');
            $table->string('package');
            $table->string('time');
            $table->json('days');
            $table->unsignedInteger('delivery_address_id');
            $table->unsignedInteger('customer_id');
            // $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('my_regulars');
    }
}
