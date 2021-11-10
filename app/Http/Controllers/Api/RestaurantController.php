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
   	public function getRestaurants(Request $request)
   	{
   		$request->validate([
            'latitude' => 'required|string',
            'longitude' => 'required|string',
         ]);

   		$restaurants = Restaurant::with(['restaurantCuisines', 'restaurantMealCategories', 'restaurantMenuCategories'])
                                    ->where('admin_approval', 1)
						                  ->where('taking_order', 1)
                                    ->whereBetween('lat', [intval($request->latitude-1), intval($request->latitude+1)])
   						               ->whereBetween('lng', [intval($request->longitude-1), intval($request->longitude+1)])
                                    ->get();

   		// return new RestaurantCollection(); // aggregations, items
         return RestaurantResource::collection($restaurants);
   	}

      public function getRestaurantMenuItems(Request $request)
      {
         $request->validate([
            'id' => 'required|numeric|exists:restaurants,id'
         ]);

         $restaurantMenuItems =  Restaurant::findOrFail($request->id)->with(['menuCategories.menuCategory', 'menuCategories.restaurantMenuItems.restaurantMenuItemVariations', 'menuCategories.restaurantMenuItems.restaurantMenuItemAddons'])->get();

         return RestaurantMenuItemResource::collection($restaurantMenuItems);
      }
}
