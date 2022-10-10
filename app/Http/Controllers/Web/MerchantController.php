<?php

namespace App\Http\Controllers\Web;

use App\Models\Kitchen;
use App\Models\Merchant;
use App\Models\MerchantDeal;
use Illuminate\Http\Request;
use App\Models\MerchantAgent;
use App\Models\MerchantOwner;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;

class MerchantController extends Controller
{
    // Merchants
	public function showAllMerchants($perPage = false)
	{
		if ($perPage) {
         return response()->json([
            
            'all' => Merchant::withTrashed()->with(['owner', 'booking'])->latest()->paginate($perPage),

            'approved' => Merchant::where('admin_approval', 1)->with(['owner', 'booking'])->latest()->paginate($perPage),

            'nonApproved' => Merchant::where('admin_approval', 0)->with(['owner', 'booking'])->latest()->paginate($perPage),

            'trashed' => Merchant::onlyTrashed()->with(['owner', 'booking'])->latest()->paginate($perPage),
            
         ], 200);
      }

      return response(Merchant::where('admin_approval', 1)->latest()->get(), 200);
	}

   public function createNewMerchant(Request $request, $perPage)
	{
		$request->validate([
			'merchant_owner_id'=>'required|exists:merchant_owners,id',
         'name'=>'required|unique:merchants,name|string|max:255',
         'user_name'=>'required|unique:merchants,user_name|string|max:255',
         'type'=>'required|string|in:restaurant,shop',
			'mobile'=>'required|unique:merchants,mobile|max:13',
         'password'=>'required|string|min:8|max:100|confirmed',
			'website'=>'nullable|url|max:255',
			// 'lat'=>'required|unique:product_categories,name|max:255',
			// 'lng'=>'required|unique:product_categories,name|max:255',
			'address'=>'required|string|max:255',
			// 'banner_preview'=>'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
         'min_order'=>'required|numeric|min:100|max:65535',
         'max_booking'=>'required|numeric|min:0|max:1000',
         'has_delivery_support'=>'nullable|boolean',
         'delivery_charge_per_kilometer'=>'required|numeric|min:0|max:1000',
         'min_delivery_charge'=>'required|numeric|min:0|max:1000',
			'max_delivery_charge'=>'required|numeric|min:0|max:1000|gte:min_delivery_charge',
         'admin_approval'=>'nullable|boolean',
         'taking_order'=>'nullable|boolean',
         'is_sponsored'=>'nullable|boolean',
         'is_post_paid'=>'nullable|boolean',
			'has_parking'=>'nullable|boolean',
         'is_self_service'=>'nullable|boolean',
			'service_schedule'=>'required',
			'booking_break_schedule'=>'required',
		]);

      // return $request;

		$newMerchant = new Merchant();

      $newMerchant->admin_approval = $request->admin_approval ?? 0;
      $newMerchant->merchant_owner_id = $request->merchant_owner_id;
      
      $newMerchant->name = strtolower($request->name);
      $newMerchant->user_name = str_replace(' ', '', strtolower($request->user_name));
      $newMerchant->password = Hash::make($request->password);
      $newMerchant->type = strtolower($request->type);
      $newMerchant->mobile = $request->mobile;
      $newMerchant->website = $request->website;
      
      $newMerchant->lat = '23.781800';
      $newMerchant->lng = '90.415710';
      
      $newMerchant->address = strtolower($request->address);
      $newMerchant->min_order = $request->min_order;

      $newMerchant->has_delivery_support = $request->has_delivery_support ?? false;
      $newMerchant->delivery_charge_per_kilometer = $request->has_delivery_support ? $request->delivery_charge_per_kilometer : 0;
      $newMerchant->min_delivery_charge = $request->has_delivery_support ? $request->min_delivery_charge : 0;
      $newMerchant->max_delivery_charge = $request->has_delivery_support ? $request->max_delivery_charge : 0;
      // $newMerchant->max_booking = $request->max_booking;
      $newMerchant->taking_order = $request->taking_order ?? 0;
      $newMerchant->is_sponsored = $request->is_sponsored ?? 0;
      $newMerchant->is_post_paid = $request->is_post_paid ?? 0;
      $newMerchant->has_parking = $request->has_parking ?? 0;
      $newMerchant->is_self_service = $request->is_self_service ?? 0;
      $newMerchant->service_schedule = json_encode($request->service_schedule);
      $newMerchant->booking_break_schedule = json_encode($request->booking_break_schedule);
      
      $newMerchant->save();
        
      // For $this->id.jpg
      $newMerchant->banner_preview = $request->banner_preview;
      
      $newMerchant->save();

      $merchantReservation = $newMerchant->booking()->create([
         'total_seat' => $request->max_booking,
         'engaged_seat' => 0
      ]);

		return $this->showAllMerchants($perPage);
	}

   public function updateMerchant(Request $request, $merchant, $perPage)
   {
      $merchantToUpdate = Merchant::find($merchant);

      $request->validate([
         'merchant_owner_id'=>'required|exists:merchant_owners,id',
         'name'=>'required|string|max:255|unique:merchants,name,'.$merchantToUpdate->id,
         'user_name'=>'required|string|max:255|unique:merchants,user_name,'.$merchantToUpdate->id,
         'password'=>'nullable|string|min:8|max:100|confirmed',
         'type'=>'required|string|in:restaurant,shop',
         'mobile'=>'required|max:13|unique:merchants,mobile,'.$merchantToUpdate->id,
         'min_order'=>'required|numeric|min:100|max:65535',
         'max_booking'=>'required|numeric|min:0|max:1000',
         'has_delivery_support'=>'nullable|boolean',
         'delivery_charge_per_kilometer'=>'required|numeric|min:0|max:1000',
         'min_delivery_charge'=>'required|numeric|min:0|max:1000',
         'max_delivery_charge'=>'required|numeric|min:0|max:1000|gte:min_delivery_charge',
         'website'=>'nullable|url|max:255',
         // 'lat'=>'required|unique:product_categories,name|max:255',
         // 'lng'=>'required|unique:product_categories,name|max:255',
         'address'=>'required|string|max:255',
         // 'banner_preview'=>'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
         'admin_approval'=>'nullable|boolean',
         'taking_order'=>'nullable|boolean',
         'is_sponsored'=>'nullable|boolean',
         'is_post_paid'=>'required|boolean',
         'has_parking'=>'required|boolean',
         'is_self_service'=>'nullable|boolean',
         'service_schedule'=>'required',
         'booking_break_schedule'=>'required',
      ]);

      $merchantToUpdate->admin_approval = $request->admin_approval ?? 0;
      $merchantToUpdate->merchant_owner_id = $request->merchant_owner_id;
      
      $merchantToUpdate->name = strtolower($request->name);
      $merchantToUpdate->user_name = str_replace(' ', '', strtolower($request->user_name));

      if ($request->password) {       
         $merchantToUpdate->password = Hash::make($request->password);
      } 

      $merchantToUpdate->type = strtolower($request->type);
      $merchantToUpdate->mobile = $request->mobile;
      $merchantToUpdate->website = $request->website;
      
      $merchantToUpdate->lat = '23.781800';
      $merchantToUpdate->lng = '90.415710';
      
      $merchantToUpdate->address = strtolower($request->address);
      $merchantToUpdate->min_order = $request->min_order;
      $merchantToUpdate->has_delivery_support = $request->has_delivery_support ?? false;
      $merchantToUpdate->delivery_charge_per_kilometer = $request->has_delivery_support ? $request->delivery_charge_per_kilometer : 0;
      $merchantToUpdate->min_delivery_charge = $request->has_delivery_support ? $request->min_delivery_charge : 0;
      $merchantToUpdate->max_delivery_charge = $request->has_delivery_support ? $request->max_delivery_charge : 0;
      // $merchantToUpdate->max_booking = $request->max_booking;
      $merchantToUpdate->taking_order = $request->taking_order ?? 0;
      $merchantToUpdate->is_sponsored = $request->is_sponsored ?? 0;
      $merchantToUpdate->is_post_paid = $request->is_post_paid;
      $merchantToUpdate->has_parking = $request->has_parking;
      $merchantToUpdate->is_self_service = $request->is_self_service;

      $merchantToUpdate->service_schedule = json_encode($request->service_schedule);
      $merchantToUpdate->booking_break_schedule = json_encode($request->booking_break_schedule);
      
      $merchantToUpdate->banner_preview = $request->banner_preview;
      
      $merchantToUpdate->save();

      $merchantUpdatedReservation = $merchantToUpdate->booking()->updateOrCreate(
         ['merchant_id' => $merchantToUpdate->id],
         [
            'total_seat' => $request->max_booking, 
         ]
      );

      return $this->showAllMerchants($perPage);
   }

   public function deleteMerchant($merchantToDelete, $perPage)
   {
      $expectedMerchant = Merchant::find($merchantToDelete);
      
      if ($expectedMerchant) {

         $expectedMerchant->merchantMeals()->delete();
         $expectedMerchant->kitchen()->delete();
         $expectedMerchant->agents()->delete();
         $expectedMerchant->merchantCuisines()->delete();
         $expectedMerchant->merchantProductCategories()->delete();
         
         $expectedMerchant->delete();
         
      }
      
      return $this->showAllMerchants($perPage);
   }

   public function restoreMerchant($merchantToRestore, $perPage)
   {
      $expectedMerchant = Merchant::onlyTrashed()->find($merchantToRestore);
      
      if ($expectedMerchant && $expectedMerchant->owner()->exists()) {

         $expectedMerchant->merchantMeals()->restore();
         $expectedMerchant->kitchen()->restore();
         $expectedMerchant->agents()->restore();
         $expectedMerchant->merchantCuisines()->restore();
         $expectedMerchant->merchantProductCategories()->restore();

         $expectedMerchant->restore();

      }
      
      return $this->showAllMerchants($perPage);
   }

   public function searchAllMerchants($search, $perPage)
   {
      $columnsToSearch = ['name', 'mobile', 'address', 'website', 'min_order'];

      $query = Merchant::withTrashed()->with('owner');

      foreach($columnsToSearch as $column)
      {
         $query->orWhere($column, 'like', "%$search%");
      }

      return response()->json([
         'all' => $query->latest()->paginate($perPage),  
      ], 200);
   }

   // All Merchant-Admins
   public function showAllMerchantOwners($perPage = false)
   {
      if ($perPage) {
         return response()->json([
            'current' => MerchantOwner::with('merchants')->paginate($perPage),
            'trashed' => MerchantOwner::onlyTrashed()->with('merchants')->paginate($perPage),

         ], 200);
      }

      return response(MerchantOwner::get(), 200);
   }

   public function createMerchantOwner(Request $request, $perPage = false)
   {
      $request->validate([
         'user_name'=>'required|unique:merchant_owners,user_name|string|max:255',
         'email'=>'required|unique:merchant_owners,email|email|string|max:255',
         'mobile'=>'required|unique:merchant_owners,mobile|max:13',
         'password'=>'required|string|min:8|max:100|confirmed',
      ]);

      $newMerchantOwner = MerchantOwner::create([
         'user_name' => str_replace(' ', '', strtolower($request->user_name)),
         'email' => strtolower($request->email),
         'mobile' => $request->mobile,
         'password' => Hash::make($request->password)
      ]);

      return $this->showAllMerchantOwners($perPage);
   }

   public function updateMerchantOwner(Request $request, $owner, $perPage)
   {
      $merchantAdminToUpdate = MerchantOwner::find($owner);

      $request->validate([
         'user_name'=>'required|string|max:255|unique:merchant_owners,user_name,'.$merchantAdminToUpdate->id,
         'email'=>'required|email|string|max:255|unique:merchant_owners,email,'.$merchantAdminToUpdate->id,
         'mobile'=>'required|max:13|unique:merchant_owners,mobile,'.$merchantAdminToUpdate->id,
         'password'=>'nullable|string|min:8|max:100|confirmed',
      ]);

      $merchantAdminToUpdate->user_name = str_replace(' ', '', strtolower($request->user_name));        
      $merchantAdminToUpdate->email = strtolower($request->email);        
      $merchantAdminToUpdate->mobile = $request->mobile;

      if ($request->password) {       
         $merchantAdminToUpdate->password = Hash::make($request->password);
      }        

      $merchantAdminToUpdate->save();        

      return $this->showAllMerchantOwners($perPage);
   }

   public function deleteMerchantOwner($merchantAdminToDelete, $perPage)
   {
      $merchantAdminToDelete = MerchantOwner::find($merchantAdminToDelete);

      if ($merchantAdminToDelete) {

         foreach ($merchantAdminToDelete->merchants as $merchant) {
            $this->deleteMerchant($merchant->id, $perPage);
         }

         $merchantAdminToDelete->delete();

      }

      return $this->showAllMerchantOwners($perPage);
   }

   public function restoreMerchantOwner($merchantAdminToRestore, $perPage)
   {
      $merchantAdminToStore = MerchantOwner::onlyTrashed()->find($merchantAdminToRestore);

      if ($merchantAdminToStore) {
         
         /*
         foreach ($merchantAdminToStore->merchants()->withTrashed()->get() as $merchant) {
            $this->restoreMerchant($merchant->id, $perPage);
         }
         */

         $merchantAdminToStore->restore();

      }
         
      return $this->showAllMerchantOwners($perPage);
   }

   public function searchAllMerchantOwners($search, $perPage)
   {
      $columnsToSearch = ['user_name', 'mobile', 'email'];

      $query = MerchantOwner::withTrashed()->with('merchants');

      foreach($columnsToSearch as $column)
      {
         $query->orWhere($column, 'like', "%$search%");
      }

      return response()->json([
         'all' => $query->paginate($perPage),  
      ], 200);
   }

   // All Merchant-Kitchens
   public function showAllMerchantKitchens($perPage = false)
   {
      if ($perPage) {
         return response()->json([
            'current' => Kitchen::with('merchant')->paginate($perPage),

            'trashed' => Kitchen::onlyTrashed()->with('merchant')->paginate($perPage),

         ], 200);
      }

      //return response(Kitchen::get(), 200);
   }

   public function createMerchantKitchen(Request $request, $perPage = false)
   {
      $request->validate([
         'user_name'=>'required|string|max:255|unique:kitchens,user_name',
         'mobile'=>'required|unique:kitchens,mobile|max:13',
         'email'=>'required|unique:kitchens,email|email|string|max:255',
         'password'=>'required|string|min:8|max:100|confirmed',
         'merchant_id'=>'numeric|required|exists:merchants,id',
         'admin_approval'=>'nullable|boolean',
      ]);

      $newMerchantOwner = Kitchen::create([
         'user_name' => str_replace(' ', '', strtolower($request->user_name)),
         'mobile' => $request->mobile,
         'email' => strtolower($request->email),
         'password' => Hash::make($request->password),
         'merchant_id' => $request->merchant_id,
         'admin_approval' => $request->admin_approval ?? false,
      ]);

      return $this->showAllMerchantKitchens($perPage);
   }

   public function updateMerchantKitchen(Request $request, $kitchenToUpdate, $perPage)
   {
      $merchantKitchenToUpdate = Kitchen::find($kitchenToUpdate);

      $request->validate([
         'user_name'=>'required|string|max:255|unique:kitchens,user_name,'.$merchantKitchenToUpdate->id,
         'mobile'=>'required|max:13|unique:kitchens,mobile,'.$merchantKitchenToUpdate->id,
         'email'=>'required|email|string|max:255|unique:kitchens,email,'.$merchantKitchenToUpdate->id,
         'password'=>'nullable|string|min:8|max:100|confirmed',
         'merchant_id'=>'numeric|required|exists:merchants,id',
         'admin_approval'=>'nullable|boolean',
      ]);

      $merchantKitchenToUpdate->user_name = str_replace(' ', '', strtolower($request->user_name));        
      $merchantKitchenToUpdate->mobile = $request->mobile;
      $merchantKitchenToUpdate->email = strtolower($request->email);        

      if ($request->password) {       
         $merchantKitchenToUpdate->password = Hash::make($request->password);
      }        

      $merchantKitchenToUpdate->merchant_id = $request->merchant_id;        
      $merchantKitchenToUpdate->admin_approval = $request->admin_approval ?? false;        
      $merchantKitchenToUpdate->save();        

      return $this->showAllMerchantKitchens($perPage);
   }

   public function deleteMerchantKitchen($kitchenToDelete, $perPage)
   {
      $merchantKitchenToDelete = Kitchen::find($kitchenToDelete);

      if ($merchantKitchenToDelete) {
         
         $merchantKitchenToDelete->delete();
         
      }

      return $this->showAllMerchantKitchens($perPage);
   }

   public function restoreMerchantKitchen($kitchenToRestore, $perPage)
   {
      $merchantKitchenToRestore = Kitchen::onlyTrashed()->find($kitchenToRestore);

      if ($merchantKitchenToRestore && $merchantKitchenToRestore->merchant()->exists()) {
         
         $merchantKitchenToRestore->restore();

      }

      return $this->showAllMerchantKitchens($perPage);
   }

   public function searchAllMerchantKitchens($search, $perPage)
   {
      $columnsToSearch = ['user_name', 'mobile', 'email'];

      $query = Kitchen::with('merchant')->withTrashed();

      foreach($columnsToSearch as $column)
      {
         $query->orWhere($column, 'like', "%$search%");
      }
      
      $query->orWhereHas('merchant', function($q)use ($search){
         $q->where('name', 'like', "%$search%");
      });

      return response()->json([
         'all' => $query->paginate($perPage),  
      ], 200);
   }

   // Merchant-Kitchen
   public function showMerchantKitchen($merchant)
   {
      return response()->json([
         'kitchen' => Kitchen::with('merchant')->where('merchant_id', $merchant)->first(),  
      ], 200);
   }

   // All Merchant-Agents
   public function showAllMerchantAgents($perPage = false)
   {
      if ($perPage) {
         return response()->json([
            'current' => MerchantAgent::with('merchant')->paginate($perPage),
            'trashed' => MerchantAgent::onlyTrashed()->with('merchant')->paginate($perPage),

         ], 200);
      }

      //return response(MerchantAgent::get(), 200);
   }

   public function createMerchantAgent(Request $request, $perPage = false)
   {
      $request->validate([
         'first_name'=>'nullable|string|max:255',
         'last_name'=>'nullable|string|max:255',
         'user_name'=>'required|string|max:255|unique:merchant_agents,user_name',
         'mobile'=>'required|unique:merchant_agents,mobile|max:13',
         'email'=>'required|unique:merchant_agents,email|email|string|max:255',
         'password'=>'required|string|min:8|max:100|confirmed',
         'merchant_id'=>'numeric|required|exists:merchants,id',
         'admin_approval'=>'nullable|boolean',
      ]);

      $newMerchantOwner = MerchantAgent::create([
         'first_name' => strtolower($request->first_name),
         'last_name' => strtolower($request->last_name),
         'user_name' => str_replace(' ', '', strtolower($request->user_name)),
         'mobile' => $request->mobile,
         'email' => strtolower($request->email),
         'password' => Hash::make($request->password),
         'merchant_id' => $request->merchant_id,
         'admin_approval' => $request->admin_approval ?? false,
      ]);

      return $this->showAllMerchantAgents($perPage);
   }

   public function updateMerchantAgent(Request $request, $waiterToUpdate, $perPage)
   {
      $merchantAgentToUpdate = MerchantAgent::find($waiterToUpdate);

      $request->validate([
         'first_name'=>'nullable|string|max:255',
         'last_name'=>'nullable|string|max:255',
         'user_name'=>'required|string|max:255|unique:merchant_agents,user_name,'.$merchantAgentToUpdate->id,
         'mobile'=>'required|max:13|unique:merchant_agents,mobile,'.$merchantAgentToUpdate->id,
         'email'=>'required|email|string|max:255|unique:merchant_agents,email,'.$merchantAgentToUpdate->id,
         'password'=>'nullable|string|min:8|max:100|confirmed',
         'merchant_id'=>'numeric|required|exists:merchants,id',
         'admin_approval'=>'nullable|boolean',
      ]);

      $merchantAgentToUpdate->first_name = strtolower($request->first_name);      
      $merchantAgentToUpdate->last_name = strtolower($request->last_name);      
      $merchantAgentToUpdate->user_name = str_replace(' ', '', strtolower($request->user_name));      
      $merchantAgentToUpdate->mobile = $request->mobile;
      $merchantAgentToUpdate->email = strtolower($request->email);        

      if ($request->password) {       
         $merchantAgentToUpdate->password = Hash::make($request->password);
      }        

      $merchantAgentToUpdate->merchant_id = $request->merchant_id;        
      $merchantAgentToUpdate->admin_approval = $request->admin_approval ?? false;        
      $merchantAgentToUpdate->save();        

      return $this->showAllMerchantAgents($perPage);
   }

   public function deleteMerchantAgent($waiterToDelete, $perPage)
   {
      $merchantAgentToDelete = MerchantAgent::find($waiterToDelete);

      if ($merchantAgentToDelete) {
         
         $merchantAgentToDelete->delete();
         
      }

      return $this->showAllMerchantAgents($perPage);
   }

   public function restoreMerchantAgent($waiterToRestore, $perPage)
   {
      $merchantAgentToRestore = MerchantAgent::onlyTrashed()->find($waiterToRestore);

      if ($merchantAgentToRestore && $merchantAgentToRestore->merchant()->exists()) {
         
         $merchantAgentToRestore->restore();

      }

      return $this->showAllMerchantAgents($perPage);
   }

   public function searchAllMerchantAgents($search, $perPage)
   {
      $columnsToSearch = ['first_name', 'last_name', 'user_name', 'mobile', 'email'];

      $query = MerchantAgent::with('merchant')->withTrashed();

      foreach($columnsToSearch as $column)
      {
         $query->orWhere($column, 'like', "%$search%");
      }
      
      $query->orWhereHas('merchant', function($q)use ($search){
         $q->where('name', 'like', "%$search%");
      });

      return response()->json([
         'all' => $query->paginate($perPage),  
      ], 200);
   }

   // Merchant-All-Agent
   public function showMerchantAllAgents($merchant, $perPage = false)
   {
      return response()->json([
         'agents' => MerchantAgent::with('merchant')->where('merchant_id', $merchant)->paginate($perPage),
      ], 200);
   }

   public function searchMerchantAllAgents($merchant, $search, $perPage)
   {
      $query = MerchantAgent::with('merchant')
                     ->where(function($q) use ($search) {
                        $q->where('first_name', 'like', "%$search%")
                           ->orWhere('last_name', 'like', "%$search%")
                           ->orWhere('user_name', 'like', "%$search%")
                           ->orWhere('mobile', 'like', "%$search%")
                           ->orWhere('email', 'like', "%$search%");
                     })
                     ->where('merchant_id', $merchant);                  

      return response()->json([

         'all' => $query->paginate($perPage),  
         
      ], 200);
   }

   // All Merchant-Deals
   public function showAllMerchantDeals($perPage = false)
   {
      if ($perPage) {
         
         return response()->json([
            
            'current' => MerchantDeal::with('merchant')->paginate($perPage),

            'trashed' => MerchantDeal::onlyTrashed()->with('merchant')->paginate($perPage),

         ], 200);

      }

      return response(MerchantDeal::with('merchant')->get(), 200);
   }

   public function createMerchantDeal(Request $request, $perPage = false)
   {
      $request->validate([
         'sale_percentage'=>'numeric|min:0|max:100',
         'promotional_discount'=>'numeric|min:0|max:100',
         'native_discount'=>'numeric|min:0|max:100',
         'net_discount'=>'required|numeric',
         'delivery_fee_addition'=>'nullable|boolean',
         'merchant_id'=>'required|numeric|exists:merchants,id',
      ]);

      MerchantDeal::updateOrCreate(
         [
            'merchant_id' => $request->merchant_id
         ], 
         [
            'sale_percentage' => $request->sale_percentage,
            'promotional_discount' => $request->promotional_discount,
            'native_discount' => $request->native_discount,
            'net_discount' => $request->net_discount,
            'delivery_fee_addition' => $request->delivery_fee_addition
         ]
      );

      return $this->showAllMerchantDeals($perPage);
   }

   public function updateMerchantDeal(Request $request, $merchantDeal, $perPage)
   {
      $request->validate([
         'sale_percentage'=>'numeric|min:0|max:100',
         'promotional_discount'=>'numeric|min:0|max:100',
         'native_discount'=>'numeric|min:0|max:100',
         'net_discount'=>'required|numeric',
         'delivery_fee_addition'=>'nullable|boolean',
         'merchant_id'=>'required|numeric|exists:merchants,id',
      ]);

      MerchantDeal::updateOrCreate(
         [
            'merchant_id' => $request->merchant_id
         ], 
         [
            'sale_percentage' => $request->sale_percentage,
            'promotional_discount' => $request->promotional_discount,
            'native_discount' => $request->native_discount,
            'net_discount' => $request->net_discount,
            'delivery_fee_addition' => $request->delivery_fee_addition
         ]
      );        

      return $this->showAllMerchantDeals($perPage);
   }

   public function searchAllMerchantDeals($search, $perPage)
   {
      $query = Merchant::withTrashed()->with('deal');

      $query->where('name', 'like', "%$search%");

      $query->orWhereHas('deal', function($q) use ($search){

         $q->where('sale_percentage', 'like', "%$search%")
         ->orWhere('promotional_discount', 'like', "%$search%")
         ->orWhere('native_discount', 'like', "%$search%"); 

      });
      
      $query->orWhereHas('deal', function($q) use ($search){
         $q->where('rate', 'like', "%$search%");
      });

      return response()->json([
         'all' => $query->paginate($perPage),  
      ], 200);
   }

}
