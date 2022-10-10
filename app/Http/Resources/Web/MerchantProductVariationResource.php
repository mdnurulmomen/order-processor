<?php

namespace App\Http\Resources\Web;

use Illuminate\Http\Resources\Json\JsonResource;

class MerchantProductVariationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $discount = $this->merchantProduct->merchantProductCategory->merchant->deal->net_discount ?? 0;

        return [

            'id' => $this->id,
            'price' => $this->price,
            'name' => $this->variation->name,
            'discount_price' => round($this->price - ($this->price * $discount/100)),
            // 'variation' => $this->variation,
        
        ];
    }
}
