<?php

namespace App\Http\Requests\Api;

use App\Models\Reservation;
use Illuminate\Validation\Rule;
use App\Models\RestaurantMenuItem;
use App\Models\RestaurantMenuItemAddon;
use App\Models\RestaurantMenuItemVariation;
use Illuminate\Foundation\Http\FormRequest;

class ReservationConfirmationRequest extends FormRequest
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
            'reservation'=>'required',
            'reservation.order_id'=>'bail|required|exists:orders,id',
            'reservation.restaurant_id'=>'bail|required|exists:restaurants,id',
            'reservation.reservation_id' => [
                'bail',
                'required',
                'exists:reservations,id',
                function ($attribute, $value, $fail) {
                    $expectedReservation = Reservation::findOrFail($value);
                    if (/* $expectedReservation->booking_confirmation || */$expectedReservation->order_id != $this->input('reservation.order_id') || $expectedReservation->restaurant_id != $this->input('reservation.restaurant_id')) {
                        $fail($attribute.' is invalid.');
                    }
                },
            ],

            'payment'=>'required',
            'payment.payment_method'=>'required|in:card,bkash',
            'payment.payment_id'=>'required|string',

            'menu_items' => 'required|array|min:1',
            'menu_items.*.id' => [
                'bail',
                'required',
                'exists:restaurant_menu_items,id',
                function ($attribute, $value, $fail) {

                    $menuItem = RestaurantMenuItem::find($value);
                    
                    if ($menuItem->restaurantMenuCategory->restaurant_id !== $this->input('reservation.restaurant_id')) {
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
                        $fail($attribute.' is required.');
                    }
                },
                */
            // ],
            // 'menu_items.*.item_variation.id' => 'required_unless:menu_items.*.item_variation,0|numeric|exists:restaurant_menu_item_variations,variation_id',
            
            'menu_items.*.item_addons' => 'present|array',
            'menu_items.*.item_addons.*.id' => [
                'required_unless:menu_items.*.item_addons.*,', 'numeric', 'exists:restaurant_menu_item_addons,id'
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

                $this->payment = json_decode(json_encode($this->input('payment')));
                $this->menu_items = json_decode(json_encode($this->input('menu_items')));
                $this->reservation = json_decode(json_encode($this->input('reservation')));

                foreach ($this->menu_items as $menuItemKey => $restaurantMenuItem) {

                    $expectedMenuItem = RestaurantMenuItem::find($restaurantMenuItem->id);

                    if ($expectedMenuItem->has_variation && empty($restaurantMenuItem->item_variation)) {
                        
                        $validator->errors()->add("menu_items.$menuItemKey", 'Menu item has variation');

                    }

                    else if ($expectedMenuItem->has_variation && ! empty($restaurantMenuItem->item_variation)) {
                        
                        $expectedMenuItemVariation = RestaurantMenuItemVariation::find($restaurantMenuItem->item_variation->id);

                        if (empty($expectedMenuItemVariation) || $expectedMenuItemVariation->restaurant_menu_item_id != $expectedMenuItem->id) {
                            
                            $validator->errors()->add("menu_items.$menuItemKey.item_variation", 'Item variation id is invalid');

                        }

                    }

                    if ($expectedMenuItem->has_addon && ! empty($restaurantMenuItem->item_addons)) {

                        foreach ($restaurantMenuItem->item_addons as $itemAddonKey => $itemAddon) {
                            
                            $expectedMenuItemAddon = RestaurantMenuItemAddon::find($itemAddon->id);

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
            'reservation.required'  => 'Reservation detail is required',

            'reservation.order_id.required'  => 'Order id is required',
            'reservation.order_id.*'  => 'Order id is invalid',

            'reservation.reservation_id.required'  => 'Reservation id is required',
            'reservation.reservation_id.*'  => 'Reservation id is invalid',

            'payment.payment_method.required'  => 'Payment method is required',
            'payment.payment_method.*'  => 'Payment method is invalid',
            'payment.payment_id.*'  => 'Payment id is required',

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
