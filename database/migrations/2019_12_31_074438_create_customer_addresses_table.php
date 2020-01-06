<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomerAddressesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customer_addresses', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('house')->nullable();  //apartment name with floor
            $table->string('road')->nullable();  //road name with no.
            $table->string('additional_hint')->nullable(); // Landmarks
            $table->string('lat'); // from suggested area name / pin pointing map
            $table->string('lang'); // from suggested area name / pin pointing map
            $table->string('address_name');
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
        Schema::dropIfExists('customer_addresses');
    }
}
