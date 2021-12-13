<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateThanksGreetingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('thanks_greetings', function (Blueprint $table) {
            $table->tinyIncrements('id');
            // $table->string('name'); // starting, accomplishment
            $table->string('title');
            $table->string('preview');
            $table->string('paragraph');
            $table->boolean('publish')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('thanks_greetings');
    }
}
