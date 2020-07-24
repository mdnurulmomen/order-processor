<?php

namespace App\Http\Controllers\Web;

use App\Models\Waiter;
use App\Models\Kitchen;
use App\Models\Restaurant;
use Illuminate\Http\Request;
use App\Models\RestaurantMeal;
use App\Models\RestaurantDeal;
use App\Models\RestaurantAdmin;
use Illuminate\Validation\Rule;
use App\Models\RestaurantCuisine;
use App\Models\RestaurantMenuItem;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use App\Models\RestaurantMenuCategory;
use App\Models\RestaurantMenuItemAddon;
use App\Models\RestaurantMenuItemVariation;

class RestaurantController extends Controller
{
   	public function showAllRestaurants($perPage = false)
   	{
   		if ($perPage) {
            return response()->json([
               'all' => Restaurant::withTrashed()->with('restaurantAdmin')->latest()->paginate($perPage),
               'approved' => Restaurant::where('admin_approval', 1)->with('restaurantAdmin')->latest()->paginate($perPage),
               'nonApproved' => Restaurant::where('admin_approval', 0)->with('restaurantAdmin')->latest()->paginate($perPage),
               'trashed' => Restaurant::onlyTrashed()->with('restaurantAdmin')->latest()->paginate($perPage),
               
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
   			'website'=>'nullable|url|max:255',
   			// 'lat'=>'required|unique:menu_categories,name|max:50',
   			// 'lng'=>'required|unique:menu_categories,name|max:50',
   			'address'=>'required|string|max:255',
   			// 'banner_preview'=>'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
   			'min_order'=>'required|numeric|min:100|max:65535',
            'is_post_paid'=>'nullable|boolean',
   			'has_parking'=>'nullable|boolean',
            'is_self_service'=>'nullable|boolean',
   			'service_schedule'=>'required',
   			'booking_break_schedule'=>'required',
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
         $newRestaurant->service_schedule = json_encode($request->service_schedule);
         $newRestaurant->booking_break_schedule = json_encode($request->booking_break_schedule);
         
         $newRestaurant->save();
           
         // For $this->id.jpg
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
            'min_order'=>'required|numeric|min:100|max:65535',
            'website'=>'nullable|url|max:255',
            // 'lat'=>'required|unique:menu_categories,name|max:50',
            // 'lng'=>'required|unique:menu_categories,name|max:50',
            'address'=>'required|string|max:255',
            // 'banner_preview'=>'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'is_post_paid'=>'required|boolean',
            'has_parking'=>'required|boolean',
            'is_self_service'=>'nullable|boolean',
            'service_schedule'=>'required',
            'booking_break_schedule'=>'required',
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

         $restaurantToUpdate->service_schedule = json_encode($request->service_schedule);
         $restaurantToUpdate->booking_break_schedule = json_encode($request->booking_break_schedule);
         
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
         
         if ($expectedRestaurant && $expectedRestaurant->restaurantAdmin()->exists()) {

            $expectedRestaurant->kitchen()->restore();
            $expectedRestaurant->waiters()->restore();
            $expectedRestaurant->restore();

         }
         
         return $this->showAllRestaurants($perPage);
      }

      public function searchAllRestaurants($search, $perPage)
      {
         $columnsToSearch = ['name', 'mobile', 'address', 'website', 'min_order'];

         $query = Restaurant::withTrashed()->with('restaurantAdmin');

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
               'current' => RestaurantAdmin::with('restaurants')->paginate($perPage),
               'trashed' => RestaurantAdmin::onlyTrashed()->with('restaurants')->paginate($perPage),

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
            
            /*
            foreach ($restaurantAdminToStore->restaurants()->withTrashed()->get() as $restaurant) {
               $this->restoreRestaurant($restaurant->id, $perPage);
            }
            */

            $restaurantAdminToStore->restore();

         }
            
         return $this->showAllRestaurantAdmins($perPage);
      }

      public function searchAllRestaurantAdmins($search, $perPage)
      {
         $columnsToSearch = ['user_name', 'mobile', 'email'];

         $query = RestaurantAdmin::withTrashed()->with('restaurants');

         foreach($columnsToSearch as $column)
         {
            $query->orWhere($column, 'like', "%$search%");
         }

         return response()->json([
            'all' => $query->paginate($perPage),  
         ], 200);
      }

      public function showAllRestaurantKitchens($perPage = false)
      {
         if ($perPage) {
            return response()->json([
               'current' => Kitchen::with('restaurant')->paginate($perPage),
               'trashed' => Kitchen::onlyTrashed()->with('restaurant')->paginate($perPage),

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

         if ($restaurantKitchenToRestore && $restaurantKitchenToRestore->restaurant()->exists()) {
            
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
               'current' => Waiter::with('restaurant')->paginate($perPage),
               'trashed' => Waiter::onlyTrashed()->with('restaurant')->paginate($perPage),

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

         if ($restaurantWaiterToRestore && $restaurantWaiterToRestore->restaurant()->exists()) {
            
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
            return response(Restaurant::with('restaurantMealCategories')->paginate($perPage), 200);
         }

         return response(Restaurant::with('restaurantMealCategories')->get(), 200);
      }

      public function createRestaurantMeal(Request $request, $perPage = false)
      {
         $request->validate([
            'meal_id.*'=>'required|numeric|exists:meals,id',
            'restaurant_id'=>'required|numeric|exists:restaurants,id',
         ]);

         $restaurant = Restaurant::find($request->restaurant_id);

         foreach ($request->meal_id as $restaurantNewMeal) {
            
            if (!$restaurant->restaurantMealCategories()->wherePivot('meal_id', $restaurantNewMeal)->count()) {
               
               $restaurant->restaurantMealCategories()->syncWithoutDetaching([$restaurantNewMeal]);
            }

         }

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
                            });

         return response()->json([
            'all' => $query->paginate($perPage),  
         ], 200);
      }



      public function showAllRestaurantCuisines($perPage = false)
      {
         if ($perPage) {
            return response(Restaurant::with('restaurantCuisines')->paginate($perPage), 200);
         }

         return response(Restaurant::with('restaurantCuisines')->get(), 200);
      }

      public function createRestaurantCuisine(Request $request, $perPage = false)
      {
         $request->validate([
            'cuisine_id.*'=>'required|numeric|exists:cuisines,id',
            'restaurant_id'=>'required|numeric|exists:restaurants,id',
         ]);

         $restaurant = Restaurant::find($request->restaurant_id);

         foreach ($request->cuisine_id as $restaurantNewCuisine) {
            
            if (!$restaurant->restaurantCuisines()->wherePivot('cuisine_id', $restaurantNewCuisine)->count()) {
               
               $restaurant->restaurantCuisines()->syncWithoutDetaching([$restaurantNewCuisine]);
            }

         }

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
                            });

         return response()->json([
            'all' => $query->paginate($perPage),  
         ], 200);
      }



      public function showRestaurantAllMenuItems($restaurant, $perPage = false)
      {
         if ($perPage) {
            return response(RestaurantMenuCategory::withTrashed()->where('restaurant_id', $restaurant)->with(['restaurant', 'menuCategory', 'restaurantMenuItems.restaurantMenuItemVariations', 'restaurantMenuItems.restaurantMenuItemAddons'])->paginate($perPage), 200);
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
            'price'=>'nullable|numeric|min:0|max:65535',
            'customizable'=>'boolean',
            'restaurant_menu_category_id'=>'required|numeric|exists:restaurant_menu_categories,id',
            'restaurant_id'=>'required|numeric|exists:restaurants,id',
         ]);

         $newMenuItem = RestaurantMenuItem::create([
            'name' => $request->name,
            'detail' => $request->detail,
            'has_variation' => $request->has_variation ?? false,
            'has_addon' => $request->has_addon ?? false,
            'price' => $request->price ?? 0,
            'customizable' => $request->customizable ?? false,
            'restaurant_menu_category_id' => $request->restaurant_menu_category_id,
         ]);

         if ($newMenuItem->has_variation) {

            for ($i=0; $i < count($request->variations_id); $i++) { 
               
               $newMenuItem->restaurantMenuItemVariations()
                           ->syncWithoutDetaching([
                              $request->variations_id[$i] => [
                                 'price' => $request->price_item_variations[$i] ?? 0
                              ]
                           ]
               );

            }

            $newMenuItem->update([
               'price' => min($request->price_item_variations)
            ]);

         }

         if ($newMenuItem->has_addon) {

            for ($i=0; $i < count($request->addons_id); $i++) { 
               
               $newMenuItem->restaurantMenuItemAddons()
                           ->syncWithoutDetaching([
                              $request->addons_id[$i] => [
                                 'price' => $request->price_addon_items[$i] ?? 0
                              ]
                           ]
               );

            }

            $newMenuItem->update([
               'price' => min($request->price_addon_items)
            ]);

         }

         return $this->showRestaurantAllMenuItems($request->restaurant_id, $perPage);
      }

      public function updateRestaurantMenuItem(Request $request, $menuItem, $perPage)
      {
         $menuItemToUpdate = RestaurantMenuItem::find($menuItem);

         $request->validate([
            'name'=>'required|string|max:255',
            'detail'=>'nullable|string|max:255',
            'has_variation'=>'boolean',
            'variations_id' => 'required_if:has_variation,true|array',
            'price_item_variations' => 'required_if:has_variation,true|array',
            'has_addon'=>'boolean',
            'addons_id' => 'required_if:has_addon,true|array',
            'price_addon_items' => 'required_if:has_addon,true|array',
            'price'=>'nullable|numeric|min:0|max:65535',
            'customizable'=>'boolean',
            'restaurant_menu_category_id'=>'required|numeric|exists:restaurant_menu_categories,id',
            'restaurant_id'=>'required|numeric|exists:restaurants,id',
         ]);

         $menuItemToUpdate->update([
            'name' => $request->name,
            'detail' => $request->detail,
            'has_variation' => $request->has_variation ?? false,
            'has_addon' => $request->has_addon ?? false,
            'price' => $request->price ?? 0,
            'customizable' => $request->customizable ?? false,
            'restaurant_menu_category_id' => $request->restaurant_menu_category_id,
         ]);

         if ($menuItemToUpdate->has_variation) {

            // Deleting temporarily all related variations
            RestaurantMenuItemVariation::where('restaurant_menu_item_id', $menuItem)
                                       ->delete();

            for ($i=0; $i<count($request->variations_id); $i++) { 
               
               /*
               $alreadyExist = $menuItemToUpdate->restaurantMenuItemVariations()
                                                ->wherePivot('variation_id', $request->variations_id[$i])
                                                ->count();
               */

               $existingVariation = RestaurantMenuItemVariation::withTrashed()
                                    ->where('restaurant_menu_item_id', $menuItem)
                                    ->where('variation_id', $request->variations_id[$i])
                                    ->first();

               if ($existingVariation) {
                  
               /*
                  $menuItemToUpdate->restaurantMenuItemVariations()
                                   ->wherePivot('variation_id', $request->variations_id[$i])
                                   ->update([
                                       'price' => $request->price_item_variations[$i],
                                       'restaurant_menu_item_variations.deleted_at' => NULL,
                                    ]);
               */
              
                  $existingVariation->update([
                     'price' => $request->price_item_variations[$i] ?? 0,
                     'deleted_at' => NULL,
                  ]);
                                   
               }

               else {

                  $menuItemToUpdate->restaurantMenuItemVariations()
                                   ->attach($request->variations_id[$i], [
                                       'price' => $request->price_item_variations[$i] ?? 0
                                    ]
                                 );

               }

            }

            $menuItemToUpdate->update([
               'price' => min($request->price_item_variations)
            ]);

         }

         if ($menuItemToUpdate->has_addon) {

            // Deleting temporarily all related variations
            RestaurantMenuItemAddon::where('restaurant_menu_item_id', $menuItem)
                                       ->delete();

            for ($i=0; $i<count($request->addons_id); $i++) { 
               
               /*
               $alreadyExist = $menuItemToUpdate->restaurantMenuItemAddons()
                                                ->wherePivot('addon_id', $request->addons_id[$i])
                                                ->count();
               */

               $existingAddon = RestaurantMenuItemAddon::withTrashed()
                                    ->where('restaurant_menu_item_id', $menuItem)
                                    ->where('addon_id', $request->addons_id[$i])
                                    ->first();

               if ($existingAddon) {
                  
               /*
                  $menuItemToUpdate->restaurantMenuItemAddons()
                                   ->wherePivot('addon_id', $request->addons_id[$i])
                                   ->update([
                                       'price' => $request->price_addon_items[$i],
                                       'restaurant_menu_item_addons.deleted_at' => NULL,
                                    ]);
               */
              
                  $existingAddon->update([
                     'price' => $request->price_addon_items[$i] ?? 0,
                     'deleted_at' => NULL,
                  ]);
                                   
               }

               else {

                  $menuItemToUpdate->restaurantMenuItemAddons()
                                   ->attach($request->addons_id[$i], [
                                       'price' => $request->price_addon_items[$i] ?? 0
                                    ]
                                 );

               }

            }

            $menuItemToUpdate->update([
               'price' => min($request->price_addon_items)
            ]);

         }

         return $this->showRestaurantAllMenuItems($request->restaurant_id, $perPage);
      }

      public function deleteRestaurantMenuItem($restaurant, $menuItem, $perPage)
      {
         $menuItemToDelete = RestaurantMenuItem::find($menuItem);
          
         if ($menuItemToDelete ) {

            // Variation deletion aint needed as not showing this menu-item anymore
            // RestaurantMenuItemVariation::where('restaurant_menu_item_id', $menuItem)->delete();
            $menuItemToDelete->delete();
         
         }

         return $this->showRestaurantAllMenuItems($restaurant, $perPage);
      }

      public function restoreRestaurantMenuItem($restaurant, $menuItem, $perPage)
      {
         $menuItemToRestore = RestaurantMenuItem::withTrashed()->find($menuItem);
          
         if ($menuItemToRestore ) {

            $menuItemToRestore->restore();
         
         }

         return $this->showRestaurantAllMenuItems($restaurant, $perPage);
      }

      public function searchRestaurantAllMenuItems($restaurant, $search, $perPage)
      {
         $query = RestaurantMenuCategory::with(['restaurant', 'menuCategory', 'restaurantMenuItems.restaurantMenuItemVariations', 'restaurantMenuItems.restaurantMenuItemAddons']);

         $query->where( function( $subquery )use ($search){

            $subquery->whereHas('menuCategory', function($q) use ($search){
               $q->where('name', 'like', "%$search%");
            });
            $subquery->orWhereHas('restaurantMenuItems.restaurantMenuItemVariations', function($q) use ($search){
               $q->where("name", 'like', "%$search%");
               $q->orWhere("detail", 'like', "%$search%");
               $q->orWhere("price", 'like', "%$search%");

               $q->orWhere("variation_name", 'like', "%$search%");
            });
         
         });

         $query->where('restaurant_id', $restaurant);

         return response()->json([
            'all' => $query->paginate($perPage),  
         ], 200);
      }

      public function updateRestaurantMenuItemStock(Request $request, $restaurant, $perPage)
      {
         $request->validate([
            'id'=>'required|exists:restaurant_menu_items,id',
            'item_stock'=>'required|boolean',
         ]);

         $menuItemToUpdate = RestaurantMenuItem::findOrFail($request->id);

         if ($menuItemToUpdate->restaurantMenuCategory->restaurant_id==$restaurant) {
            
            $menuItemToUpdate->update([
               'item_stock' => $request->item_stock
            ]);

         }

         return $this->showRestaurantAllMenuItems($restaurant, $perPage);
      }

      

      public function showAllRestaurantMenuCategories($perPage = false)
      {
         if ($perPage) {
            return response(Restaurant::with('restaurantMenuCategories')->paginate($perPage), 200);
         }

         return response(Restaurant::with('restaurantMenuCategories')->get(), 200);
      }

      public function searchAllRestaurantMenuCategories($search, $perPage)
      {
         $query = Restaurant::with('restaurantMenuCategories')
                            ->where('name', 'like', "%$search%")
                            ->orWhereHas('restaurantMenuCategories', function($q)use ($search){
                              $q->where('name', 'like', "%$search%");
                            });

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

            // return response(RestaurantMenuCategory::where('restaurant_id', $restaurant)->with(['restaurant', 'menuCategory', 'restaurantMenuItems'])->paginate($perPage), 200);
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
               
               $expectedRestaurant->restaurantMenuCategories()->wherePivot('menu_category_id', $restaurantNewMenuCategory)->update([
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
            'menu_category_id.0'  => Rule::unique('restaurant_menu_categories', 'menu_category_id')
                                     ->where(function($query) use ($request) {
                                       $query->where('restaurant_id', $request->restaurant_id);
                                     })
                                     ->ignore($restaurantMenuCategoryToUpdate->id),
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
          
         if ($restaurantMenuItemToDelete) {

            $restaurantMenuItemToDelete->restaurantMenuItems()->delete();
            $restaurantMenuItemToDelete->delete();
         
         }

         return $this->showRestaurantAllMenuCategories($restaurantMenuItemToDelete->restaurant_id, $perPage);
      }

      public function restoreRestaurantMenuCategory($menuCategory, $perPage)
      {
         $restaurantMenuItemToRestore = RestaurantMenuCategory::onlyTrashed()->find($menuCategory);
          
         if ($restaurantMenuItemToRestore && $restaurantMenuItemToRestore->menuCategory()->exists()) {

            $restaurantMenuItemToRestore->restore();
         
         }

         return $this->showRestaurantAllMenuCategories($restaurantMenuItemToRestore->restaurant_id, $perPage);
      }

      public function searchRestaurantAllMenuCategories($restaurant, $search, $perPage)
      {
         $query = RestaurantMenuCategory::withTrashed()
                                          ->with(['restaurant', 'menuCategory', 'restaurantMenuItems'])
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

      public function showRestaurantAllMeals($restaurant, $perPage)
      {
         return response()->json([
            'meals' => RestaurantMeal::with('meal')->where('restaurant_id', $restaurant)->paginate($perPage),  
         ], 200);
      }

      public function searchRestaurantAllMeals($restaurant, $search, $perPage)
      {
         $query = RestaurantMeal::with('meal')
                                 ->where('restaurant_id', $restaurant)
                                 ->whereHas('meal', function($q) use ($search){
                                    $q->where('name', 'like', "%$search%");
                                 });

         return response()->json([
            'all' => $query->paginate($perPage),  
         ], 200);
      }

      public function showRestaurantAllCuisines($restaurant, $perPage)
      {
         return response()->json([
            'cuisines' => RestaurantCuisine::with('cuisine')->where('restaurant_id', $restaurant)->paginate($perPage),  
         ], 200);
      }

      public function searchRestaurantAllCuisines($restaurant, $search, $perPage)
      {
         $query = RestaurantCuisine::with('cuisine')
                                    ->where('restaurant_id', $restaurant)
                                    ->whereHas('cuisine', function($q) use ($search){
                                       $q->where('name', 'like', "%$search%");
                                    });

         return response()->json([
            'all' => $query->paginate($perPage),  
         ], 200);
      }

      public function showRestaurantKitchen($restaurant)
      {
         return response()->json([
            'kitchen' => Kitchen::with('restaurant')->where('restaurant_id', $restaurant)->first(),  
         ], 200);
      }

      public function showRestaurantAllWaiters($restaurant, $perPage = false)
      {
         return response()->json([
            'waiters' => Waiter::with('restaurant')->where('restaurant_id', $restaurant)->paginate($perPage),
         ], 200);
      }

      public function searchRestaurantAllWaiters($restaurant, $search, $perPage)
      {
         $query = Waiter::with('restaurant')
                        ->where(function($q) use ($search) {
                           $q->where('first_name', 'like', "%$search%")
                              ->orWhere('last_name', 'like', "%$search%")
                              ->orWhere('user_name', 'like', "%$search%")
                              ->orWhere('mobile', 'like', "%$search%")
                              ->orWhere('email', 'like', "%$search%");
                        })
                        ->where('restaurant_id', $restaurant);                  

         return response()->json([
            'all' => $query->paginate($perPage),  
         ], 200);
      }

}
