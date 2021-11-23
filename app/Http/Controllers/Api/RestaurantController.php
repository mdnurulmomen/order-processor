<?php

namespace App\Http\Controllers\Api;

use App\Models\Meal;
use App\Models\Cuisine;
use App\Models\Restaurant;
use App\Models\MenuCategory;
use Illuminate\Http\Request;
use App\Models\RestaurantReview;
use App\Models\RestaurantMenuItem;
use App\Http\Controllers\Controller;
// use App\Models\RestaurantMenuCategory;
use App\Http\Resources\Api\RestaurantResource;
use App\Http\Resources\Api\RestaurantReviewResource;
use App\Http\Resources\Api\SearchedRestaurantResource;
use App\Http\Resources\Api\RestaurantReviewCollection;
// use App\Http\Resources\Api\RestaurantMenuItemResource;
use App\Http\Resources\Api\SearchedRestaurantCollection;
use App\Http\Resources\Api\SponsoredRestaurantCollection;
use App\Http\Resources\Api\SponsoredRestaurantResource;
use App\Http\Resources\Api\PromotedMenuItemCollection;
use App\Http\Resources\Api\PromotedMenuItemResource;

class RestaurantController extends Controller
{
    public function getRestaurants(Request $request)
    {
      $request->validate([
        'latitude' => 'required|string',
        'longitude' => 'required|string',
        'perPage' => 'nullable|numeric',
        // 'preference' => 'nullable',
        'preference.type' => [ 'nullable', 'string', 'in:meals,cuisines,menus' ],
        'preference.ids' => 'nullable|array|required_unless:preference.type,',
        'preference.ids.*' => [
          'required_unless:preference.type,',
          function ($attribute, $value, $fail) use ($request) {
            if ($request->input('preference.type') === 'meals' && Meal::find($value) === null) {
              $fail('Invalid meal preference.');
            }
            else if ($request->input('preference.type') === 'cuisines' && Cuisine::find($value) === null) {
              $fail('Invalid cuisine preference.');
            }
            else if ($request->input('preference.type') === 'menus' && MenuCategory::find($value) === null) {
              $fail('Invalid menu preference.');
            }
          },
        ]
      ]);

      $restaurantsInArea = Restaurant::where('admin_approval', 1)->where('taking_order', 1)
      ->whereBetween('lat', [intval($request->latitude-1), intval($request->latitude+1)])
      ->whereBetween('lng', [intval($request->longitude-1), intval($request->longitude+1)]);

      // preferences
      if (! empty($request->preference['type']) && ! empty($request->preference['ids'])) {

        if ($request->preference['type']=='meals') {

          $restaurants = $restaurantsInArea->whereHas('meals', function ($query) use ($request) {
            $query->whereIn('meal_id', $request->preference['ids']);
          });

        }
        else if ($request->preference['type']=='cuisines') {

          $restaurants = $restaurantsInArea->whereHas('cuisines', function ($query) use ($request) {
            $query->whereIn('cuisine_id', $request->preference['ids']);
          });

        }
        else if ($request->preference['type']=='menus') {

          $restaurants = $restaurantsInArea->whereHas('menuCategories', function ($query) use ($request) {
            $query->whereIn('menu_category_id', $request->preference['ids']);
          });

        }

      }
      else {

        $restaurants = $restaurantsInArea;

      }

      // pagination
      if ($request->perPage) {
        return new SearchedRestaurantCollection($restaurants->paginate($request->perPage));
      }
      else {
        // return new RestaurantCollection(); // aggregations, items
        return SearchedRestaurantResource::collection($restaurants->get());
      }

    }

    public function showRestaurantDetails($restaurant)
    {
      return new RestaurantResource(Restaurant::with(['booking', 'meals', 'cuisines', 'menuCategories.restaurantMenuItems', 'reviews'])->findOrFail($restaurant));
    }

    public function getSponsoredRestaurants(Request $request)
    {
      $request->validate([
        'latitude' => 'required|string',
        'longitude' => 'required|string',
        'perPage' => 'nullable|numeric',
        // 'preference' => 'nullable',
        /*
        'preference.type' => [ 'nullable', 'string', 'in:meals,cuisines,menus' ],
        'preference.ids' => 'nullable|array|required_unless:preference.type,',
        'preference.ids.*' => [
          'required_unless:preference.type,',
          function ($attribute, $value, $fail) use ($request) {
            if ($request->input('preference.type') === 'meals' && Meal::find($value) === null) {
              $fail('Invalid meal preference.');
            }
            else if ($request->input('preference.type') === 'cuisines' && Cuisine::find($value) === null) {
              $fail('Invalid cuisine preference.');
            }
            else if ($request->input('preference.type') === 'menus' && MenuCategory::find($value) === null) {
              $fail('Invalid menu preference.');
            }
          },
        ]
        */
      ]);

      $restaurantsInArea = Restaurant::where('admin_approval', 1)
      ->where('taking_order', 1)->where('sponsored', 1)
      ->whereBetween('lat', [intval($request->latitude-1), intval($request->latitude+1)])
      ->whereBetween('lng', [intval($request->longitude-1), intval($request->longitude+1)]);

      // preferences
      /*
      if (! empty($request->preference['type']) && ! empty($request->preference['ids'])) {

        if ($request->preference['type']=='meals') {

          $restaurants = $restaurantsInArea->whereHas('meals', function ($query) use ($request) {
            $query->whereIn('meal_id', $request->preference['ids']);
          });

        }
        else if ($request->preference['type']=='cuisines') {

          $restaurants = $restaurantsInArea->whereHas('cuisines', function ($query) use ($request) {
            $query->whereIn('cuisine_id', $request->preference['ids']);
          });

        }
        else if ($request->preference['type']=='menus') {

          $restaurants = $restaurantsInArea->whereHas('menuCategories', function ($query) use ($request) {
            $query->whereIn('menu_category_id', $request->preference['ids']);
          });

        }

      }
      else {

        $restaurants = $restaurantsInArea;

      }
      */

      // pagination
      if ($request->perPage) {
        return new SponsoredRestaurantCollection(/*$restaurants->paginate($request->perPage)*/ $restaurantsInArea->paginate($request->perPage));
      }
      else {
        // return new RestaurantCollection(); // aggregations, items
        return SponsoredRestaurantResource::collection(/*$restaurants->get()*/ $restaurantsInArea->get());
      }     

    }

    public function getPromotedMenuItems(Request $request)
    {
      $request->validate([
        'latitude' => 'required|string',
        'longitude' => 'required|string',
        'perPage' => 'nullable|numeric'
      ]);

      $restaurantsInArea = RestaurantMenuItem::where('promoted', 1)
      ->whereHas('restaurantMenuCategory.restaurant', function ($query) use ($request) {
          $query->where('taking_order', 1)->where('admin_approval', 1)
          ->whereBetween('lat', [intval($request->latitude-1), intval($request->latitude+1)])
          ->whereBetween('lng', [intval($request->longitude-1), intval($request->longitude+1)]);
      });

      // pagination
      if ($request->perPage) {
        return new PromotedMenuItemCollection($restaurantsInArea->paginate($request->perPage));
      }
      else {
        // return new RestaurantCollection(); // aggregations, items
        return PromotedMenuItemResource::collection($restaurantsInArea->get());
      }     

    }

    /*
    public function getRestaurantMenuItems(Request $request)
    {
      $request->validate([
        'id' => 'required|numeric|exists:restaurants,id'
      ]);

      $restaurantMenuItems =  Restaurant::findOrFail($request->id)->with(['menuCategories.menuCategory', 'menuCategories.restaurantMenuItems.restaurantMenuItemVariations', 'menuCategories.restaurantMenuItems.restaurantMenuItemAddons'])->get();

      return RestaurantMenuItemResource::collection($restaurantMenuItems);
    }
    */

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
