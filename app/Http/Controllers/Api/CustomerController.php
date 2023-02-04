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
use App\Http\Resources\Api\MerchantOrderResource;
use App\Http\Resources\Api\UserFavouriteCollection;

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
    		'merchant_id' => 'required|numeric|exists:merchants,id',
    		'customer_address_id' => 'required|numeric|exists:customer_addresses,id',
            'per_page' => 'nullable|numeric'
    		// 'id' => 'required|numeric|exists:customers,id'
    	]);

    	$existingFavourite = CustomerFavourite::where('customer_address_id', $request->customer_address_id)->where('merchant_id', $request->merchant_id)->exists();

    	if (! $existingFavourite) {
    		
    		CustomerFavourite::create([
    			'merchant_id' => $request->merchant_id,
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
                Order::with(['schedule', 'address'])
                ->whereHasMorph('orderer', [ Customer::class ], 
                    function($query) use($user) {
                        $query->where('id', $user);
                    }
                )
                ->where(function ($query) {
                    $query->where('type', 'delivery')
                    ->orWhere('type', 'serving')
                    ->orWhere('type', 'take-away');
                })
                ->paginate($per_page)
            );

        }
        else {

            return UserOrderResource::collection(
                Order::with(['schedule', 'address'])
                ->whereHasMorph('orderer', [ Customer::class ], 
                    function($query) use($user) {
                        $query->where('id', $user);
                    }
                )
                ->where(function ($query) {
                    $query->where('type', 'delivery')
                    ->orWhere('type', 'serving')
                    ->orWhere('type', 'take-away');
                })
                ->get()
            );

        }
    }

    public function getUserActiveOrders($user, $per_page = false)
    {
        if ($per_page) {
            
            return new UserOrderCollection(
                Order::with(['schedule', 'address'])
                ->whereHasMorph('orderer', [ Customer::class ], 
                    function($query) use($user) {
                        $query->where('id', $user);
                    }
                )
                ->where(function ($query) {
                    $query->where('type', 'delivery')
                    ->orWhere('type', 'serving')
                    ->orWhere('type', 'take-away');
                })
                ->where('in_progress', 1)
                ->paginate($per_page)
            );

        }
        else {

            return UserOrderResource::collection(
                Order::with(['schedule', 'address'])
                ->whereHasMorph('orderer', [ Customer::class ], 
                    function($query) use($user) {
                        $query->where('id', $user);
                    }
                )
                ->where(function ($query) {
                    $query->where('type', 'delivery')
                    ->orWhere('type', 'serving')
                    ->orWhere('type', 'take-away');
                })
                ->where('in_progress', 1)
                ->get()
            );

        }
    }

    public function getUserReservations($user, $per_page = false)
    {
        $reservations = Reservation::with('order')
        ->whereHas('order', function ($query) use ($user) {
            $query->where('type', 'reservation')
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

    public function showOrderMerchants($order)
    {
        $expectedOrder = Order::findOrFail($order);
        return MerchantOrderResource::collection($expectedOrder->merchants);
    }

    public function getMyRegularProducts($user, $per_page=false)
    {
        if ($per_page) {
            
            return new MyRegularCollection(MyRegular::where('customer_id', $user)->paginate($per_page));

        }

        return MyRegularResource::collection(MyRegular::where('customer_id', $user)->get());
    }

    public function setMyRegularProducts(MyRegularRequest $request)
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

        $request->merchants = json_decode(json_encode($request->merchants));

        foreach ($request->merchants as $myMerchantKey => $myMerchant) {
            
            $myRegularMerchant = $myRegular->merchants()->create([
                'merchant_id' => $myMerchant->merchant_id,
            ]);

            foreach ($myMerchant->menu_items as $myMerchantProductKey => $myMerchantProduct) {
                
                $myRegularProduct = $myRegularMerchant->products()->create([
                    'merchant_product_id'=> $myMerchantProduct->id,
                    'quantity'=> $myMerchantProduct->quantity,
                ]);

                if ($myRegularProduct->merchantProduct->has_variation && ! empty($myMerchantProduct->variation)) {
                    
                    $myRegularItemVariation = $myRegularProduct->variation()->create([
                        'merchant_product_variation_id'=> $myMerchantProduct->variation->id
                    ]);

                }

                if ($myRegularProduct->merchantProduct->has_addon && count($myMerchantProduct->addons)) {
                    
                    foreach ($myMerchantProduct->addons as $myMerchantItemAddonkey => $myMerchantProductAddon) {
                        
                        $myRegularItemAddon = $myRegularProduct->addons()->create([
                            'merchant_product_addon_id'=> $myMerchantProductAddon->id,
                            'quantity'=> $myMerchantProductAddon->quantity,
                        ]);

                    }  

                }

            }

        }

        return $this->getMyRegularProducts($request->customer_id);
    }

    public function updateMyRegularProducts(MyRegularRequest $request, $regular)
    {
        $myRegular = MyRegular::findOrFail($regular);
     
        $myRegular->update([
            'package'=>$request->package,
            'time'=>$request->time,
            'days'=>$request->days,
            'delivery_address_id'=>$request->delivery_address_id
        ]);

        $this->deleteMyRegularMerchants($myRegular);

        $request->merchants = json_decode(json_encode($request->merchants));

        foreach ($request->merchants as $myMerchantKey => $myMerchant) {
            
            $myRegularMerchant = $myRegular->merchants()->create([
                'merchant_id' => $myMerchant->merchant_id,
            ]);

            foreach ($myMerchant->menu_items as $myMerchantProductKey => $myMerchantProduct) {
                
                $myRegularProduct = $myRegularMerchant->products()->create([
                    'merchant_product_id'=> $myMerchantProduct->id,
                    'quantity'=> $myMerchantProduct->quantity,
                ]);

                if ($myRegularProduct->merchantProduct->has_variation && ! empty($myMerchantProduct->variation)) {
                    
                    $myRegularItemVariation = $myRegularProduct->variation()->create([
                        'merchant_product_variation_id'=> $myMerchantProduct->variation->id
                    ]);

                }

                if ($myRegularProduct->merchantProduct->has_addon && count($myMerchantProduct->addons)) {
                    
                    foreach ($myMerchantProduct->addons as $myMerchantItemAddonkey => $myMerchantProductAddon) {
                        
                        $myRegularItemAddon = $myRegularProduct->addons()->create([
                            'merchant_product_addon_id'=> $myMerchantProductAddon->id,
                            'quantity'=> $myMerchantProductAddon->quantity,
                        ]);

                    }  

                }

            }

        }

        return $this->getMyRegularProducts($request->customer_id);
    }

    public function deleteMyRegularItems($regular, $per_page = false)
    {
        $myRegular = MyRegular::findOrFail($regular);
        
        $user = $myRegular->customer;

        $this->deleteMyRegularMerchants($myRegular);

        $myRegular->delete(); 

        return $this->getMyRegularProducts($user->id, $per_page);
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

    private function deleteMyRegularMerchants(MyRegular $myRegular)
    {
        foreach ($myRegular->merchants as $myMerchantKey => $myMerchant) {
            
            foreach ($myMerchant->products as $myMerchantProductKey => $myMerchantProduct) {
                
                $myMerchantProduct->addons()->delete();
                $myMerchantProduct->variation()->delete();
                $myMerchantProduct->delete();

            }

            $myMerchant->delete();

        }
    }
}
