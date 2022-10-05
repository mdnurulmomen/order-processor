<?php

namespace App\Http\Requests\Api;

use App\Models\Merchant;
use Illuminate\Validation\Rule;
use App\Models\MerchantProduct;
use App\Models\MerchantProductAddon;
use App\Models\MerchantProductVariation;
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
            'order.type'=>'required|regex:(reservation)',
            'order.price'=>'required|numeric',
            'order.vat'=>'required|numeric',
            'order.discount'=>'required|numeric',
            'order.net_payable'=>'required|numeric',
            'order.has_cutlery'=>'boolean',
            'order.orderer_type'=>'required|in:customer,waiter',
            'order.orderer_id'=>'required|exists:customers,id',

            'payment'=>'required',
            'payment.method'=>'required|in:cash,card,bkash',
            'payment.id'=>'required_unless:method,cash|string',

            'reservation'=>'required',
            'reservation.guest_number' => 'required|numeric',
            'reservation.arriving_time' => 'required|date|date_format:Y-m-d H:i:s|after:'.now()->subYears(1),
            'reservation.mobile' => 'required|regex:/(01)[0-9]{9}/',
            'reservation.merchant_id' => [
                'required',
                'exists:merchants,id',
                function ($attribute, $value, $fail) {

                    $availableSeats = Merchant::findOrFail($value)->booking->seat_left;

                    if ($availableSeats < $this->input('reservation.guest_number')) {
                        $fail($availableSeats.' seat is available');
                    }
                },
            ],
            // 'reservation.max_payment_time' => 'required|date|date_format:Y-m-d H:i:s|after:'.now()->subYears(1),

            'merchant_products' => 'required|array|min:1',
            'merchant_products.*.id' => [
                'bail',
                'required',
                'exists:merchant_products,id',
                function ($attribute, $value, $fail) {

                    $menuItem = MerchantProduct::findOrFail($value);
                    
                    if ($menuItem->restaurantMenuCategory->merchant_id != $this->input('reservation.merchant_id')) {
                        $fail($attribute.' is invalid.');
                    }
                },
            ],
            'merchant_products.*.quantity' => 'required|numeric',

            // 'merchant_products.*.variation' => 'required',
            // 'merchant_products.*.variation.id' => [
                // 'required_unless:merchant_products.*.variation,0,', 
                // 'numeric', 
                /*
                function ($attribute, $value, $fail) {
                    $variationAvailable = MerchantProduct::where('id', $this->input('merchant_products.*.id'))->has_variation;

                    if ($variationAvailable && empty($value)) {
                        $fail($attribute.' is invalid.');
                    }
                },
                */
            // ],
            // 'merchant_products.*.variation.id' => 'required_unless:merchant_products.*.variation,0|numeric|exists:merchant_product_variations,variation_id',
            
            'merchant_products.*.addons' => 'present|array',
            'merchant_products.*.addons.*.id' => [
                'required_unless:merchant_products.*.addons.*,', 
                'numeric', 'exists:merchant_product_addons,id'
                /*
                Rule::exists('merchant_product_addons', 'addon_id')->where(function ($query) {
                    $query->where('merchant_product_id', $this->input('merchant_products.*.id'));
                }),
                */
            ],
            'merchant_products.*.addons.*.quantity' => 'required_unless:merchant_products.*.addons.*,|numeric',
            // 'merchant_products.*.addons.*.id' => 'required_unless:merchant_products.*.addons.*,|numeric|exists:merchant_product_addons,addon_id',

            'merchant_products.*.customization' => 'nullable|string',

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
                $this->merchant_products = json_decode(json_encode($this->input('merchant_products')));
                $this->reservation = json_decode(json_encode($this->input('reservation')));

                foreach ($this->merchant_products as $merchantProductKey => $merchantProduct) {

                    $expectedMerchantProduct = MerchantProduct::findOrFail($merchantProduct->id);

                    if ($expectedMerchantProduct->has_variation && empty($merchantProduct->variation)) {
                        
                        $validator->errors()->add("merchant_products.$merchantProductKey", 'Merchant product has variation');

                    }

                    else if ($expectedMerchantProduct->has_variation && ! empty($merchantProduct->variation)) {
                        
                        $expectedMerchantProductVariation = MerchantProductVariation::findOrFail($merchantProduct->variation->id);

                        if (empty($expectedMerchantProductVariation) || $expectedMerchantProductVariation->merchant_product_id != $expectedMerchantProduct->id) {
                            
                            $validator->errors()->add("merchant_products.$merchantProductKey.variation", 'Product variation id is invalid');

                        }

                    }

                    if ($expectedMerchantProduct->has_addon && ! empty($merchantProduct->addons)) {

                        foreach ($merchantProduct->addons as $merchantProductAddonKey => $merchantProductAddon) {
                            
                            $expectedMerchantProductAddon = MerchantProductAddon::findOrFail($merchantProductAddon->id);

                            if (empty($expectedMerchantProductAddon) || $expectedMerchantProductAddon->merchant_product_id != $expectedMerchantProduct->id) {
                                
                                $validator->errors()->add("merchant_products.$merchantProductKey.addons.$merchantProductAddonKey", 'Merchant product has no such addon');

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

            'order.type.required' => 'Order type is required',
            'order.type.*' => 'Order type is invalid',
            'order.price.required'  => 'Order price is required',
            'order.vat.required'  => 'Vat amount is required',
            'order.discount.required'  => 'Discount amount is required',
            'order.net_payable.required'  => 'Net payable amount is required',
            'order.has_cutlery.boolean'  => 'Cutlery addion value is invalid',
            // 'order.orderer_type.required'  => 'Orderer type should be customer or waiter',
            'order.orderer_id.required'  => 'Orderer id is required',
            'order.orderer_id.numeric'  => 'Orderer id is invalid',

            // 'payment.method.required'  => 'Payment method is required',
            'payment.id.*'  => 'Payment id is required',

            'reservation.guest_number.*'  => 'Reservation guest number is required',
            'reservation.arriving_time.*'  => 'Guest arriving time is invalid',
            'reservation.mobile.required'  => 'Guest mobile number is required',
            'reservation.mobile.*'  => 'Guest mobile number is invalid',
            'reservation.merchant_id.required'  => 'Merchant id is required',
            'reservation.merchant_id.*'  => 'Merchant id is invalid',

            'merchant_products.*.id.required'  => 'Merchant product id is required',
            // 'merchant_products.*.id.exists'  => 'Merchant product id is invalid',
            'merchant_products.*.quantity.required'  => 'Merchant product quantity is required',
            'merchant_products.*.quantity.numeric'  => 'Merchant product quantity is invalid',

            // 'merchant_products.*.variation.required'  => 'Merchant product variation is required',
            // 'merchant_products.*.variation.id.required'  => 'Product variation id is required',
            // 'merchant_products.*.variation.id.*'  => 'Product variation id is invalid',

            'merchant_products.*.addons.present' => 'Merchant product addons is required',
            'merchant_products.*.addons.array' => 'Merchant product addons must be an array',
            'merchant_products.*.addons.*.id.required'  => 'Addon item id is required',
            'merchant_products.*.addons.*.id.*'  => 'Addon item id is invalid',
            'merchant_products.*.addons.*.quantity.required'  => 'Addon quantity is required',
            'merchant_products.*.addons.*.quantity.*'  => 'Addon quantity is invalid',

            'merchant_products.*.customization.*' => 'Merchant product customization should be string',
        ];
    }

}
