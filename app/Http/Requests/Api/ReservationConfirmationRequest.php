<?php

namespace App\Http\Requests\Api;

use App\Models\Reservation;
use Illuminate\Validation\Rule;
use App\Models\MerchantProduct;
use App\Models\MerchantProductAddon;
use App\Models\MerchantProductVariation;
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
            'reservation.merchant_id'=>'bail|required|exists:merchants,id',
            'reservation.id' => [
                'bail',
                'required',
                'exists:reservations,id',
                function ($attribute, $value, $fail) {
                    $expectedReservation = Reservation::findOrFail($value);
                    if (/* $expectedReservation->booking_confirmation || */$expectedReservation->order_id != $this->input('reservation.order_id') || $expectedReservation->merchant_id != $this->input('reservation.merchant_id')) {
                        $fail($attribute.' is invalid.');
                    }
                },
            ],

            'payment'=>'required',
            'payment.method'=>'required|in:card,bkash',
            'payment.id'=>'required|string',

            'merchant_products' => 'required|array|min:1',
            'merchant_products.*.id' => [
                'bail',
                'required',
                'exists:merchant_products,id',
                function ($attribute, $value, $fail) {

                    $merchantProduct = MerchantProduct::find($value);
                    
                    if ($merchantProduct->merchantProductCategory->merchant_id !== $this->input('reservation.merchant_id')) {
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
                        $fail($attribute.' is required.');
                    }
                },
                */
            // ],
            // 'merchant_products.*.variation.id' => 'required_unless:merchant_products.*.variation,0|numeric|exists:restaurant_menu_item_variations,variation_id',
            
            'merchant_products.*.addons' => 'present|array',
            'merchant_products.*.addons.*.id' => [
                'required_unless:merchant_products.*.addons.*,', 'numeric', 'exists:merchant_product_addons,id'
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

                if (! $validator->failed()) {
                    
                    $this->payment = json_decode(json_encode($this->input('payment')));
                    $this->merchant_products = json_decode(json_encode($this->input('merchant_products')));
                    $this->reservation = json_decode(json_encode($this->input('reservation')));

                    foreach ($this->merchant_products as $merchantProductKey => $merchantProduct) {

                        $expectedMerchantProduct = MerchantProduct::findOrFail($merchantProduct->id);

                        if ($expectedMerchantProduct->has_variation && empty($merchantProduct->variation)) {
                            
                            $validator->errors()->add("merchant_products.$merchantProductKey", 'Product has variation');

                        }

                        else if ($expectedMerchantProduct->has_variation && ! empty($merchantProduct->variation)) {
                            
                            $expectedMerchantProductVariation = MerchantProductVariation::find($merchantProduct->variation->id);

                            if (empty($expectedMerchantProductVariation) || $expectedMerchantProductVariation->merchant_product_id != $expectedMerchantProduct->id) {
                                
                                $validator->errors()->add("merchant_products.$merchantProductKey.variation", 'Product variation id is invalid');

                            }

                        }

                        if ($expectedMerchantProduct->has_addon && ! empty($merchantProduct->addons)) {

                            foreach ($merchantProduct->addons as $merchantProductAddonKey => $merchantProductAddon) {
                                
                                $expectedMerchantProductAddon = MerchantProductAddon::find($merchantProductAddon->id);

                                if (empty($expectedMerchantProductAddon) || $expectedMerchantProductAddon->merchant_product_id != $expectedMerchantProduct->id) {
                                    
                                    $validator->errors()->add("merchant_products.$merchantProductKey.addons.$merchantProductAddonKey", 'Product has no such addon');

                                }

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

            'reservation.id.required'  => 'Reservation id is required',
            'reservation.id.*'  => 'Reservation id is invalid',

            'payment.method.required'  => 'Payment method is required',
            'payment.method.*'  => 'Payment method is invalid',
            'payment.id.*'  => 'Payment id is required',

            'merchant_products.*.id.required'  => 'Product id is required',
            // 'merchant_products.*.id.exists'  => 'Product id is invalid',
            'merchant_products.*.quantity.required'  => 'Product quantity is required',
            'merchant_products.*.quantity.numeric'  => 'Product quantity is invalid',

            // 'merchant_products.*.variation.required'  => 'Product variation is required',
            // 'merchant_products.*.variation.id.required'  => 'Product variation id is required',
            // 'merchant_products.*.variation.id.*'  => 'Product variation id is invalid',

            'merchant_products.*.addons.present' => 'Product addons is required',
            'merchant_products.*.addons.array' => 'Product addons must be an array',
            'merchant_products.*.addons.*.id.required'  => 'Addon item id is required',
            'merchant_products.*.addons.*.id.*'  => 'Addon item id is invalid',
            'merchant_products.*.addons.*.quantity.required'  => 'Addon quantity is required',
            'merchant_products.*.addons.*.quantity.*'  => 'Addon quantity is invalid',

            'merchant_products.*.customization.*' => 'Product customization should be string',  
        ];
    }
}
