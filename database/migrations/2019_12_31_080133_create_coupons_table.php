<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCouponsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('coupons', function (Blueprint $table) {
            $table->smallIncrements('id');
            $table->string('code');
            $table->unsignedTinyInteger('percentage');
            $table->unsignedTinyInteger('min_order');
            $table->unsignedTinyInteger('max_discount_per_order');
            $table->boolean('durability')->default(false);
            $table->date('valid_from')->nullable();
            $table->date('valid_to')->nullable();
            $table->boolean('status')->default(false);
            $table->unsignedTinyInteger('editor_id');
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
        Schema::dropIfExists('coupons');
    }
}
