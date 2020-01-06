<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRiderEvaluationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rider_evaluations', function (Blueprint $table) {
            $table->mediumIncrements('id');
            $table->unsignedTinyInteger('order_acceptance_percentage')->default(0);
            $table->unsignedTinyInteger('mean_rating')->default(0);
            $table->unsignedMediumInteger('rider_id')->unique();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('rider_evaluations');
    }
}
