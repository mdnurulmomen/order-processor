<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Resources\Json\JsonResource;

class MyRegularProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'merchant_product'=>new MerchantProductResource($this->merchantProduct),
            'quantity'=>$this->quantity,
            'variation'=>($this->merchantProduct->has_variation & $this->variation()->exists()) ? new MyRegularProductVariationResource($this->variation) : false,
            'addons'=>($this->merchantProduct->has_addon & $this->addons()->exists()) ? MyRegularProductAddonResource::collection($this->addons) : [],
        ];
    }
}
