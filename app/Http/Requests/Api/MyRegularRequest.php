<?php

namespace App\Http\Requests\Api;

use App\Models\Customer;
use Illuminate\Validation\Rule;
use App\Models\MerchantProduct;
use Illuminate\Support\Facades\Log;
use App\Models\MerchantProductAddon;
use App\Models\MerchantProductVariation;
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

            'merchants' => 'required|array|min:1',
            'merchants' => [
                'required', 'array', 'min:1', 'between:1,3', 
                function ($attribute, $value, $fail) {
                    if (count($this->input('merchants.*.id')) > 1 && $this->input('order.type')==='serve-on-table') {
                        $fail('Multiple restaurant aint allowed for serve order.');
                    }
                },
            ],
            'merchants.*.id' => 'required|exists:merchants,id',

            'merchants.*.products' => 'required|array|min:1',
            'merchants.*.products.*.id' => [
                'bail', 'required', 'exists:merchant_products,id', 
            ],
            'merchants.*.products.*.quantity' => 'required|numeric',

            'merchants.*.products.*.addons' => 'present|array',
            'merchants.*.products.*.addons.*.id' => [
                'required_unless:merchants.*.products.*.addons.*,', 
                'numeric', 
                'exists:merchant_product_addons,id'
            ],
            'merchants.*.products.*.addons.*.quantity' => 'required_unless:merchants.*.products.*.addons.*,|
                numeric',

            // 'merchants.*.products.*.customization' => 'nullable|string',
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

                $this->merchants = json_decode(json_encode($this->input('merchants')));

                foreach ($this->merchants as $merchantOrderKey => $merchantOrder) {

                    foreach ($merchantOrder->products as $merchantProductKey => $merchantProduct) {

                        $expectedMerchantProduct = MerchantProduct::findOrFail($merchantProduct->id);

                        if ($expectedMerchantProduct->merchantProductCategory->id == $merchantOrder->id) {
                            
                            if ($expectedMerchantProduct->has_variation && empty($merchantProduct->variation)) {
                                
                                $validator->errors()->add("merchants.$merchantOrderKey.products.$merchantProductKey", 'Product has variation');

                            }

                            else if ($expectedMerchantProduct->has_variation && ! empty($merchantProduct->variation)) {
                                
                                $expectedMerchantProductVariation = MerchantProductVariation::findOrFail($merchantProduct->variation->id);

                                if (empty($expectedMerchantProductVariation) || $expectedMerchantProductVariation->merchant_product_id != $expectedMerchantProduct->id) {
                                    
                                    $validator->errors()->add("merchants.$merchantOrderKey.products.$merchantProductKey.variation", 'Product variation id is invalid');

                                }

                            }

                            if ($expectedMerchantProduct->has_addon && ! empty($merchantProduct->addons)) {

                                foreach ($merchantProduct->addons as $merchantProductAddonKey => $merchantProductAddon) {
                                    
                                    $expectedMerchantProductAddon = MerchantProductAddon::findOrFail($merchantProductAddon->id);

                                    if (empty($expectedMerchantProductAddon) || $expectedMerchantProductAddon->merchant_product_id != $expectedMerchantProduct->id) {
                                        
                                        $validator->errors()->add("merchants.$merchantOrderKey.products.$merchantProductKey.addons.$merchantProductAddonKey", 'Product has no such addon');

                                    }

                                }

                            }

                        }

                        else {
                            $validator->errors()->add("merchants.$merchantOrderKey.products.$merchantProductKey", "Product id doesn't belong to restaurant");
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

            'merchants.*.id.required'  => 'Restaurant id is required',
            'merchants.*.id.*'  => 'Restaurant id is invalid',

            'merchants.*.products.*.id.required'  => 'Product id is required',
            // 'merchants.*.products.*.id.exists'  => 'Product id is invalid',
            
            'merchants.*.products.*.quantity.required'  => 'Product quantity is required',
            'merchants.*.products.*.quantity.numeric'  => 'Product quantity is invalid',

            // 'merchants.*.products.*.variation.required'  => 'Product variation is required',
            // 'merchants.*.products.*.variation.id.required'  => 'Product variation id is required',
            // 'merchants.*.products.*.variation.id.*'  => 'Product variation id is invalid',

            'merchants.*.products.*.addons' => [
                'present' => 'Product addons is required',
                'array' => 'Product addons must be an array',
            ],

            'merchants.*.products.*.addons.*.id' => [
                'required' => 'Addon item id is required',
                '*' => 'Addon item id is invalid',
            ],

            'merchants.*.products.*.addons.*.quantity' => [
                'required' => 'Addon quantity is required',
                '*' => 'Addon quantity is invalid',
            ],

            'merchants.*.products.*.customization.*' => 'Product customization should be string',
        ];
    }

}
