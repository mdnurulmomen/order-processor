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
use App\Models\OrderRestaurant;
use App\Events\UpdateRestaurant;
use App\Models\OrderDeliveryInfo;
use App\Models\RiderDeliveryRecord;
use App\Models\RestaurantEvaluation;
use App\Http\Controllers\Controller;
use App\Models\RestaurantOrderRecord;
use App\Http\Resources\Web\OrderCollection;
use App\Http\Resources\Web\OrderDetailResource;

class OrderController extends Controller
{
	// Admin
	public function showOrderDetail($order)
	{
		return new OrderDetailResource(Order::with(['orderer', 'asap', 'schedule', 'cutlery', 'restaurants.items.restaurantMenuItem', 'restaurants.items.variation.restaurantMenuItemVariation.variation', 'restaurants.items.addons.restaurantMenuItemAddon.addon', 'restaurants.restaurant', 'restaurantAcceptances.restaurant', 'riderAssignment', 'orderReadyConfirmations.restaurant', 'riderFoodPickConfirmations.restaurant', 'riderFoodPickConfirmations.rider', 'riderDeliveryConfirmation.rider', 'orderServeConfirmation', 'restaurantOrderCancelations.canceller', 'adminOrderCancelation.canceller', 'payment', 'delivery.customerAddress'])->find($order));
	}

	public function showAllOrders($perPage = false)
	{
	 	if ($perPage) {
		 	
            return response()->json([

               'all' => new OrderCollection(Order::with(['restaurantAcceptances.restaurant', 'riderAssignment', 'orderReadyConfirmations.restaurant', 'riderFoodPickConfirmations.restaurant', 'riderDeliveryConfirmation', 'orderServeConfirmation', 'restaurantOrderCancelations.canceller', 'adminOrderCancelation.canceller'])->latest()->paginate($perPage)),

               'unconfirmed' => new OrderCollection(Order::where('customer_confirmation', -1)->with(['restaurantAcceptances.restaurant', 'riderAssignment', 'orderReadyConfirmations.restaurant', 'riderFoodPickConfirmations.restaurant', 'riderDeliveryConfirmation', 'orderServeConfirmation', 'restaurantOrderCancelations.canceller', 'adminOrderCancelation.canceller'])->latest()->paginate($perPage)),

               'active' => new OrderCollection(Order::where('customer_confirmation', 1)->where('in_progress', 1)->with(['restaurantAcceptances.restaurant', 'riderAssignment', 'orderReadyConfirmations.restaurant', 'riderFoodPickConfirmations.restaurant', 'riderDeliveryConfirmation', 'orderServeConfirmation', 'restaurantOrderCancelations.canceller', 'adminOrderCancelation.canceller'])->latest()->paginate($perPage)),

               'prepaid' => new OrderCollection(Order::with(['restaurantAcceptances.restaurant', 'riderAssignment', 'orderReadyConfirmations.restaurant', 'riderFoodPickConfirmations.restaurant', 'riderDeliveryConfirmation', 'orderServeConfirmation', 'restaurantOrderCancelations.canceller', 'adminOrderCancelation.canceller'])->has('payment')->latest()->paginate($perPage)),
               			
               'postpaid' => new OrderCollection(Order::with(['restaurantAcceptances.restaurant', 'riderAssignment', 'orderReadyConfirmations.restaurant', 'riderFoodPickConfirmations.restaurant', 'riderDeliveryConfirmation', 'orderServeConfirmation', 'restaurantOrderCancelations.canceller', 'adminOrderCancelation.canceller'])->doesntHave('payment')->latest()->paginate($perPage)),

               'deliveredOrServed' => new OrderCollection(Order::where('complete_order', 1)->with(['restaurantAcceptances.restaurant', 'riderAssignment', 'orderReadyConfirmations.restaurant', 'riderFoodPickConfirmations.restaurant', 'riderDeliveryConfirmation', 'orderServeConfirmation', 'restaurantOrderCancelations.canceller', 'adminOrderCancelation.canceller'])
				/*
				->whereHas('riderDeliveryConfirmation', function($q){
   					$q->where('rider_delivery_confirmation', 1);
				})
				->orWhereHas('orderServeConfirmation', function($q){
   					$q->where('food_serve_confirmation', 1);
				})
				*/
				->latest()->paginate($perPage)),				

               'cancelled' => new OrderCollection(Order::with(['restaurantAcceptances.restaurant', 'riderAssignment', 'orderReadyConfirmations.restaurant', 'riderFoodPickConfirmations.restaurant', 'riderDeliveryConfirmation', 'orderServeConfirmation', 'restaurantOrderCancelations.canceller', 'adminOrderCancelation.canceller'])
				->where('complete_order', 0)
				/*
				->orHas('restaurantOrderCancelations')
				->orWhereHas('restaurantAcceptances', function($q){
   					$q->where('food_order_acceptance', 0);
				})
				*/
				->latest()->paginate($perPage)),
               
            ], 200);

	 	}

	 	// return response(Order::get(), 200);
	}

	public function confirmNewOrder(Request $request, $perPage)
	{
	 	$request->validate([
	 		'id' => 'required|exists:orders,id',
	 		'order_type' => 'required|in:home-delivery,serve-on-table,take-away'
	 	]);

	 	/*
	 	if ($request->order_type == 'reservation') {
	 		
	 		return response()->json(
	 			[
	 				'errors' => ['order' => ['Reservation is confirmed through API.']]
	 			], 422
	 		);

	 	}
	 	*/

	 	$orderToConfirm = Order::findOrFail($request->id);
	 	
	 	if ($orderToConfirm->customer_confirmation) {			// -1 / 1

		 	$this->confirmCustomerOrderRequest($orderToConfirm);
	 		$this->makeRestaurantOrderCalls($orderToConfirm);

	 	}

	 	return $this->showAllOrders($perPage);
	}

	// Admin
	public function cancelNewOrder(Request $request, $order, $perPage)
	{
		$request->validate([
	 		'canceller' => 'required|string|in:customer,rider,restaurants,admin',
	 		'reason_id' => 'required|numeric|exists:cancelation_reasons,id',
	 		'restaurant_id' => 'required_if:canceller,restaurants',
	 		// 'rider_id' => 'required_if:canceller,rider',
	 	]);

		$orderToCancel = Order::findOrFail($order);	 	

		// already customer or same restaurant cancelled this order or order has been picked or order is stopped
		if ($orderToCancel->customerOrderCancelation()->exists() || $orderToCancel->restaurantOrderCancelations()->where('canceller_id', $request->restaurant_id)->exists() || $orderToCancel->riderFoodPickConfirmations()->where('rider_food_pick_confirmation', 1)->exists() || $orderToCancel->in_progress === 0 && $orderToCancel->complete_order === 1) {
			
			return response()->json(
	 			[
	 				'errors' => ['invalidCancellation' => ['Canceller or order status is invalid']]
	 			], 422
	 		);

		}

		// if the order aint confirmed from customer yet
	 	else if ($request->canceller==='customer' && $orderToCancel->customer_confirmation===-1) {
		 	
		 	// cancelling the order
	 		$this->cancelCustomerOrderRequest($orderToCancel);
		 	
		 	// making cancelation reason
		 	$this->makeCustomerOrderCancelationReason($orderToCancel, $request->reason_id);

	 	}

	 	// if the restaurant actually belongs to ordered-restaurants and accepted the food request
	 	else if ($request->canceller==='restaurants' && $orderToCancel->customer_confirmation===1 && $orderToCancel->restaurants()->where('restaurant_id', $request->restaurant_id)->exists() /* && $orderToCancel->restaurantAcceptances()->where('restaurant_id', $request->restaurant_id)->exists()*/) {

		 	// cancelling just arrived order request
			$this->cancelRestaurantOrderRequest($orderToCancel, $request->restaurant_id);

		 	// making cancelation reason
		 	$this->makeRestaurantOrderCancelationReason($orderToCancel, $request->reason_id, $request->restaurant_id);

			// evaluating restaurant
			$this->updateRestaurantEvaluation($request->restaurant_id);

			// Broadcast to restaruant for order cancelation with OrderRestaurant Model
			$this->notifyRestaurant($orderToCancel->restaurants()->where('restaurant_id', $request->restaurant_id)->first());

			// if all restauraant cancelled this order
			if ($orderToCancel->restaurants->count() == $orderToCancel->restaurantOrderCancelations->count()) {
				
				$this->disableOrderStatus($orderToCancel);

			}

			// inform rider on restaurant-cancelation if any rider has been assigned
			if ($orderToCancel->riderAssignment()->exists()) {
				
				$this->notifyRider($orderToCancel->riderAssignment);

			}

	 	}

	 	// if any rider is actually assigned and the food has't been picked up yet 
	 	else if ($request->canceller==='rider' && $orderToCancel->customer_confirmation===1 && $orderToCancel->riderAssignment()->where('rider_id', $request->rider_id)->exists()) {
		
		 	// deleting related rider food pick records if any
		 	$orderToCancel->riderFoodPickConfirmations()->delete();

		 	// retreiving the expected RiderDeliveryOrder
		 	$riderDeliveryOrderToCancel = $orderToCancel->riderAssignment;

		 	// cancelling accepted order-delivery
		 	$this->cancelRiderDeliveryOrder($orderToCancel);
		 	
		 	// reason of the cancelled order
		 	$this->makeRiderDeliveryCancelationReason($orderToCancel, $request->reason_id);
		 	
		 	// evaluating related rider
		 	$this->updateRiderEvaluation($request->rider_id);

		 	// inform rider about delivery-order cancelation
			$this->notifyRider($riderDeliveryOrderToCancel);

		 	// making rider call to assign another food man for the order
			$this->makeRiderOrderCall($orderToCancel, $riderDeliveryOrderToCancel->rider_id);

	 	}

	 	// stop order at any phase until order delivered / served
	 	else if ($request->canceller=='admin' && $orderToCancel->customer_confirmation===1) {
	 		
	 		$this->disableOrderStatus($orderToCancel);

	 		$this->makeAdminOrderCancelationReason($orderToCancel, $request->reason_id);

	 		// $this->notifyCustomer($orderToCancel);

	 		$this->notifyOrderRestaurants($orderToCancel);

	 		// $this->notifyRestaurantWaiters($orderAccepted, $request->restaurant_id);

	 		// inform rider on restaurant-cancelation if any rider has been assigned
			if ($orderToCancel->riderAssignment()->exists()) {
				
				$this->notifyRider($orderToCancel->riderAssignment);

			}

	 	}
	 	else {

	 		return response()->json(
	 			[
	 				'errors' => ['invalidCancellation' => ['Canceller or order status is invalid']]
	 			], 422
	 		);

	 	}

	 	return $this->showAllOrders($perPage);
	}

  	public function searchAllOrders($search, $perPage)
	{	
		$query = Order::with(['restaurantAcceptances.restaurant', 'riderAssignment', 'orderReadyConfirmations.restaurant', 'riderFoodPickConfirmations.restaurant', 'riderDeliveryConfirmation', 'orderServeConfirmation', 'restaurantOrderCancelations.canceller'])
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

			'all' => new OrderCollection($query->paginate($perPage)),  
		
		], 200);
	}

	// Restaurant
	public function showAllRestaurantOrders($restaurant, $perPage = false)
	{
	 	if ($perPage) {
		 	
            return response()->json([

               'all' => OrderRestaurant::where('restaurant_id', $restaurant)->with(['items.restaurantMenuItem', 'items.variation.restaurantMenuItemVariation.variation', 'items.addons.restaurantMenuItemAddon.addon', 'order.orderer', 'order.asap', 'order.schedule', 'order.cutlery', 'order.restaurantAcceptances', 'order.orderReadyConfirmations', 'order.orderServeConfirmation', 'order.restaurantOrderCancelations'])
       			->whereHas('order', function($q){
   					$q->where('customer_confirmation', 1)
   					  ->orWhere('order_type', 'reservation');
				})
				->latest()->paginate($perPage),

               'served' => OrderRestaurant::where('restaurant_id', $restaurant)->with(['items.restaurantMenuItem', 'items.variation.restaurantMenuItemVariation.variation', 'items.addons.restaurantMenuItemAddon.addon', 'order.orderer', 'order.asap', 'order.schedule', 'order.cutlery', 'order.restaurantAcceptances', 'order.orderReadyConfirmations', 'order.orderServeConfirmation', 'order.restaurantOrderCancelations'])
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
			            $restaurantExist = OrderRestaurant::where('restaurant_id', $value)
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

	 	// already customer or same restaurant cancelled this order or order is stopped or completed
		if (! $this->confirmedOrder($orderToConfirm) || $orderToConfirm->restaurantOrderCancelations()->where('canceller_id', $request->restaurant_id)->exists() || $orderToConfirm->in_progress ===0 || $orderToConfirm->complete_order ===1) {
			
			return $this->showAllRestaurantOrders($request->restaurant_id, $perPage);

		}

 		else if ($request->serveOrder) {

 			$orderServed = $this->makeRestaurantOrderServed($orderToConfirm, 'App\Models\Restaurant', $request->restaurant_id);
 			$orderConfirmed = $this->makeRestaurantOrderReady($orderToConfirm, $request->restaurant_id);
 			$orderAccepted = $this->makeRestaurantOrderAccepted($orderToConfirm, $request->restaurant_id);

 			$this->accomplishOrderStatus($orderToConfirm);

 		}
 		else if ($request->orderReady) {
 			
 			$orderConfirmed = $this->makeRestaurantOrderReady($orderToConfirm, $request->restaurant_id);
 			$orderAccepted = $this->makeRestaurantOrderAccepted($orderToConfirm, $request->restaurant_id);

 		}
 		else {

 			$orderAccepted = $this->makeRestaurantOrderAccepted($orderToConfirm, $request->restaurant_id);
 		
 		}

 		// checking if order is for home-delivery and order is from customer and any rider notification is already broadcasted
		if ($orderToConfirm->order_type==='home-delivery' && $orderToConfirm->orderer instanceof Customer) {

			if (! $orderToConfirm->riderCall()->exists()) {

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

		// checking if order is for serve-on-table or reservation
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
						$restaurantExist = OrderRestaurant::where('restaurant_id', $value)
															->where('order_id', $order)
															->exists();
			            if (! $restaurantExist) {
			                $fail('Invalid Restaurant or Order');
			            }
			        },
			        function ($attribute, $value, $fail) use ($order) {
			            $restaurantExist = RestaurantOrderRecord::where('restaurant_id', $value)
			            										->where('order_id', $order)
			            										->where('food_order_acceptance', -1)
			            										->exists();
			            if (! $restaurantExist) {
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

 		
 		// if all restauraant cancelled this order
		if ($orderToCancel->restaurants->count() == $orderToCancel->restaurantOrderCancelations->count()) {
			
			$this->disableOrderStatus($orderToCancel);

		}

		// inform rider on restaurant-cancelation if any rider has been assigned
 		if ($orderToCancel->riderAssignment()->exists()) {
 			
 			$this->notifyRider($orderToCancel->riderAssignment);

 		}

 		return $this->showAllRestaurantOrders($request->restaurant_id, $perPage);
		
		// }

		// return response('Bad Request', 401);
	}
	

	// Rider
	public function showAllRiderOrders($rider = false, $perPage = false)
	{
	 	if ($rider && $perPage) {

            return response()->json([

               'all' => RiderDeliveryRecord::where('rider_id', $rider)->with(['order.orderer', 'order.asap', 'order.schedule', 'order.cutlery', 'restaurants.items.restaurantMenuItem', 'restaurants.items.variation.restaurantMenuItemVariation.variation', 'restaurants.items.addons.restaurantMenuItemAddon.addon', 'restaurants.restaurant', 'restaurantsAccepted.restaurant', 'riderOrderCancelations', 'riderFoodPickConfirmations', 'riderDeliveryConfirmation', 'restaurantOrderCancelations'])->latest()->paginate($perPage),
            
            ], 200);

	 	}
	 	else if (! $rider && $perPage) {

	 		return response()->json([

               'all' => RiderDeliveryRecord::with(['order.orderer', 'order.asap', 'order.schedule', 'order.cutlery', 'restaurants.items.restaurantMenuItem', 'restaurants.items.variation.restaurantMenuItemVariation.variation', 'restaurants.items.addons.restaurantMenuItemAddon.addon', 'restaurants.restaurant', 'restaurantsAccepted.restaurant', 'riderOrderCancelations', 'riderFoodPickConfirmations', 'riderDeliveryConfirmation', 'restaurantOrderCancelations'])->latest()->paginate($perPage),
            
            ], 200);

	 	}
	 	else {

	 		return response()->json([

               'all' => RiderDeliveryRecord::with(['order.orderer', 'order.asap', 'order.schedule', 'order.cutlery', 'restaurants.items.restaurantMenuItem', 'restaurants.items.variation.restaurantMenuItemVariation.variation', 'restaurants.items.addons.restaurantMenuItemAddon.addon', 'restaurants.restaurant', 'restaurantsAccepted.restaurant', 'riderOrderCancelations', 'riderFoodPickConfirmations', 'riderDeliveryConfirmation', 'restaurantOrderCancelations'])->latest()->get(),
            
            ], 200);

	 	}

	 	return response(Order::get(), 200);
	}

	public function confirmRiderDeliveryOrder(Request $request, $order, $perPage)
    {
        // validation
        $request->validate([
        	'orderReturned' => 'boolean',
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
        
        // if already cancelled order by restaurants or customer
        if ($this->cancelledOrder($orderToConfirm)) {
        	
        	return $this->showAllRiderOrders($request->rider_id, $perPage);

        }

        $deliveryToConfirm = RiderDeliveryRecord::where('rider_id', $request->rider_id)->where('order_id', $order)->first();

        // if already not accepted
        if ($request->orderAccepted && $deliveryToConfirm->delivery_order_acceptance==0 && ! $this->timeOutDeliveryOrder($deliveryToConfirm)) {
                
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
        // confirming pick-up
        else if ($request->orderPicked /*&& $deliveryToConfirm->delivery_order_acceptance==1*/) {
            
            $restaurantToPick = $deliveryToConfirm->riderFoodPickConfirmations()->where('rider_id', $request->rider_id)->where('restaurant_id', $request->restaurant_id)->first();

            // if already not picked
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
        // confirming delivery
        else if ($request->orderDropped && $this->allOrderPicked($deliveryToConfirm) /*&& $deliveryToConfirm->delivery_order_acceptance==1*/ && $deliveryToConfirm->riderDeliveryConfirmation()->exists()) {
                
            $deliveryToConfirm->riderDeliveryConfirmation()->update([
                'rider_delivery_confirmation' => 1,
            ]);

            $this->accomplishOrderStatus($deliveryToConfirm->order);

        }
        // food returning
        // if delivery record was created
        else if ($request->orderReturned && $this->allOrderPicked($deliveryToConfirm) /*&& $deliveryToConfirm->delivery_order_acceptance==1*/ && $deliveryToConfirm->riderDeliveryConfirmation()->exists()) {

            // deleting delivery record as not deliverd
            $deliveryToConfirm->riderDeliveryConfirmation()->update([
            	'rider_delivery_confirmation' => 2, // returned
            ]);

            $this->disableOrderStatus($deliveryToConfirm->order);

        }

        // Broadcast to admin for restaurant order ready confirmation
        $this->notifyAdmin($deliveryToConfirm->order);

        return $this->showAllRiderOrders($request->rider_id, $perPage);

    }


    // Waiter
	public function showWaiterAllOrders($restaurant, $perPage = false)
	{
	 	if ($perPage) {

            return response()->json([

				'all' => RestaurantOrderRecord::whereHas('order', function($q) {
	   						$q->where('order_type', 'serve-on-table')
	   						  ->orWhere('order_type', 'reservation');
						})
						->where('food_order_acceptance', 1)
						->where('restaurant_id', $restaurant)
						->with([
							'orderedRestaurants' => function($orderedRestaurant) use ($restaurant) {
						    	$orderedRestaurant->where('restaurant_id', $restaurant)->with(['items.variation.restaurantMenuItemVariation.variation', 'items.addons.restaurantMenuItemAddon.addon', 'items.restaurantMenuItem']);
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
						->with(['orderServeProgression', 'order.orderer', 'order.asap', 'order.schedule', 'order.cutlery'])
						->latest()
						->paginate($perPage),
            
            ], 200);

	 	}

	 	return response(Order::get(), 200);
	}

	public function confirmWaiterOrder(Request $request, $order, $perPage)
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
        else if ($request->orderServed && $this->restaurantOrderReady($orderToServe, $request->restaurant_id) && ! $this->servedOrder($orderToServe)) {

            $this->makeRestaurantOrderServed($orderToServe, 'App\Models\Waiter', $request->waiter_id);

            $this->accomplishOrderStatus($orderToServe);

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

    	return $netRestaurantInOrder==$numberCancelledRestaurants || $order->in_progress===0 || $order->customer_confirmation===0 ? true : false;
    }

    private function servedOrder(Order $order)
    {
    	return $order->orderServeConfirmation()->exists();
    }

    private function timeOutDeliveryOrder(RiderDeliveryRecord $riderDeliveryRecord)
    {
    	return $riderDeliveryRecord->created_at->diffInSeconds(now()) > 30 ? true : false;
    }

    private function allOrderPicked(RiderDeliveryRecord $deliveryToConfirm)
    {
    	$netRestaurantsToPick = $deliveryToConfirm->restaurants->count() - $deliveryToConfirm->restaurantOrderCancelations->count();

        $alreadyPicked = $deliveryToConfirm->riderFoodPickConfirmations()->where('rider_food_pick_confirmation', 1)->count();

    	return $netRestaurantsToPick === $alreadyPicked ? true : false;
    }

    private function makeAdminOrderCancelationReason(Order $orderToCancel, $reasonId)
	{
 		$currentUser = \Auth::guard('admin')->user();

		$orderToCancel->adminOrderCancelation()->create([
			'reason_id' => $reasonId,
			'canceller_type' => get_class($currentUser),
			'canceller_id' => $currentUser->id,
		]);
	}

    private function makeRiderDeliveryCancelationReason(Order $orderToCancel, $reasonId)
	{
 		$orderToCancel->riderOrderCancelations()->create([
	 		'reason_id' => $reasonId,
	 		'canceller_type' => 'App\Models\Rider',
			'canceller_id' => $orderToCancel->riderAssignment->rider_id,
	 	]);
	}

	private function cancelRiderDeliveryOrder(Order $orderToCancel)
	{
		$orderToCancel->riderAssignment()->update([
	 		'delivery_order_acceptance' => 0
	 	]);
	}

	private function updateRiderEvaluation($rider)
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

	private function notifyRestaurant(OrderRestaurant $orderRestaurant)
	{
		event(new UpdateRestaurant($orderRestaurant));
	}

	private function notifyOrderRestaurants(Order $order)
	{
		foreach ($order->restaurants as $orderRestaurantKey => $orderRestaurant) {
			$this->notifyRestaurant($orderRestaurant);
		}
	}

	private function notifyRider(RiderDeliveryRecord $riderNewDeliveryRecord)
	{
		event(new UpdateRider($riderNewDeliveryRecord));
	}

	private function makeRestaurantOrderCalls(Order $order)
	{
		// checking for order confirmation and if already has made
        if ($order->customer_confirmation===1 && ! $order->restaurantAcceptances()->exists()) {
           
           	foreach ($order->restaurants as $orderRestaurant) {

              	$order->restaurantAcceptances()->create([
                 	'food_order_acceptance' => -1, // ringing
                 	'restaurant_id' => $orderRestaurant->restaurant_id,
              	]);
              
              	// Broadcast for restaurant
              	$this->notifyRestaurant($orderRestaurant);

           	}

        }
	}

	private function makeCustomerOrderCancelationReason(Order $orderToCancel, $reasonId)
	{
		// if same order hasn't already been cancelled by customer
	 	if (! $orderToCancel->customerOrderCancelation()->exists()) {
		 	
		 	// reason of the cancelled order
	 		$customerOrderCancelation = $orderToCancel->customerOrderCancelation()->create([
		 		'reason_id' => $reasonId,
		 		'canceller_type' => 'App\Models\Customer',
				'canceller_id' => $orderToCancel->orderer->id,
		 	]);
	 		
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
	 	if (! $orderToCancel->restaurantOrderCancelations()->where('canceller_id', $restaurant)->exists()) {
		 	
		 	// reason of the cancelled order
	 		$restaurantOrderCancelation = $orderToCancel->restaurantOrderCancelations()->create([
		 		'reason_id' => $reason,
		 		'canceller_type' => 'App\Models\Restaurant',
				'canceller_id' => $restaurant,
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
		// created when confirmed
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
		if (! $order->orderReadyConfirmations()->where('restaurant_id', $restaurant)->exists()) {
			
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

			$delay += 30;	// 30 seconds

		}

	}

	private function notifyRestaurantWaiters(RestaurantOrderRecord $restaurantOrderRecord, $restaurantId)
	{
		// checking if restaurant has any approved waiter
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
	    	if (! $riderDeliveryRecord->riderFoodPickConfirmations()->where('restaurant_id', $restaurantOrderRecord->restaurant_id)->exists()) {
        		
		    	$riderDeliveryRecord->riderFoodPickConfirmations()->create([
		            // 'rider_food_pick_confirmation' => -1,
		            'restaurant_id'=>$restaurantOrderRecord->restaurant_id,
		            'rider_id'=>$riderDeliveryRecord->rider_id,
		        ]);

        	}
        	
    	}
    }

    private function confirmCustomerOrderRequest(Order $orderToConfirm)
	{
		// Confirming customer order request
		$orderToConfirm->update([
	 		'customer_confirmation' => 1,
	 		'in_progress' => 1,
	 		'complete_order' => -1		// -1 (incomplete) / 1 (complete) / 0 (incomplete)
	 	]);
	}

    private function cancelCustomerOrderRequest(Order $orderToCancel)
	{
		// Cancelling customer order request
		$orderToCancel->update([
	 		'customer_confirmation' => 0,
	 		'in_progress' => 0,
	 		'complete_order' => 0		// -1 (incomplete) / 1 (complete) / 0 (incomplete)
	 	]);
	}

	private function accomplishOrderStatus(Order $order)
	{
		// Confirming order accomplishment
	 	$order->update([
    		'in_progress' => 0,
    		'complete_order' => 1,		// -1 (incomplete) / 1 (complete) / 0 (incomplete)
    	]);
	}

    private function disableOrderStatus(Order $order)
    {
    	$order->update([
			'in_progress' => 0,
			'complete_order' => 0		// -1 (incomplete) / 1 (complete) / 0 (incomplete)
		]);
    }
    
}
