<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\CustomerFavourite;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\UserFavouriteResource;
use App\Http\Resources\Api\UserFavouriteCollection;

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
    		'customer_address_id' => 'required|numeric|exists:customer_addresses,id'
    		// 'id' => 'required|numeric|exists:customers,id'
    	]);

    	$existingFavourite = CustomerFavourite::where('customer_address_id', $request->customer_address_id)->where('restaurant_id', $request->restaurant_id)->exists();

    	if (! $existingFavourite) {
    		
    		CustomerFavourite::create([
    			'restaurant_id' => $request->restaurant_id,
    			'customer_address_id' => $request->customer_address_id,
    		]);

    	}

    	return $this->getUserFavourites($request->customer_address_id);
    }

    public function removeUserFavourite($customer_favourite_id)
    {
        $existingFavourite = CustomerFavourite::findOrFail($customer_favourite_id);
        $customer_address_id = $existingFavourite->customer_address_id;    
        $existingFavourite->delete();

        return $this->getUserFavourites($customer_address_id);
    }
}
