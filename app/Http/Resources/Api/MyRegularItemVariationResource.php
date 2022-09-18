<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Resources\Json\JsonResource;

class MyRegularItemVariationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $merchantProductVariation = $this->merchantProductVariation()->withTrashed()->first();

        return [
            'merchant_product_variation_id' => $this->id,
            'merchant_product_variation' => /* new RestaurantMenuItemVariationResource($this->merchantProductVariation()->withTrashed()->first()) */ $merchantProductVariation->variation->name,
            'price' => $merchantProductVariation->price,
        ];
    }
}
