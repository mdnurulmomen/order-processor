<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Resources\Json\JsonResource;

class SponsoredRestaurantResource extends JsonResource
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
            'preview' => $this->banner_preview,
            "url" => route('api.v1.restaurants.show', [ 'restaurant'=>$this->id ])
        ];
    }
}
