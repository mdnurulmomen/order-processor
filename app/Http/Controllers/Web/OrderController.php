<?php

namespace App\Http\Controllers\Web;

use App\Models\Order;
use App\Models\Rider;
use App\Models\Customer;
use App\Events\UpdateRider;
use App\Events\UpdateAdmin;
use Illuminate\Http\Request;
use App\Models\RiderEvaluation;
use Illuminate\Validation\Rule;
use App\Events\UpdateRestaurant;
use App\Models\OrderedRestaurant;
use App\Models\OrderDeliveryInfo;
use App\Models\RiderDeliveryRecord;
use Illuminate\Support\Facades\Log;
use App\Models\RestaurantEvaluation;
use App\Http\Controllers\Controller;
use App\Models\RestaurantOrderRecord;

class OrderController extends Controller
{
	public function showOrderDetail($order)
	{
		return response()->json([

			'expectedOrder' => Order::with(['orderer', 'restaurants.items.restaurantMenuItem', 'restaurants.restaurant', 'restaurantAcceptances.restaurant', 'riderAssignment', 'orderReadyConfirmations.restaurant', 'riderFoodPickConfirmations.restaurant', 'riderFoodPickConfirmations.rider', 'riderDeliveryConfirmation.rider', 'waiterServeConfirmation', 'restaurantOrderCancelations.restaurant', 'payment', 'delivery.customerAddress'])->find($order),
		
		], 200);
	}

	public function showAllOrders($perPage = false)
	{
	 	if ($perPage) {
		 	
            return response()->json([

               'all' => Order::with(['restaurantAcceptances.restaurant', 'riderAssignment', 'orderReadyConfirmations.restaurant', 'riderFoodPickConfirmations.restaurant', 'riderFoodPickConfirmations.rider', 'riderDeliveryConfirmation.rider', 'waiterServeConfirmation', 'restaurantOrderCancelations.restaurant'])->latest()->paginate($perPage),

               'new' => Order::where('call_confirmation', -1)->with(['restaurantAcceptances.restaurant', 'riderAssignment', 'orderReadyConfirmations.restaurant', 'riderFoodPickConfirmations.restaurant', 'riderFoodPickConfirmations.rider', 'riderDeliveryConfirmation.rider', 'waiterServeConfirmation', 'restaurantOrderCancelations.restaurant'])->latest()->paginate($perPage),

               'deliveredOrServed' => Order::with(['restaurantAcceptances.restaurant', 'riderAssignment', 'orderReadyConfirmations.restaurant', 'riderFoodPickConfirmations.restaurant', 'riderFoodPickConfirmations.rider', 'waiterServeConfirmation', 'restaurantOrderCancelations.restaurant'])
               					->whereHas('riderDeliveryConfirmation.rider', function($q){
				   					$q->where('rider_delivery_confirmation', 1);
								})
								->orWhereHas('waiterServeConfirmation', function($q){
				   					$q->where('waiter_serve_confirmation', 1);
								})
								->latest()->paginate($perPage),				

               'cancelled' => Order::where('call_confirmation', 0)->with(['restaurantAcceptances.restaurant', 'riderAssignment', 'orderReadyConfirmations.restaurant', 'riderFoodPickConfirmations.restaurant', 'riderFoodPickConfirmations.rider', 'riderDeliveryConfirmation.rider', 'waiterServeConfirmation', 'restaurantOrderCancelations.restaurant'])
               					->orHas('restaurantOrderCancelations')
               					->orWhereHas('restaurantAcceptances', function($q){
				   					$q->where('food_order_acceptance', 0);
								})
               					->latest()->paginate($perPage),

               'prepaid' => Order::with(['restaurantAcceptances.restaurant', 'riderAssignment', 'orderReadyConfirmations.restaurant', 'riderFoodPickConfirmations.restaurant', 'riderFoodPickConfirmations.rider', 'riderDeliveryConfirmation.rider', 'waiterServeConfirmation', 'restaurantOrderCancelations.restaurant'])->has('payment')->latest()->paginate($perPage),
               			
               'postpaid' => Order::with(['restaurantAcceptances.restaurant', 'riderAssignment', 'orderReadyConfirmations.restaurant', 'riderFoodPickConfirmations.restaurant', 'riderFoodPickConfirmations.rider', 'riderDeliveryConfirmation.rider', 'waiterServeConfirmation', 'restaurantOrderCancelations.restaurant'])->doesntHave('payment')->latest()->paginate($perPage),
               
            ], 200);

	 	}

	 	// return response(Order::get(), 200);
	}

	public function confirmNewOrder(Request $request, $perPage)
	{
	 	$request->validate([
	 		'id' => 'required|exists:orders,id'
	 	]);

	 	$orderToConfirm = Order::findOrFail($request->id);
	 	$orderToConfirm->call_confirmation = 1;
	 	$orderToConfirm->save();

 		$this->makeRestaurantOrderCalls($orderToConfirm);

	 	return $this->showAllOrders($perPage);
	}

	public function cancelNewOrder(Request $request, $order, $perPage)
	{
		$request->validate([
	 		'cancelledBy' => 'required|in:Customer,Rider,Restaurants',
	 		'reason_id' => 'required_if:cancelledBy,Rider|required_if:cancelledBy,Restaurants',
	 		// 'rider_id' => 'required_if:cancelledBy,Rider',
	 		'restaurant_id' => 'required_if:cancelledBy,Restaurants',
	 	]);

		$orderToCancel = Order::findOrFail($order);	 	

		// if the order aint confirmed from customer yet
	 	if ($request->cancelledBy==='Customer' && $orderToCancel->call_confirmation===-1) {

		 	$orderToCancel->update([
		 		'call_confirmation' => 0
		 	]);

	 	}

	 	// if the restaurant actually belongs to ordered-restaurants and accepted the food request
	 	else if ($request->cancelledBy==='Restaurants' && $orderToCancel->restaurants()->where('restaurant_id', $request->restaurant_id)->exists() && $orderToCancel->restaurantAcceptances()->where('restaurant_id', $request->restaurant_id)->exists()) {

		 	// making cancelation reason
		 	$this->makeRestaurantOrderCancelationReason($orderToCancel, $request->reason_id, $request->restaurant_id);	
			// evaluating restaurant
			$this->updateRestaurantEvaluation($request->restaurant_id);

			// Broadcast to restaruant for order cancelation with OrderedRestaurant Model
			$this->notifyRestaurant($orderToCancel->restaurants()->where('restaurant_id', $request->restaurant_id)->first());

			// inform rider if any rider has been assigned
			if ($orderToCancel->riderAssignment()->exists()) {
				
				$this->notifyRider($orderToCancel->riderAssignment);

			}

	 	}

	 	// if any rider is actually assigned and the food has't been picked up yet 
	 	else if ($request->cancelledBy==='Rider' && $orderToCancel->riderAssignment()->exists() && !$orderToCancel->riderFoodPickConfirmations()->where('rider_food_pick_confirmation', 1)->exists()) {
		
		 	// reason of the cancelled order
		 	$riderOrderCancelationReason = 	$this->makeRiderDeliveryCancelationReason($orderToCancel, $request->reason_id);
		 	// cancelling accepted order-delivery
		 	$this->cancelRiderDeliveryOrder($orderToCancel);
		 	// deleting related rider food pick records
		 	$orderToCancel->riderFoodPickConfirmations()->delete();
		 	// evaluating related rider
		 	$this->makeRiderEvaluation($riderOrderCancelationReason->rider_id);

		 	// inform rider if any rider has been assigned
			$this->notifyRider($orderToCancel->riderAssignment);

		 	// Assigning another food man for the order
		 	// make rider call with RiderDeliveryRecord
			// $this->makeRiderOrderCall($orderToCancel);

	 	}

	 	return $this->showAllOrders($perPage);
	}

  	public function searchAllOrders($search, $perPage)
	{
		$query = Order::with(['orderer', 'restaurants.items.restaurantMenuItem', 'restaurants.restaurant', 'restaurantAcceptances.restaurant', 'riderAssignment', 'orderReadyConfirmations.restaurant', 'riderFoodPickConfirmations.restaurant', 'riderFoodPickConfirmations.rider', 'riderDeliveryConfirmation.rider', 'waiterServeConfirmation', 'payment', 'delivery.customerAddress'])
						->where('id', 'like', "%$search%")
						->orWhere('order_type', 'like', "%$search%")
						->orWhere('is_asap_order', 'like', "%$search%")
						->orWhere('delivery_datetime', 'like', "%$search%")
						->orWhere('order_price', 'like', "%$search%")
						->orWhere('vat', 'like', "%$search%")
						->orWhere('discount', 'like', "%$search%")
						->orWhere('delivery_fee', 'like', "%$search%")
						->orWhere('net_payable', 'like', "%$search%")
						->orWhere('payment_method', 'like', "%$search%")
						->orWhere('orderer_type', 'like', "%$search%")
						->orWhere('orderer_id', 'like', "%$search%")
						->orWhere('call_confirmation', 'like', "%$search%");

        // Retrieve comments associated to posts or videos with a title like foo%...
		$query->whereHasMorph('orderer', ['App\Models\Customer', 'App\Models\Waiter'], function($q)use ($search) {
			$q->where('user_name', 'like', "%$search%");
        	$q->orWhere('mobile', 'like', "%$search%");
        	$q->orWhere('email', 'like', "%$search%");
		});

        $query->orWhereHas('restaurants.items.restaurantMenuItem', function($q)use ($search){
        	$q->where('name', 'like', "%$search%");
        	$q->where('quantity', 'like', "%$search%");
        });

        $query->orWhereHas('restaurants.restaurant', function($q)use ($search){
        	$q->where('name', 'like', "%$search%");
        });

        $query->orWhereHas('restaurantAcceptances.restaurant', function($q)use ($search){
        	$q->where('order_id', 'like', "%$search%");
        	$q->orWhere('restaurant_id', 'like', "%$search%");
        	$q->orWhere('name', 'like', "%$search%");
        });

        $query->orWhereHas('riderAssignment', function($q)use ($search){
        	$q->where('order_id', 'like', "%$search%");
        	$q->orWhere('rider_id', 'like', "%$search%");
        });

        $query->orWhereHas('orderReadyConfirmations.restaurant', function($q)use ($search){
        	$q->where('order_id', 'like', "%$search%");
        	$q->orWhere('restaurant_id', 'like', "%$search%");
        	$q->orWhere('name', 'like', "%$search%");
        });

        $query->orWhereHas('riderFoodPickConfirmations.restaurant', function($q)use ($search){
        	$q->where('order_id', 'like', "%$search%");
        	$q->orWhere('rider_id', 'like', "%$search%");
        	$q->orWhere('restaurant_id', 'like', "%$search%");
        	$q->orWhere('name', 'like', "%$search%");
        });

        $query->orWhereHas('riderFoodPickConfirmations.rider', function($q)use ($search){
        	$q->where('user_name', 'like', "%$search%");
        });

        $query->orWhereHas('riderDeliveryConfirmation.rider', function($q)use ($search){
        	$q->where('order_id', 'like', "%$search%");
        	$q->orWhere('rider_id', 'like', "%$search%");
        });

        $query->orWhereHas('waiterServeConfirmation', function($q)use ($search){
        	$q->where('order_id', 'like', "%$search%");
        	$q->orWhere('waiter_id', 'like', "%$search%");
        	$q->orWhere('restaurant_id', 'like', "%$search%");
        });

        $query->orWhereHas('payment', function($q)use ($search){
        	$q->where('payment_id', 'like', "%$search%");
        	$q->orWhere('order_id', 'like', "%$search%");
        });

        $query->orWhereHas('delivery.customerAddress', function($q)use ($search){
        	$q->where('order_id', 'like', "%$search%");
        	$q->orWhere('additional_info', 'like', "%$search%");
        	$q->orWhere('house', 'like', "%$search%");
        	$q->orWhere('road', 'like', "%$search%");
        	$q->orWhere('additional_hint', 'like', "%$search%");
        	$q->orWhere('address_name', 'like', "%$search%");
        });

		return response()->json([

			'all' => $query->paginate($perPage),  
		
		], 200);
	}

	// Restaurant
	public function showAllRestaurantOrders($restaurant, $perPage = false)
	{
	 	if ($perPage) {
		 	
            return response()->json([

               'all' => OrderedRestaurant::where('restaurant_id', $restaurant)->with(['order.orderer', 'order.restaurants.items.restaurantMenuItem', 'order.restaurants.items.selectedItemVariation', 'order.restaurants.items.additionalOrderedAddons', 'order.restaurantAcceptances', 'order.orderReadyConfirmations', 'order.waiterServeConfirmation', 'order.restaurantOrderCancelations'])
				               			->whereHas('order', function($q){
						   					$q->where('call_confirmation', 1);
										})
				       					->latest()->paginate($perPage),

               'new' => RestaurantOrderRecord::where('restaurant_id', $restaurant)->where('food_order_acceptance', -1)->with(['order.orderer', 'order.restaurants.items.restaurantMenuItem', 'order.restaurants.items.selectedItemVariation', 'order.restaurants.items.additionalOrderedAddons', 'order.restaurantAcceptances', 'order.orderReadyConfirmations', 'order.waiterServeConfirmation', 'order.restaurantOrderCancelations'])
				               			->whereHas('order', function($q){
						   					$q->where('call_confirmation', 1);
										})
				               			->latest()->paginate($perPage),

               'served' => OrderedRestaurant::where('restaurant_id', $restaurant)->with(['order.orderer', 'order.restaurants.items.restaurantMenuItem', 'order.restaurants.items.selectedItemVariation', 'order.restaurants.items.additionalOrderedAddons', 'order.restaurantAcceptances', 'order.orderReadyConfirmations', 'order.waiterServeConfirmation', 'order.restaurantOrderCancelations'])
										->whereHas('order.waiterServeConfirmation', function($q){
						   					$q->where('waiter_serve_confirmation', 1);
										})
										->latest()->paginate($perPage),				

               'cancelled' => RestaurantOrderRecord::where('restaurant_id', $restaurant)->where('food_order_acceptance', 0)->with(['order.orderer', 'order.restaurants.items.restaurantMenuItem', 'order.restaurants.items.selectedItemVariation', 'order.restaurants.items.additionalOrderedAddons', 'order.restaurantAcceptances', 'order.orderReadyConfirmations', 'order.waiterServeConfirmation', 'order.restaurantOrderCancelations'])
               							->orWhereHas('order.restaurantOrderCancelations', function($q) use ($restaurant){
						   					$q->where('restaurant_id', $restaurant);
										})
               							->latest()->paginate($perPage),
               
            ], 200);

	 	}

	 	// return response(Order::get(), 200);
	}

	public function confirmRestaurantOrder(Request $request, $order, $perPage)
	{
	 	// validation
	 	$request->validate([
			'restaurant_id' => ['required','exists:restaurants,id',
			        function ($attribute, $value, $fail) use ($order) {
			            $restaurantExist = OrderedRestaurant::where('restaurant_id', $value)
			            									->where('order_id', $order)
			            									->exists();
			            if (!$restaurantExist) {
			                $fail('Invalid Restaurant or Order');
			            }
			        },
			        function ($attribute, $value, $fail) use ($order) {
			            $restaurantExist = RestaurantOrderRecord::where('restaurant_id', $value)
			            										->where('order_id', $order)
			            										->exists();
			            if (!$restaurantExist) {
			                $fail('Invalid Restaurant or Order');
			            }
			        },
		    ],
		    'orderReady' => 'boolean'
	 	]);

	 	$orderToConfirm = Order::findOrFail($order);

 		if ($request->orderReady) {
 			
 			$orderConfirmedOrAccepted = $this->makeRestaurantOrderReady($orderToConfirm, $request->restaurant_id);

 		}else {

 			$orderConfirmedOrAccepted = $this->makeRestaurantOrderAccepted($orderToConfirm, $request->restaurant_id);
 		}


 		// checking if order is for home-delivery, order is from customer and any rider notification is already broadcasted
		if ($orderToConfirm->order_type==='home-delivery' && $orderToConfirm->orderer instanceof Customer && !RiderDeliveryRecord::where('order_id', $order)->exists()) {

			// make rider call with RiderDeliveryRecord
			$this->makeRiderOrderCall($orderToConfirm);

		}

 		// Broadcast to admin for restaurant order ready confirmation
 		$this->notifyAdmin($orderToConfirm);

 		return $this->showAllRestaurantOrders($request->restaurant_id, $perPage);

	}

	public function cancelRestaurantOrder(Request $request, $order, $perPage)
	{
		// validation
		$request->validate([
			'reason_id' => 'required|exists:cancelation_reasons,id',
			'restaurant_id' => ['required','exists:restaurants,id',
					function ($attribute, $value, $fail) use ($order) {
						$restaurantExist = OrderedRestaurant::where('restaurant_id', $value)
															->where('order_id', $order)
															->exists();
			            if (!$restaurantExist) {
			                $fail('Invalid Restaurant or Order');
			            }
			        },
			        function ($attribute, $value, $fail) use ($order) {
			            $restaurantExist = RestaurantOrderRecord::where('restaurant_id', $value)
			            										->where('order_id', $order)
			            										->exists();
			            if (!$restaurantExist) {
			                $fail('Invalid Restaurant or Order');
			            }
			        },
			]
		]);

		$orderToCancel = Order::findOrFail($order);

		// Checking if this restaurant is in restaurant order record for this same order
		if ($orderToCancel->restaurantAcceptances()->where('restaurant_id', $request->restaurant_id)->exists()) {
			
			// cancelling just arrived order request
			$this->cancelRestaurantOrderRequest($orderToCancel, $request->restaurant_id);
			// making cancelation reason 
			$this->makeRestaurantOrderCancelationReason($orderToCancel, $request->reason_id, $request->restaurant_id);
			// evaluating restaurant
			$this->updateRestaurantEvaluation($request->restaurant_id);

			// Broadcast to admin for restaurant order cancelation
	 		$this->notifyAdmin($orderToCancel);

	 		return $this->showAllRestaurantOrders($request->restaurant_id, $perPage);
		}

		return response('Bad Request', 401);
	}

// Done

	// Rider
	public function showRiderAllOrders($rider, $perPage = false)
	{
	 	if ($perPage) {

            return response()->json([

               'all' => RiderDeliveryRecord::where('rider_id', $rider)->with(['order.orderer', 'restaurants.items.restaurantMenuItem', 'restaurants.restaurant', 'restaurantsAccepted.restaurant', 'riderOrderCancelations', 'delivery', 'riderFoodPickConfirmations', 'riderDeliveryConfirmation', 'restaurantOrderCancelations'])->latest()->paginate($perPage),
            
            ], 200);

	 	}

	 	return response(Order::get(), 200);
	}

	public function confirmRiderDeliveryOrder(Request $request, $order, $perPage)
    {
        // validation
        $request->validate([
            'orderDropped' => 'boolean',
            'orderPicked' => 'boolean',
            'orderAccepted' => 'boolean',
            'rider_id' => ['required','exists:riders,id',
                    function ($attribute, $value, $fail) use ($order) {
                        $riderExist = RiderDeliveryRecord::where('rider_id', $value)
                                                            ->where('order_id', $order)
                                                            ->exists();
                        if (!$riderExist) {
                            $fail('Invalid Rider or Order');
                        }
                    }
            ],
            'restaurant_id' => 'required_if:orderPicked,true'
        ]);

        $orderToConfirm = Order::findOrFail($order);

        // if already cancelled order
        if ($this->cancelledOrder($orderToConfirm)) {
        	
        	return $this->showRiderAllOrders($request->rider_id, $perPage);

        }

        $deliveryToConfirm = RiderDeliveryRecord::where('rider_id', $request->rider_id)->where('order_id', $order)->first();

        // if already not accepted
        if ($request->orderAccepted && $deliveryToConfirm->delivery_order_acceptance==0) {
                
            $deliveryToConfirm->update([
                'delivery_order_acceptance' => 1
            ]);

            foreach ($deliveryToConfirm->restaurantsAccepted as $restaurantOrderRecord) {
               
                $this->makeRestaurantFoodPickStatus($deliveryToConfirm, $restaurantOrderRecord);

            }

        }

        else if ($request->orderPicked && $deliveryToConfirm->delivery_order_acceptance==1) {
            
            $restaurantToPick = $deliveryToConfirm->riderFoodPickConfirmations()->where('rider_id', $request->rider_id)->where('restaurant_id', $request->restaurant_id)->first();

            if ($restaurantToPick && $restaurantToPick->rider_food_pick_confirmation==-1) {
                
                $restaurantPicked = $restaurantToPick->update([
                    'rider_food_pick_confirmation' => 1,
                ]);

                // as picked, creating delivery status if already not created
                if (!$deliveryToConfirm->riderDeliveryConfirmation()->exists()) {
                    
                    $deliveryToConfirm->riderDeliveryConfirmation()->create([
                        'rider_delivery_confirmation' => -1,
                        'rider_id' => $deliveryToConfirm->rider_id,
                    ]);

                }

            }

        }

        else if ($request->orderDropped) {

            $netRestaurantsToPick = $deliveryToConfirm->restaurants->count() - $deliveryToConfirm->restaurantOrderCancelations->count();

            $alreadyPicked = $deliveryToConfirm->riderFoodPickConfirmations()->where('rider_food_pick_confirmation', 1)->count();

            if ($netRestaurantsToPick===$alreadyPicked) {
                
                $deliveryToConfirm->riderDeliveryConfirmation()->update([
                    'rider_delivery_confirmation' => 1,
                ]);

            }

        }

        // Broadcast to admin for restaurant order ready confirmation
        $this->notifyAdmin($deliveryToConfirm->order);

        return $this->showRiderAllOrders($request->rider_id, $perPage);

    }

    private function cancelledOrder(Order $order)
    {
    	$netRestaurantInOrder = $order->restaurants->count();
    	$numberCancelledRestaurants = $order->restaurantOrderCancelations->count();

    	return $netRestaurantInOrder==$numberCancelledRestaurants ? true : false;
    }

    private function makeRiderDeliveryCancelationReason(Order $orderToCancel, $reasonId)
	{
 		return 	$orderToCancel->riderOrderCancelations()->create([
			 		'reason_id' => $reasonId,
			 		'rider_id' => $orderToCancel->riderAssignment->rider_id,
			 	]);
	}

	private function cancelRiderDeliveryOrder(Order $orderToCancel)
	{
		$orderToCancel->riderAssignment()->update([
	 		'delivery_order_acceptance' => 0
	 	]);
	}

	private function makeRiderEvaluation($rider)
	{
		$totalDeliveryRequestReceived = RiderDeliveryRecord::where('rider_id', $rider)->where('delivery_order_acceptance', 1)->count();
		$totalDeliveryRequestAchieved = RiderDeliveryRecord::where('rider_id', $rider)->count();

		$riderNewEvaluation = RiderEvaluation::updateOrCreate(
			['rider_id' => $rider],
			['order_acceptance_percentage' => ($totalDeliveryRequestReceived / $totalDeliveryRequestAchieved)*100]
		);
	}

	private function notifyAdmin(Order $order) 
	{
		Log::info('UpdateAdmin');
		event(new UpdateAdmin($order));
	}

	private function notifyRestaurant(OrderedRestaurant $orderedRestaurant)
	{
		Log::info('UpdateRestaurant');
		event(new UpdateRestaurant($orderedRestaurant));
	}

	private function notifyRider(RiderDeliveryRecord $riderNewDeliveryRecord)
	{
		Log::info('UpdateRider');
		event(new UpdateRider($riderNewDeliveryRecord));
	}

	private function makeRestaurantOrderCalls(Order $order)
	{
		// checking for order confirmation and if already has made
        if ($order->call_confirmation===1 && !$order->restaurantAcceptances()->exists()) {
           
           	foreach ($order->restaurants as $orderedRestaurant) {

              	$order->restaurantAcceptances()->create([
                 	'food_order_acceptance' => -1, // ringing
                 	'restaurant_id' => $orderedRestaurant->restaurant_id,
              	]);
              
              	// Broadcast for restaurant
              	$this->notifyRestaurant($orderedRestaurant);

           	}

        }
	}

	private function cancelRestaurantOrderRequest(Order $orderToCancel, $restaurant)
	{
		// Cancelling restaurant order acceptance for this order
		$restaurantCancelledAcceptance = $orderToCancel->restaurantAcceptances()
														->where('restaurant_id', $restaurant)
														->update([
													 		'food_order_acceptance' => 0
													 	]);
	}

	private function makeRestaurantOrderCancelationReason(Order $orderToCancel, $reason, $restaurant)
	{
		// if same restaurnat hasn't already cancelled the same order
	 	if (!$orderToCancel->restaurantOrderCancelations()->where('restaurant_id', $restaurant)->exists()) {
		 	
		 	// reason of the cancelled order
	 		$restaurantOrderCancelation = $orderToCancel->restaurantOrderCancelations()->create([
		 		'reason_id' => $reason,
		 		'restaurant_id' => $restaurant,
		 	]);
	 		
	 	}
	}

	private function updateRestaurantEvaluation($restaurant)
	{
		$totalRequestReceived = RestaurantOrderRecord::where('restaurant_id', $restaurant)->count();

		$totalRequestAccepted = RestaurantOrderRecord::where('restaurant_id', $restaurant)
													->where('food_order_acceptance', 1)
													->count();

		// Avoiding O exception
		if ($totalRequestReceived) {
			// Updating restaurant evaluation
			$restaurantEvaluated = 	RestaurantEvaluation::updateOrCreate(
													    ['restaurant_id' =>  $restaurant],
													    ['order_acceptance_percentage' => (($totalRequestAccepted/$totalRequestReceived)*100)]
													);
		}
	}

	private function makeRestaurantOrderAccepted(Order $order, $restaurant)
	{
		$restaurnatOrderToAccept = $order->restaurantAcceptances()->where('restaurant_id', $restaurant)->first();

		// if exists & not accepted yet
 		if ($restaurnatOrderToAccept && $restaurnatOrderToAccept->food_order_acceptance!=1) {

			return $restaurnatOrderToAccept->update(['food_order_acceptance' => 1]);

		}
	}

	private function makeRestaurantOrderReady(Order $order, $restaurant)
	{
		// if not already entered for this order & restaurant
		if (!$order->orderReadyConfirmations()->where('restaurant_id', $restaurant)->exists()) {
			
			return $order->orderReadyConfirmations()->create([
											 			'food_ready_confirmation' => 1,
											 			'restaurant_id' => $restaurant,
											 		]);
		}
	}

	private function makeRiderOrderCall(Order $order)
	{	
		// to calculate the nearest rider laterly
		$allNearestRiders = $this->findNearestRiders($order->delivery);

		foreach ($allNearestRiders as $rider) {
			
			$riderNewDeliveryRecord = $order->riderAssignment()->create([
				'delivery_order_acceptance' => 0,
				'rider_id' => $rider->id
			]);

			// Broadcast to Rider for order request			
			$this->notifyRider($riderNewDeliveryRecord);

			sleep(1);

			if ($order->riderAssignment()->exists()) {
				break;
			}
		}

	}

	// should calculate the nearest rider and snoozing time laterly
	private function findNearestRiders(OrderDeliveryInfo $delivery=null) 
	{
		return Rider::whereNull('current_lat')->whereNull('current_lang')->get();
	}

    private function makeRestaurantFoodPickStatus(RiderDeliveryRecord $riderDeliveryRecord, RestaurantOrderRecord $restaurantOrderRecord)
    {
    	// if not already created order-pick-progression for the same order-delivery
    	if (!$riderDeliveryRecord->riderFoodPickConfirmations()->exists()) {
    		
	    	$riderDeliveryRecord->riderFoodPickConfirmations()->create([
	            'rider_food_pick_confirmation' => -1,
	            'restaurant_id'=>$restaurantOrderRecord->restaurant_id,
	            'rider_id'=>$riderDeliveryRecord->rider_id,
	        ]);

    	}

    }
    
}
