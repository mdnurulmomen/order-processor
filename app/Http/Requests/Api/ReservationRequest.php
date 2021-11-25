<?php

namespace App\Http\Requests\Api;

use App\Models\Restaurant;
use Illuminate\Validation\Rule;
use App\Models\RestaurantMenuItem;
use App\Models\RestaurantMenuItemAddon;
use App\Models\RestaurantMenuItemVariation;
use Illuminate\Foundation\Http\FormRequest;

class ReservationRequest extends FormRequest
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
            
            'order'=>'required',
            'order.order_type'=>'required|regex:(reservation)',
            'order.order_price'=>'required|numeric',
            'order.vat'=>'required|numeric',
            'order.discount'=>'required|numeric',
            'order.net_payable'=>'required|numeric',
            'order.cutlery_addition'=>'boolean',
            'order.orderer_type'=>'required|in:customer,waiter',
            'order.orderer_id'=>'required|exists:customers,id',

            'payment'=>'required',
            'payment.payment_method'=>'required|in:cash,card,bkash',
            'payment.payment_id'=>'required_unless:payment_method,cash|string',

            'reservation'=>'required',
            'reservation.guest_number' => 'required|numeric',
            'reservation.arriving_time' => 'required|date|date_format:Y-m-d H:i:s|after:'.now()->subYears(1),
            'reservation.mobile' => 'required|regex:/(01)[0-9]{9}/',
            'reservation.restaurant_id' => [
                'required',
                'exists:restaurants,id',
                function ($attribute, $value, $fail) {

                    $availableSeats = Restaurant::findOrFail($value)->booking->seat_left;

                    if ($availableSeats < $this->input('reservation.guest_number')) {
                        $fail($availableSeats.' seat is available');
                    }
                },
            ],
            // 'reservation.max_payment_time' => 'required|date|date_format:Y-m-d H:i:s|after:'.now()->subYears(1),

            'menu_items' => 'required|array|min:1',
            'menu_items.*.id' => [
                'bail',
                'required',
                'exists:restaurant_menu_items,id',
                function ($attribute, $value, $fail) {

                    $menuItem = RestaurantMenuItem::findOrFail($value);
                    
                    if ($menuItem->restaurantMenuCategory->restaurant_id != $this->input('reservation.restaurant_id')) {
                        $fail($attribute.' is invalid.');
                    }
                },
            ],
            'menu_items.*.quantity' => 'required|numeric',

            // 'menu_items.*.item_variation' => 'required',
            // 'menu_items.*.item_variation.id' => [
                // 'required_unless:menu_items.*.item_variation,0,', 
                // 'numeric', 
                /*
                function ($attribute, $value, $fail) {
                    $variationAvailable = RestaurantMenuItem::where('id', $this->input('menu_items.*.id'))->has_variation;

                    if ($variationAvailable && empty($value)) {
                        $fail($attribute.' is invalid.');
                    }
                },
                */
            // ],
            // 'menu_items.*.item_variation.id' => 'required_unless:menu_items.*.item_variation,0|numeric|exists:restaurant_menu_item_variations,variation_id',
            
            'menu_items.*.item_addons' => 'present|array',
            'menu_items.*.item_addons.*.id' => [
                'required_unless:menu_items.*.item_addons.*,', 
                'numeric', 'exists:restaurant_menu_item_addons,id'
                /*
                Rule::exists('restaurant_menu_item_addons', 'addon_id')->where(function ($query) {
                    $query->where('restaurant_menu_item_id', $this->input('menu_items.*.id'));
                }),
                */
            ],
            'menu_items.*.item_addons.*.quantity' => 'required_unless:menu_items.*.item_addons.*,|numeric',
            // 'menu_items.*.item_addons.*.id' => 'required_unless:menu_items.*.item_addons.*,|numeric|exists:restaurant_menu_item_addons,addon_id',

            'menu_items.*.customization' => 'nullable|string',

        ];
    }

    /**
     * Configure the validator instance.
     *
     * @param  \Illuminate\Validation\Validator  $validator
     * @return void
     */
    public function withValidator($validator)
    {
        $validator->after(
            function ($validator) {

                $this->order = json_decode(json_encode($this->input('order')));
                $this->payment = json_decode(json_encode($this->input('payment')));
                $this->menu_items = json_decode(json_encode($this->input('menu_items')));
                $this->reservation = json_decode(json_encode($this->input('reservation')));

                foreach ($this->menu_items as $menuItemKey => $restaurantMenuItem) {

                    $expectedMenuItem = RestaurantMenuItem::findOrFail($restaurantMenuItem->id);

                    if ($expectedMenuItem->has_variation && empty($restaurantMenuItem->item_variation)) {
                        
                        $validator->errors()->add("menu_items.$menuItemKey", 'Menu item has variation');

                    }

                    else if ($expectedMenuItem->has_variation && ! empty($restaurantMenuItem->item_variation)) {
                        
                        $expectedMenuItemVariation = RestaurantMenuItemVariation::findOrFail($restaurantMenuItem->item_variation->id);

                        if (empty($expectedMenuItemVariation) || $expectedMenuItemVariation->restaurant_menu_item_id != $expectedMenuItem->id) {
                            
                            $validator->errors()->add("menu_items.$menuItemKey.item_variation", 'Item variation id is invalid');

                        }

                    }

                    if ($expectedMenuItem->has_addon && ! empty($restaurantMenuItem->item_addons)) {

                        foreach ($restaurantMenuItem->item_addons as $itemAddonKey => $itemAddon) {
                            
                            $expectedMenuItemAddon = RestaurantMenuItemAddon::findOrFail($itemAddon->id);

                            if (empty($expectedMenuItemAddon) || $expectedMenuItemAddon->restaurant_menu_item_id != $expectedMenuItem->id) {
                                
                                $validator->errors()->add("menu_items.$menuItemKey.item_addons.$itemAddonKey", 'Menu item has no such addon');

                            }

                        }

                    }

                }
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

            'order.order_type.required' => 'Order type is required',
            'order.order_type.*' => 'Order type is invalid',
            'order.order_price.required'  => 'Order price is required',
            'order.vat.required'  => 'Vat amount is required',
            'order.discount.required'  => 'Discount amount is required',
            'order.net_payable.required'  => 'Net payable amount is required',
            'order.cutlery_addition.boolean'  => 'Cutlery addion value is invalid',
            // 'order.orderer_type.required'  => 'Orderer type should be customer or waiter',
            'order.orderer_id.required'  => 'Orderer id is required',
            'order.orderer_id.numeric'  => 'Orderer id is invalid',

            // 'payment.payment_method.required'  => 'Payment method is required',
            'payment.payment_id.*'  => 'Payment id is required',

            'reservation.guest_number.*'  => 'Reservation guest number is required',
            'reservation.arriving_time.*'  => 'Guest arriving time is invalid',
            'reservation.mobile.required'  => 'Guest mobile number is required',
            'reservation.mobile.*'  => 'Guest mobile number is invalid',
            'reservation.restaurant_id.required'  => 'Restaurant id is required',
            'reservation.restaurant_id.*'  => 'Restaurant id is invalid',

            'menu_items.*.id.required'  => 'Menu item id is required',
            // 'menu_items.*.id.exists'  => 'Menu item id is invalid',
            'menu_items.*.quantity.required'  => 'Menu item quantity is required',
            'menu_items.*.quantity.numeric'  => 'Menu item quantity is invalid',

            // 'menu_items.*.item_variation.required'  => 'Menu item variation is required',
            // 'menu_items.*.item_variation.id.required'  => 'Item variation id is required',
            // 'menu_items.*.item_variation.id.*'  => 'Item variation id is invalid',

            'menu_items.*.item_addons.present' => 'Menu item addons is required',
            'menu_items.*.item_addons.array' => 'Menu item addons must be an array',
            'menu_items.*.item_addons.*.id.required'  => 'Addon item id is required',
            'menu_items.*.item_addons.*.id.*'  => 'Addon item id is invalid',
            'menu_items.*.item_addons.*.quantity.required'  => 'Addon quantity is required',
            'menu_items.*.item_addons.*.quantity.*'  => 'Addon quantity is invalid',

            'menu_items.*.customization.*' => 'Menu item customization should be string',
        ];
    }

}
