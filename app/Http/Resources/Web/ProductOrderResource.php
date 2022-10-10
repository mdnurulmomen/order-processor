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
            'merchant_product' => new MerchantProductResource($this->whenLoaded('merchantProduct')),
            'quantity' => $this->quantity,
            'variation' => new ProductOrderVariationResource($this->whenLoaded('variation')),
            'addons' => ProductOrderAddonResource::collection($this->whenLoaded('addons')),
            'customization' => $this->when($this->customization, $this->customization ? $this->customization->custom_instruction : false),
        ];
    }
}
