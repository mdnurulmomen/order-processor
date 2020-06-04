<?php

namespace App\Http\Controllers\Api;

use App\Models\Restaurant;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\RestaurantMenuCategory;
use App\Http\Resources\Api\RestaurantResource;
use App\Http\Resources\Api\RestaurantMenuItemResource;

class RestaurantController extends Controller
{
   	public function getRestaurants($latitude, $longitude)
   	{
   		// setting primarily
   		$latitude = '23.781800';
   		$longitude = '90.415710';

   		$restaurants = Restaurant::with(['restaurantCuisines', 'restaurantMealCategories', 'restaurantMenuCategories'])
                                    ->where('admin_approval', 1)
						                  ->where('taking_order', 1)
                                    ->whereBetween('lat', [intval($latitude-1), intval($latitude+1)])
   						               ->whereBetween('lng', [intval($longitude-1), intval($longitude+1)])
                                    ->get();

   		// return new RestaurantCollection(); // aggregations, items
         return RestaurantResource::collection($restaurants);
   	}

      public function getRestaurantMenuItems($expectedRestaurant)
      {
         $restaurantMenuItems =  Restaurant::findOrFail($expectedRestaurant)->with(['menuCategories.menuCategory', 'menuCategories.restaurantMenuItems.restaurantMenuItemVariations', 'menuCategories.restaurantMenuItems.restaurantMenuItemAddons'])->get();

         return RestaurantMenuItemResource::collection($restaurantMenuItems);
      }
}
