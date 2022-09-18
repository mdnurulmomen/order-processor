<?php

namespace App\Http\Resources\Web;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductOrderVariationResource extends JsonResource
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
            // 'restaurant_menu_item_variation_id'=>$this->restaurant_menu_item_variation_id,
            'merchant_product_variation' => /* new RestaurantMenuItemVariationResource($this->restaurantMenuItemVariation()->withTrashed()->first()) */ $this->merchantProductVariation()->withTrashed()->first()->variation->name,
        ];
    }
}
