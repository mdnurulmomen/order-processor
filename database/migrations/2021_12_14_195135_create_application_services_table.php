<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateApplicationServicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('application_services', function (Blueprint $table) {
            $table->tinyIncrements('id');
            $table->string('name');         // home-delivery (delivery) / take-away (collection) / reservation / serving
            $table->string('code')->unique();
            $table->string('logo')->default('logo.png');
            $table->boolean('status')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('application_services');
    }
}
