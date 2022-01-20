<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Resources\Json\JsonResource;

class SponsorRestaurantResource extends JsonResource
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
            'id' => $this->id,
            'banner_preview' => $this->banner_preview,
            'cuisines' => RestaurantCuisineResource::collection($this->restaurantCuisines),
            'url' => route('api.v1.restaurants.show', [ 'restaurant'=>$this->id ])
        ];
    }
}
