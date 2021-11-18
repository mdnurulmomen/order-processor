<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Resources\Json\ResourceCollection;

class SearchedRestaurantCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'hot_deals' => [
                [
                    "id" => 1,
                    "preview" => "",
                    "url" => route('api.v1.restaurants.show', ['restaurant'=>1])
                ],
                [
                    "id" => 2,
                    "preview" => "",
                    "url" => route('api.v1.restaurants.show', ['restaurant'=>1])
                ]
            ],
            
            'data'=> SearchedRestaurantResource::collection($this->collection),

            'current_page' => $this->currentPage(),
            'first_page_url'=> $this->url(1),
            'from'=> $this->firstItem(),
            'last_page'=> $this->lastPage(),
            'last_page_url'=> $this->url($this->lastPage()),
            'next_page_url'=> $this->nextPageUrl(),
            'path'=> $this->path(),
            'per_page'=> $this->perPage(),
            'prev_page_url'=> $this->previousPageUrl(),
            'to'=> $this->lastItem(),
            'total'=> $this->total()
        ];
    }
}
