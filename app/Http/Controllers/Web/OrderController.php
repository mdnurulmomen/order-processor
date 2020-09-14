<?php

namespace App\Http\Controllers\Web;

use App\Models\Order;
use App\Models\Rider;
use App\Models\Customer;
use App\Models\Restaurant;
use App\Jobs\NotifyRiders;
use App\Events\UpdateRider;
use App\Events\UpdateAdmin;
use Illuminate\Http\Request;
use App\Events\UpdateWaiters;
use App\Models\RiderEvaluation;
use App\Events\UpdateRestaurant;
use App\Models\OrderedRestaurant;
use App\Models\OrderDeliveryInfo;
use App\Models\RiderDeliveryRecord;
use App\Models\RestaurantEvaluation;
use App\Http\Controllers\Controller;
use App\Models\RestaurantOrderRecord;

class OrderController extends Controller
{
	public function showOrderDetail($order)
	{
		return response()->json([

			'expectedOrder' => Order::with(['orderer', 'asap', 'scheduled', 'cutleryAdded', 'restaurants.items.restaurantMenuItem', 'restaurants.items.selectedItemVariation.restaurantMenuItemVariation.variation', 'restaurants.items.additionalOrderedAddons.restaurantMenuItemAddon.addon', 'restaurants.restaurant', 'restaurantAcceptances.restaurant', 'riderAssignment', 'orderReadyConfirmations.restaurant', 'riderFoodPickConfirmations.restaurant', 'riderFoodPickConfirmations.rider', 'riderDeliveryConfirmation.rider', 'orderServeConfirmation', 'restaurantOrderCancelations.restaurant', 'payment', 'delivery.customerAddress'])->find($order),
		
		], 200);
	}

	public function showAllOrders($perPage = false)
	{
	 	if ($perPage) {
		 	
            return response()->json([

               'all' => Order::with(['restaurantAcceptances.restaurant', 'riderAssignment', 'orderReadyConfirmations.restaurant', 'riderFoodPickConfirmations.restaurant', 'riderDeliveryConfirmation', 'orderServeConfirmation', 'restaurantOrderCancelations.restaurant'])->latest()->paginate($perPage),

               'new' => Order::where('customer_confirmation', -1)->with(['restaurantAcceptances.restaurant', 'riderAssignment', 'orderReadyConfirmations.restaurant', 'riderFoodPickConfirmations.restaurant', 'riderDeliveryConfirmation', 'orderServeConfirmation', 'restaurantOrderCancelations.restaurant'])->latest()->paginate($perPage),

               'deliveredOrServed' => Order::with(['restaurantAcceptances.restaurant', 'riderAssignment', 'orderReadyConfirmations.restaurant', 'riderFoodPickConfirmations.restaurant', 'orderServeConfirmation', 'restaurantOrderCancelations.restaurant'])
               					->whereHas('riderDeliveryConfirmation', function($q){
				   					$q->where('rider_delivery_confirmation', 1);
								})
								->orWhereHas('orderServeConfirmation', function($q){
				   					$q->where('food_serve_confirmation', 1);
								})
								->latest()->paginate($perPage),				

               'cancelled' => Order::where('customer_confirmation', 0)->with(['restaurantAcceptances.restaurant', 'riderAssignment', 'orderReadyConfirmations.restaurant', 'riderFoodPickConfirmations.restaurant', 'riderDeliveryConfirmation', 'orderServeConfirmation', 'restaurantOrderCancelations.restaurant'])
               					->orHas('restaurantOrderCancelations')
               					->orWhereHas('restaurantAcceptances', function($q){
				   					$q->where('food_order_acceptance', 0);
								})
               					->latest()->paginate($perPage),

               'prepaid' => Order::with(['restaurantAcceptances.restaurant', 'riderAssignment', 'orderReadyConfirmations.restaurant', 'riderFoodPickConfirmations.restaurant', 'riderDeliveryConfirmation', 'orderServeConfirmation', 'restaurantOrderCancelations.restaurant'])->has('payment')->latest()->paginate($perPage),
               			
               'postpaid' => Order::with(['restaurantAcceptances.restaurant', 'riderAssignment', 'orderReadyConfirmations.restaurant', 'riderFoodPickConfirmations.restaurant', 'riderDeliveryConfirmation', 'orderServeConfirmation', 'restaurantOrderCancelations.restaurant'])->doesntHave('payment')->latest()->paginate($perPage),
               
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
	 	
	 	if ($orderToConfirm->customer_confirmation) {			// -1

		 	$this->confirmCustomerOrderRequest($orderToConfirm);
	 		$this->makeRestaurantOrderCalls($orderToConfirm);

	 	}

	 	return $this->showAllOrders($perPage);
	}

	public function cancelNewOrder(Request $request, $order, $perPage)
	{
		$request->validate([
	 		'cancelledBy' => 'required|in:customer,rider,restaurants',
	 		'reason_id' => 'required',
	 		'restaurant_id' => 'required_if:cancelledBy,restaurants',
	 		// 'rider_id' => 'required_if:cancelledBy,rider',
	 	]);

		$orderToCancel = Order::findOrFail($order);	 	

		if ($orderToCancel->customerOrderCancelation()->exists() || $orderToCancel->restaurantOrderCancelations()->where('restaurant_id', $request->restaurant_id)->exists()) {
			
			return $this->showAllOrders($perPage);

		}

		// if the order aint confirmed from customer yet
	 	else if ($request->cancelledBy==='customer' && $orderToCancel->customer_confirmation===-1) {
		 	
		 	// cancelling the order
	 		$this->cancelCustomerOrderRequest($orderToCancel);
		 	
		 	// making cancelation reason
		 	$this->makeCustomerOrderCancelationReason($orderToCancel, $request->reason_id);

	 	}

	 	// if the restaurant actually belongs to ordered-restaurants and accepted the food request
	 	else if ($request->cancelledBy==='restaurants' && $orderToCancel->restaurants()->where('restaurant_id', $request->restaurant_id)->exists() && $orderToCancel->restaurantAcceptances()->where('restaurant_id', $request->restaurant_id)->exists()) {

		 	// making cancelation reason
		 	$this->makeRestaurantOrderCancelationReason($orderToCancel, $request->reason_id, $request->restaurant_id);

			// evaluating restaurant
			$this->updateRestaurantEvaluation($request->restaurant_id);

			// Broadcast to restaruant for order cancelation with OrderedRestaurant Model
			$this->notifyRestaurant($orderToCancel->restaurants()->where('restaurant_id', $request->restaurant_id)->first());

			// inform rider on restaurant-cancelation if any rider has been assigned
			if ($orderToCancel->riderAssignment()->exists()) {
				
				$this->notifyRider($orderToCancel->riderAssignment);

			}

	 	}

	 	// if any rider is actually assigned and the food has't been picked up yet 
	 	else if ($request->cancelledBy==='rider' && $orderToCancel->riderAssignment()->exists() && !$orderToCancel->riderFoodPickConfirmations()->where('rider_food_pick_confirmation', 1)->exists()) {
		
		 	// retreiving the expected RiderDeliveryOrder
		 	$riderDeliveryOrderToCancel = $orderToCancel->riderAssignment;

		 	// cancelling accepted order-delivery
		 	$this->cancelRiderDeliveryOrder($orderToCancel);
		 	
		 	// deleting related rider food pick records
		 	$orderToCancel->riderFoodPickConfirmations()->delete();

		 	// reason of the cancelled order
		 	$riderOrderCancelationReason = $this->makeRiderDeliveryCancelationReason($orderToCancel, $request->reason_id);
		 	
		 	// evaluating related rider
		 	$this->makeRiderEvaluation($riderOrderCancelationReason->rider_id);

		 	// inform rider about delivery-order cancelation
			$this->notifyRider($riderDeliveryOrderToCancel);

		 	// making rider call to assign another food man for the order
			$this->makeRiderOrderCall($orderToCancel, $riderDeliveryOrderToCancel->rider_id);

	 	}

	 	return $this->showAllOrders($perPage);
	}

  	public function searchAllOrders($search, $perPage)
	{	
		$query = Order::with(['restaurantAcceptances.restaurant', 'riderAssignment', 'orderReadyConfirmations.restaurant', 'riderFoodPickConfirmations.restaurant', 'riderDeliveryConfirmation', 'orderServeConfirmation', 'restaurantOrderCancelations.restaurant'])
						->where('id', 'like', "%$search%")
						->orWhere('order_type', 'like', "%$search%")
						->orWhere('order_price', 'like', "%$search%")
						->orWhere('vat', 'like', "%$search%")
						->orWhere('discount', 'like', "%$search%")
						->orWhere('delivery_fee', 'like', "%$search%")
						->orWhere('net_payable', 'like', "%$search%")
						->orWhere('payment_method', 'like', "%$search%")
						->orWhere('orderer_type', 'like', "%$search%")
						->orWhere('orderer_id', 'like', "%$search%")
						->orWhere('customer_confirmation', 'like', "%$search%");

        // Retrieve comments associated to posts or videos with a title like foo%...
		$query->whereHasMorph('orderer', ['App\Models\Customer', 'App\Models\Waiter'], function($q)use ($search) {
			$q->where('first_name', 'like', "%$search%");
			$q->orWhere('last_name', 'like', "%$search%");
			$q->orWhere('user_name', 'like', "%$search%");
        	$q->orWhere('mobile', 'like', "%$search%");
        	$q->orWhere('email', 'like', "%$search%");
		});

        $query->orWhereHas('restaurants.items.restaurantMenuItem', function($q)use ($search){
        	$q->where('name', 'like', "%$search%");
        	$q->orWhere('quantity', 'like', "%$search%");
        });

        $query->orWhereHas('restaurants.restaurant', function($q)use ($search){
        	$q->where('order_id', 'like', "%$search%");
        	$q->orWhere('restaurant_id', 'like', "%$search%");
        	$q->orWhere('name', 'like', "%$search%");
        });

        $query->orWhereHas('riderAssignment.rider', function($q)use ($search){
        	$q->where('rider_id', 'like', "%$search%");
        	$q->orWhere('user_name', 'like', "%$search%");
        });

        $query->orWhereHas('orderServeConfirmation', function($q)use ($search){
        	$q->where('confirmer_id', 'like', "%$search%");
        });

        $query->orWhereHas('payment', function($q)use ($search){
        	$q->where('payment_id', 'like', "%$search%");
        });

        $query->orWhereHas('delivery.customerAddress', function($q)use ($search){
        	$q->where('additional_info', 'like', "%$search%");
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

               'all' => OrderedRestaurant::where('restaurant_id', $restaurant)->with(['items.restaurantMenuItem', 'items.selectedItemVariation.restaurantMenuItemVariation.variation', 'items.additionalOrderedAddons.restaurantMenuItemAddon.addon', 'order.orderer', 'order.asap', 'order.scheduled', 'order.cutleryAdded', 'order.restaurantAcceptances', 'order.orderReadyConfirmations', 'order.orderServeConfirmation', 'order.restaurantOrderCancelations'])
				               			->whereHas('order', function($q){
						   					$q->where('customer_confirmation', 1)
						   					  ->orWhere('order_type', 'reservation');
										})
				       					->latest()->paginate($perPage),

               'served' => OrderedRestaurant::where('restaurant_id', $restaurant)->with(['items.restaurantMenuItem', 'items.selectedItemVariation.restaurantMenuItemVariation.variation', 'items.additionalOrderedAddons.restaurantMenuItemAddon.addon', 'order.orderer', 'order.asap', 'order.scheduled', 'order.cutleryAdded', 'order.restaurantAcceptances', 'order.orderReadyConfirmations', 'order.orderServeConfirmation', 'order.restaurantOrderCancelations'])
										->whereHas('order.orderServeConfirmation', function($q){
						   					$q->where('food_serve_confirmation', 1);
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
		    'orderReady' => 'boolean',
		    'serveOrder' => 'boolean'
	 	]);

	 	$orderToConfirm = Order::findOrFail($order);

	 	if (!$this->confirmedOrder($orderToConfirm)) {
	 		
	 		return $this->showAllRestaurantOrders($request->restaurant_id, $perPage);

	 	}

 		else if ($request->serveOrder) {

 			$orderServed = $this->makeRestaurantOrderServed($orderToConfirm, 'App\Models\Restaurant', $request->restaurant_id);
 			$orderConfirmed = $this->makeRestaurantOrderReady($orderToConfirm, $request->restaurant_id);
 			$orderAccepted = $this->makeRestaurantOrderAccepted($orderToConfirm, $request->restaurant_id);

 		}
 		else if ($request->orderReady) {
 			
 			$orderConfirmed = $this->makeRestaurantOrderReady($orderToConfirm, $request->restaurant_id);
 			$orderAccepted = $this->makeRestaurantOrderAccepted($orderToConfirm, $request->restaurant_id);

 		}
 		else {

 			$orderAccepted = $this->makeRestaurantOrderAccepted($orderToConfirm, $request->restaurant_id);
 		
 		}

 		// checking if order is for home-delivery, order is from customer and any rider notification is already broadcasted
		if ($orderToConfirm->order_type==='home-delivery' && $orderToConfirm->orderer instanceof Customer) {

			if (!$orderToConfirm->riderCall()->exists()) {

				// make rider call with RiderDeliveryRecord
				$this->makeRiderOrderCall($orderToConfirm);

			}
			else if($orderToConfirm->riderAssignment()->exists()) {

				// adding new restaurant to pick up for assigned rider
				$this->makeRestaurantFoodPickStatus($orderToConfirm->riderAssignment);

				// notifying rider about updation
				$this->notifyRider($orderToConfirm->riderAssignment);
			}

		}

		// checking if order is for serve-on-table and restaurant has any approved waiter
		else if ($orderToConfirm->order_type==='serve-on-table' || $orderToConfirm->order_type==='reservation') {

			// make waiter call with RestaurantOrderRecord
			$this->notifyRestaurantWaiters($orderAccepted, $request->restaurant_id);

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
			            										->where('food_order_acceptance', -1)
			            										->exists();
			            if (!$restaurantExist) {
			                $fail('Invalid Restaurant or Order');
			            }
			        },
			]
		]);

		$orderToCancel = Order::findOrFail($order);

		// Checking if this restaurant is in restaurant order record for this same order
		// if ($orderToCancel->restaurantAcceptances()->where('restaurant_id', $request->restaurant_id)->exists()) {
			
			// cancelling just arrived order request
			$this->cancelRestaurantOrderRequest($orderToCancel, $request->restaurant_id);

			// making cancelation reason 
			$this->makeRestaurantOrderCancelationReason($orderToCancel, $request->reason_id, $request->restaurant_id);
			
			// evaluating restaurant
			$this->updateRestaurantEvaluation($request->restaurant_id);

			// Broadcast to admin for restaurant order cancelation
	 		$this->notifyAdmin($orderToCancel);

	 		return $this->showAllRestaurantOrders($request->restaurant_id, $perPage);
		
		// }

		return response('Bad Request', 401);
	}
	

	// Rider
	public function showRiderAllOrders($rider, $perPage = false)
	{
	 	if ($perPage) {

            return response()->json([

               'all' => RiderDeliveryRecord::where('rider_id', $rider)->with(['order.orderer', 'order.asap', 'order.scheduled', 'order.cutleryAdded', 'restaurants.items.restaurantMenuItem', 'restaurants.items.selectedItemVariation.restaurantMenuItemVariation.variation', 'restaurants.items.additionalOrderedAddons.restaurantMenuItemAddon.addon', 'restaurants.restaurant', 'restaurantsAccepted.restaurant', 'riderOrderCancelations', 'riderFoodPickConfirmations', 'riderDeliveryConfirmation', 'restaurantOrderCancelations'])->latest()->paginate($perPage),
            
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
        $deliveryToConfirm = RiderDeliveryRecord::where('rider_id', $request->rider_id)->where('order_id', $order)->first();

        // if already cancelled order or delivery-order is more than 30 seconds ago 
        if ($this->cancelledOrder($orderToConfirm)) {
        	
        	return $this->showRiderAllOrders($request->rider_id, $perPage);

        }

        // if already not accepted
        if ($request->orderAccepted && $deliveryToConfirm->delivery_order_acceptance==0 && !$this->timeOutDeliveryOrder($deliveryToConfirm)) {
                
            // accepting rider-delivery-order
            $deliveryToConfirm->update([
                'delivery_order_acceptance' => 1
            ]);

            // Nullifying rider paused-time 
            $deliveryToConfirm->rider()->update([
            	'paused_at' => NULL
            ]);

            $this->makeRestaurantFoodPickStatus($orderToConfirm->riderAssignment);

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

        else if ($request->orderDropped && $this->allOrderPicked($deliveryToConfirm) && $deliveryToConfirm->delivery_order_acceptance==1) {
                
            $deliveryToConfirm->riderDeliveryConfirmation()->update([
                'rider_delivery_confirmation' => 1,
            ]);

        }

        // Broadcast to admin for restaurant order ready confirmation
        $this->notifyAdmin($deliveryToConfirm->order);

        return $this->showRiderAllOrders($request->rider_id, $perPage);

    }


    // Waiter
	public function showWaiterAllOrders($restaurant, $perPage = false)
	{
	 	if ($perPage) {

            return response()->json([

				'all' => RestaurantOrderRecord::with([
							'orderedRestaurants' => function($orderedRestaurant) use ($restaurant) {
						    	$orderedRestaurant->where('restaurant_id', $restaurant)->with(['items.selectedItemVariation.restaurantMenuItemVariation.variation', 'items.additionalOrderedAddons.restaurantMenuItemAddon.addon', 'items.restaurantMenuItem']);
							}
						])
						->with([
							'orderCancellationReasons' => function($orderCancellationReason) use ($restaurant) {
						    	$orderCancellationReason->where('restaurant_id', $restaurant);
							}
						])
						->with([
							'orderReadyConfirmations' => function($orderReadyConfirmation) use ($restaurant) {
						    	$orderReadyConfirmation->where('restaurant_id', $restaurant);
							}
						])
						->with(['orderServeProgression', 'order.orderer', 'order.asap', 'order.scheduled', 'order.cutleryAdded'])
						->where('restaurant_id', $restaurant)
						->where('food_order_acceptance', 1)
						->whereHas('order', function($q) {
	   						$q->where('order_type', 'serve-on-table')
	   						  ->orWhere('order_type', 'reservation');
						})
						->latest()
						->paginate($perPage),
            
            ], 200);

	 	}

	 	return response(Order::get(), 200);
	}

	public function confirmOrderPresentation(Request $request, $order, $perPage)
    {
        // validation
        $request->validate([
            'orderServed' => 'required|boolean',
            'waiter_id' => 'required|exists:waiters,id',
            'restaurant_id' => ['required',
            	function ($attribute, $value, $fail) use ($order) {
                    // if this restaurant has this specific waiter
                    $validWaiter = Restaurant::find($value)->waiters()->where('restaurant_id', $value)->exists();
                    
                    if (!$validWaiter) {
                        $fail('Invalid Waiter or Restaurant');
                    }
                }
        	]
        ]);

        $orderToServe = Order::findOrFail($order);

        // if already cancelled order or delivery-order is more than 30 seconds ago 
        if ($this->cancelledOrder($orderToServe) || $this->servedOrder($orderToServe)) {
        	
        	return $this->showWaiterAllOrders($request->restaurant_id, $perPage);

        }

        // if order is ready and not already served
        else if ($request->orderServed && $this->restaurantOrderReady($orderToServe, $request->restaurant_id) && !$this->servedOrder($orderToServe)) {

            $this->makeRestaurantOrderServed($orderToServe, 'App\Models\Waiter', $request->waiter_id);

	        // Broadcast to admin for restaurant order ready confirmation
	        $this->notifyAdmin($orderToServe);

	        // Broadcast to related restaurant about serve
	        $this->notifyRestaurant($orderToServe->restaurants()->where('restaurant_id', $request->restaurant_id)->first());
        
        }

        return $this->showWaiterAllOrders($request->restaurant_id, $perPage);

    }

    private function confirmedOrder(Order $order) 
    {
    	return $order->customer_confirmation === 1 ? true : false;
    }

    private function cancelledOrder(Order $order)
    {
    	$netRestaurantInOrder = $order->restaurants->count();
    	$numberCancelledRestaurants = $order->restaurantOrderCancelations->count();

    	return $netRestaurantInOrder==$numberCancelledRestaurants ? true : false;
    }

    private function servedOrder(Order $order)
    {
    	return $order->orderServeConfirmation()->exists();
    }

    private function timeOutDeliveryOrder(RiderDeliveryRecord $riderDeliveryRecord)
    {
    	return $riderDeliveryRecord->created_at->diffInSeconds(now()) > 60 ? true : false;
    }

    private function allOrderPicked(RiderDeliveryRecord $deliveryToConfirm)
    {
    	$netRestaurantsToPick = $deliveryToConfirm->restaurants->count() - $deliveryToConfirm->restaurantOrderCancelations->count();

        $alreadyPicked = $deliveryToConfirm->riderFoodPickConfirmations()->where('rider_food_pick_confirmation', 1)->count();

    	return $netRestaurantsToPick === $alreadyPicked ? true : false;
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
		event(new UpdateAdmin($order));
	}

	private function notifyRestaurant(OrderedRestaurant $orderedRestaurant)
	{
		event(new UpdateRestaurant($orderedRestaurant));
	}

	private function notifyRider(RiderDeliveryRecord $riderNewDeliveryRecord)
	{
		event(new UpdateRider($riderNewDeliveryRecord));
	}

	private function makeRestaurantOrderCalls(Order $order)
	{
		// checking for order confirmation and if already has made
        if ($order->customer_confirmation===1 && !$order->restaurantAcceptances()->exists()) {
           
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

	private function makeCustomerOrderCancelationReason(Order $orderToCancel, $reasonId)
	{
		// if same order hasn't already been cancelled by customer
	 	if (!$orderToCancel->customerOrderCancelation()->exists()) {
		 	
		 	// reason of the cancelled order
	 		$customerOrderCancelation = $orderToCancel->customerOrderCancelation()->create([
		 		'reason_id' => $reasonId,
		 		'customer_id' => $orderToCancel->orderer->id,
		 	]);
	 		
	 	}
	}

	private function confirmCustomerOrderRequest(Order $orderToConfirm)
	{
		// Confirming customer order request
		$orderToConfirm->update([
	 		'customer_confirmation' => 1
	 	]);

	}

	private function cancelCustomerOrderRequest(Order $orderToCancel)
	{
		// Cancelling customer order request
		$orderToCancel->update([
	 		'customer_confirmation' => 0
	 	]);
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
		$restaurantOrderToAccept = $order->restaurantAcceptances()->where('restaurant_id', $restaurant)->first();

		// if exists & not accepted yet
 		if ($restaurantOrderToAccept && $restaurantOrderToAccept->food_order_acceptance==-1) {

			$restaurantOrderToAccept->update(['food_order_acceptance' => 1]);

		}

		return $restaurantOrderToAccept;
	}

	private function restaurantOrderReady(Order $order, $restaurantId)
	{
		return $order->orderReadyConfirmations()->where('restaurant_id', $restaurantId)->where('food_ready_confirmation', 1)->exists();
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

	private function makeRestaurantOrderServed(Order $order, $className, $id)
	{
		// if not already entered for this order & restaurant
		if (!$order->orderServeConfirmation()->exists()) {
			
			$order->orderServeConfirmation()->create([
	 			'food_serve_confirmation' => 1,
	 			'confirmer_id' => $id,
	 			'confirmer_type' => $className,
	 		]);
		}
	}

	// broadcasting for riders
	private function makeRiderOrderCall(Order $order, $cancelledRiderId=NULL)
	{	
		// to calculate the nearest rider laterly
		$allNearestRiders = $this->findNearestRiders($order->delivery);
		$allNearestRiders = $allNearestRiders->reject(function ($rider) use ($cancelledRiderId){
												    return $rider->id==$cancelledRiderId;
												});
		
		$delay = 0;

		foreach ($allNearestRiders as $rider) {
			
			NotifyRiders::dispatch($order, $rider)->delay(now()->addSeconds($delay));

			$delay += 120;

		}

	}

	private function notifyRestaurantWaiters(RestaurantOrderRecord $restaurantOrderRecord, $restaurantId)
	{
		if (Restaurant::find($restaurantId)->waiters()->where('admin_approval', 1)->exists()) {
			
			// broadcast for waiter with RestaurantOrderRecord
			event(new UpdateWaiters($restaurantOrderRecord));

		}
	}

	// should calculate the nearest rider and snoozing time laterly
	private function findNearestRiders(OrderDeliveryInfo $delivery=null) 
	{
		return Rider::whereNull('current_lat')
					->whereNull('current_lang')
					->where('available', true)
					->where('admin_approval', true)
					->where(function ($query) {
					    $query->whereNull('paused_at')->orWhere('paused_at', '<=', now()->subMinutes(1)->toDateTimeString());
					})
					// ->orderBy('evaluation.order_acceptance_percentage', 'desc')
					->take(10)
					->get();
	}

    private function makeRestaurantFoodPickStatus(RiderDeliveryRecord $riderDeliveryRecord)
    {	
		foreach ($riderDeliveryRecord->restaurantsAccepted as $restaurantOrderRecord) {
	    	
	    	// if not already created order-pick-progression for the same order-delivery
	    	if (!$riderDeliveryRecord->riderFoodPickConfirmations()->where('restaurant_id', $restaurantOrderRecord->restaurant_id)->exists()) {
        		
		    	$riderDeliveryRecord->riderFoodPickConfirmations()->create([
		            // 'rider_food_pick_confirmation' => -1,
		            'restaurant_id'=>$restaurantOrderRecord->restaurant_id,
		            'rider_id'=>$riderDeliveryRecord->rider_id,
		        ]);

        	}
        	
    	}
    }
    
}
