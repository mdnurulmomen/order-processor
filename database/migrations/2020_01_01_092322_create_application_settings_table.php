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
            $table->unsignedTinyInteger('delivery_charge')->default(0);
            $table->unsignedTinyInteger('multiple_delivery_charge_percentage')->default(0);
            $table->string('official_mail_address')->default('email@qupaid.com');
            $table->string('official_contact_address')->default('House : XX, Road : XX, Area : XXXX, Dhaka-XXX');
            $table->char('official_customer_care_number', 13)->default('880XXXXXXXXX');
            $table->unsignedTinyInteger('vat_rate')->default(0);
            $table->string('official_bank')->default('XXXXX Bank');
            $table->string('official_bank_account_holder_name')->default('Mr./Mrs. XXXX');
            $table->string('official_bank_account_number')->default('XXXXXXXXXXXX');
            $table->char('merchant_number',13)->default('8801XXXXXXXXX');
            $table->string('logo')->default('logo.png');
            $table->string('favicon')->default('favicon.png');
            $table->unsignedSmallInteger('admin_id')->default(1);
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
