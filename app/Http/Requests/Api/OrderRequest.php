<?php

namespace App\Http\Requests\Api;

use App\Models\Waiter;
use App\Models\Customer;
use Illuminate\Validation\Rule;
use App\Models\RestaurantMenuItem;
use Illuminate\Support\Facades\Log;
use App\Models\RestaurantMenuItemAddon;
use App\Models\RestaurantMenuItemVariation;
use Illuminate\Foundation\Http\FormRequest;

class OrderRequest extends FormRequest
{
    /**
    * Indicates if the validator should stop on the first rule failure.
    *
    * @var bool
    */
    protected $stopOnFirstFailure = true;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [

            'order'=>'required',
            'order.order_type'=>'required|in:home-delivery,serve-on-table,take-away',
            'order.is_asap_order'=>'required_without:order.order_schedule|boolean',
            'order.order_schedule'=>'required_if:order.is_asap_order,0|date|date_format:Y-m-d H:i:s|after:'.now()->subMinutes(3),
            'order.order_price'=>'required|numeric',
            'order.vat'=>'required|numeric',
            'order.discount'=>'required|numeric',
            'order.delivery_fee'=>'required|numeric',
            'order.net_payable'=>'required|numeric',
            'order.cutlery_addition'=>'boolean',
            'order.orderer_type'=>'required|in:customer,waiter',
            'order.orderer_id'=>[
                'required', 'numeric', 
                function($attribute, $value, $fail) {

                    if ($this->input('order.orderer_type') === 'customer' && ! Customer::where('id', $value)->exists()) {
                        return $fail($attribute.' is invalid.');
                    }
                    else if ($this->input('order.orderer_type') === 'waiter' && ! Waiter::where('id', $value)->exists()) {
                        return $fail($attribute.' is invalid.');
                    }
                    else if ($this->input('order.orderer_type') === 'waiter' && $this->input('order.order_type') === 'home-delivery') {
                        return $fail('Delivery order is invalid.');
                    }
                    
                    /*
                        if (!Customer::where('id', $value)->exists() && !Waiter::where('id', $value)->exists()) { 
                            return $fail($attribute.' is invalid.');
                        }
                    */
                   
                },
            ],

            'order.delivery_new_address' => [
                Rule::requiredIf(function () {

                    return (($this->input('order.order_type')=='home-delivery') && ($this->missing('order.delivery_address_id')));

                }),
            ],
            'order.delivery_new_address.house' => 'required_unless:order.delivery_new_address,|string',
            'order.delivery_new_address.road' => 'required_unless:order.delivery_new_address,|string',
            'order.delivery_new_address.additional_hint' => 'nullable|string',
            'order.delivery_new_address.lat' => 'required_unless:order.delivery_new_address,|string',
            'order.delivery_new_address.lang' => 'required_unless:order.delivery_new_address,|string',
            'order.delivery_new_address.address_name' => 'required_unless:order.delivery_new_address,|in:work,home,other|string',

            'order.delivery_address_id' => [
                'exists:customer_addresses,id',
                Rule::requiredIf(function () {
                    
                    return (($this->input('order.order_type')=='home-delivery') && ($this->missing('order.delivery_new_address')));

                }),
            ],


            'order.delivery_additional_info' => 'nullable|string',
            
            'payment'=>'required',
            'payment.payment_method'=>'required|in:cash,card,bkash',
            'payment.payment_id'=>'required_if:payment.payment_method,bkash,card|string',

            // 'restaurants' => 'required|array|min:1',
            'restaurants' => [
                'required', 'array', 'min:1', 'between:1,3', 
                function ($attribute, $value, $fail) {
                    if (count($this->input('restaurants.*.restaurant_id')) > 1 && $this->input('order.order_type')==='serve-on-table') {
                        $fail('Multiple restaurant aint allowed for serve order.');
                    }
                },
            ],
            'restaurants.*.restaurant_id' => 'required|exists:restaurants,id',

            'restaurants.*.menuItems' => 'required|array|min:1',
            'restaurants.*.menuItems.*.id' => [
                'bail', 'required', 'exists:restaurant_menu_items,id', 
        /*
                function ($attribute, $value, $fail) {
                    
                    $menuRestaurantId = RestaurantMenuItem::find($value)->restaurantMenuCategory->restaurant_id;
                    $givenRestaurantIds = $this->input('restaurants.*.restaurant_id');

                    // Log::info($this->input('restaurants.*.restaurant_id'));
                    // Log::warning($this->input('restaurants.*.menuItems.*.id'));

                    if (!in_array($menuRestaurantId, $givenRestaurantIds)) {
                        $fail($attribute.' is invalid.');
                    }
                },
        */
            ],
            'restaurants.*.menuItems.*.quantity' => 'required|numeric',

            // 'restaurants.*.menuItems.*.itemVariation' => 'required',
            // 'restaurants.*.menuItems.*.itemVariation.id' => [
                // 'required_unless:restaurants.*.menuItems.*.itemVariation,0,', 'numeric', 
        /*
                function ($attribute, $value, $fail) {

                    // Log::info($this->input('restaurants.*.menuItems.*.id'));

                    $variationAvailable = RestaurantMenuItem::where('id', $this->input('restaurants.*.menuItems.*.id'))->first()->has_variation;

                    if ($variationAvailable && empty($value)) {
                        $fail($attribute.' is required.');
                    }
                },
        */
        /*
                Rule::exists('restaurant_menu_item_variations', 'id')->where(function ($query) {
                    
                    Log::info($this->input('restaurants.*.menuItems.*.id'));

                    $query->where('restaurant_menu_item_id', $this->input('restaurants.*.menuItems.*.id'));
                }),
        */
            // ],

            'restaurants.*.menuItems.*.itemAddons' => 'present|array',
            'restaurants.*.menuItems.*.itemAddons.*.id' => [
                'required_unless:restaurants.*.menuItems.*.itemAddons.*,', 
                'numeric', 
                'exists:restaurant_menu_item_addons,id'
                /*
                    Rule::exists('restaurant_menu_item_addons', 'id')->where(function ($query) {
                        $query->where('restaurant_menu_item_id', $this->input('restaurants.*.menuItems.*.id'));
                    }),
                */
            ],
            'restaurants.*.menuItems.*.itemAddons.*.quantity' => 'required_unless:restaurants.*.menuItems.*.itemAddons.*,|
                numeric',

            'restaurants.*.menuItems.*.customization' => 'nullable|string',
            
            // 'selected_item_variations'=>'required|string',
            // 'added_addon_id'=>'required|string',
            
        ];
    }

    /**
     * Adding After Hooks To Form Requests
     * Configure the validator instance.
     *
     * @param  \Illuminate\Validation\Validator  $validator
     * @return void
     */
    public function withValidator($validator)
    {
        if ($validator->fails()) return;

        $validator->after(
            function ($validator) {

                $this->order = json_decode(json_encode($this->input('order')));
                $this->payment = json_decode(json_encode($this->input('payment')));
                $this->restaurants = json_decode(json_encode($this->input('restaurants')));

                foreach ($this->restaurants as $orderedRestaurantKey => $orderedRestaurant) {

                    foreach ($orderedRestaurant->menuItems as $menuItemKey => $restaurantMenuItem) {

                        $expectedMenuItem = RestaurantMenuItem::findOrFail($restaurantMenuItem->id);

                        /*
                        // as already has been checked previously
                        if (empty($expectedMenuItem)) {
                            
                            $validator->errors()->add("restaurants.$orderedRestaurantKey.menuItems.$menuItemKey", 'Menu item id is invalid');

                        }
                        */

                        if ($expectedMenuItem->restaurantMenuCategory->restaurant_id == $orderedRestaurant->restaurant_id) {
                            
                            if ($expectedMenuItem->has_variation && empty($restaurantMenuItem->itemVariation)) {
                                
                                $validator->errors()->add("restaurants.$orderedRestaurantKey.menuItems.$menuItemKey", 'Menu item has variation');

                            }

                            else if ($expectedMenuItem->has_variation && ! empty($restaurantMenuItem->itemVariation)) {
                                
                                $expectedMenuItemVariation = RestaurantMenuItemVariation::findOrFail($restaurantMenuItem->itemVariation->id);

                                if (empty($expectedMenuItemVariation) || $expectedMenuItemVariation->restaurant_menu_item_id != $expectedMenuItem->id) {
                                    
                                    $validator->errors()->add("restaurants.$orderedRestaurantKey.menuItems.$menuItemKey.itemVariation", 'Item variation id is invalid');

                                }

                            }

                            if ($expectedMenuItem->has_addon && ! empty($restaurantMenuItem->itemAddons)) {

                                foreach ($restaurantMenuItem->itemAddons as $itemAddonKey => $itemAddon) {
                                    
                                    $expectedMenuItemAddon = RestaurantMenuItemAddon::findOrFail($itemAddon->id);

                                    if (empty($expectedMenuItemAddon) || $expectedMenuItemAddon->restaurant_menu_item_id != $expectedMenuItem->id) {
                                        
                                        $validator->errors()->add("restaurants.$orderedRestaurantKey.menuItems.$menuItemKey.itemAddons.$itemAddonKey", 'Menu item has no such addon');

                                    }

                                }

                            }

                        }

                        else {
                            $validator->errors()->add("restaurants.$orderedRestaurantKey.menuItems.$menuItemKey", "Menu item id doesn't belong to restaurant");
                        }

                    }

                }

            /*
                if ($this->somethingElseIsInvalid()) {
                    $validator->errors()->add('field', 'Something is wrong with this field!');
                }

            */

            }
        );
    }

    /**
    * Get the error messages for the defined validation rules.
    *
    * @return array
    */
    public function messages()
    {
        return [
            // 'order.order_type.required' => 'Order type is required',
            'order.is_asap_order.*'  => 'Order schedule is required',
            'order.order_schedule.*'  => 'Delivery datetime is not a valid',
            'order.order_price.required'  => 'Order price is required',
            'order.vat.required'  => 'Vat amount is required',
            'order.discount.required'  => 'Discount amount is required',
            'order.delivery_fee.required'  => 'Delivery fee amount is required',
            'order.net_payable.required'  => 'Net payable amount is required',
            'order.cutlery_addition.boolean'  => 'Cutlery addion value is invalid',
            // 'order.orderer_type.required'  => 'Orderer type should be customer or waiter',
            'order.orderer_id.required'  => 'Orderer id is required',
            'order.orderer_id.numeric'  => 'Orderer id is invalid',

            'order.delivery_new_address.house.*' => 'House name should be string',
            'order.delivery_new_address.road.*' => 'Road address should be string',
            'order.delivery_new_address.lat.*' => 'Address lat is required',
            'order.delivery_new_address.lang.*' => 'Address lang is required',
            'order.delivery_new_address.address_name.required_with' => 'Address name is required',
            'order.delivery_new_address.address_name.in' => 'Address name is invalid',
            'order.delivery_address_id.required' => 'Address id is required without new address',
            'order.delivery_address_id.*' => 'Address id is invalid',
            // 'delivery_additional_info' => 'Address additional info should be string',

            'payment.payment_id.*'  => 'Payment id is required',
            // 'payment.payment_method.required'  => 'Payment method is required',

            'restaurants.*.restaurant_id.required'  => 'Restaurant id is required',
            'restaurants.*.restaurant_id.*'  => 'Restaurant id is invalid',

            'restaurants.*.menuItems.*.id.required'  => 'Menu item id is required',
            // 'restaurants.*.menuItems.*.id.exists'  => 'Menu item id is invalid',
            'restaurants.*.menuItems.*.quantity.required'  => 'Menu item quantity is required',
            'restaurants.*.menuItems.*.quantity.numeric'  => 'Menu item quantity is invalid',

            // 'restaurants.*.menuItems.*.itemVariation.required'  => 'Menu item variation is required',
            // 'restaurants.*.menuItems.*.itemVariation.id.required'  => 'Item variation id is required',
            // 'restaurants.*.menuItems.*.itemVariation.id.*'  => 'Item variation id is invalid',

            'restaurants.*.menuItems.*.itemAddons.present' => 'Menu item addons is required',
            'restaurants.*.menuItems.*.itemAddons.array' => 'Menu item addons must be an array',
            'restaurants.*.menuItems.*.itemAddons.*.id.required'  => 'Addon item id is required',
            'restaurants.*.menuItems.*.itemAddons.*.id.*'  => 'Addon item id is invalid',
            'restaurants.*.menuItems.*.itemAddons.*.quantity.required'  => 'Addon quantity is required',
            'restaurants.*.menuItems.*.itemAddons.*.quantity.*'  => 'Addon quantity is invalid',

            'restaurants.*.menuItems.*.customization.*' => 'Menu item customization should be string',
        ];
    }
}
