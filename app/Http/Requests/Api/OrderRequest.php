<?php

namespace App\Http\Requests\Api;

use App\Models\Waiter;
use App\Models\Customer;
use Illuminate\Foundation\Http\FormRequest;

class OrderRequest extends FormRequest
{
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
            'order_type'=>'required|in:home-delivery,serve-on-table,take-away,reservation',
            'is_asap_order'=>'required_without:delivery_datetime|boolean',
            'delivery_datetime'=>'required_if:is_asap_order,false|date|date_format:Y-m-d H:i:s|after:'.now()->subMinutes(3),
            'order_price'=>'required|numeric',
            'vat'=>'required|numeric',
            'discount'=>'required|numeric',
            'delivery_fee'=>'required|numeric',
            'net_payable'=>'required|numeric',
            'payment_method'=>'required|in:cash,card,bkash',
            'cutlery_addition'=>'boolean',
            'orderer_type'=>'required|in:customer,waiter',
            
            'orderer_id'=>['required', 'numeric', 
                function($attribute, $value, $fail) {

                    if ($this->input('orderer_type') === 'customer' && !Customer::where('id', $value)->exists()) {
                        return $fail($attribute.' is invalid.');
                    }
                    else if ($this->input('orderer_type') === 'waiter' && !Waiter::where('id', $value)->exists()) {
                        return $fail($attribute.' is invalid.');
                    }
                    
                    /*
                        if (!Customer::where('id', $value)->exists() && !Waiter::where('id', $value)->exists()) { 
                            return $fail($attribute.' is invalid.');
                        }
                    */
                },
            ],

            'delivery_new_address.house' => 'required_with:delivery_new_address|string',
            'delivery_new_address.road' => 'required_with:delivery_new_address|string',
            'delivery_new_address.additional_hint' => 'nullable|string',
            'delivery_new_address.lat' => 'required_with:delivery_new_address|string',
            'delivery_new_address.lang' => 'required_with:delivery_new_address|string',
            'delivery_new_address.address_name' => 'required_with:delivery_new_address|in:work,home,other|string',
            'delivery_address_id' => 'required_without:delivery_new_address|exists:customer_addresses,id',
            'delivery_additional_info' => 'nullable|string',
            
            'payment_id'=>'required_if:payment_method,bkash,card|string',

            'orderItems' => 'required|array|min:1',
            'orderItems.*.restaurant_id' => 'required|exists:restaurants,id',

            'orderItems.*.menuItems' => 'required|array|min:1',
            'orderItems.*.menuItems.*.id' => 'required|exists:restaurant_menu_items,id',
            'orderItems.*.menuItems.*.quantity' => 'required|numeric',

            'orderItems.*.menuItems.*.itemVariations' => 'required',
            'orderItems.*.menuItems.*.itemVariations.id' => 'required_unless:orderItems.*.menuItems.*.itemVariations,0|numeric|exists:restaurant_menu_item_variations,variation_id',
            
            'orderItems.*.menuItems.*.itemAddons' => 'present|array|min:0',
            'orderItems.*.menuItems.*.itemAddons.*.id' => 'required_unless:orderItems.*.menuItems.*.itemAddons.*,|numeric|exists:restaurant_menu_item_addons,addon_id',
            'orderItems.*.menuItems.*.itemAddons.*.quantity' => 'required_unless:orderItems.*.menuItems.*.itemAddons.*,|numeric',

            'orderItems.*.menuItems.*.customization' => 'nullable|string',
            
            // 'selected_item_variations'=>'required|string',
            // 'added_addon_id'=>'required|string',
        ];
    }

    /**
    * Get the error messages for the defined validation rules.
    *
    * @return array
    */
    public function messages()
    {
        return [
            // 'order_type.required' => 'Order type is required',
            'is_asap_order.*'  => 'Order schedule is required',
            'delivery_datetime.*'  => 'The delivery datetime is not a valid',
            'order_price.required'  => 'Order price is required',
            'vat.required'  => 'Vat amount is required',
            'discount.required'  => 'Discount amount is required',
            'delivery_fee.required'  => 'Delivery fee amount is required',
            'net_payable.required'  => 'Net payable amount is required',

            // 'payment_method.required'  => 'Payment method is required',
            'cutlery_addition.boolean'  => 'Cutlery-addion value is invalid',
            // 'orderer_type.required'  => 'Orderer type should be customer or waiter',
            'orderer_id.required'  => 'Orderer id is required',
            'orderer_id.numeric'  => 'Orderer id is invalid',

            'delivery_new_address.house.*' => 'House name should be string',
            'delivery_new_address.road.*' => 'Road address should be string',
            'delivery_new_address.lat.*' => 'Address lat is required',
            'delivery_new_address.lang.*' => 'Address lang is required',
            'delivery_new_address.address_name.required_with' => 'Address name is required',
            'delivery_new_address.address_name.in' => 'Address name is invalid',
            'delivery_address_id.required_without' => 'Address id is required without new address',
            // 'delivery_additional_info' => 'Address additional info should be string',

            'payment_id.*'  => 'Payment id is required',

            'orderItems.*.restaurant_id.required'  => 'Restaurant id is required',
            'orderItems.*.restaurant_id.*'  => 'Restaurant id is invalid',

            'orderItems.*.menuItems.*.id.required'  => 'Menu item id is required',
            // 'orderItems.*.menuItems.*.id.exists'  => 'Menu item id is invalid',
            'orderItems.*.menuItems.*.quantity.required'  => 'Menu-item quantity is required',
            'orderItems.*.menuItems.*.quantity.numeric'  => 'Menu-item quantity is invalid',

            'orderItems.*.menuItems.*.itemVariations.required'  => 'Menu-item-variation is required',
            'orderItems.*.menuItems.*.itemVariations.id.required'  => 'Item variation id is required',
            'orderItems.*.menuItems.*.itemVariations.id.*'  => 'Item variation id is invalid',

            'orderItems.*.menuItems.*.itemAddons.present' => 'Menu-item-addons is required',
            'orderItems.*.menuItems.*.itemAddons.array' => 'Menu-item-addons must be an array',
            'orderItems.*.menuItems.*.itemAddons.*.id.required'  => 'Addon item id is required',
            'orderItems.*.menuItems.*.itemAddons.*.id.*'  => 'Addon item id is invalid',
            'orderItems.*.menuItems.*.itemAddons.*.quantity.required'  => 'Addon quantity is required',
            'orderItems.*.menuItems.*.itemAddons.*.quantity.*'  => 'Addon quantity is invalid',

            'orderItems.*.menuItems.*.customization.*' => 'Menu item customization should be string',
        ];
    }
}
