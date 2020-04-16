<?php

namespace App\Http\Controllers\Web;

use App\Models\Waiter;
use App\Models\Kitchen;
use App\Models\Discount;
use App\Models\Restaurant;
use Illuminate\Http\Request;
use App\Models\RestaurantDeal;
use App\Models\RestaurantAdmin;
use App\Models\RestaurantMenuItem;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use App\Models\RestaurantMenuCategory;

class RestaurantController extends Controller
{
   	public function showAllRestaurants($perPage = false)
   	{
   		if ($perPage) {
            return response()->json([
               'all' => Restaurant::withTrashed()->with(['restaurantAdmin', 'restaurantCuisines', 'restaurantMenuCategories', 'restaurantMealCategories'])->latest()->paginate($perPage),
               'approved' => Restaurant::where('admin_approval', 1)->with(['restaurantAdmin', 'restaurantCuisines', 'restaurantMenuCategories', 'restaurantMealCategories'])->latest()->paginate($perPage),
               'nonApproved' => Restaurant::where('admin_approval', 0)->with(['restaurantAdmin', 'restaurantCuisines', 'restaurantMenuCategories', 'restaurantMealCategories'])->latest()->paginate($perPage),
               'trashed' => Restaurant::onlyTrashed()->with(['restaurantAdmin', 'restaurantCuisines', 'restaurantMenuCategories', 'restaurantMealCategories'])->latest()->paginate($perPage),
               
            ], 200);
         }

         return response(Restaurant::latest()->get(), 200);
   	}

   	public function createNewRestaurant(Request $request, $perPage)
   	{
   		$request->validate([
   			'restaurant_admins_id'=>'required',
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
         $newRestaurant->restaurant_admins_id = $request->restaurant_admins_id;
         
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
            'restaurant_admins_id'=>'required',
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
         $restaurantToUpdate->restaurant_admins_id = $request->restaurant_admins_id;
         
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

         // Deleting menu items with restaurant old menu categories;
         RestaurantMenuItem::whereNotIn('restaurant_menu_category_id', RestaurantMenuCategory::get()->pluck('id'))->delete();

         $restaurantToUpdate->banner_preview = $request->banner_preview;
         
         $restaurantToUpdate->save();

         return $this->showAllRestaurants($perPage);
      }

      public function deleteRestaurant($restaurantToDelete, $perPage)
      {
         $expectedRestaurant = Restaurant::find($restaurantToDelete);
         
         if ($expectedRestaurant) {
            $expectedRestaurant->kitchen()->delete();
            $expectedRestaurant->waiters()->delete();
            $expectedRestaurant->delete();
         }
         
         return $this->showAllRestaurants($perPage);
      }

      public function restoreRestaurant($restaurantToRestore, $perPage)
      {
         $expectedRestaurant = Restaurant::onlyTrashed()->find($restaurantToRestore);
         
         if ($expectedRestaurant) {
            $expectedRestaurant->kitchen()->restore();
            $expectedRestaurant->waiters()->restore();
            $expectedRestaurant->restore();
         }
         
         return $this->showAllRestaurants($perPage);
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



      public function showAllRestaurantAdmins($perPage = false)
      {
         if ($perPage) {
            return response()->json([
               'current' => RestaurantAdmin::with(['restaurants'])->paginate($perPage),
               'trashed' => RestaurantAdmin::onlyTrashed()->with(['restaurants'])->paginate($perPage),

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

         if ($restaurantAdminToDelete) {

            foreach ($restaurantAdminToDelete->restaurants as $restaurant) {
               $this->deleteRestaurant($restaurant->id, $perPage);
            }

            $restaurantAdminToDelete->delete();

         }

         return $this->showAllRestaurantAdmins($perPage);
      }

      public function restoreRestaurantAdmin($restaurantAdminToRestore, $perPage)
      {
         $restaurantAdminToStore = RestaurantAdmin::onlyTrashed()->find($restaurantAdminToRestore);

         if ($restaurantAdminToStore) {
            
            foreach ($restaurantAdminToStore->restaurants()->withTrashed()->get() as $restaurant) {
               $this->restoreRestaurant($restaurant->id, $perPage);
            }

            $restaurantAdminToStore->restore();

         }
            
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



      public function showAllDiscounts($perPage = false)
      {
         if ($perPage) {
            return response(Discount::paginate($perPage), 200);
         }

         return response(Discount::get(), 200);
      }

      public function createNewDiscount(Request $request, $perPage = false)
      {
         $request->validate([
            'rate'=>'required|numeric|unique:discounts,rate|min:1|max:100'
         ]);

         $newCuisine = Discount::create(['rate' => $request->rate]);

         return $this->showAllDiscounts($perPage);
      }

      public function updateDiscount(Request $request, $discount, $perPage)
      {
         $discountToUpdate = Discount::find($discount);

         $request->validate([
            'rate'=>'required|numeric|min:1|max:100|unique:discounts,rate,'.$discountToUpdate->id,
         ]);

         $discountUpdated = $discountToUpdate->update([
            'rate' => $request->rate
         ]);

         return $this->showAllDiscounts($perPage);
      }

      public function searchAllDiscounts($search, $perPage)
      {
         return response()->json([
            'all' => Discount::where('rate', 'like', "%$search%")->paginate($perPage),  
         ], 200);
      }



      public function showAllRestaurantKitchens($perPage = false)
      {
         if ($perPage) {
            return response()->json([
               'current' => Kitchen::with(['restaurant'])->paginate($perPage),
               'trashed' => Kitchen::onlyTrashed()->with(['restaurant'])->paginate($perPage),

            ], 200);
         }

         //return response(Kitchen::get(), 200);
      }

      public function createRestaurantKitchen(Request $request, $perPage = false)
      {
         $request->validate([
            'user_name'=>'required|string|max:50|unique:kitchens,user_name',
            'mobile'=>'required|unique:kitchens,mobile|max:13',
            'email'=>'required|unique:kitchens,email|email|string|max:50',
            'password'=>'required|string|min:8|max:100|confirmed',
            'restaurant_id'=>'numeric|required|exists:restaurants,id',
            'admin_approval'=>'nullable|boolean',
         ]);

         $newRestaurantAdmin = Kitchen::create([
            'user_name' => $request->user_name,
            'mobile' => $request->mobile,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'restaurant_id' => $request->restaurant_id,
            'admin_approval' => $request->admin_approval ?? false,
         ]);

         return $this->showAllRestaurantKitchens($perPage);
      }

      public function updateRestaurantKitchen(Request $request, $kitchenToUpdate, $perPage)
      {
         $restaurantKitchenToUpdate = Kitchen::find($kitchenToUpdate);

         $request->validate([
            'user_name'=>'required|string|max:50|unique:kitchens,user_name,'.$restaurantKitchenToUpdate->id,
            'mobile'=>'required|max:13|unique:kitchens,mobile,'.$restaurantKitchenToUpdate->id,
            'email'=>'required|email|string|max:50|unique:kitchens,email,'.$restaurantKitchenToUpdate->id,
            'password'=>'nullable|string|min:8|max:100|confirmed',
            'restaurant_id'=>'numeric|required|exists:restaurants,id',
            'admin_approval'=>'nullable|boolean',
         ]);

         $restaurantKitchenToUpdate->user_name = $request->user_name;        
         $restaurantKitchenToUpdate->mobile = $request->mobile;
         $restaurantKitchenToUpdate->email = $request->email;        

         if ($request->password) {       
            $restaurantKitchenToUpdate->password = Hash::make($request->password);
         }        

         $restaurantKitchenToUpdate->restaurant_id = $request->restaurant_id;        
         $restaurantKitchenToUpdate->admin_approval = $request->admin_approval ?? false;        
         $restaurantKitchenToUpdate->save();        

         return $this->showAllRestaurantKitchens($perPage);
      }

      public function deleteRestaurantKitchen($kitchenToDelete, $perPage)
      {
         $restaurantKitchenToDelete = Kitchen::find($kitchenToDelete);

         if ($restaurantKitchenToDelete) {
            
            $restaurantKitchenToDelete->delete();
            
         }

         return $this->showAllRestaurantKitchens($perPage);
      }

      public function restoreRestaurantKitchen($kitchenToRestore, $perPage)
      {
         $restaurantKitchenToRestore = Kitchen::onlyTrashed()->find($kitchenToRestore);

         if ($restaurantKitchenToRestore) {
            
            $restaurantKitchenToRestore->restore();

         }
   
         return $this->showAllRestaurantKitchens($perPage);
      }

      public function searchAllRestaurantKitchens($search, $perPage)
      {
         $columnsToSearch = ['user_name', 'mobile', 'email'];

         $query = Kitchen::with('restaurant')->withTrashed();

         foreach($columnsToSearch as $column)
         {
            $query->orWhere($column, 'like', "%$search%");
         }
         
         $query->orWhereHas('restaurant', function($q)use ($search){
            $q->where('name', 'like', "%$search%");
         });

         return response()->json([
            'all' => $query->paginate($perPage),  
         ], 200);
      }



      public function showAllRestaurantWaiters($perPage = false)
      {
         if ($perPage) {
            return response()->json([
               'current' => Waiter::with(['restaurant'])->paginate($perPage),
               'trashed' => Waiter::onlyTrashed()->with(['restaurant'])->paginate($perPage),

            ], 200);
         }

         //return response(Waiter::get(), 200);
      }

      public function createRestaurantWaiter(Request $request, $perPage = false)
      {
         $request->validate([
            'first_name'=>'nullable|string|max:50',
            'last_name'=>'nullable|string|max:50',
            'user_name'=>'required|string|max:50|unique:waiters,user_name',
            'mobile'=>'required|unique:waiters,mobile|max:13',
            'email'=>'required|unique:waiters,email|email|string|max:50',
            'password'=>'required|string|min:8|max:100|confirmed',
            'restaurant_id'=>'numeric|required|exists:restaurants,id',
            'admin_approval'=>'nullable|boolean',
         ]);

         $newRestaurantAdmin = Waiter::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'user_name' => $request->user_name,
            'mobile' => $request->mobile,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'restaurant_id' => $request->restaurant_id,
            'admin_approval' => $request->admin_approval ?? false,
         ]);

         return $this->showAllRestaurantWaiters($perPage);
      }

      public function updateRestaurantWaiter(Request $request, $waiterToUpdate, $perPage)
      {
         $restaurantWaiterToUpdate = Waiter::find($waiterToUpdate);

         $request->validate([
            'first_name'=>'nullable|string|max:50',
            'last_name'=>'nullable|string|max:50',
            'user_name'=>'required|string|max:50|unique:waiters,user_name,'.$restaurantWaiterToUpdate->id,
            'mobile'=>'required|max:13|unique:waiters,mobile,'.$restaurantWaiterToUpdate->id,
            'email'=>'required|email|string|max:50|unique:waiters,email,'.$restaurantWaiterToUpdate->id,
            'password'=>'nullable|string|min:8|max:100|confirmed',
            'restaurant_id'=>'numeric|required|exists:restaurants,id',
            'admin_approval'=>'nullable|boolean',
         ]);

         $restaurantWaiterToUpdate->first_name = $request->first_name;      
         $restaurantWaiterToUpdate->last_name = $request->last_name;      
         $restaurantWaiterToUpdate->user_name = $request->user_name;      
         $restaurantWaiterToUpdate->mobile = $request->mobile;
         $restaurantWaiterToUpdate->email = $request->email;        

         if ($request->password) {       
            $restaurantWaiterToUpdate->password = Hash::make($request->password);
         }        

         $restaurantWaiterToUpdate->restaurant_id = $request->restaurant_id;        
         $restaurantWaiterToUpdate->admin_approval = $request->admin_approval ?? false;        
         $restaurantWaiterToUpdate->save();        

         return $this->showAllRestaurantWaiters($perPage);
      }

      public function deleteRestaurantWaiter($waiterToDelete, $perPage)
      {
         $restaurantWaiterToDelete = Waiter::find($waiterToDelete);

         if ($restaurantWaiterToDelete) {
            
            $restaurantWaiterToDelete->delete();
            
         }

         return $this->showAllRestaurantWaiters($perPage);
      }

      public function restoreRestaurantWaiter($waiterToRestore, $perPage)
      {
         $restaurantWaiterToRestore = Waiter::onlyTrashed()->find($waiterToRestore);

         if ($restaurantWaiterToRestore) {
            
            $restaurantWaiterToRestore->restore();

         }
   
         return $this->showAllRestaurantWaiters($perPage);
      }

      public function searchAllRestaurantWaiters($search, $perPage)
      {
         $columnsToSearch = ['first_name', 'last_name', 'user_name', 'mobile', 'email'];

         $query = Waiter::with('restaurant')->withTrashed();

         foreach($columnsToSearch as $column)
         {
            $query->orWhere($column, 'like', "%$search%");
         }
         
         $query->orWhereHas('restaurant', function($q)use ($search){
            $q->where('name', 'like', "%$search%");
         });

         return response()->json([
            'all' => $query->paginate($perPage),  
         ], 200);
      }



      public function showAllRestaurantDeals($perPage = false)
      {
         if ($perPage) {
            return response()->json([
               'current' => Restaurant::with(['deal', 'deal.discount'])->paginate($perPage),
               'trashed' => Restaurant::onlyTrashed()->with(['deal', 'deal.discount'])->paginate($perPage),

            ], 200);
         }

         return response(Restaurant::with(['deal', 'deal.discount'])->get(), 200);
      }

      public function createRestaurantDeal(Request $request, $perPage = false)
      {
         $request->validate([
            'sale_percentage'=>'numeric|min:0|max:100',
            'restaurant_promotional_discount'=>'numeric|min:0|max:100',
            'native_discount'=>'numeric|min:0|max:100',
            'discount_id'=>'required|numeric|exists:discounts,id',
            'delivery_fee_addition'=>'nullable|boolean',
            'restaurant_id'=>'required|numeric|exists:restaurants,id|unique:restaurant_deals,restaurant_id',
         ]);

         $newRestaurantDeal = RestaurantDeal::create([
            'sale_percentage' => $request->sale_percentage,
            'restaurant_promotional_discount' => $request->restaurant_promotional_discount,
            'native_discount' => $request->native_discount,
            'discount_id' => $request->discount_id,
            'delivery_fee_addition' => $request->delivery_fee_addition ?? false,
            'restaurant_id' => $request->restaurant_id,
         ]);

         return $this->showAllRestaurantDeals($perPage);
      }

      public function updateRestaurantDeal(Request $request, $restaurantDeal, $perPage)
      {
         $restaurantDealToUpdate = RestaurantDeal::find($restaurantDeal);

         $request->validate([
            'sale_percentage'=>'numeric|min:0|max:100',
            'restaurant_promotional_discount'=>'numeric|min:0|max:100',
            'native_discount'=>'numeric|min:0|max:100',
            'discount_id'=>'required|numeric|exists:discounts,id',
            'delivery_fee_addition'=>'nullable|boolean',
            'restaurant_id'=>'required|numeric|exists:restaurants,id|unique:restaurant_deals,restaurant_id,'.$restaurantDealToUpdate->id,
         ]);

         $restaurantDealToUpdate->sale_percentage = $request->sale_percentage;        
         $restaurantDealToUpdate->restaurant_promotional_discount = $request->restaurant_promotional_discount;
         $restaurantDealToUpdate->native_discount = $request->native_discount;        
         $restaurantDealToUpdate->discount_id = $request->discount_id;     
         $restaurantDealToUpdate->delivery_fee_addition = $request->delivery_fee_addition ?? false;        
         $restaurantDealToUpdate->restaurant_id = $request->restaurant_id;     
         $restaurantDealToUpdate->save();        

         return $this->showAllRestaurantDeals($perPage);
      }

      public function searchAllRestaurantDeals($search, $perPage)
      {
         $query = Restaurant::withTrashed()->with(['deal', 'deal.discount']);

         $query->where('name', 'like', "%$search%");

         $query->orWhereHas('deal', function($q) use ($search){
  
            $q->where('sale_percentage', 'like', "%$search%")
              ->orWhere('restaurant_promotional_discount', 'like', "%$search%")
              ->orWhere('native_discount', 'like', "%$search%"); 

         });
         
         $query->orWhereHas('deal.discount', function($q) use ($search){
            $q->where('rate', 'like', "%$search%");
         });

         return response()->json([
            'all' => $query->paginate($perPage),  
         ], 200);
      }



      public function showAllRestaurantMeals($perPage = false)
      {
         if ($perPage) {
            return response(Restaurant::where('admin_approval', 1)->with('restaurantMealCategories')->paginate($perPage), 200);
         }

         return response(Restaurant::where('admin_approval', 1)->with('restaurantMealCategories')->get(), 200);
      }

      public function createRestaurantMeal(Request $request, $perPage = false)
      {
         $request->validate([
            'meal_id.*'=>'required|numeric|exists:meals,id',
            'restaurant_id'=>'required|numeric|exists:restaurants,id',
         ]);

         $restaurant = Restaurant::find($request->restaurant_id);
         $restaurant->restaurantMealCategories()->sync($request->meal_id);

         return $this->showAllRestaurantMeals($perPage);
      }

      public function updateRestaurantMeal(Request $request, $restaurant, $perPage)
      {
         $restaurantToUpdate = Restaurant::find($restaurant);

         $request->validate([
            'meal_id.*'=>'required|numeric|exists:meals,id',
            'restaurant_id'=>'required|numeric|exists:restaurants,id',
         ]);

         $restaurantToUpdate->restaurantMealCategories()->sync($request->meal_id);       

         return $this->showAllRestaurantMeals($perPage);
      }

      public function deleteRestaurantMeal($restaurant, $perPage)
      {
         $restaurantToDelete = Restaurant::find($restaurant);

         if ($restaurantToDelete) {
            
            $restaurantToDelete->restaurantMealCategories()->sync([]);  

         }

         return $this->showAllRestaurantMeals($perPage);
      }

      public function searchAllRestaurantMeals($search, $perPage)
      {
         $query = Restaurant::with('restaurantMealCategories')
                            ->where('name', 'like', "%$search%")
                            ->orWhereHas('restaurantMealCategories', function($q)use ($search){
                              $q->where('name', 'like', "%$search%");
                            })
                            ->where('admin_approval', 1);

         return response()->json([
            'all' => $query->paginate($perPage),  
         ], 200);
      }



      public function showAllRestaurantCuisines($perPage = false)
      {
         if ($perPage) {
            return response(Restaurant::where('admin_approval', 1)->with('restaurantCuisines')->paginate($perPage), 200);
         }

         return response(Restaurant::where('admin_approval', 1)->with('restaurantCuisines')->get(), 200);
      }

      public function createRestaurantCuisine(Request $request, $perPage = false)
      {
         $request->validate([
            'cuisine_id.*'=>'required|numeric|exists:cuisines,id',
            'restaurant_id'=>'required|numeric|exists:restaurants,id',
         ]);

         $restaurant = Restaurant::find($request->restaurant_id);
         $restaurant->restaurantCuisines()->sync($request->cuisine_id);

         return $this->showAllRestaurantCuisines($perPage);
      }

      public function updateRestaurantCuisine(Request $request, $restaurant, $perPage)
      {
         $restaurantToUpdate = Restaurant::find($restaurant);

         $request->validate([
            'cuisine_id.*'=>'required|numeric|exists:meals,id',
            'restaurant_id'=>'required|numeric|exists:restaurants,id',
         ]);

         $restaurantToUpdate->restaurantCuisines()->sync($request->cuisine_id);       

         return $this->showAllRestaurantCuisines($perPage);
      }

      public function deleteRestaurantCuisine($restaurant, $perPage)
      {
         $restaurantToDelete = Restaurant::find($restaurant);

         if ($restaurantToDelete) {
            
            $restaurantToDelete->restaurantCuisines()->sync([]);  
         
         }

         return $this->showAllRestaurantCuisines($perPage);
      }

      public function searchAllRestaurantCuisines($search, $perPage)
      {
         $query = Restaurant::with('restaurantCuisines')
                            ->orWhere('name', 'like', "%$search%")
                            ->orWhereHas('restaurantCuisines', function($q)use ($search){
                              $q->where('name', 'like', "%$search%");
                            })
                            ->where('admin_approval', 1);

         return response()->json([
            'all' => $query->paginate($perPage),  
         ], 200);
      }



      public function showRestaurantAllMenuItems($restaurant, $perPage = false)
      {
         if ($perPage) {
            return response(RestaurantMenuCategory::where('restaurant_id', $restaurant)->with(['restaurant', 'menuCategory', 'restaurantMenuItems'])->paginate($perPage), 200);
         }

         return response(RestaurantMenuCategory::where('restaurant_id', $restaurant)->with(['restaurant', 'menuCategory', 'restaurantMenuItems'])->get(), 200);
      }

      public function createRestaurantMenuItem(Request $request, $perPage = false)
      {
         $request->validate([
            'name'=>'required|string|max:255',
            'detail'=>'nullable|string|max:255',
            'has_variation'=>'boolean',
            'has_addon'=>'boolean',
            'price'=>'required|numeric|min:0|max:65535',
            'customizable'=>'boolean',
            'restaurant_menu_category_id'=>'required|numeric|exists:restaurant_menu_categories,id',
            'restaurant_id'=>'required|numeric|exists:restaurants,id',
         ]);

         $newMenuItem = RestaurantMenuItem::create([
            'name' => $request->name,
            'detail' => $request->detail,
            'has_variation' => $request->has_variation ?? false,
            'has_addon' => $request->has_addon ?? false,
            'price' => $request->price,
            'customizable' => $request->customizable ?? false,
            'restaurant_menu_category_id' => $request->restaurant_menu_category_id,
         ]);

         return $this->showRestaurantAllMenuItems($request->restaurant_id, $perPage);
      }

      public function updateRestaurantMenuItem(Request $request, $menuItemId, $perPage)
      {
         $menuItemToUpdate = RestaurantMenuItem::find($menuItemId);

         $request->validate([
            'name'=>'required|string|max:255',
            'detail'=>'nullable|string|max:255',
            'has_variation'=>'boolean',
            'has_addon'=>'boolean',
            'price'=>'required|numeric|min:0|max:65535',
            'customizable'=>'boolean',
            'restaurant_menu_category_id'=>'required|numeric|exists:restaurant_menu_categories,id',
            'restaurant_id'=>'required|numeric|exists:restaurants,id',
         ]);

         $menuItemToUpdate->update([
            'name' => $request->name,
            'detail' => $request->detail,
            'has_variation' => $request->has_variation ?? false,
            'has_addon' => $request->has_addon ?? false,
            'price' => $request->price,
            'customizable' => $request->customizable ?? false,
            'restaurant_menu_category_id' => $request->restaurant_menu_category_id,
         ]);

         return $this->showRestaurantAllMenuItems($request->restaurant_id, $perPage);
      }

      public function deleteRestaurantMenuItem($restaurant, $menuItemId, $perPage)
      {
         $menuItemToDelete = RestaurantMenuItem::find($menuItemId);
         // $restaurant = $menuItemToDelete->restaurantMenuCategory->restaurant_id;
          
         if ($menuItemToDelete ) {

            $menuItemToDelete->delete();
         
         }

         return $this->showRestaurantAllMenuItems($restaurant, $perPage);
      }

      public function searchRestaurantAllMenuItems($restaurant, $search, $perPage)
      {
         $query = RestaurantMenuCategory::with(['menuCategory', 'restaurantMenuItems']);

         $query->where( function( $subquery )use ($search){
            $subquery->whereHas('restaurantMenuItems', function($q) use ($search){
               $q->where("name", 'like', "%$search%");
               $q->orWhere("detail", 'like', "%$search%");
               $q->orWhere("price", 'like', "%$search%");
            });
            $subquery->orWhereHas('menuCategory', function($q) use ($search){
               $q->where('name', 'like', "%$search%");
            });
         });

         $query->where('restaurant_id', $restaurant);

         return response()->json([
            'all' => $query->paginate($perPage),  
         ], 200);
      }

      

      public function showAllRestaurantMenuCategories($perPage = false)
      {
         if ($perPage) {
            return response(Restaurant::where('admin_approval', 1)->with('restaurantMenuCategories')->paginate($perPage), 200);
         }

         return response(Restaurant::where('admin_approval', 1)->with('restaurantMenuCategories')->get(), 200);
      }

      public function searchAllRestaurantMenuCategories($search, $perPage)
      {
         $query = Restaurant::with('restaurantMenuCategories')
                            ->where('name', 'like', "%$search%")
                            ->orWhereHas('restaurantMenuCategories', function($q)use ($search){
                              $q->where('name', 'like', "%$search%");
                            })
                            ->where('admin_approval', 1);

         return response()->json([
            'all' => $query->paginate($perPage),  
         ], 200);
      }



      public function showRestaurantAllMenuCategories($restaurant, $perPage = false)
      {
         if ($perPage) {
            return response()->json([
               'current' => RestaurantMenuCategory::where('restaurant_id', $restaurant)->with(['restaurant', 'menuCategory', 'restaurantMenuItems'])->paginate($perPage),
               'trashed' => RestaurantMenuCategory::onlyTrashed()->where('restaurant_id', $restaurant)->with(['restaurant', 'menuCategory', 'restaurantMenuItems'])->paginate($perPage),

            ], 200);


            return response(RestaurantMenuCategory::where('restaurant_id', $restaurant)->with(['restaurant', 'menuCategory', 'restaurantMenuItems'])->paginate($perPage), 200);
         }

         return response(RestaurantMenuCategory::where('restaurant_id', $restaurant)->with(['menuCategory', 'restaurantMenuItems'])->get(), 200);
      }

      public function createRestaurantMenuCategory(Request $request, $perPage = false)
      {
         $request->validate([
            'menu_category_id'=>'required|array|min:1',
            'menu_category_id.*'  => "required|numeric|exists:menu_categories,id",
            'serving_from'=>'nullable|string|max:20',
            'serving_to'=>'nullable|string|max:20',
            'restaurant_id'=>'required|numeric|exists:restaurants,id',
         ]);

         $expectedRestaurant = Restaurant::find($request->restaurant_id);

         foreach ($request->menu_category_id as $restaurantNewMenuCategory) {
            
            if ($expectedRestaurant->restaurantMenuCategories()->where('menu_category_id', $restaurantNewMenuCategory)->count()) {
               
               $expectedRestaurant->restaurantMenuCategories()->where('menu_category_id', $restaurantNewMenuCategory)->update([
                  'serving_from' => $request->serving_from,
                  'serving_to' => $request->serving_to,
               ]);
            }

            else{

               $expectedRestaurant->restaurantMenuCategories()->syncWithoutDetaching([
                  $restaurantNewMenuCategory => [
                     'serving_from' => $request->serving_from,
                     'serving_to' => $request->serving_to,
                  ]
               ]);

            }

         }

         if ($request->from_menu_item_index) {
            return $this->showRestaurantAllMenuItems($request->restaurant_id, $perPage);
         }
         else if ($request->from_menu_category_index) {
            return $this->showAllRestaurantMenuCategories($perPage);
         }
         else
            return $this->showRestaurantAllMenuCategories($request->restaurant_id, $perPage);
      }

      public function updateRestaurantMenuCategory(Request $request, $menuCategory, $perPage)
      {
         $restaurantMenuCategoryToUpdate = RestaurantMenuCategory::find($menuCategory);

         $request->validate([
            'menu_category_id'=>'required|array|min:1',
            'menu_category_id.*'  => "required|numeric|exists:menu_categories,id",
            'serving_from'=>'nullable|string|max:20',
            'serving_to'=>'nullable|string|max:20',
            'restaurant_id'=>'required|numeric|exists:restaurants,id',
         ]);

         // As editing single restaurantMenuCategroy from request
         $restaurantMenuCategoryToUpdate->menu_category_id = $request->menu_category_id[0];
         $restaurantMenuCategoryToUpdate->serving_from = $request->serving_from;
         $restaurantMenuCategoryToUpdate->serving_to = $request->serving_to;

         $restaurantMenuCategoryToUpdate->save();

         return $this->showRestaurantAllMenuCategories($request->restaurant_id, $perPage);
      }

      public function deleteRestaurantMenuCategory($menuCategory, $perPage)
      {
         $restaurantMenuItemToDelete = RestaurantMenuCategory::find($menuCategory);
          
         if ($restaurantMenuItemToDelete ) {

            $restaurantMenuItemToDelete->restaurantMenuItems()->delete();
            $restaurantMenuItemToDelete->delete();
         
         }

         return $this->showRestaurantAllMenuCategories($restaurantMenuItemToDelete->restaurant_id, $perPage);
      }

      public function restoreRestaurantMenuCategory($menuCategory, $perPage)
      {
         $restaurantMenuItemToRestore = RestaurantMenuCategory::onlyTrashed()->find($menuCategory);
          
         if ($restaurantMenuItemToRestore ) {

            $restaurantMenuItemToRestore->restore();
         
         }

         return $this->showRestaurantAllMenuCategories($restaurantMenuItemToRestore->restaurant_id, $perPage);
      }

      public function searchRestaurantAllMenuCategories($restaurant, $search, $perPage)
      {
         $query = RestaurantMenuCategory::withTrashed()
                                          ->with(['menuCategory', 'restaurantMenuItems'])
                                          ->where("serving_from", 'like', "%$search%")
                                          ->orWhere("serving_to", 'like', "%$search%");
         
         $query->orWhereHas('menuCategory', function($q) use ($search){
            $q->where('name', 'like', "%$search%");
         });

         $query->where('restaurant_id', $restaurant);

         return response()->json([
            'all' => $query->paginate($perPage),  
         ], 200);
      }

}
