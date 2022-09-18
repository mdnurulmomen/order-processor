<?php

namespace App\Http\Resources\Web;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductOrderResource extends JsonResource
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
            // 'restaurant_menu_item_id' => $this->restaurant_menu_item_id,
            'merchant_product' => new MerchantProductResource($this->merchantProduct),
            'quantity' => $this->quantity,
            'variation' => $this->when($this->merchantProduct->has_variation, new ProductOrderVariationResource($this->variation)),
            'addons' => ProductOrderAddonResource::collection($this->addons),
            'customization' => $this->when($this->customization, $this->customization ? $this->customization->custom_instruction : false),
        ];
    }
}
