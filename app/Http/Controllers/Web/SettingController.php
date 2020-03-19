<?php

namespace App\Http\Controllers\Web;

use Illuminate\Http\Request;
use App\Models\ApplicationSetting;
use App\Http\Controllers\Controller;

class SettingController extends Controller
{
   	/**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function showAdminSettings()
    {
        return response(ApplicationSetting::first(), 200);
    }

    /**
     * Update Admin Profile.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function updateAdminSettings(Request $request)
    {   
        $request->validate([
            'delivery_charge'=>'required|numeric',
            'multiple_delivery_charge_percentage'=>'required|numeric|max:100',
            'official_mail_address'=>'required|email',
            'official_contact_address'=>'required|string',
            'official_customer_care_number'=>'required|numeric',
            'vat_rate'=>'required|numeric|max:100',
            'official_bank'=>'required|string',
            'official_bank_account_holder_name'=>'required|string',
            'official_bank_account_number'=>'required|string',
            'merchant_number'=>'required|numeric',
            // 'profile_picture'=>'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);
        
        $settings = ApplicationSetting::first();

        $settings->delivery_charge = $request->delivery_charge;
        $settings->multiple_delivery_charge_percentage = $request->multiple_delivery_charge_percentage;
        $settings->official_mail_address = $request->official_mail_address;
        $settings->official_contact_address = $request->official_contact_address;
        $settings->official_customer_care_number = $request->official_customer_care_number;
        $settings->vat_rate = $request->vat_rate;
        $settings->official_bank = $request->official_bank;
        $settings->official_bank_account_holder_name = $request->official_bank_account_holder_name;
        $settings->official_bank_account_number = $request->official_bank_account_number;
        $settings->merchant_number = $request->merchant_number;

        $settings->save();

        return response()->json([
            'success' => "Settings has been updated"
        ], 200);
    }
}
