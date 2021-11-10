<?php

namespace App\Http\Controllers\Api;

use App\Models\Meal;
use App\Models\Cuisine;
use App\Models\Restaurant;
use App\Models\MenuCategory;
use Illuminate\Http\Request;
use App\Models\RestaurantReview;
use App\Http\Controllers\Controller;
// use App\Models\RestaurantMenuCategory;
use App\Http\Resources\Api\RestaurantResource;
use App\Http\Resources\Api\RestaurantReviewResource;
use App\Http\Resources\Api\RestaurantReviewCollection;
use App\Http\Resources\Api\RestaurantMenuItemResource;

class RestaurantController extends Controller
{
    public function getRestaurants(Request $request)
    {
      $request->validate([
        'latitude' => 'required|string',
        'longitude' => 'required|string',
        // 'preference' => 'nullable',
        'preference.type' => [
          'nullable', 'string', 'in:meal,cuisine,menu', 
          function ($attribute, $value, $fail) use ($request) {
            if ($value === 'meal' && Meal::find($request->input('preference.id')) === null) {
              $fail('Invalid meal preference.');
            }
            else if ($value === 'cuisine' && Cuisine::find($request->input('preference.id')) === null) {
              $fail('Invalid cuisine preference.');
            }
            else if ($value === 'menu' && MenuCategory::find($request->input('preference.id')) === null) {
              $fail('Invalid menu preference.');
            }
          },
        ],
        'preference.id' => 'nullable|required_unless:preference.type,'
      ]);

      $restaurantsInArea = Restaurant::with(['booking', 'restaurantCuisines', 'restaurantMealCategories', 'restaurantMenuCategories'])
      ->where('admin_approval', 1)
      ->where('taking_order', 1)
      ->whereBetween('lat', [intval($request->latitude-1), intval($request->latitude+1)])
      ->whereBetween('lng', [intval($request->longitude-1), intval($request->longitude+1)]);

      if (! empty($request->preference['type']) && ! empty($request->preference['id'])) {

        if ($request->preference['type']=='meal') {

          $restaurants = $restaurantsInArea->whereHas('meals', function ($query) use ($request) {
            $query->where('meal_id', $request->preference['id']);
          })
          ->get();

        }
        else if ($request->preference['type']=='cuisine') {

          $restaurants = $restaurantsInArea->whereHas('cuisines', function ($query) use ($request) {
            $query->where('cuisine_id', $request->preference['id']);
          })
          ->get();

        }
        else if ($request->preference['type']=='menu') {

          $restaurants = $restaurantsInArea->whereHas('menuCategories', function ($query) use ($request) {
            $query->where('menu_category_id', $request->preference['id']);
          })
          ->get();

        }

      }
      else {

        $restaurants = $restaurantsInArea->get();

      }

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

    public function getRestaurantReview($id, $perPage=false)
    {
        $expectedRestaurant = Restaurant::findOrFail($id);

        if ($perPage) {
          return new RestaurantReviewCollection(RestaurantReview::where('restaurant_id', $id)->paginate($perPage));
        }

        return RestaurantReviewResource::collection($expectedRestaurant->reviews);
    }

    public function addRestaurantReview(Request $request)
    {
        $request->validate([
          'rating' => 'required|numeric',
          'customer_id' => 'required|numeric|exists:customers,id',
          'restaurant_id' => 'required|numeric|exists:restaurants,id'
        ]);

        $expectedRestaurant = Restaurant::findOrFail($request->restaurant_id);

        $expectedRestaurant->reviews()->updateOrCreate(
          [ 'customer_id' => $request->customer_id ],
          [ 'rating' => $request->rating ]
        );

        return $this->getRestaurantReview($expectedRestaurant->id);
    }
}
