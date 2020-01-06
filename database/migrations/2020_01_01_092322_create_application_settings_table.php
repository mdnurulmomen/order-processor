<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateApplicationSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('application_settings', function (Blueprint $table) {
            $table->tinyIncrements('id');
            $table->unsignedTinyInteger('delivery_fee');
            $table->unsignedTinyInteger('min_percentage_delivery_fee');
            $table->unsignedTinyInteger('vat_percentage');
            $table->string('official_mail_address')->default('qupaid@email.com');
            $table->char('official_bkash_number',13)->default('8801XXXXXXXXX');
            $table->string('official_bank')->default('XXXXX Bank');
            $table->string('official_account_number')->default('XXXXXXXXXXXX');
            $table->unsignedSmallInteger('admin_id');
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
        Schema::dropIfExists('application_settings');
    }
}
