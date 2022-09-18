<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMerchantOwnersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('merchant_owners', function (Blueprint $table) {
            $table->increments('id');
            $table->string('user_name')->unique(); // Auto Generated
            $table->char('mobile')->unique();
            $table->string('email')->unique();
            $table->string('password'); // Login with username / mobile

            $table->rememberToken();
            
            $table->softDeletes();
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
        Schema::dropIfExists('merchant_owners');
    }
}
