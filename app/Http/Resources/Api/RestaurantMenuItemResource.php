<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Api\RestaurantMenuCategoryDetailResource;

class RestaurantMenuItemResource extends JsonResource
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

            'restaurantName' => $this->name,
            'restaurantMobile' => $this->mobile,
            'restaurantAddress' => $this->address,
            'addressLatitude' => $this->lat,
            'addressLongitude' => $this->lng,
            'restaurantWebsite' => $this->website,
            'restaurantBanner' => $this->banner_preview,
            'minimumOrder' => $this->min_order,
            'payFirst' => $this->is_post_paid ? 0 : 1,
            'selfServiceAvailability' => $this->is_self_service,
            'parkingAvailability' => $this->has_parking,
            'serviceHours' => json_decode($this->service_schedule),
            'bookingBreakHours' => json_decode($this->booking_break_schedule),

            // 'availableMeals' => RestaurantMealResource::collection($this->restaurantMealCategories),
            // 'availableCuisines' => RestaurantCuisineResource::collection($this->restaurantCuisines),
            // 'availableMenuCategories' => RestaurantMenuCategoryResource::collection($this->restaurantMenuCategories),
            
            'availableMenuCategoryDetails' => RestaurantMenuCategoryDetailResource::collection($this->menuCategories),
        ];
    }
}
