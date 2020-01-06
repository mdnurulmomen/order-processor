<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRidersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('riders', function (Blueprint $table) {
            $table->mediumIncrements('id');
            $table->string('first_name')->nullable();
            $table->string('second_name')->nullable();
            $table->string('user_name')->unique();
            $table->string('email')->nullable()->unique();
            $table->char('mobile', 13)->unique();
            $table->string('password');
            $table->timestamp('birth_date')->nullable();
            $table->string('gender')->default('male');
            $table->string('profile_pic_preview');
            $table->string('present_address')->nullable(); // house, road, area
            $table->string('nid_number')->unique();
            $table->string('nid_front_preview');
            $table->string('nid_back_preview');
            $table->string('payment_method'); // bkash / nexus
            $table->string('payment_account_number'); // bkash / nexus no.
            $table->boolean('available')->default(true);
            $table->string('current_lat')->nullable();
            $table->string('current_lang')->nullable();
            $table->boolean('admin_approval')->default(false);
            $table->string('approver_type')->nullable();
            $table->string('approver_id')->nullable();
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
        Schema::dropIfExists('riders');
    }
}
