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
   		return response(Cuisine::get(), 200);
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
   		return response(MenuCategory::get(), 200);
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
         return response(Meal::get(), 200);
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
   		return response()->json([
            'all' => Restaurant::withTrashed()->with(['restaurantCuisines', 'restaurantMenuCategories', 'restaurantMealCategories'])->latest()->paginate(1),
            'approved' => Restaurant::where('admin_approval', 1)->with(['restaurantCuisines', 'restaurantMenuCategories', 'restaurantMealCategories'])->latest()->paginate(1),
            'nonApproved' => Restaurant::where('admin_approval', 0)->with(['restaurantCuisines', 'restaurantMenuCategories', 'restaurantMealCategories'])->latest()->paginate(1),
            'trashed' => Restaurant::onlyTrashed()->with(['restaurantCuisines', 'restaurantMenuCategories', 'restaurantMealCategories'])->latest()->paginate(1),
            
         ], 200);

         // return response(
            // Restaurant::with(['restaurantCuisines', 'restaurantMenuCategories', 'restaurantMealCategories'])->orderBy('id', 'DESC')->paginate(1), 200
         // );
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
   			'min_order'=>'required|numeric|min:100|max:65535',
            'is_post_paid'=>'nullable|boolean',
            'restaurantFoodTags' => 'present|array|max:3',
            'restaurantFoodTags.*' => 'numeric|distinct',
            'restaurantMealTags' => 'present|array|max:6',
            'restaurantMealTags.*' => 'numeric|distinct',

   			'has_parking'=>'nullable|boolean',
            'is_self_service'=>'nullable|boolean',
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
         $newRestaurant->is_post_paid = $request->is_post_paid ?? 0;
         $newRestaurant->has_parking = $request->has_parking ?? 0;
         $newRestaurant->is_self_service = $request->is_self_service ?? 0;
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

      public function updateRestaurant(Request $request, $restaurant)
      {
         $restaurantToUpdate = Restaurant::find($restaurant);

         $request->validate([
            'name'=>'required|string|max:50|unique:restaurants,name,'.$restaurantToUpdate->id,
            'user_name'=>'required|string|max:50|unique:restaurants,user_name,'.$restaurantToUpdate->id,
            'email'=>'required|email|string|max:50|unique:restaurants,email,'.$restaurantToUpdate->id,
            'mobile'=>'required|max:13|unique:restaurants,mobile,'.$restaurantToUpdate->id,
            'password'=>'nullable|string|min:8|max:100|confirmed',
            'restaurantCuisineTags' => 'present|array|max:3',
            'restaurantCuisineTags.*' => 'numeric|distinct',
            'website'=>'nullable|url|max:255',
            // 'lat'=>'required|unique:menu_categories,name|max:50',
            // 'lng'=>'required|unique:menu_categories,name|max:50',
            'address'=>'required|string|max:255',
            // 'banner_preview'=>'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'min_order'=>'required|numeric|min:100|max:65535',
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

         $restaurantToUpdate->name = $request->name;
         $restaurantToUpdate->user_name = $request->user_name;
         $restaurantToUpdate->mobile = $request->mobile;
         $restaurantToUpdate->email = $request->email;

         if ($request->has('password')) {   
            $restaurantToUpdate->password = Hash::make($request->password);
         }

         $restaurantToUpdate->website = $request->website;
         
         $restaurantToUpdate->lat = '23.781800';
         $restaurantToUpdate->lng = '90.415710';
         
         $restaurantToUpdate->address = $request->address;
         $restaurantToUpdate->min_order = $request->min_order;
         $restaurantToUpdate->is_post_paid = $request->is_post_paid;
         $restaurantToUpdate->has_parking = $request->has_parking;
         $restaurantToUpdate->is_self_service = $request->is_self_service;
         // $newRestaurant->service_schedule = $request->service_schedule;
         // $newRestaurant->booking_schedule_break = $request->booking_schedule_break;
         $restaurantToUpdate->restaurantCuisines()->sync($request->restaurantCuisineTags);
         $restaurantToUpdate->restaurantMenuCategories()->sync($request->restaurantFoodTags);
         $restaurantToUpdate->restaurantMealCategories()->sync($request->restaurantMealTags);  
         $restaurantToUpdate->banner_preview = $request->banner_preview;
         
         $restaurantToUpdate->save();

         return $this->showAllRestaurants();
      }

      public function deleteRestaurant($restaurantToDelete)
      {
         Restaurant::destroy($restaurantToDelete);
         return $this->showAllRestaurants();
      }

      public function restoreRestaurant($restaurantToRestore)
      {
         $restorationToStore = Restaurant::onlyTrashed()->find($restaurantToRestore);
         $restorationToStore->restore();
         
         return $this->showAllRestaurants();
      }
}
