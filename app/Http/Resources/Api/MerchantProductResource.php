<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Resources\Json\JsonResource;

class MerchantProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $discount = $this->merchantProductCategory->merchant->deal->net_discount ?? 0;

        return [

            'id' => $this->id,
            'name' => $this->name,
            'price' => $this->price,
            'detail' => $this->detail,
            'has_addon' => $this->has_addon,
            'has_variation' => $this->has_variation,
            'customizable' => $this->customizable,
            'discount_price' => round($this->price - ($this->price * $discount/100)),
            
            'addons' => MerchantProductAddonResource::collection($this->whenLoaded('merchantProductAddons')),
            'variations' => MerchantProductVariationResource::collection($this->whenLoaded('merchantProductVariations')),

            'available' => $this->available,
            
            'merchant_id' => $this->when(strpos(Route::currentRouteName(), 'promotional-products.index') != false, $this->merchantProductCategory->merchant_id),
            'merchant_name' => $this->when(strpos(Route::currentRouteName(), 'promotional-products.index') != false, $this->merchantProductCategory->merchant->name)

        ];
    }
}
