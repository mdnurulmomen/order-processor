<?php

namespace App\Http\Resources\Web;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderedRestaurantItemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        // return parent::toArray($request);
        return [
           'restaurantMenuItem'=>$this->restaurantMenuItem,
           'quantity'=>$this->quantity,
           'selectedItemVariation'=>($this->restaurantMenuItem->has_variation & $this->selectedItemVariation()->exists()) ? $this->selectedItemVariation : false,
           'additionalOrderedAddons'=>($this->restaurantMenuItem->has_addon & $this->additionalOrderedAddons()->exists()) ? $this->additionalOrderedAddons : false,
        ];
    }
}
