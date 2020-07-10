<?php

namespace App\Http\Controllers\Web;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\OrderedRestaurant;
use App\Models\RestaurantEvaluation;
use App\Http\Controllers\Controller;
use App\Models\RestaurantOrderRecord;

class OrderController extends Controller
{
	public function showOrderDetail($order)
	{
		return response()->json([

			'expectedOrder' => Order::with(['orderer', 'restaurants.items.restaurantMenuItem', 'restaurants.restaurant', 'restaurantAcceptances.restaurant', 'riderAssignment', 'orderReadyConfirmations.restaurant', 'riderFoodPickConfirmations.restaurant', 'riderFoodPickConfirmations.rider', 'riderDeliveryConfirmation.rider', 'waiterServeConfirmation', 'payment', 'delivery.customerAddress'])->find($order),  
		
		], 200);
	}

	public function showAllOrders($perPage = false)
	{
	 	if ($perPage) {
		 	
            return response()->json([

               'all' => Order::with(['orderer', 'restaurantAcceptances.restaurant', 'riderAssignment', 'orderReadyConfirmations.restaurant', 'riderFoodPickConfirmations.restaurant', 'riderFoodPickConfirmations.rider', 'riderDeliveryConfirmation.rider', 'waiterServeConfirmation'])->latest()->paginate($perPage),

               'new' => Order::where('call_confirmation', -1)->with(['orderer', 'restaurantAcceptances.restaurant', 'riderAssignment', 'orderReadyConfirmations.restaurant', 'riderFoodPickConfirmations.restaurant', 'riderFoodPickConfirmations.rider', 'riderDeliveryConfirmation.rider', 'waiterServeConfirmation'])->latest()->paginate($perPage),

               'deliveredOrServed' => Order::with(['orderer', 'restaurantAcceptances.restaurant', 'riderAssignment', 'orderReadyConfirmations.restaurant', 'riderFoodPickConfirmations.restaurant', 'riderFoodPickConfirmations.rider', 'waiterServeConfirmation'])
               					->whereHas('riderDeliveryConfirmation.rider', function($q){
				   					$q->where('rider_delivery_confirmation', 1);
								})
								->orWhereHas('waiterServeConfirmation', function($q){
				   					$q->where('waiter_serve_confirmation', 1);
								})
								->latest()->paginate($perPage),				

               'cancelled' => Order::where('call_confirmation', 0)->with(['orderer', 'restaurantAcceptances.restaurant', 'riderAssignment', 'orderReadyConfirmations.restaurant', 'riderFoodPickConfirmations.restaurant', 'riderFoodPickConfirmations.rider', 'riderDeliveryConfirmation.rider', 'waiterServeConfirmation'])->latest()->paginate($perPage),

               'prepaid' => Order::with(['orderer', 'restaurantAcceptances.restaurant', 'riderAssignment', 'orderReadyConfirmations.restaurant', 'riderFoodPickConfirmations.restaurant', 'riderFoodPickConfirmations.rider', 'riderDeliveryConfirmation.rider', 'waiterServeConfirmation'])->has('payment')->latest()->paginate($perPage),
               			
               'postpaid' => Order::with(['orderer', 'restaurantAcceptances.restaurant', 'riderAssignment', 'orderReadyConfirmations.restaurant', 'riderFoodPickConfirmations.restaurant', 'riderFoodPickConfirmations.rider', 'riderDeliveryConfirmation.rider', 'waiterServeConfirmation'])->doesntHave('payment')->latest()->paginate($perPage),
               
            ], 200);

	 	}

	 	// return response(Order::get(), 200);
	}

	public function confirmNewOrder(Request $request, $order, $perPage)
	{
	 	$orderToConfirm = Order::findOrFail($order);

	 	$confirmedOrder = $orderToConfirm->update([
	 		'call_confirmation' => 1
	 	]);

	 	return $this->showAllOrders($perPage);
	}

	public function cancelNewOrder(Request $request, $order, $perPage)
	{
		$orderToCancel = Order::findOrFail($order);	 	

	 	if ($request->cancelledBy==='Customer' && $orderToCancel->call_confirmation===-1) {

		 	$cancelledOrder = $orderToCancel->update([
		 		'call_confirmation' => 0
		 	]);

	 	}

	 	// if any rider is actually assigned and the food has't been picked up yet 
	 	else if ($request->cancelledBy==='Rider' && $orderToCancel->riderAssignment->count() && !$orderToCancel->riderFoodPickConfirmations()->where('rider_food_pick_confirmation', 1)->count()) {

	 	/*
		 	$cancelledRiderAssignment = $orderToCancel->riderAssignment->update([
		 		'delivery_order_acceptance' => 0
		 	]);
		*/

		 	// the rider accepted the food request but the food was never picked up(was cancelled)
		 	$cancelledRiderOrder = $orderToCancel->riderFoodPickConfirmations()->update([
		 		'rider_food_pick_confirmation' => 0
		 	]);

		 	// reason of the cancelled order
	 		$riderOrderCancelation = $orderToCancel->riderOrderCancelation->update([
		 		'reason_id' => $request->reason_id,
		 		'rider_id' => $request->rider_id,
		 	]);

		 	// Assign another food man for the order

	 	}
	 	// if the restaurant actually accepted the food request
	 	else if ($request->cancelledBy==='Restaurants' && $orderToCancel->restaurantAcceptances()->where('restaurant_id', $request->restaurant_id)->count()) {
	 		
 		/*
	 		$cancelledOrder = $orderToCancel->restaurantAcceptances()->where('restaurant_id', $request->restaurant_id)->update([
		 		'food_order_acceptance' => 0
		 	]);
		*/

	 		// the restaurant accepted the food request but the food was never ready(was cancelled)
	 		$cancelledRestaurantOrder = $orderToCancel->orderReadyConfirmations()->where('restaurant_id', $request->restaurant_id)->update([
		 		'food_ready_confirmation' => 0
		 	]);

		 	// reason of the cancelled order
	 		$restaurantOrderCancelation = $orderToCancel->restaurantOrderCancelation->update([
		 		'reason_id' => $request->reason_id,
		 		'restaurant_id' => $request->restaurant_id,
		 	]);

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
	public function showRestaurantAllOrders($restaurant, $perPage = false)
	{
	 	if ($perPage) {
		 	
            return response()->json([

               'all' => OrderedRestaurant::where('restaurant_id', $restaurant)->with(['order.orderer', 'order.restaurants.items.restaurantMenuItem', 'order.restaurantAcceptances', 'order.orderReadyConfirmations', 'order.waiterServeConfirmation'])
				               			->whereHas('order', function($q){
						   					$q->where('call_confirmation', 1);
										})
				       					->latest()->paginate($perPage),

               'new' => RestaurantOrderRecord::where('restaurant_id', $restaurant)->where('food_order_acceptance', -1)->with(['order.orderer', 'order.restaurants.items.restaurantMenuItem', 'order.restaurantAcceptances', 'order.orderReadyConfirmations', 'order.waiterServeConfirmation'])
					               			->whereHas('order', function($q){
							   					$q->where('call_confirmation', 1);
											})
					               			->latest()->paginate($perPage),

               'served' => OrderedRestaurant::where('restaurant_id', $restaurant)->with(['order.orderer', 'order.restaurants.items.restaurantMenuItem', 'order.restaurantAcceptances', 'order.orderReadyConfirmations', 'order.waiterServeConfirmation'])
											->whereHas('order.waiterServeConfirmation', function($q){
							   					$q->where('waiter_serve_confirmation', 1);
											})
											->latest()->paginate($perPage),				

               'cancelled' => RestaurantOrderRecord::where('restaurant_id', $restaurant)->where('food_order_acceptance', 0)->with(['order.orderer', 'order.restaurants.items.restaurantMenuItem', 'order.restaurantAcceptances', 'order.orderReadyConfirmations', 'order.waiterServeConfirmation'])->latest()->paginate($perPage),

               
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
			            									->count();
			            if (!$restaurantExist) {
			                $fail('Restaurant is invalid for this order');
			            }
			        },
			        function ($attribute, $value, $fail) use ($order) {
			            $restaurantExist = RestaurantOrderRecord::where('restaurant_id', $value)
			            										->where('order_id', $order)
			            										->count();
			            if (!$restaurantExist) {
			                $fail('Invalid Restaurant or Order');
			            }
			        },
		    ],
	 	]);

	 	$orderToConfirm = Order::findOrFail($order);

 		if ($request->orderReady) {
 			
 			$orderToConfirm->orderReadyConfirmations()->create([
	 			'food_ready_confirmation' => 1,
	 			'restaurant_id' => $request->restaurant_id,
	 		]);

 		}

 		// if not accepted yet
 		if (!$orderToConfirm->restaurantAcceptances()->where('restaurant_id', $request->restaurant_id)->where('food_order_acceptance', 1)->count()) {

	 		$orderToConfirm->restaurantAcceptances()->where('restaurant_id', $request->restaurant_id)->update([
	 			'food_order_acceptance' => 1
	 		]);
	 		
 		}

 		return $this->showRestaurantAllOrders($request->restaurant_id, $perPage);

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
															->count();
			            if (!$restaurantExist) {
			                $fail('Invalid Restaurant or Order');
			            }
			        },
			        function ($attribute, $value, $fail) use ($order) {
			            $restaurantExist = RestaurantOrderRecord::where('restaurant_id', $value)
			            										->where('order_id', $order)
			            										->count();
			            if (!$restaurantExist) {
			                $fail('Invalid Restaurant or Order');
			            }
			        },
			]
		]);

		$orderToCancel = Order::findOrFail($order);

		if ($orderToCancel->restaurants()->where('restaurant_id', $request->restaurant_id)->count()) {
			
			// Cancelling restaurant order acceptance for this order
			$restaurantCancelledAcceptance = $orderToCancel->restaurantAcceptances()
															->where('restaurant_id', $request->restaurant_id)
															->update([
														 		'food_order_acceptance' => 0
														 	]);

			// Creating Order Cancelation reason for this order
			$orderCancelationReason = $orderToCancel->restaurantOrderCancelations()
													->updateOrCreate([
												 		'reason_id' => $request->reason_id,
												 		'restaurant_id' => $request->restaurant_id,
												 	]);

			$totalRequestReceived = RestaurantOrderRecord::where('restaurant_id', $request->restaurant_id)->count();

			$totalRequestAccepted = RestaurantOrderRecord::where('restaurant_id', $request->restaurant_id)
														->where('food_order_acceptance', 1)
														->count();

			// Avoiding O exception
			if ($totalRequestReceived) {
				// Updating restaurant evaluation
				$restaurantEvaluated = RestaurantEvaluation::where('restaurant_id', $request->restaurant_id)
															->updateOrCreate([
																'order_acceptance_percentage' => (($totalRequestAccepted/$totalRequestReceived)*100),
																'restaurant_id' => $request->restaurant_id,
															]);
			}

	 		return $this->showRestaurantAllOrders($request->restaurant_id, $perPage);
		}

		return response('Bad Request', 401);
	}
}
