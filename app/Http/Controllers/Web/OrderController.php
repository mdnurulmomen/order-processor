<?php

namespace App\Http\Controllers\Web;

use App\Models\Order;
use App\Events\UpdateRider;
use App\Events\UpdateAdmin;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Events\UpdateRestaurant;
use App\Models\OrderedRestaurant;
use Illuminate\Support\Facades\Log;
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

               'all' => Order::with(['restaurantAcceptances.restaurant', 'riderAssignment', 'orderReadyConfirmations.restaurant', 'riderFoodPickConfirmations.restaurant', 'riderFoodPickConfirmations.rider', 'riderDeliveryConfirmation.rider', 'waiterServeConfirmation', 'restaurantOrderCancelations.restaurant', 'riderOrderCancelation.rider'])->latest()->paginate($perPage),

               'new' => Order::where('call_confirmation', -1)->with(['restaurantAcceptances.restaurant', 'riderAssignment', 'orderReadyConfirmations.restaurant', 'riderFoodPickConfirmations.restaurant', 'riderFoodPickConfirmations.rider', 'riderDeliveryConfirmation.rider', 'waiterServeConfirmation', 'restaurantOrderCancelations.restaurant', 'riderOrderCancelation.rider'])->latest()->paginate($perPage),

               'deliveredOrServed' => Order::with(['restaurantAcceptances.restaurant', 'riderAssignment', 'orderReadyConfirmations.restaurant', 'riderFoodPickConfirmations.restaurant', 'riderFoodPickConfirmations.rider', 'waiterServeConfirmation', 'restaurantOrderCancelations.restaurant', 'riderOrderCancelation.rider'])
               					->whereHas('riderDeliveryConfirmation.rider', function($q){
				   					$q->where('rider_delivery_confirmation', 1);
								})
								->orWhereHas('waiterServeConfirmation', function($q){
				   					$q->where('waiter_serve_confirmation', 1);
								})
								->latest()->paginate($perPage),				

               'cancelled' => Order::where('call_confirmation', 0)->with(['restaurantAcceptances.restaurant', 'riderAssignment', 'orderReadyConfirmations.restaurant', 'riderFoodPickConfirmations.restaurant', 'riderFoodPickConfirmations.rider', 'riderDeliveryConfirmation.rider', 'waiterServeConfirmation', 'restaurantOrderCancelations.restaurant', 'riderOrderCancelation.rider'])->latest()->paginate($perPage),

               'prepaid' => Order::with(['restaurantAcceptances.restaurant', 'riderAssignment', 'orderReadyConfirmations.restaurant', 'riderFoodPickConfirmations.restaurant', 'riderFoodPickConfirmations.rider', 'riderDeliveryConfirmation.rider', 'waiterServeConfirmation', 'restaurantOrderCancelations.restaurant', 'riderOrderCancelation.rider'])->has('payment')->latest()->paginate($perPage),
               			
               'postpaid' => Order::with(['restaurantAcceptances.restaurant', 'riderAssignment', 'orderReadyConfirmations.restaurant', 'riderFoodPickConfirmations.restaurant', 'riderFoodPickConfirmations.rider', 'riderDeliveryConfirmation.rider', 'waiterServeConfirmation', 'restaurantOrderCancelations.restaurant', 'riderOrderCancelation.rider'])->doesntHave('payment')->latest()->paginate($perPage),
               
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

		 	$cancelledOrder = $orderToCancel->update([
		 		'call_confirmation' => 0
		 	]);

	 	}

	 	// if the restaurant actually belongs to ordered-restaurants and accepted the food request
	 	else if ($request->cancelledBy==='Restaurants' && $orderToCancel->restaurants()->where('restaurant_id', $request->restaurant_id)->count() && $orderToCancel->restaurantAcceptances()->where('restaurant_id', $request->restaurant_id)->count()) {

		 	$this->makeRestaurantOrderCancelationReason($orderToCancel, $request->reason_id, $request->restaurant_id);	
			$this->updateRestaurantEvaluation($request->restaurant_id);

			// Broadcast to restaruant for order cancelation
			$this->notifyRestaurant($orderToCancel->restaurants()->where('restaurant_id', $request->restaurant_id)->first());

	 	}

/*
	 	// if any rider is actually assigned and the food has't been picked up yet 
	 	else if ($request->cancelledBy==='Rider' && $orderToCancel->riderAssignment->count() && !$orderToCancel->riderFoodPickConfirmations()->where('rider_food_pick_confirmation', 1)->count()) {
		
		 	// reason of the cancelled order
	 		$riderOrderCancelationReason = $orderToCancel->riderOrderCancelation()->create([
		 		'reason_id' => $request->reason_id,
		 		'rider_id' => $request->rider_id,
		 	]);

		 	// Should assign another food man for the order

	 	}
*/	

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

               'all' => OrderedRestaurant::where('restaurant_id', $restaurant)->with(['order.orderer', 'order.restaurants.items.restaurantMenuItem', 'order.restaurantAcceptances', 'order.orderReadyConfirmations', 'order.waiterServeConfirmation', 'order.restaurantOrderCancelations'])
				               			->whereHas('order', function($q){
						   					$q->where('call_confirmation', 1);
										})
				       					->latest()->paginate($perPage),

               'new' => RestaurantOrderRecord::where('restaurant_id', $restaurant)->where('food_order_acceptance', -1)->with(['order.orderer', 'order.restaurants.items.restaurantMenuItem', 'order.restaurantAcceptances', 'order.orderReadyConfirmations', 'order.waiterServeConfirmation', 'order.restaurantOrderCancelations'])
					               			->whereHas('order', function($q){
							   					$q->where('call_confirmation', 1);
											})
					               			->latest()->paginate($perPage),

               'served' => OrderedRestaurant::where('restaurant_id', $restaurant)->with(['order.orderer', 'order.restaurants.items.restaurantMenuItem', 'order.restaurantAcceptances', 'order.orderReadyConfirmations', 'order.waiterServeConfirmation', 'order.restaurantOrderCancelations'])
											->whereHas('order.waiterServeConfirmation', function($q){
							   					$q->where('waiter_serve_confirmation', 1);
											})
											->latest()->paginate($perPage),				

               'cancelled' => RestaurantOrderRecord::where('restaurant_id', $restaurant)->where('food_order_acceptance', 0)->with(['order.orderer', 'order.restaurants.items.restaurantMenuItem', 'order.restaurantAcceptances', 'order.orderReadyConfirmations', 'order.waiterServeConfirmation', 'order.restaurantOrderCancelations'])->latest()->paginate($perPage),

               
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
		    ],
	 	]);

	 	$orderToConfirm = Order::findOrFail($order);

 		if ($request->orderReady) {
 			
 			$orderConfirmedOrAccepted = $this->makeRestaurantOrderReady($orderToConfirm, $request->restaurant_id);

 		}

 		// if not accepted yet
 		if (!$orderToConfirm->restaurantAcceptances()->where('restaurant_id', $request->restaurant_id)->where('food_order_acceptance', 1)->count()) {

	 		$orderConfirmedOrAccepted = $this->makeRestaurantOrderAccepted($orderToConfirm, $request->restaurant_id);

	 		// checking if order is for home-delivery, order is from customer and any rider has been assigned yet
			if ($orderToConfirm->order_type==='home-delivery' && $orderToConfirm->orderer() instanceof Customer && !$orderToConfirm->riderAssignment()->count()) {

				// Broadcast for rider
				$this->makeRiderOrderCall($orderToConfirm);

			}
	 		
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

		// Checking if this restaurant is in ordered-restaurants
		if ($orderToCancel->restaurants()->where('restaurant_id', $request->restaurant_id)->count()) {
			
			$this->cancelRestaurantOrderRequest($orderToCancel, $request->restaurant_id);
			$this->makeRestaurantOrderCancelationReason($orderToCancel, $request->reason_id, $request->restaurant_id);
			$this->updateRestaurantEvaluation($request->restaurant_id);

			// Broadcast to admin for restaurant order cancelation
	 		$this->notifyAdmin($orderToCancel);

	 		return $this->showAllRestaurantOrders($request->restaurant_id, $perPage);
		}

		return response('Bad Request', 401);
	}

// Done

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

	private function notifyRider(Order $order)
	{
		Log::info('UpdateRider');
		event(new UpdateRider($order));
	}

	private function makeRestaurantOrderCalls(Order $order)
	{
		// checking for order confirmation
        if ($order->call_confirmation===1) {
           
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
		// if same restaurnat hasn't already cancelled 
	 	if (!$orderToCancel->restaurantOrderCancelations()->where('restaurant_id', $restaurant)->count()) {
		 	
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
		return $order->restaurantAcceptances()->where('restaurant_id', $restaurant)->update(['food_order_acceptance' => 1]);
	}

	private function makeRestaurantOrderReady(Order $order, $restaurant)
	{
		return $order->orderReadyConfirmations()->create([
										 			'food_ready_confirmation' => 1,
										 			'restaurant_id' => $restaurant,
										 		]);
	}

	private function makeRiderOrderCall(Order $order)
	{	
		// Broadcast to Rider for order request
		$this->notifyRider($order);
	}

}
