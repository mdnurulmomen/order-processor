<?php

namespace App\Http\Controllers\Api;

use App\Models\Order;
use App\Models\Customer;
use App\Models\MyRegular;
use App\Models\Reservation;
use Illuminate\Http\Request;
use App\Models\CustomerFavourite;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\MyRegularRequest;
use App\Http\Resources\Api\MyRegularResource;
use App\Http\Resources\Api\UserOrderResource;
use App\Http\Resources\Api\MyRegularCollection;
use App\Http\Resources\Api\UserOrderCollection;
use App\Http\Resources\Api\ReservationResource;
use App\Http\Resources\Api\UserFavouriteResource;
use App\Http\Resources\Api\ReservationCollection;
use App\Http\Resources\Api\UserFavouriteCollection;
use App\Http\Resources\Api\OrderRestaurantResource;

class CustomerController extends Controller
{
    public function getUserFavourites($customer_address_id, $per_page = false)
    {	
		if ($per_page) {
            
            return new UserFavouriteCollection(CustomerFavourite::where('customer_address_id', $customer_address_id)->paginate($per_page));

        }
        else {

            return UserFavouriteResource::collection(CustomerFavourite::where('customer_address_id', $customer_address_id)->get());

        }
    }

    public function addUserFavourite(Request $request)
    {
    	$request->validate([
    		// 'lat' => 'required|string',
    		// 'lang' => 'required|string',
    		'restaurant_id' => 'required|numeric|exists:restaurants,id',
    		'customer_address_id' => 'required|numeric|exists:customer_addresses,id',
            'per_page' => 'nullable|numeric'
    		// 'id' => 'required|numeric|exists:customers,id'
    	]);

    	$existingFavourite = CustomerFavourite::where('customer_address_id', $request->customer_address_id)->where('restaurant_id', $request->restaurant_id)->exists();

    	if (! $existingFavourite) {
    		
    		CustomerFavourite::create([
    			'restaurant_id' => $request->restaurant_id,
    			'customer_address_id' => $request->customer_address_id,
    		]);

    	}

    	return $this->getUserFavourites($request->customer_address_id, $request->per_page);
    }

    public function removeUserFavourite($customer_favourite_id, $per_page = false)
    {
        $existingFavourite = CustomerFavourite::findOrFail($customer_favourite_id);
        $customer_address_id = $existingFavourite->customer_address_id;    
        $existingFavourite->delete();

        return $this->getUserFavourites($customer_address_id, $per_page);
    }

    public function getUserOrders($user, $per_page = false)
    {
        if ($per_page) {
            
            return new UserOrderCollection(
                Order::with(['asap', 'schedule', 'cutlery', 'delivery'])
                ->whereHasMorph('orderer', [ Customer::class ], 
                    function($query) use($user) {
                        $query->where('id', $user);
                    }
                )
                ->where(function ($query) {
                    $query->where('order_type', 'home-delivery')
                    ->orWhere('order_type', 'serve-on-table')
                    ->orWhere('order_type', 'take-away');
                })
                ->paginate($per_page)
            );

        }
        else {

            return UserOrderResource::collection(
                Order::with(['asap', 'schedule', 'cutlery', 'delivery'])
                ->whereHasMorph('orderer', [ Customer::class ], 
                    function($query) use($user) {
                        $query->where('id', $user);
                    }
                )
                ->where(function ($query) {
                    $query->where('order_type', 'home-delivery')
                    ->orWhere('order_type', 'serve-on-table')
                    ->orWhere('order_type', 'take-away');
                })
                ->get()
            );

        }
    }

    public function getUserReservations($user, $per_page = false)
    {
        $reservations = Reservation::with('order')
        ->whereHas('order', function ($query) use ($user) {
            $query->where('order_type', 'reservation')
            ->whereHasMorph('orderer', [ Customer::class ], 
                function($query) use($user) {
                    $query->where('id', $user);
                }
            );
        });

        if ($per_page) {
            
            return new ReservationCollection($reservations->paginate($per_page));

        }
        else {

            return ReservationResource::collection($reservations->get());

        }
    }

    public function showOrderRestaurants($order)
    {
        $expectedOrder = Order::findOrFail($order);
        return OrderRestaurantResource::collection($expectedOrder->restaurants);
    }

    public function getMyRegularItems($user, $per_page=false)
    {
        if ($per_page) {
            
            return new MyRegularCollection(MyRegular::where('customer_id', $user)->paginate($per_page));

        }

        return MyRegularResource::collection(MyRegular::where('customer_id', $user)->get());
    }

    public function setMyRegularItems(MyRegularRequest $request)
    {
        $myRegular = MyRegular::where('package', $request->package)->where('customer_id', $request->customer_id)->first();

        if ($myRegular) {
            
            $myRegular->update([
                'package'=>$request->package,
                'time'=>$request->time,
                'days'=>$request->days,
                'delivery_address_id'=>$request->delivery_address_id
            ]);

        }
        else {

            $myRegular = MyRegular::create([
                'package'=>$request->package,
                'time'=>$request->time,
                'days'=>$request->days,
                'delivery_address_id'=>$request->delivery_address_id,
                'customer_id'=>$request->customer_id,
            ]);

        }

        $request->restaurants = json_decode(json_encode($request->restaurants));

        foreach ($request->restaurants as $myRestaurantKey => $myRestaurant) {
            
            $myRegularRestaurant = $myRegular->restaurants()->create([
                'restaurant_id' => $myRestaurant->restaurant_id,
            ]);

            foreach ($myRestaurant->menu_items as $myRestaurantItemKey => $myRestaurantItem) {
                
                $myRegularItem = $myRegularRestaurant->menuItems()->create([
                    'restaurant_menu_item_id'=> $myRestaurantItem->id,
                    'quantity'=> $myRestaurantItem->quantity,
                ]);

                if ($myRegularItem->restaurantMenuItem->has_variation && ! empty($myRestaurantItem->variation)) {
                    
                    $myRegularItemVariation = $myRegularItem->variation()->create([
                        'restaurant_menu_item_variation_id'=> $myRestaurantItem->variation->id
                    ]);

                }

                if ($myRegularItem->restaurantMenuItem->has_addon && count($myRestaurantItem->addons)) {
                    
                    foreach ($myRestaurantItem->addons as $myRestaurantItemAddonkey => $myRestaurantItemAddon) {
                        
                        $myRegularItemAddon = $myRegularItem->addons()->create([
                            'restaurant_menu_item_addon_id'=> $myRestaurantItemAddon->id,
                            'quantity'=> $myRestaurantItemAddon->quantity,
                        ]);

                    }  

                }

            }

        }

        return $this->getMyRegularItems($request->customer_id);
    }

    public function updateMyRegularItems(MyRegularRequest $request, $regular)
    {
        $myRegular = MyRegular::findOrFail($regular);
     
        $myRegular->update([
            'package'=>$request->package,
            'time'=>$request->time,
            'days'=>$request->days,
            'delivery_address_id'=>$request->delivery_address_id
        ]);

        $this->deleteMyRegularRestaurants($myRegular);

        $request->restaurants = json_decode(json_encode($request->restaurants));

        foreach ($request->restaurants as $myRestaurantKey => $myRestaurant) {
            
            $myRegularRestaurant = $myRegular->restaurants()->create([
                'restaurant_id' => $myRestaurant->restaurant_id,
            ]);

            foreach ($myRestaurant->menu_items as $myRestaurantItemKey => $myRestaurantItem) {
                
                $myRegularItem = $myRegularRestaurant->menuItems()->create([
                    'restaurant_menu_item_id'=> $myRestaurantItem->id,
                    'quantity'=> $myRestaurantItem->quantity,
                ]);

                if ($myRegularItem->restaurantMenuItem->has_variation && ! empty($myRestaurantItem->variation)) {
                    
                    $myRegularItemVariation = $myRegularItem->variation()->create([
                        'restaurant_menu_item_variation_id'=> $myRestaurantItem->variation->id
                    ]);

                }

                if ($myRegularItem->restaurantMenuItem->has_addon && count($myRestaurantItem->addons)) {
                    
                    foreach ($myRestaurantItem->addons as $myRestaurantItemAddonkey => $myRestaurantItemAddon) {
                        
                        $myRegularItemAddon = $myRegularItem->addons()->create([
                            'restaurant_menu_item_addon_id'=> $myRestaurantItemAddon->id,
                            'quantity'=> $myRestaurantItemAddon->quantity,
                        ]);

                    }  

                }

            }

        }

        return $this->getMyRegularItems($request->customer_id);
    }

    public function deleteMyRegularItems($regular, $per_page = false)
    {
        $myRegular = MyRegular::findOrFail($regular);
        
        $user = $myRegular->customer;

        $this->deleteMyRegularRestaurants($myRegular);

        $myRegular->delete(); 

        return $this->getMyRegularItems($user->id, $per_page);
    }

    public function logout(Request $request)
    {
        $request->validate([
            'id'=>'required|exists:customers,id'
        ]);

        return [
            "title" => "Thank You Title",
            "preview" => "uploads/application/thanks.jpg",
            "paragraph" => "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer posuere erat."
        ];
    }

    private function deleteMyRegularRestaurants(MyRegular $myRegular)
    {
        foreach ($myRegular->restaurants as $myRestaurantKey => $myRestaurant) {
            
            foreach ($myRestaurant->menuItems as $myRestaurantItemKey => $myRestaurantItem) {
                
                $myRestaurantItem->addons()->delete();
                $myRestaurantItem->variation()->delete();
                $myRestaurantItem->delete();

            }

            $myRestaurant->delete();

        }
    }
}
