<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateKitchensTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('kitchens', function (Blueprint $table) {
            $table->mediumIncrements('id');
            $table->string('user_name', 30)->unique(); // phone or user_name
            $table->string('mobile', 13)->unique();
            $table->string('email')->nullable()->unique();
            $table->string('password'); // Login with username / mobile
            $table->unsignedMediumInteger('restaurant_id')->unique(); //Each restaurant has one kitchen
            $table->rememberToken();
            $table->boolean('admin_approval')->default(true);
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
        Schema::dropIfExists('kitchens');
    }
}
