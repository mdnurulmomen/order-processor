<?php

namespace App\Http\Requests\Api;

use Illuminate\Validation\Rule;
use App\Models\TableBookingDetail;
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
                'exists:table_booking_details,id',
                function ($attribute, $value, $fail) {
                    $expectedReservation = TableBookingDetail::findOrFail($value);
                    if ($expectedReservation->booking_confirmation || $expectedReservation->order_id != $this->input('reservation.order_id') || $expectedReservation->restaurant_id != $this->input('reservation.restaurant_id')) {
                        $fail($attribute.' is invalid.');
                    }
                },
            ],

            'payment'=>'required',
            'payment.payment_method'=>'required|in:card,bkash',
            'payment.payment_id'=>'required|string',

            'menuItems' => 'required|array|min:1',
            'menuItems.*.id' => [
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
            'menuItems.*.quantity' => 'required|numeric',

            // 'menuItems.*.itemVariation' => 'required',
            // 'menuItems.*.itemVariation.id' => [
                // 'required_unless:menuItems.*.itemVariation,0,', 
                // 'numeric', 
                /*
                function ($attribute, $value, $fail) {
                    $variationAvailable = RestaurantMenuItem::where('id', $this->input('menuItems.*.id'))->has_variation;

                    if ($variationAvailable && empty($value)) {
                        $fail($attribute.' is required.');
                    }
                },
                */
            // ],
            // 'menuItems.*.itemVariation.id' => 'required_unless:menuItems.*.itemVariation,0|numeric|exists:restaurant_menu_item_variations,variation_id',
            
            'menuItems.*.itemAddons' => 'present|array',
            'menuItems.*.itemAddons.*.id' => [
                'required_unless:menuItems.*.itemAddons.*,', 'numeric', 'exists:restaurant_menu_item_addons,id'
                /*
                Rule::exists('restaurant_menu_item_addons', 'addon_id')->where(function ($query) {
                    $query->where('restaurant_menu_item_id', $this->input('menuItems.*.id'));
                }),
                */
            ],
            'menuItems.*.itemAddons.*.quantity' => 'required_unless:menuItems.*.itemAddons.*,|numeric',
            // 'menuItems.*.itemAddons.*.id' => 'required_unless:menuItems.*.itemAddons.*,|numeric|exists:restaurant_menu_item_addons,addon_id',

            'menuItems.*.customization' => 'nullable|string',
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
                $this->menuItems = json_decode(json_encode($this->input('menuItems')));
                $this->reservation = json_decode(json_encode($this->input('reservation')));

                foreach ($this->menuItems as $menuItemKey => $restaurantMenuItem) {

                    $expectedMenuItem = RestaurantMenuItem::find($restaurantMenuItem->id);

                    if ($expectedMenuItem->has_variation && empty($restaurantMenuItem->itemVariation)) {
                        
                        $validator->errors()->add("menuItems.$menuItemKey", 'Menu item has variation');

                    }

                    else if ($expectedMenuItem->has_variation && ! empty($restaurantMenuItem->itemVariation)) {
                        
                        $expectedMenuItemVariation = RestaurantMenuItemVariation::find($restaurantMenuItem->itemVariation->id);

                        if (empty($expectedMenuItemVariation) || $expectedMenuItemVariation->restaurant_menu_item_id != $expectedMenuItem->id) {
                            
                            $validator->errors()->add("menuItems.$menuItemKey.itemVariation", 'Item variation id is invalid');

                        }

                    }

                    if ($expectedMenuItem->has_addon && ! empty($restaurantMenuItem->itemAddons)) {

                        foreach ($restaurantMenuItem->itemAddons as $itemAddonKey => $itemAddon) {
                            
                            $expectedMenuItemAddon = RestaurantMenuItemAddon::find($itemAddon->id);

                            if (empty($expectedMenuItemAddon) || $expectedMenuItemAddon->restaurant_menu_item_id != $expectedMenuItem->id) {
                                
                                $validator->errors()->add("menuItems.$menuItemKey.itemAddons.$itemAddonKey", 'Menu item has no such addon');

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

            'menuItems.*.id.required'  => 'Menu item id is required',
            // 'menuItems.*.id.exists'  => 'Menu item id is invalid',
            'menuItems.*.quantity.required'  => 'Menu item quantity is required',
            'menuItems.*.quantity.numeric'  => 'Menu item quantity is invalid',

            // 'menuItems.*.itemVariation.required'  => 'Menu item variation is required',
            // 'menuItems.*.itemVariation.id.required'  => 'Item variation id is required',
            // 'menuItems.*.itemVariation.id.*'  => 'Item variation id is invalid',

            'menuItems.*.itemAddons.present' => 'Menu item addons is required',
            'menuItems.*.itemAddons.array' => 'Menu item addons must be an array',
            'menuItems.*.itemAddons.*.id.required'  => 'Addon item id is required',
            'menuItems.*.itemAddons.*.id.*'  => 'Addon item id is invalid',
            'menuItems.*.itemAddons.*.quantity.required'  => 'Addon quantity is required',
            'menuItems.*.itemAddons.*.quantity.*'  => 'Addon quantity is invalid',

            'menuItems.*.customization.*' => 'Menu item customization should be string',  
        ];
    }
}
