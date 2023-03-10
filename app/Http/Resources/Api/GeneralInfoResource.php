<?php

namespace App\Http\Resources\Api;

use App\Models\Meal;
use App\Models\Cuisine;
use App\Models\ProductCategory;
use App\Models\ThanksGreeting;
use App\Models\WelcomeGreeting;
use App\Models\PromotionalSlider;
use App\Models\ApplicationService;
use App\Models\ApplicationPaymentMethod;
use Illuminate\Http\Resources\Json\JsonResource;

class GeneralInfoResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        // return parent::toArray($request);

        return [
            'app_name' => $this->app_name ?? 'Qupaid',
            
            /*
            'starting_greetings' => [
                [
                    "title" => "Starting Title 1",
                    "preview" => "uploads/application/greeting1.jpg",
                    "paragraph" => "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer posuere erat.",
                ],

                [
                    "title" => "Starting Title 2",
                    "preview" => "uploads/application/greeting2.jpg",
                    "paragraph" => "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer posuere erat.",
                ],

                [
                    "title" => "Starting Title 3",
                    "preview" => "uploads/application/greeting3.jpg",
                    "paragraph" => "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer posuere erat.",
                ],
            ],
            */
            
            'welcome_greetings' => WelcomeGreetingResource::collection(WelcomeGreeting::where('status', true)->get()),


            /*
            "accomplishment_message" => [
                "title" => "Accomplishment Title",
                "preview" => "uploads/application/thanks.jpg",
                "paragraph" => "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer posuere erat.",
            ],
            */
           
            'accomplishment_message' => new OrderAccomplishmentGreetingResource(
                ThanksGreeting::firstOr(function () {
                    return ThanksGreeting::create([
                        'title' => 'Thanksgiving Title',
                        'preview' => 'Thanksgiving preview',
                        'paragraph' => 'Thanksgiving paragraph',
                        // 'status' => true,
                    ]);
                })
            ),


            /*
            "promotional_sliders" => [
                "uploads/application/promotional-1.jpg", 
                "uploads/application/promotional-2.jpg",
                "uploads/application/promotional-3.jpg"
            ],
            */
           
            "promotional_sliders" => PromotionalSlider::where('status', true)->get()->pluck('preview'),

            /*
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
            */
           
            "payment_methods" => PaymentMethodResource::collection(ApplicationPaymentMethod::where('status', true)->get()),

            /*
            "order_types" => [
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
            */
           
            'services' => ServiceResource::collection(ApplicationService::get()),

            "search_preferences" => [
                "cuisines" => $this->when(Cuisine::where('search_preference', true)->count(), AssetResource::collection(Cuisine::where('search_preference', true)->get())),  

                "meals" => $this->when(Meal::where('search_preference', true)->count(), AssetResource::collection(Meal::where('search_preference', true)->get())), 
                   
                "product_categories" => $this->when(ProductCategory::where('search_preference', true)->count(), AssetResource::collection(ProductCategory::where('search_preference', true)->get()))
            ],

            // "preferences" => MerchantProductCategoryResource::collection(ProductCategory::all()),

            // 'delivery_charge' => $this->delivery_charge,
            // 'multiple_delivery_charge_percentage' => $this->multiple_delivery_charge_percentage,
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
