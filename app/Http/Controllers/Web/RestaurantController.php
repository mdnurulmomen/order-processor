<?php

namespace App\Http\Controllers\Web;

use App\Models\Meal;
use App\Models\Cuisine;
use App\Models\Restaurant;
use App\Models\MenuCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;

class RestaurantController extends Controller
{
   	public function showAllRestaurantCuisines()
   	{
   		return response(Cuisine::orderBy('id', 'DESC')->get(), 200);
   	}

   	public function createRestaurantCuisine(Request $request)
   	{
   		$request->validate([
   			'name'=>'required|unique:cuisines,name|max:50'
   		]);

   		$newCuisine = Cuisine::create(['name' => $request->name]);

   		return $this->showAllRestaurantCuisines();
   	}

   	public function showAllMenuCategories()
   	{
   		return response(MenuCategory::orderBy('id', 'DESC')->get(), 200);
   	}

   	public function createMenuCategory(Request $request)
   	{
   		$request->validate([
   			'name'=>'required|unique:menu_categories,name|max:50'
   		]);

   		$newCuisine = MenuCategory::create(['name' => $request->name]);

   		return $this->showAllMenuCategories();
   	}

      public function showAllMeals()
      {
         return response(Meal::orderBy('id', 'DESC')->get(), 200);
      }

      public function createNewMeal(Request $request)
      {
         $request->validate([
            'name'=>'required|unique:meals,name|max:50'
         ]);

         $newCuisine = Meal::create(['name' => $request->name]);

         return $this->showAllMeals();
      }

   	public function showAllRestaurants()
   	{
   		return response(Restaurant::orderBy('id', 'DESC')->get(), 200);
   	}

   	public function createNewRestaurant(Request $request)
   	{
   		$request->validate([
   			'name'=>'required|unique:restaurants,name|string|max:50',
   			'user_name'=>'required|unique:restaurants,user_name|string|max:50',
   			'email'=>'required|unique:restaurants,email|email|string|max:50',
   			'mobile'=>'required|unique:restaurants,mobile|max:13',
   			'password'=>'required|string|min:8|max:100|confirmed',
            'restaurantCuisineTags' => 'present|array|max:3',
            'restaurantCuisineTags.*' => 'numeric|distinct',
   			'website'=>'nullable|url|max:255',
   			// 'lat'=>'required|unique:menu_categories,name|max:50',
   			// 'lng'=>'required|unique:menu_categories,name|max:50',
   			'address'=>'required|string|max:255',
   			// 'banner_preview'=>'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
   			'min_order'=>'required|numeric|min:100',
            'is_post_paid'=>'required|boolean',
            'restaurantFoodTags' => 'present|array|max:3',
            'restaurantFoodTags.*' => 'numeric|distinct',
            'restaurantMealTags' => 'present|array|max:6',
            'restaurantMealTags.*' => 'numeric|distinct',

   			'has_parking'=>'required|boolean',
            'is_self_service'=>'required|boolean',
   			// 'service_schedule'=>'required|unique:menu_categories,name|max:50',
   			// 'booking_schedule_break'=>'required|unique:menu_categories,name|max:50',
   		]);

         // return $request;

   		$newRestaurant = new Restaurant();
         $newRestaurant->name = $request->name;
         $newRestaurant->user_name = $request->user_name;
         $newRestaurant->mobile = $request->mobile;
         $newRestaurant->email = $request->email;
         $newRestaurant->password = Hash::make($request->password);
         $newRestaurant->website = $request->website;
         
         $newRestaurant->lat = '23.781800';
         $newRestaurant->lng = '90.415710';
         
         $newRestaurant->address = $request->address;
         $newRestaurant->min_order = $request->min_order;
         $newRestaurant->is_post_paid = $request->is_post_paid;
         $newRestaurant->has_parking = $request->has_parking;
         $newRestaurant->is_self_service = $request->is_self_service;
         // $newRestaurant->service_schedule = $request->service_schedule;
         // $newRestaurant->booking_schedule_break = $request->booking_schedule_break;
         $newRestaurant->save();
         
         $newRestaurant->restaurantCuisines()->sync($request->restaurantCuisineTags);
         $newRestaurant->restaurantMenuCategories()->sync($request->restaurantFoodTags);
         $newRestaurant->restaurantMealCategories()->sync($request->restaurantMealTags);  
         $newRestaurant->banner_preview = $request->banner_preview;
         
         $newRestaurant->save();


   		return $this->showAllRestaurants();
   	}
}
