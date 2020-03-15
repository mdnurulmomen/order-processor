<?php

namespace App\Http\Controllers\Web;

use App\Models\Restaurant;
use Illuminate\Http\Request;
use App\Models\RestaurantAdmin;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;

class RestaurantController extends Controller
{
   	public function showAllRestaurants($perPage)
   	{
   		return response()->json([
            'all' => Restaurant::withTrashed()->with(['restaurantAdmin', 'restaurantCuisines', 'restaurantMenuCategories', 'restaurantMealCategories'])->latest()->paginate($perPage),
            'approved' => Restaurant::where('admin_approval', 1)->with(['restaurantAdmin', 'restaurantCuisines', 'restaurantMenuCategories', 'restaurantMealCategories'])->latest()->paginate($perPage),
            'nonApproved' => Restaurant::where('admin_approval', 0)->with(['restaurantAdmin', 'restaurantCuisines', 'restaurantMenuCategories', 'restaurantMealCategories'])->latest()->paginate($perPage),
            'trashed' => Restaurant::onlyTrashed()->with(['restaurantAdmin', 'restaurantCuisines', 'restaurantMenuCategories', 'restaurantMealCategories'])->latest()->paginate($perPage),
            
         ], 200);
   	}

      public function searchAllRestaurants($search, $perPage)
      {
         $columnsToSearch = ['name', 'mobile', 'address', 'website', 'min_order'];

         $query = Restaurant::withTrashed()->with(['restaurantAdmin', 'restaurantCuisines', 'restaurantMenuCategories', 'restaurantMealCategories']);

         foreach($columnsToSearch as $column)
         {
            $query->orWhere($column, 'like', "%$search%");
         }

         return response()->json([
            'all' => $query->latest()->paginate($perPage),  
         ], 200);
      }

   	public function createNewRestaurant(Request $request, $perPage)
   	{
   		$request->validate([
   			'restaurantAdmin'=>'required',
            'name'=>'required|unique:restaurants,name|string|max:50',
   			'mobile'=>'required|unique:restaurants,mobile|max:13',
            'restaurantCuisineTags' => 'present|array|max:3',
            'restaurantCuisineTags.*' => 'numeric|distinct',
   			'website'=>'nullable|url|max:255',
   			// 'lat'=>'required|unique:menu_categories,name|max:50',
   			// 'lng'=>'required|unique:menu_categories,name|max:50',
   			'address'=>'required|string|max:255',
   			// 'banner_preview'=>'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
   			'min_order'=>'required|numeric|min:100|max:65535',
            'is_post_paid'=>'nullable|boolean',
            'restaurantFoodTags' => 'present|array|max:3|min:1',
            'restaurantFoodTags.*' => 'numeric|distinct',
            'restaurantMealTags' => 'present|array|max:6|min:1',
            'restaurantMealTags.*' => 'numeric|distinct',

   			'has_parking'=>'nullable|boolean',
            'is_self_service'=>'nullable|boolean',
   			// 'service_schedule'=>'required|unique:menu_categories,name|max:50',
   			// 'booking_schedule_break'=>'required|unique:menu_categories,name|max:50',
   		]);

         // return $request;

   		$newRestaurant = new Restaurant();
         $newRestaurant->admin_approval = $request->admin_approval ?? 0;
         $newRestaurant->restaurant_admins_id = $request->restaurantAdmin;
         
         $newRestaurant->name = $request->name;
         $newRestaurant->mobile = $request->mobile;
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

   		return $this->showAllRestaurants($perPage);
   	}

      public function updateRestaurant(Request $request, $restaurant, $perPage)
      {
         $restaurantToUpdate = Restaurant::find($restaurant);

         $request->validate([
            'restaurantAdmin'=>'required',
            'name'=>'required|string|max:50|unique:restaurants,name,'.$restaurantToUpdate->id,
            'mobile'=>'required|max:13|unique:restaurants,mobile,'.$restaurantToUpdate->id,
            'restaurantCuisineTags' => 'present|array|max:3',
            'restaurantCuisineTags.*' => 'numeric|distinct',
            'website'=>'nullable|url|max:255',
            // 'lat'=>'required|unique:menu_categories,name|max:50',
            // 'lng'=>'required|unique:menu_categories,name|max:50',
            'address'=>'required|string|max:255',
            // 'banner_preview'=>'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'min_order'=>'required|numeric|min:100|max:65535',
            'is_post_paid'=>'required|boolean',
            'restaurantFoodTags' => 'present|array|max:3|min:1',
            'restaurantFoodTags.*' => 'numeric|distinct',
            'restaurantMealTags' => 'present|array|max:6|min:1',
            'restaurantMealTags.*' => 'numeric|distinct',

            'has_parking'=>'required|boolean',
            'is_self_service'=>'nullable|boolean',
            // 'service_schedule'=>'required|unique:menu_categories,name|max:50',
            // 'booking_schedule_break'=>'required|unique:menu_categories,name|max:50',
         ]);

         $restaurantToUpdate->admin_approval = $request->admin_approval ?? 0;
         $restaurantToUpdate->restaurant_admins_id = $request->restaurantAdmin;
         
         $restaurantToUpdate->name = $request->name;
         $restaurantToUpdate->mobile = $request->mobile;
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

         return $this->showAllRestaurants($perPage);
      }

      public function deleteRestaurant($restaurantToDelete, $perPage)
      {
         Restaurant::destroy($restaurantToDelete);
         return $this->showAllRestaurants($perPage);
      }

      public function restoreRestaurant($restaurantToRestore, $perPage)
      {
         $restorationToStore = Restaurant::onlyTrashed()->find($restaurantToRestore);
         $restorationToStore->restore();
         
         return $this->showAllRestaurants($perPage);
      }

      public function showAllRestaurantAdmins($perPage = false)
      {
         if ($perPage) {
            return response()->json([
               'current' => RestaurantAdmin::paginate($perPage),
               'trashed' => RestaurantAdmin::onlyTrashed()->paginate($perPage),

            ], 200);
         }

         return response(RestaurantAdmin::get(), 200);
      }

      public function createRestaurantAdmin(Request $request, $perPage = false)
      {
         $request->validate([
            'user_name'=>'required|unique:restaurant_admins,user_name|string|max:50',
            'email'=>'required|unique:restaurant_admins,email|email|string|max:50',
            'mobile'=>'required|unique:restaurant_admins,mobile|max:13',
            'password'=>'required|string|min:8|max:100|confirmed',
         ]);

         $newRestaurantAdmin = RestaurantAdmin::create([
            'user_name' => $request->user_name,
            'email' => $request->email,
            'mobile' => $request->mobile,
            'password' => Hash::make($request->password)
         ]);

         return $this->showAllRestaurantAdmins($perPage);
      }

      public function updateRestaurantAdmin(Request $request, $restaurantAdmin, $perPage)
      {
         $restaurantAdminToUpdate = RestaurantAdmin::find($restaurantAdmin);

         $request->validate([
            'user_name'=>'required|string|max:50|unique:restaurant_admins,user_name,'.$restaurantAdminToUpdate->id,
            'email'=>'required|email|string|max:50|unique:restaurant_admins,email,'.$restaurantAdminToUpdate->id,
            'mobile'=>'required|max:13|unique:restaurant_admins,mobile,'.$restaurantAdminToUpdate->id,
            'password'=>'nullable|string|min:8|max:100|confirmed',
         ]);

         $restaurantAdminToUpdate->user_name = $request->user_name;        
         $restaurantAdminToUpdate->email = $request->email;        
         $restaurantAdminToUpdate->mobile = $request->mobile;

         if ($request->password) {       
            $restaurantAdminToUpdate->password = Hash::make($request->password);
         }        

         $restaurantAdminToUpdate->save();        

         return $this->showAllRestaurantAdmins($perPage);
      }

      public function deleteRestaurantAdmin($restaurantAdminToDelete, $perPage)
      {
         $restaurantAdminToDelete = RestaurantAdmin::find($restaurantAdminToDelete);

         $restaurantAdminToDelete->restaurants()->delete();
         $restaurantAdminToDelete->delete();

         return $this->showAllRestaurantAdmins($perPage);
      }

      public function restoreRestaurantAdmin($restaurantAdminToRestore, $perPage)
      {
         $restaurantAdminToStore = RestaurantAdmin::onlyTrashed()->find($restaurantAdminToRestore);
         $restaurantAdminToStore->restore();
            
           return $this->showAllRestaurantAdmins($perPage);
      }

      public function searchAllRestaurantAdmins($search, $perPage)
      {
         $columnsToSearch = ['user_name', 'mobile', 'email'];

         $query = RestaurantAdmin::withTrashed();

         foreach($columnsToSearch as $column)
         {
            $query->orWhere($column, 'like', "%$search%");
         }

         return response()->json([
            'all' => $query->paginate($perPage),  
         ], 200);
      }
}
