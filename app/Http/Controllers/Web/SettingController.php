<?php

namespace App\Http\Controllers\Web;

use Illuminate\Http\Request;
use App\Models\ApplicationSetting;
use App\Http\Controllers\Controller;
use App\Http\Resources\Web\GeneralSettingResource;

class SettingController extends Controller
{
   	/**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function showAllSettings()
    {
        // return response(ApplicationSetting::firstOrCreate(['id' => 1]), 200);
        return new GeneralSettingResource(ApplicationSetting::firstOrCreate(['id' => 1]));
    }

    /**
     * Update Settings.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function updateApplicationSettings(Request $request)
    {   
        $request->validate([
            'app_name'=>'required|string|max:255',
            'searching_radius'=>'required|numeric',
            'multiple_delivery_charge_percentage'=>'required|numeric|max:100',

            'welcome_greetings'=>'required|array|min:1',
            'welcome_greetings.*.title'=>'required|string|max:255',
            'welcome_greetings.*.paragraph'=>'required|string|max:255',
            'welcome_greetings.*.preview'=>'required|string|max:255',

            'thanks_greetings'=>'required|array|min:1',
            'thanks_greetings.*.title'=>'required|string|max:255',
            'thanks_greetings.*.paragraph'=>'required|string|max:255',
            'thanks_greetings.*.preview'=>'required|string|max:255',

            'promotional_sliders'=>'required|array|min:1',
            'promotional_sliders.*.preview'=>'required|string|max:255',
        ]);
        
        $settings = ApplicationSetting::firstOrCreate(['id' => 1]);

        $settings->app_name = $request->app_name;
        $settings->searching_radius = $request->searching_radius;
        $settings->multiple_delivery_charge_percentage = $request->multiple_delivery_charge_percentage;
        $settings->admin_id = \Auth::guard('admin')->user()->id;

        $settings->save();

        // welcome greeting
        $settings->welcome_greetings = json_decode(json_encode($request['welcome_greetings']));

        // thanks greeting
        $settings->thanks_greetings = json_decode(json_encode($request['thanks_greetings']));

        // thanks greeting
        $settings->promotional_sliders = json_decode(json_encode($request['promotional_sliders']));

        return response()->json([
            'success' => "Application Settings has been updated"
        ], 200);
    }

    /**
     * Update Settings.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function updatePaymentSettings(Request $request)
    {   
        $request->validate([
            'vat_rate'=>'required|numeric|max:100',
            'official_bank'=>'required|string',
            'official_bank_account_holder_name'=>'required|string',
            'official_bank_account_number'=>'required|string',
            'merchant_number'=>'required|numeric',
        ]);
        
        $settings = ApplicationSetting::firstOrCreate(['id' => 1]);

        $settings->vat_rate = $request->vat_rate;
        $settings->official_bank = $request->official_bank;
        $settings->official_bank_account_holder_name = $request->official_bank_account_holder_name;
        $settings->official_bank_account_number = $request->official_bank_account_number;
        $settings->merchant_number = $request->merchant_number;
        $settings->admin_id = \Auth::guard('admin')->user()->id;

        $settings->save();

        return response()->json([
            'success' => "Payment Settings has been updated"
        ], 200);
    }

    public function updateContactSettings(Request $request)
    {   
        $request->validate([
            'official_contact_address'=>'required|string',
            'official_mail_address'=>'required|email',
            'official_customer_care_number'=>'required|numeric',
        ]);
        
        $settings = ApplicationSetting::firstOrCreate(['id' => 1]);

        $settings->official_contact_address = $request->official_contact_address;
        $settings->official_mail_address = $request->official_mail_address;
        $settings->official_customer_care_number = $request->official_customer_care_number;
        $settings->admin_id = \Auth::guard('admin')->user()->id;

        $settings->save();

        return response()->json([
            'success' => "Contact Settings has been updated"
        ], 200);
    }

    /*
    public function updateDeliverySettings(Request $request)
    {   
        $request->validate([
            'delivery_charge'=>'required|numeric',
            'multiple_delivery_charge_percentage'=>'required|numeric|max:100',
        ]);
        
        $settings = ApplicationSetting::firstOrCreate(['id' => 1]);

        $settings->delivery_charge = $request->delivery_charge;
        $settings->multiple_delivery_charge_percentage = $request->multiple_delivery_charge_percentage;
        $settings->admin_id = \Auth::guard('admin')->user()->id;

        $settings->save();

        return response()->json([
            'success' => "Delivery Settings has been updated"
        ], 200);
    }
    */

    public function updateOtherSettings(Request $request)
    {   
        $settings = ApplicationSetting::firstOrCreate(['id' => 1]);
        $settings->logo_icon = $request->logo;
        $settings->favicon_icon = $request->favicon;
        $settings->admin_id = \Auth::guard('admin')->user()->id;
        $settings->save();

        return response()->json([
            'success' => "Media Settings has been updated"
        ], 200);
    }
}
