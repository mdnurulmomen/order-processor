<?php

namespace App\Http\Requests\Api;

use App\Models\Customer;
use Illuminate\Validation\Rule;
use App\Models\RestaurantMenuItem;
use Illuminate\Support\Facades\Log;
use App\Models\RestaurantMenuItemAddon;
use App\Models\RestaurantMenuItemVariation;
use Illuminate\Foundation\Http\FormRequest;

class MyRegularRequest extends FormRequest
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
            'customer_id' => 'required|exists:customers,id',
            'package' => 'required|string|max:255',
            'time' => 'required|date_format:G:i',
            'days'=>'required|array|max:7',
            'days.*'=>'required|numeric|min:0|max:6',
            'delivery_address_id' => [
                'required',
                Rule::exists('customer_addresses', 'id')->where(function ($query) {
                    return $query->where('customer_id', $this->input('customer_id'));
                })
            ],

            'restaurants' => 'required|array|min:1',
            'restaurants' => [
                'required', 'array', 'min:1', 'between:1,3', 
                function ($attribute, $value, $fail) {
                    if (count($this->input('restaurants.*.restaurant_id')) > 1 && $this->input('order.order_type')==='serve-on-table') {
                        $fail('Multiple restaurant aint allowed for serve order.');
                    }
                },
            ],
            'restaurants.*.restaurant_id' => 'required|exists:restaurants,id',

            'restaurants.*.items' => 'required|array|min:1',
            'restaurants.*.items.*.id' => [
                'bail', 'required', 'exists:restaurant_menu_items,id', 
            ],
            'restaurants.*.items.*.quantity' => 'required|numeric',

            'restaurants.*.items.*.addons' => 'present|array',
            'restaurants.*.items.*.addons.*.id' => [
                'required_unless:restaurants.*.items.*.addons.*,', 
                'numeric', 
                'exists:restaurant_menu_item_addons,id'
            ],
            'restaurants.*.items.*.addons.*.quantity' => 'required_unless:restaurants.*.items.*.addons.*,|
                numeric',

            // 'restaurants.*.items.*.customization' => 'nullable|string',
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

                $this->restaurants = json_decode(json_encode($this->input('restaurants')));

                foreach ($this->restaurants as $orderedRestaurantKey => $orderedRestaurant) {

                    foreach ($orderedRestaurant->items as $menuItemKey => $restaurantMenuItem) {

                        $expectedMenuItem = RestaurantMenuItem::findOrFail($restaurantMenuItem->id);

                        if ($expectedMenuItem->restaurantMenuCategory->restaurant_id == $orderedRestaurant->restaurant_id) {
                            
                            if ($expectedMenuItem->has_variation && empty($restaurantMenuItem->variation)) {
                                
                                $validator->errors()->add("restaurants.$orderedRestaurantKey.items.$menuItemKey", 'Menu item has variation');

                            }

                            else if ($expectedMenuItem->has_variation && ! empty($restaurantMenuItem->variation)) {
                                
                                $expectedMenuItemVariation = RestaurantMenuItemVariation::findOrFail($restaurantMenuItem->variation->id);

                                if (empty($expectedMenuItemVariation) || $expectedMenuItemVariation->restaurant_menu_item_id != $expectedMenuItem->id) {
                                    
                                    $validator->errors()->add("restaurants.$orderedRestaurantKey.items.$menuItemKey.variation", 'Item variation id is invalid');

                                }

                            }

                            if ($expectedMenuItem->has_addon && ! empty($restaurantMenuItem->addons)) {

                                foreach ($restaurantMenuItem->addons as $itemAddonKey => $itemAddon) {
                                    
                                    $expectedMenuItemAddon = RestaurantMenuItemAddon::findOrFail($itemAddon->id);

                                    if (empty($expectedMenuItemAddon) || $expectedMenuItemAddon->restaurant_menu_item_id != $expectedMenuItem->id) {
                                        
                                        $validator->errors()->add("restaurants.$orderedRestaurantKey.items.$menuItemKey.addons.$itemAddonKey", 'Menu item has no such addon');

                                    }

                                }

                            }

                        }

                        else {
                            $validator->errors()->add("restaurants.$orderedRestaurantKey.items.$menuItemKey", "Menu item id doesn't belong to restaurant");
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

            'customer_id.required' => 'Custoner id is required',
            'customer_id.*' => 'Customer invalid id',
            'package.required' => 'Package name is required',
            'package.*' => 'Invalid package name',
            'time.required' => 'Time is required',
            'time.*' => 'Invalid time format',
            'days.required'=>'Week days are required',
            'days.*'=>'Week invalid days',
            'days.*.required'=>'Week days are required',
            'days.*.*'=>'Invalid week days',
            'order.delivery_address_id.required' => 'Customer address id is required',
            'order.delivery_address_id.*' => 'Address id is invalid',

            'restaurants.*.restaurant_id.required'  => 'Restaurant id is required',
            'restaurants.*.restaurant_id.*'  => 'Restaurant id is invalid',

            'restaurants.*.items.*.id.required'  => 'Menu item id is required',
            // 'restaurants.*.items.*.id.exists'  => 'Menu item id is invalid',
            'restaurants.*.items.*.quantity.required'  => 'Menu item quantity is required',
            'restaurants.*.items.*.quantity.numeric'  => 'Menu item quantity is invalid',

            // 'restaurants.*.items.*.variation.required'  => 'Menu item variation is required',
            // 'restaurants.*.items.*.variation.id.required'  => 'Item variation id is required',
            // 'restaurants.*.items.*.variation.id.*'  => 'Item variation id is invalid',

            'restaurants.*.items.*.addons.present' => 'Menu item addons is required',
            'restaurants.*.items.*.addons.array' => 'Menu item addons must be an array',
            'restaurants.*.items.*.addons.*.id.required'  => 'Addon item id is required',
            'restaurants.*.items.*.addons.*.id.*'  => 'Addon item id is invalid',
            'restaurants.*.items.*.addons.*.quantity.required'  => 'Addon quantity is required',
            'restaurants.*.items.*.addons.*.quantity.*'  => 'Addon quantity is invalid',

            'restaurants.*.items.*.customization.*' => 'Menu item customization should be string',
        ];
    }

}
