<?php

namespace App\Http\Controllers\Api;

use App\Models\Order;
use App\Models\Customer;
use Illuminate\Http\Request;
use App\Models\CustomerFavourite;
use App\Models\TableBookingDetail;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\UserOrderResource;
use App\Http\Resources\Api\UserOrderCollection;
use App\Http\Resources\Api\ReservationResource;
use App\Http\Resources\Api\UserFavouriteResource;
use App\Http\Resources\Api\ReservationCollection;
use App\Http\Resources\Api\UserFavouriteCollection;
use App\Http\Resources\Api\OrderedRestaurantResource;

class CustomerController extends Controller
{
    public function getUserFavourites($customer_address_id, $perPage = false)
    {	
		if ($perPage) {
            
            return new UserFavouriteCollection(CustomerFavourite::where('customer_address_id', $customer_address_id)->paginate($perPage));

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

    public function removeUserFavourite($customer_favourite_id, $perPage = false)
    {
        $existingFavourite = CustomerFavourite::findOrFail($customer_favourite_id);
        $customer_address_id = $existingFavourite->customer_address_id;    
        $existingFavourite->delete();

        return $this->getUserFavourites($customer_address_id, $perPage);
    }

    public function getUserOrders($user, $perPage = false)
    {
        if ($perPage) {
            
            return new UserOrderCollection(
                Order::with(['asap', 'scheduled', 'cutleryAdded', 'delivery'])
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
                ->paginate($perPage)
            );

        }
        else {

            return UserOrderResource::collection(
                Order::with(['asap', 'scheduled', 'cutleryAdded', 'delivery'])
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

    public function getUserReservations($user, $perPage = false)
    {
        $reservations = TableBookingDetail::with('order')
        ->whereHas('order', function ($query) use ($user) {
            $query->where('order_type', 'reservation')
            ->whereHasMorph('orderer', [ Customer::class ], 
                function($query) use($user) {
                    $query->where('id', $user);
                }
            );
        });

        if ($perPage) {
            
            return new ReservationCollection($reservations->paginate($perPage));

        }
        else {

            return ReservationResource::collection($reservations->get());

        }
    }

    public function showOrderedRestaurants($order)
    {
        $expectedOrder = Order::findOrFail($order);
        return OrderedRestaurantResource::collection($expectedOrder->restaurants);
    }

    public function logout(Request $request)
    {
        return [
            "title" => "Thank You Title",
            "preview" => "uploads/application/thanks.jpg",
            "paragraph" => "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer posuere erat."
        ];
    }
}
