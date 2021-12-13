<?php

namespace App\Http\Resources\Web;

use App\Models\Meal;
use App\Models\Cuisine;
use App\Models\MenuCategory;
use App\Models\ThanksGreeting;
use App\Models\WelcomeGreeting;
use App\Models\PromotionalSlider;
use Illuminate\Http\Resources\Json\JsonResource;

class GeneralSettingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'app_name' => $this->app_name ?? 'Qupaid',
            
            'welcome_greetings' => WelcomeGreetingResource::collection(WelcomeGreeting::where('publish', true)->get()),

            "thanks_greeting" => new ThanksGreetingResource(
                ThanksGreeting::where('publish', true)->firstOrCreate(
                    [ 'publish' => true ],
                    [
                        'id'=>1,
                        'title'=>'Thanksgiving title', 
                        'preview'=>'Thanksgiving preview', 
                        'paragraph' => 'Thanksgiving paragraph',   
                    ]
                )
            ),

            "promotional_sliders" => [
                PromotionalSlider::where('publish', true)->get()->pluck('preview')
            ],

            "payment_methods" => [
                [
                    "name" => "Credit or Debit Card",
                    "logo" => "uploads/application/card.png",
                ],

                [
                    "name" => "BKash",
                    "logo" => "uploads/application/bcash.png",
                ],

                [
                    "name" => "Nagad",
                    "logo" => "uploads/application/nagad.png",
                ],
            ],

            "services" => [
                [
                    "name" => "Home Delivery",
                    "code" => "home-delivery",
                    "logo" => "uploads/application/delivery.png",
                ],

                [
                    "name" => "Pick Up",
                    "code" => "take-away",
                    "logo" => "uploads/application/pick.png",
                ],

                [
                    "name" => "Serve",
                    "code" => "serve-on-table",
                    "logo" => "uploads/application/serve.png",
                ],

                [
                    "name" => "Reservation",
                    "code" => "reservation",
                    "logo" => "uploads/application/reservation.png",
                ]
            ],

            "search_preferences" => [
                "cuisines" => AssetResource::collection(Cuisine::all()),    
                "meals" => AssetResource::collection(Meal::all()),    
                "menus" => AssetResource::collection(MenuCategory::all())
            ],

            // "preferences" => RestaurantMenuCategoryResource::collection(MenuCategory::all()),

            // 'delivery_charge' => $this->delivery_charge,
            'multiple_delivery_charge_percentage' => $this->multiple_delivery_charge_percentage,
            'official_mail_address' => $this->official_mail_address,
            'official_contact_address' => $this->official_contact_address,
            'official_customer_care_number' => $this->official_customer_care_number,
            'vat_rate' => $this->vat_rate,
            'official_bank' => $this->official_bank,
            'official_bank_account_holder_name' => $this->official_bank_account_holder_name,
            'official_bank_account_number' => $this->official_bank_account_number,
            'merchant_number' => $this->merchant_number,
            'logo' => $this->logo,
            'favicon' => $this->favicon,
        ];
    }
}
