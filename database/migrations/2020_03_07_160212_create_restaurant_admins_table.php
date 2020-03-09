<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRestaurantAdminsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('restaurant_admins', function (Blueprint $table) {
            $table->increments('id');
            $table->string('user_name', 50)->unique(); // Auto Generated
            $table->char('mobile', 13)->unique();
            $table->string('email', 50)->nullable()->unique();
            $table->string('password'); // Login with username / mobile
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
        Schema::dropIfExists('restaurant_admins');
    }
}
