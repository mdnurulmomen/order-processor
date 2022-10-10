<?php

namespace App\Http\Requests\Api;

use App\Models\Customer;
use App\Models\MerchantAgent;
use Illuminate\Validation\Rule;
use App\Models\MerchantProduct;
use Illuminate\Support\Facades\Log;
use App\Models\MerchantProductAddon;
use App\Models\MerchantProductVariation;
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
            'order.type'=>'required|in:delivery,serving,collection',
            'order.is_asap_order'=>'required_without:order.schedule|boolean',
            'order.schedule'=>'required_if:order.is_asap_order,0|date|date_format:Y-m-d H:i:s|after:'.now()->subMinutes(3),
            'order.price'=>'required|numeric',
            'order.vat'=>'required|numeric',
            'order.discount'=>'required|numeric',
            'order.delivery_fee'=>'required|numeric',
            'order.net_payable'=>'required|numeric',
            'order.has_cutlery'=>'boolean',
            'order.orderer_type'=>'required|in:customer,merchant_agent',
            'order.orderer_id'=>[
                'required', 'numeric', 
                function($attribute, $value, $fail) {

                    if ($this->input('order.orderer_type') === 'customer' && ! Customer::where('id', $value)->exists()) {
                        return $fail($attribute.' is invalid.');
                    }
                    else if ($this->input('order.orderer_type') === 'merchant_agent' && ! MerchantAgent::where('id', $value)->exists()) {
                        return $fail($attribute.' is invalid.');
                    }
                    else if ($this->input('order.orderer_type') === 'merchant_agent' && $this->input('order.type') === 'delivery') {
                        return $fail('Delivery order is invalid.');     // Merchant Agent cant order for delivery
                    }
                    
                    /*
                        if (!Customer::where('id', $value)->exists() && !MerchantAgent::where('id', $value)->exists()) { 
                            return $fail($attribute.' is invalid.');
                        }
                    */
                   
                },
            ],

            'order.guest_number' => [
                'numeric', 'min:1',
                Rule::requiredIf(function () {

                    return $this->input('order.type')=='serving';

                }),
            ],

            'order.delivery_new_address' => [
                Rule::requiredIf(function () {

                    return (($this->input('order.type')=='delivery') && ($this->missing('order.delivery_address_id')));

                }),
            ],
            'order.delivery_new_address.house' => 'required_unless:order.delivery_new_address,|string',
            'order.delivery_new_address.road' => 'required_unless:order.delivery_new_address,|string',
            'order.delivery_new_address.additional_hint' => 'nullable|string',
            'order.delivery_new_address.lat' => 'required_unless:order.delivery_new_address,|string',
            'order.delivery_new_address.lang' => 'required_unless:order.delivery_new_address,|string',
            'order.delivery_new_address.address_name' => 'required_unless:order.delivery_new_address,|in:work,home,other|string',

            'order.delivery_address_id' => [
                Rule::exists('customer_addresses', 'id')->where(function ($query) {
                    return $query->where('customer_id', $this->input('order.orderer_id'));
                }),
                Rule::requiredIf(function () {
                    
                    return (($this->input('order.type')=='delivery') && ($this->missing('order.delivery_new_address')));

                }),
            ],


            'order.delivery_additional_info' => 'nullable|string',
            
            'payment'=>'required',
            'payment.id'=>'required_if:payment.method,bkash,card|string',
            'payment.method'=>'required|in:cash,card,bkash',

            // 'merchants' => 'required|array|min:1',
            'merchants' => [
                'required', 'array', 'min:1', 'between:1,3', 
                function ($attribute, $value, $fail) {
                    if (count($this->input('merchants.*.id')) > 1 && $this->input('order.type')==='serving') {
                        $fail('Multiple merchant aint allowed for serve order.');
                    }
                },
            ],
            'merchants.*.id' => 'required|exists:merchants,id',

            'merchants.*.products' => 'required|array|min:1',
            'merchants.*.products.*.id' => [
                'bail', 'required', 'exists:merchant_products,id', 
        /*
                function ($attribute, $value, $fail) {
                    
                    $productMerchantId = MerchantProduct::find($value)->merchantProductCategory->id;
                    $givenMerchantIds = $this->input('merchants.*.id');

                    // Log::info($this->input('merchants.*.id'));
                    // Log::warning($this->input('merchants.*.products.*.id'));

                    if (!in_array($productMerchantId, $givenMerchantIds)) {
                        $fail($attribute.' is invalid.');
                    }
                },
        */
            ],
            'merchants.*.products.*.quantity' => 'required|numeric',

            // 'merchants.*.products.*.variation' => 'required',
            // 'merchants.*.products.*.variation.id' => [
                // 'required_unless:merchants.*.products.*.variation,0,', 'numeric', 
        /*
                function ($attribute, $value, $fail) {

                    // Log::info($this->input('merchants.*.products.*.id'));

                    $variationAvailable = MerchantProduct::where('id', $this->input('merchants.*.products.*.id'))->first()->has_variation;

                    if ($variationAvailable && empty($value)) {
                        $fail($attribute.' is required.');
                    }
                },
        */
        /*
                Rule::exists('merchant_product_variations', 'id')->where(function ($query) {
                    
                    Log::info($this->input('merchants.*.products.*.id'));

                    $query->where('merchant_product_id', $this->input('merchants.*.products.*.id'));
                }),
        */
            // ],

            'merchants.*.products.*.addons' => 'present|array',
            'merchants.*.products.*.addons.*.id' => [
                'required_unless:merchants.*.products.*.addons.*,', 
                'numeric', 
                'exists:merchant_product_addons,id'
                /*
                    Rule::exists('merchant_product_addons', 'id')->where(function ($query) {
                        $query->where('merchant_product_id', $this->input('merchants.*.products.*.id'));
                    }),
                */
            ],
            'merchants.*.products.*.addons.*.quantity' => 'required_unless:merchants.*.products.*.addons.*,|
                numeric',

            'merchants.*.products.*.customization' => 'nullable|string',
            
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
                $this->merchants = json_decode(json_encode($this->input('merchants')));

                foreach ($this->merchants as $merchantOrderKey => $merchantOrder) {

                    foreach ($merchantOrder->products as $productKey => $merchantProduct) {

                        $expectedMerchantProduct = MerchantProduct::findOrFail($merchantProduct->id);

                        /*
                        // as already has been checked previously
                        if (empty($expectedMerchantProduct)) {
                            
                            $validator->errors()->add("merchants.$merchantOrderKey.products.$productKey", 'Product id is invalid');

                        }
                        */

                        if ($expectedMerchantProduct->merchantProductCategory->merchant_id == $merchantOrder->id) { // checking merchant
                            
                            if ($expectedMerchantProduct->has_variation && empty($merchantProduct->variation)) {
                                
                                $validator->errors()->add("merchants.$merchantOrderKey.products.$productKey", 'Product has variation');

                            }

                            else if ($expectedMerchantProduct->has_variation && ! empty($merchantProduct->variation)) {
                                
                                $expectedMerchantVariation = MerchantProductVariation::findOrFail($merchantProduct->variation->id);

                                if (empty($expectedMerchantVariation) || $expectedMerchantVariation->merchant_product_id != $expectedMerchantProduct->id) {
                                    
                                    $validator->errors()->add("merchants.$merchantOrderKey.products.$productKey.variation", 'Product variation id is invalid');

                                }

                            }

                            if ($expectedMerchantProduct->has_addon && ! empty($merchantProduct->addons)) {

                                foreach ($merchantProduct->addons as $productAddonKey => $productAddon) {
                                    
                                    $expectedMerchantProductAddon = MerchantProductAddon::findOrFail($productAddon->id);

                                    if (empty($expectedMerchantProductAddon) || $expectedMerchantProductAddon->merchant_product_id != $expectedMerchantProduct->id) {
                                        
                                        $validator->errors()->add("merchants.$merchantOrderKey.products.$productKey.addons.$productAddonKey", 'Product has no such addon');

                                    }

                                }

                            }

                        }

                        else {
                            
                            $validator->errors()->add("merchants.$merchantOrderKey.products.$productKey", "Product id doesn't belong to merchant");
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
            // 'order.type.required' => 'Order type is required',
            'order.is_asap_order.*'  => 'Order schedule is required',
            'order.schedule.*'  => 'Order schedule is not a valid',
            'order.price.required'  => 'Order price is required',
            'order.vat.required'  => 'Vat amount is required',
            'order.discount.required'  => 'Discount amount is required',
            'order.delivery_fee.required'  => 'Delivery fee amount is required',
            'order.net_payable.required'  => 'Net payable amount is required',
            'order.has_cutlery.boolean'  => 'Cutlery addion value is invalid',
            // 'order.orderer_type.required'  => 'Orderer type should be customer or merchant-agent',
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

            'payment.id.*'  => 'Payment id is required',
            // 'payment.method.required'  => 'Payment method is required',

            'merchants.*.id.required'  => 'Merchant id is required',
            'merchants.*.id.*'  => 'Merchant id is invalid',

            'merchants.*.products.*.id.required'  => 'Product id is required',
            // 'merchants.*.products.*.id.exists'  => 'Product id is invalid',
            'merchants.*.products.*.quantity.required'  => 'Product quantity is required',
            'merchants.*.products.*.quantity.numeric'  => 'Product quantity is invalid',

            // 'merchants.*.products.*.variation.required'  => 'Product variation is required',
            // 'merchants.*.products.*.variation.id.required'  => 'Product variation id is required',
            // 'merchants.*.products.*.variation.id.*'  => 'Product variation id is invalid',

            'merchants.*.products.*.addons.present' => 'Product addons is required',
            'merchants.*.products.*.addons.array' => 'Product addons must be an array',
            'merchants.*.products.*.addons.*.id.required'  => 'Addon item id is required',
            'merchants.*.products.*.addons.*.id.*'  => 'Addon item id is invalid',
            'merchants.*.products.*.addons.*.quantity.required'  => 'Addon quantity is required',
            'merchants.*.products.*.addons.*.quantity.*'  => 'Addon quantity is invalid',

            'merchants.*.products.*.customization.*' => 'Product customization should be string',
        ];
    }
}
