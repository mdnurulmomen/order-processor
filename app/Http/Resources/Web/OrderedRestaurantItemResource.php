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
           'quantity'=>$this->quantity,
           'restaurantMenuItem'=>$this->restaurantMenuItem
        ];
    }
}
