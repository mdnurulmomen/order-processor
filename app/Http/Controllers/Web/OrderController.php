<?php

namespace App\Http\Controllers\Web;

use App\Models\Order;
use App\Models\Rider;
use App\Models\Customer;
use App\Models\Merchant;
use App\Jobs\NotifyRiders;
use App\Events\UpdateRider;
use App\Events\UpdateAdmin;
use Illuminate\Http\Request;
use App\Events\UpdateAgents;
use App\Models\MerchantOrder;
use App\Models\RiderDelivery;
use App\Events\UpdateMerchant;
use App\Models\DeliveryAddress;
use App\Models\ApplicationSetting;
use App\Http\Controllers\Controller;
use App\Jobs\MonitorOrderProgression;
use App\Http\Resources\Web\OrderResource;
use App\Http\Resources\Web\OrderCollection;
use App\Http\Resources\Web\MerchantOrderCollection;

class OrderController extends Controller
{
	// Admin
	public function showOrderDetail($order)
	{
		return new OrderResource(Order::with(['orderer', 'schedule', 'merchants.products.merchantProduct', 'merchants.products.variation.merchantProductVariation.variation', 'merchants.products.addons.merchantProductAddon.addon', 'merchants.merchant', 'merchants.take', 'merchants.selfDelivery', 'riderAssigned', 'collections.merchant', 'serve', 'merchantOrderCancellations.canceller', 'merchantOrderCancellations.cancellationReason', 'adminOrderCancellation.canceller', 'adminOrderCancellation.cancellationReason', 'payment', 'address.customerAddress'])->find($order));
	}

	public function showAllOrders($perPage = false)
	{
		if ($perPage) {

			return response()->json([

				'all' => new OrderCollection(Order::with(['merchants.merchant', 'merchants.selfDelivery', 'merchants.take', 'riderAssigned', 'readyMerchants.merchant', 'collections.merchant', 'serve', 'merchantOrderCancellations.canceller', 'adminOrderCancellation.canceller'])->latest()->paginate($perPage)),

				'unconfirmed' => new OrderCollection(Order::where('customer_confirmation', -1)->with(['merchants.merchant', 'merchants.selfDelivery', 'merchants.take', 'riderAssigned', 'readyMerchants.merchant', 'collections.merchant', 'serve', 'merchantOrderCancellations.canceller', 'adminOrderCancellation.canceller'])->latest()->paginate($perPage)),

				'active' => new OrderCollection(Order::where('customer_confirmation', 1)->where('in_progress', 1)->with(['merchants.merchant', 'merchants.selfDelivery', 'merchants.take', 'riderAssigned', 'readyMerchants.merchant', 'collections.merchant', 'serve', 'merchantOrderCancellations.canceller', 'adminOrderCancellation.canceller'])->latest()->paginate($perPage)),

				'prepaid' => new OrderCollection(Order::with(['merchants.merchant', 'merchants.selfDelivery', 'merchants.take', 'riderAssigned', 'readyMerchants.merchant', 'collections.merchant', 'serve', 'merchantOrderCancellations.canceller', 'adminOrderCancellation.canceller'])
					->has('payment')->latest()->paginate($perPage)),

				'postpaid' => new OrderCollection(Order::with(['merchants.merchant', 'merchants.selfDelivery', 'merchants.take', 'riderAssigned', 'readyMerchants.merchant', 'collections.merchant', 'serve', 'merchantOrderCancellations.canceller', 'adminOrderCancellation.canceller'])
					->doesntHave('payment')->latest()->paginate($perPage)),

				'deliveredOrServed' => new OrderCollection(Order::where('success_rate', 100)->with(['merchants.merchant', 'merchants.selfDelivery', 'merchants.take', 'riderAssigned', 'readyMerchants.merchant', 'collections.merchant', 'serve', 'merchantOrderCancellations.canceller', 'adminOrderCancellation.canceller'])
					->latest()->paginate($perPage)),	

				'partial' => new OrderCollection(Order::where('success_rate', '>', 0)->where('success_rate', '<', 100)->with(['merchants.merchant', 'merchants.selfDelivery', 'merchants.take', 'riderAssigned', 'readyMerchants.merchant', 'collections.merchant', 'serve', 'merchantOrderCancellations.canceller', 'adminOrderCancellation.canceller'])
					->latest()->paginate($perPage)),				

				'cancelled' => new OrderCollection(Order::with(['merchants.merchant', 'merchants.selfDelivery', 'merchants.take', 'riderAssigned', 'readyMerchants.merchant', 'collections.merchant', 'serve', 'merchantOrderCancellations.canceller', 'adminOrderCancellation.canceller'])
					->where('success_rate', 0)
					->latest()->paginate($perPage)),

			], 
			200);

		}

	 	// return response(Order::get(), 200);
	}

	// Admin
	public function confirmNewOrder(Request $request, $perPage)
	{
		$request->validate([
			'id' => 'required|exists:orders,id',
	 		// 'type' => 'required|in:delivery,serving,collection'
		]);

		$orderToConfirm = Order::findOrFail($request->id);

	 	if ($orderToConfirm->customer_confirmation) {			// -1 / 1

	 		$this->confirmCustomerOrderRequest($orderToConfirm);

		 	// merchant call for all orders 
	 		$this->makeMerchantCalls(
	 			$orderToConfirm, 

	 			$orderToConfirm->merchants()
	 			->where(function ($query) {
			 		$query->where('is_self_delivery', 1)        // for delivery orders
			       	->orWhereNull('is_self_delivery');    // for non-delivery order
			       })
	 			->get()
	 		);

	 		// make rider call if merchant has delivery support
	 		if ($orderToConfirm->type=='delivery' && $orderToConfirm->merchants()->where('is_self_delivery', 0)->exists()) {

	 			$this->makeRiderOrderCall($orderToConfirm);

	 		}

	 	}

	 	return $this->showAllOrders($perPage);
	 }

	// Admin Panel
	 public function cancelNewOrder(Request $request, $order, $perPage)
	 {
	 	$request->validate([
	 		'canceller' => 'required|string|in:customer,rider,merchants,admin',
	 		'cancellation_reason_id' => 'required|numeric|exists:cancellation_reasons,id',
	 		'merchant_id' => 'required_if:canceller,merchants',
	 		// 'rider_id' => 'required_if:canceller,rider',
	 	]);

	 	$orderToCancel = Order::findOrFail($order);

		// already customer or same merchant cancelled this order or order is stopped
	 	if ($orderToCancel->customerOrderCancellation()->exists() || $orderToCancel->merchantOrderCancellations()->where('canceller_id', $request->merchant_id)->exists() || $orderToCancel->in_progress === 0) {

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

		 	// making cancellation reason
	 		$this->makeCustomerOrderCancellationReason($orderToCancel, $request->cancellation_reason_id);

	 	}

	 	// if the merchant actually belongs to ordered-merchants and accepted the food request and order has been picked
	 	else if ($request->canceller==='merchants' && $orderToCancel->customer_confirmation===1 && $orderToCancel->merchants()->where('merchant_id', $request->merchant_id)->exists() && ! $orderToCancel->collections()->where('merchant_id', $request->merchant_id)->where('is_collected', 1)->exists()) {

		 	// cancelling just arrived order request
	 		$this->cancelMerchantOrderRequest($orderToCancel, $request->merchant_id);

		 	// making cancellation reason
	 		$this->makeMerchantOrderCancellationReason($orderToCancel, $request->cancellation_reason_id, $request->merchant_id);

			// evaluating merchant
	 		$this->updateMerchantEvaluation($request->merchant_id);

			// Broadcast to restaruant for order cancellation with MerchantOrder Model
	 		$this->notifyMerchant($orderToCancel->merchants()->where('merchant_id', $request->merchant_id)->first());

			// if all restauraant cancelled this order
	 		if ($orderToCancel->merchants->count() == $orderToCancel->merchantOrderCancellations->count()) {

	 			$this->disableOrderStatus($orderToCancel);

	 		}

			// inform rider on merchant-cancellation if any rider has been assigned and rider is to pick from the merchant
	 		if ($orderToCancel->riderAssigned()->exists() && $orderToCancel->merchants()->where('merchant_id', $request->merchant_id)->where('is_self_delivery', 0)->exists()) {

	 			$this->notifyRider($orderToCancel->riderAssigned);

	 		}

	 	}

	 	// if any rider is actually assigned and the food has't been picked up yet 
	 	else if ($request->canceller==='rider' && $orderToCancel->customer_confirmation===1 && $orderToCancel->riderAssigned()->exists() && ! $orderToCancel->collections()->where('is_collected', 1)->exists()) {

		 	// deleting related rider food pick records if any
	 		$orderToCancel->collections()->delete();

		 	// retreiving the expected RiderDeliveryOrder
	 		$riderDeliveryOrderToCancel = $orderToCancel->riderAssigned;

		 	// cancelling accepted order-delivery
	 		$this->cancelRiderDeliveryOrder($orderToCancel);

		 	// reason of the cancelled order
	 		$this->makeRiderDeliveryCancellationReason($orderToCancel, $request->cancellation_reason_id);

		 	// evaluating related rider
	 		$this->updateRiderEvaluation($orderToCancel->riderAssigned->rider_id);

		 	// inform rider about delivery-order cancellation
	 		$this->notifyRider($riderDeliveryOrderToCancel);

		 	// making rider call to assign another food man for the order
	 		$this->makeRiderOrderCall($orderToCancel);

	 	}

	 	// stop order at any phase until order delivered / served
	 	else if ($request->canceller=='admin' && $orderToCancel->customer_confirmation===1 && $orderToCancel->success_rate===-1) {
	 		
	 		$this->disableOrderStatus($orderToCancel);

	 		$this->makeAdminOrderCancellationReason($orderToCancel, $request->cancellation_reason_id);

	 		$this->notifyOrderMerchants($orderToCancel);

	 		// inform rider on merchant-cancellation if any rider has been assigned
	 		if ($orderToCancel->riderAssigned()->exists()) {

	 			$this->notifyRider($orderToCancel->riderAssigned);

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
	 	$query = Order::with(['merchants.merchant', 'riderAssigned', 'readyMerchants.merchant', 'collections.merchant', 'serve', 'merchantOrderCancellations.canceller'])
	 	->where('id', 'like', "%$search%")
	 	->orWhere('type', 'like', "%$search%")
	 	->orWhere('price', 'like', "%$search%")
	 	->orWhere('vat', 'like', "%$search%")
	 	->orWhere('discount', 'like', "%$search%")
	 	->orWhere('delivery_fee', 'like', "%$search%")
	 	->orWhere('net_payable', 'like', "%$search%")
	 	->orWhere('payment_method', 'like', "%$search%")
	 	->orWhere('orderer_type', 'like', "%$search%")
	 	->orWhere('orderer_id', 'like', "%$search%")
	 	->orWhere('customer_confirmation', 'like', "%$search%");

        // Retrieve comments associated to posts or videos with a title like foo%...
	 	$query->whereHasMorph('orderer', ['App\Models\Customer', 'App\Models\MerchantAgent'], function($q)use ($search) {
	 		$q->where('first_name', 'like', "%$search%");
	 		$q->orWhere('last_name', 'like', "%$search%");
	 		$q->orWhere('user_name', 'like', "%$search%");
	 		$q->orWhere('mobile', 'like', "%$search%");
	 		$q->orWhere('email', 'like', "%$search%");
	 	});

	 	$query->orWhereHas('merchants.products.merchantProduct', function($q)use ($search){
	 		$q->where('name', 'like', "%$search%");
	 		$q->orWhere('quantity', 'like', "%$search%");
	 	});

	 	$query->orWhereHas('merchants.merchant', function($q)use ($search){
	 		$q->where('order_id', 'like', "%$search%");
	 		$q->orWhere('merchant_id', 'like', "%$search%");
	 		$q->orWhere('name', 'like', "%$search%");
	 	});

	 	$query->orWhereHas('riderAssigned.rider', function($q)use ($search){
	 		$q->where('rider_id', 'like', "%$search%");
	 		$q->orWhere('user_name', 'like', "%$search%");
	 	});

	 	$query->orWhereHas('serve', function($q)use ($search){
	 		$q->where('confirmer_id', 'like', "%$search%");
	 	});

	 	$query->orWhereHas('payment', function($q)use ($search){
	 		$q->where('payment_id', 'like', "%$search%");
	 	});

	 	$query->orWhereHas('address.customerAddress', function($q)use ($search){
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

	// Merchant
	 public function showMerchantAllOrders($merchant, $perPage = false)
	 {
	 	if ($perPage) {

	 		return response()->json([

	 			'all' => new MerchantOrderCollection(MerchantOrder::where('merchant_id', $merchant)->with(['selfDelivery', 'products.merchantProduct', 'products.variation.merchantProductVariation.variation', 'products.addons.merchantProductAddon.addon', 'products.customization', 'order.orderer', 'serve'])
	 				->with(['merchantOrderCancellations' => function ($query) use ($merchant) {
	 					$query->where('canceller_id', $merchant);
	 				}])
	 				->where(function ($query) {
					   $query->whereNull('is_self_delivery')		// Non delivery order
					   ->orWhere('is_self_delivery', 1)			// Delivery order but had own delivery
					   ->orWhere(function ($query2) {
							$query2->where('is_self_delivery', 0)			// Delivery order and rider also found
							->where('is_rider_available', 1);
						});
					})
	 				->whereHas('order', function($q){
	 					$q->where('customer_confirmation', 1)
	 					->orWhere('type', 'reservation');
	 				})
	 				->latest()
	 				->paginate($perPage)),

	 			'served' => new MerchantOrderCollection(MerchantOrder::where('merchant_id', $merchant)->with(['products.merchantProduct', 'products.variation.merchantProductVariation.variation', 'products.addons.merchantProductAddon.addon', 'products.customization', 'order.orderer', 'serve'])
	 				->with(['merchantOrderCancellations' => function ($query) use ($merchant) {
	 					$query->where('canceller_id', $merchant);
	 				}])
	 				->whereHas('serve', function($q){
	 					$q->where('is_served', 1);
	 				})
	 				->latest()
	 				->paginate($perPage)),

	 		], 200);

	 	}

	 	// return response(Order::get(), 200);
	 }

	 public function confirmMerchantOrder(Request $request, $order, $perPage)
	 {
	 	// validation
	 	$request->validate([
	 		'merchant_id' => [
	 			'required','exists:merchants,id',
	 			function ($attribute, $value, $fail) use ($order) {
	 				$merchantExist = MerchantOrder::where('merchant_id', $value)
	 				->where('order_id', $order)
	 				->exists();

	 				if (!$merchantExist) {
	 					$fail('Invalid Merchant or Order');
	 				}
	 			},
	 		],
	 		'orderReady' => 'boolean',
	 		'serveOrder' => 'boolean',
	 		'deliverOrder' => 'boolean',
	 		'givenOrder' => [
	 			'boolean',
	 			function ($attribute, $value, $fail) use ($order) {
	 				$orderExist = Order::where('id', $order)
	 				->where('type', 'take-away')
	 				->exists();

	 				if (!$orderExist) {
	 					$fail('Invalid Merchant or Order');
	 				}
	 			}
	 		]
	 ]);

	 	$orderToConfirm = Order::findOrFail($order);
	 	$merchantOrderToUpdate = $orderToConfirm->merchants()->where('merchant_id', $request->merchant_id)->firstOrFail();

	 	// already customer or same merchant cancelled this order or order is stopped or completed
	 	if (! $this->confirmedOrder($orderToConfirm) || $orderToConfirm->merchantOrderCancellations()->where('canceller_id', $request->merchant_id)->exists() || $orderToConfirm->in_progress === 0 || $orderToConfirm->success_rate > -1) {

	 		return $this->showMerchantAllOrders($request->merchant_id, $perPage);

	 	}
	 	else if ($request->givenOrder) {

	 		$this->updateMerchantOrderTakenStatus($merchantOrderToUpdate, 'App\Models\Merchant', $request->merchant_id);
	 		$this->updateMerchantOrderReadyStatus($merchantOrderToUpdate);
	 		$this->updateMerchantOrderAcceptanceStatus($merchantOrderToUpdate);
	 		$this->updateMerchantOrderAccomplishmentStatus($merchantOrderToUpdate);

	 	}
	 	else if ($request->deliverOrder) {

	 		$this->updateMerchantOrderSelfDeliveryStatus($merchantOrderToUpdate, 'App\Models\Merchant', $request->merchant_id);
	 		$this->updateMerchantOrderReadyStatus($merchantOrderToUpdate);
	 		$this->updateMerchantOrderAcceptanceStatus($merchantOrderToUpdate);
	 		$this->updateMerchantOrderAccomplishmentStatus($merchantOrderToUpdate);

	 	}
	 	else if ($request->serveOrder) {

	 		$orderServed = $this->makeMerchantOrderServed($orderToConfirm, 'App\Models\Merchant', $request->merchant_id);
	 		$this->updateMerchantOrderReadyStatus($merchantOrderToUpdate);
	 		$this->updateMerchantOrderAcceptanceStatus($merchantOrderToUpdate);
	 		$this->updateMerchantOrderAccomplishmentStatus($merchantOrderToUpdate);

	 	}
	 	else if ($request->orderReady) {

	 		$this->updateMerchantOrderReadyStatus($merchantOrderToUpdate);
	 		$this->updateMerchantOrderAcceptanceStatus($merchantOrderToUpdate);

	 	}
	 	else {

	 		$this->updateMerchantOrderAcceptanceStatus($merchantOrderToUpdate);

	 	}

 		// update main order according to all merchant order accomplished or stoped progression
	 	if ($this->allMerchantOrderProgressionIsStopped($orderToConfirm)) {

	 		$this->stopOrderProgression($orderToConfirm);

	 	}

 		// checking if order is for delivery and order is from customer and any rider notification is already broadcasted
	 	if($orderToConfirm->riderAssigned()->exists() && $merchantOrderToUpdate->where('is_self_delivery', 0)->exists()) {

			// notifying rider about updation
	 		$this->notifyRider($orderToConfirm->riderAssigned);

			// adding new merchant to pick up for assigned rider
	 		$this->makeMerchantCollections($orderToConfirm->riderAssigned);

	 	}

		// delivery order but no rider has been called merchant need not delivery or order is for serving or reservation
	 	else if ($orderToConfirm->type==='serving' || $orderToConfirm->type==='reservation') {

			// make agent call with MerchantOrder
	 		$this->notifyMerchantAgents($merchantOrderToUpdate, $request->merchant_id);

	 	}

 		// Broadcast to admin for merchant order ready confirmation
	 	$this->notifyAdmin($orderToConfirm);

	 	return $this->showMerchantAllOrders($request->merchant_id, $perPage);

	 }

	 public function cancelMerchantOrder(Request $request, $order, $perPage)
	 {
		// validation
	 	$request->validate([
	 		'cancellation_reason_id' => 'required|exists:cancellation_reasons,id',
	 		'merchant_id' => ['required','exists:merchants,id',
	 		function ($attribute, $value, $fail) use ($order) {
	 			$merchantExist = MerchantOrder::where('merchant_id', $value)
	 			->where('order_id', $order)
	 			->where('is_accepted', -1)
	 			->exists();

	 			if (! $merchantExist) {
	 				$fail('Invalid Merchant or Order');
	 			}
	 		},
	 	]
	 ]);

	 	$orderToCancel = Order::findOrFail($order);

		// cancelling just arrived order request
	 	$this->cancelMerchantOrderRequest($orderToCancel, $request->merchant_id);

		// making cancellation reason 
	 	$this->makeMerchantOrderCancellationReason($orderToCancel, $request->cancellation_reason_id, $request->merchant_id);

		// evaluating merchant
	 	$this->updateMerchantEvaluation($request->merchant_id);

 		// if all restauraant cancelled this order
	 	if ($orderToCancel->merchants->count() == $orderToCancel->merchantOrderCancellations->count()) {

	 		$this->disableOrderStatus($orderToCancel);

	 	}

 		// update main order according to all merchant order accomplished or stoped progression
	 	else if ($this->allMerchantOrderProgressionIsStopped($orderToCancel)) {

	 		$this->stopOrderProgression($orderToCancel);

	 	}

		// inform rider on merchant-cancellation if any rider has been assigned
	 	if ($orderToCancel->riderAssigned()->exists() && $orderToCancel->merchants()->where('merchant_id', $request->merchant_id)->where('is_self_delivery', 0)->exists()) {

	 		$this->notifyRider($orderToCancel->riderAssigned);

	 	}

		// Broadcast to admin for merchant order cancellation
	 	$this->notifyAdmin($orderToCancel);

	 	return $this->showMerchantAllOrders($request->merchant_id, $perPage);

		// return response('Bad Request', 401);
	 }


	// For Both Admin & Rider
	 public function showAllRiderOrders($rider = false, $perPage = false)
	 {
	 	if (filter_var($rider, FILTER_VALIDATE_BOOLEAN) && (bool) $perPage) {

	 		return response()->json([

	 			'all' => RiderDelivery::where('rider_id', $rider)
	 			->with(['merchants' => function ($query) {
	 				$query->where('is_self_delivery', 0);
	 			}])
	 			->with(['order.orderer', 'order.schedule', 'merchants.products.merchantProduct', 'merchants.products.variation.merchantProductVariation.variation', 'merchants.products.addons.merchantProductAddon.addon', 'merchants.merchant', 'merchantsAccepted.merchant', 'riderOrderCancellations', 'collections', 'merchantOrderCancellations'])
	 			->latest()
	 			->paginate($perPage),

	 		], 200);

	 	}
	 	else if (! filter_var($rider, FILTER_VALIDATE_BOOLEAN) && (bool) $perPage) {

	 		return response()->json([

	 			'all' => RiderDelivery::with(['merchants' => function ($query) {
	 				$query->where('is_self_delivery', 0)
	 				->with(['products.merchantProduct', 'products.variation.merchantProductVariation.variation', 'products.addons.merchantProductAddon.addon', 'merchant']);
	 			}])
	 			->with(['order.orderer', 'order.schedule', 'merchantsAccepted.merchant', 'riderOrderCancellations', 'collections', 'merchantOrderCancellations'])->latest()->paginate($perPage),

	 		], 200);

	 	}
	 	else {

	 		return response()->json([

	 			'all' => RiderDelivery::with(['merchants' => function ($query) {
	 				$query->where('is_self_delivery', 0)
	 				->with(['products.merchantProduct', 'products.variation.merchantProductVariation.variation', 'products.addons.merchantProductAddon.addon', 'merchant']);
	 			}])
	 			->with(['order.orderer', 'order.schedule', 'merchantsAccepted.merchant', 'riderOrderCancellations', 'collections', 'merchantOrderCancellations'])->latest()->get(),

	 		], 200);

	 	}

	 	return response(Order::get(), 200);
	 }

	 public function confirmRiderDeliveryOrder(Request $request, $order, $perPage)
	 {
        // validation
	 	$request->validate([
	 		'orderReturned' => [
	 			'boolean',
	 			function ($attribute, $value, $fail) use ($request) {
	 				if (empty($value) && empty($request->input('orderDropped')) && empty($request->input('orderPicked')) && empty($request->input('orderAccepted'))) {
	 					$fail('Invalid request');
	 				}
	 			},

	 		],
	 		'orderDropped' => 'boolean',
	 		'orderPicked' => 'boolean',
	 		'orderAccepted' => 'boolean',
	 		'rider_id' => ['required','exists:riders,id',
	 		function ($attribute, $value, $fail) use ($order) {
	 			$riderExist = RiderDelivery::where('rider_id', $value)
	 			->where('order_id', $order)
	 			->exists();

	 			if (!$riderExist) {
	 				$fail('Invalid Rider or Order');
	 			}
	 		}
	 	],
	 	'merchant_id' => 'required_if:orderPicked,true'
	 ]);

	 	$orderToConfirm = Order::findOrFail($order);

	 	$deliveryToConfirm = RiderDelivery::where('rider_id', $request->rider_id)->where('order_id', $order)->first();

	 	if ($request->orderAccepted && $deliveryToConfirm->created_at->diffInSeconds(now()) > $deliveryToConfirm->rider_call_receiving_time) {

	 		return response()->json(
	 			[
	 				'errors' => ['timeOut' => ['Accepting time is over.']]
	 			], 422
	 		);

	 	}

        // if already not accepted
	 	else if ($request->orderAccepted && $deliveryToConfirm->is_accepted == 0) {

            // accepting rider-delivery-order
	 		$deliveryToConfirm->update([
	 			'is_accepted' => 1,
	 			'answered_at' => now(),
	 		]);

            // Nullifying rider paused-time 
            /*
            $deliveryToConfirm->rider()->update([
            	'paused_at' => NULL
            ]);
            */

            $this->updateAllMerchantRiderAvailablityStatus($orderToConfirm->merchants()->where('is_self_delivery', 0)->get());
            $this->makeMerchantCalls($orderToConfirm, $orderToConfirm->merchants()->where('is_self_delivery', 0)->get());

         }
        // confirming pick-up
         else if ($request->orderPicked && $deliveryToConfirm->is_accepted==1) {

         	$merchantToPick = $deliveryToConfirm->collections()->where('rider_id', $request->rider_id)->where('merchant_id', $request->merchant_id)->first();

            // if already not picked
         	if ($merchantToPick && $merchantToPick->is_collected==-1) {

         		$merchantPicked = $merchantToPick->update([
         			'is_collected' => 1,
         		]);

                // as picked, creating delivery status if already not created
         		$deliveryToConfirm->update([
         			'is_delivered' => -1,
         		]);

         	}

         }

        // confirming delivery
      else if ($request->orderDropped && $this->allOrderIsCollected($deliveryToConfirm) /*&& $deliveryToConfirm->is_accepted==1*/) {

      	$updated = $deliveryToConfirm->update([
      		'is_delivered' => 1,
      		'confirmed_at' => now()
      	]);

      	$this->accomplishAllMerchantRiderDeliveryOrders($deliveryToConfirm->order->merchants()->where('is_self_delivery', 0)->get());


      }

        // food returning
        // if delivery record was created
   else if ($request->orderReturned && $this->allOrderIsCollected($deliveryToConfirm) /*&& $deliveryToConfirm->is_accepted==1*/) {

            // deleting delivery record as not deliverd
   	$deliveryToConfirm->update([
            	'is_delivered' => 2, // returned
            	'confirmed_at' => now()
            ]);

   	$this->disableOrderStatus($deliveryToConfirm->order);

   }

	 		// update main order according to all merchant order accomplished or stoped progression
   if ($this->allMerchantOrderProgressionIsStopped($deliveryToConfirm->order)) {

   	$this->stopOrderProgression($deliveryToConfirm->order);

   }

        // Broadcast to admin for merchant order ready confirmation
   $this->notifyAdmin($deliveryToConfirm->order);

   return $this->showAllRiderOrders($request->rider_id, $perPage);

}


    // MerchantAgent
public function showAgentAllOrders($merchant, $perPage = false)
{
	if ($perPage) {

		return response()->json([

			'all' => MerchantOrder::whereHas('order', function($q) {
				$q->where('type', 'serving')
				->orWhere('type', 'reservation');
			})
			->where('is_accepted', 1)
			->where('merchant_id', $merchant)
			->with(['products.merchantProduct', 'products.variation.merchantProductVariation.variation', 'products.addons.merchantProductAddon.addon'])
			->with([
				'orderCancellations' => function($orderCancellationReason) use ($merchant) {
					$orderCancellationReason->where('merchant_id', $merchant);
				}
			])
			->with(['serve', 'order.orderer', 'order.schedule'])
			->latest()
			->paginate($perPage),

		], 200);

	}

	return response(Order::get(), 200);
}

public function confirmAgentOrder(Request $request, $order, $perPage)
{
        // validation
	$request->validate([
		'orderDelivered' => 'required_without:orderServed|boolean',
		'orderServed' => 'required_without:orderDelivered|boolean',
		'merchant_agent_id' => 'required|exists:merchant_agents,id',
		'merchant_id' => ['required',
		function ($attribute, $value, $fail) use ($order) {
                    // if this merchant has this specific agent
			$validAgent = Merchant::find($value)->agents()->where('merchant_id', $value)->exists();

			if (!$validAgent) {
				$fail('Invalid MerchantAgent or Merchant');
			}
		}
	]
]);

	$orderToConfirm = Order::findOrFail($order);
	$merchantOrderToUpdate = $orderToConfirm->merchants()->where('merchant_id', $request->merchant_id)->firstOrFail();

        // if already cancelled order or delivery-order is more than 30 seconds ago 
	if ($this->cancelledOrder($orderToConfirm) || $this->orderIsRiderDeliverable($merchantOrderToUpdate) || $this->orderIsSelfDelivered($merchantOrderToUpdate) || $this->orderIsServed($orderToConfirm)) {

		return $this->showAgentAllOrders($request->merchant_id, $perPage);

	}

        // if order is ready and not already delivered
else if ($request->orderDelivered && $this->orderIsReady($merchantOrderToUpdate) /*&& ! $this->orderIsSelfDelivered($merchantOrderToUpdate)*/) {

	$this->updateMerchantOrderSelfDeliveryStatus($merchantOrderToUpdate, 'App\Models\MerchantAgent', $request->merchant_agent_id);

	$this->updateMerchantOrderAccomplishmentStatus($merchantOrderToUpdate);

	        // Broadcast to admin for merchant order ready confirmation
	$this->notifyAdmin($orderToConfirm);

	        // Broadcast to related merchant about serve
	$this->notifyMerchant($orderToConfirm->merchants()->where('merchant_id', $request->merchant_id)->first());

}

        // if order is ready and not already served
else if ($request->orderServed && $this->orderIsReady($merchantOrderToUpdate) && ! $this->orderIsServed($orderToConfirm)) {

	$this->makeMerchantOrderServed($orderToConfirm, 'App\Models\MerchantAgent', $request->merchant_agent_id);

	$this->updateMerchantOrderAccomplishmentStatus($merchantOrderToUpdate);

	        // Broadcast to admin for merchant order ready confirmation
	$this->notifyAdmin($orderToConfirm);

	        // Broadcast to related merchant about serve
	$this->notifyMerchant($orderToConfirm->merchants()->where('merchant_id', $request->merchant_id)->first());

}

	 		// update main order according to all merchant order accomplished or stoped progression
if ($this->allMerchantOrderProgressionIsStopped($orderToConfirm)) {

	$this->stopOrderProgression($orderToConfirm);

}

return $this->showAgentAllOrders($request->merchant_id, $perPage);

}

private function confirmedOrder(Order $order) 
{
	return $order->customer_confirmation === 1 ? true : false;
}

private function cancelledOrder(Order $order)
{
	return $order->in_progress===0 || $order->customer_confirmation===0 || $order->merchants->count()===$order->merchantOrderCancellations->count() ? true : false;
}

private function orderIsServed(Order $order)
{
	return $order->serve()->exists();
}

private function orderIsSelfDelivered(MerchantOrder $merchantOrder)
{
	return $merchantOrder->is_self_delivery == 1 && $merchantOrder->selfDelivery()->where('is_delivered', 1)->exists();
}

    private function orderIsRiderDeliverable(MerchantOrder $merchantOrder)		// rider-delivery
    {
    	return $merchantOrder->is_self_delivery==0;
    }

    private function allOrderIsCollected(RiderDelivery $deliveryToConfirm)
    {
    	$netMerchantsToPick = $deliveryToConfirm->merchants()->where('is_self_delivery', 0)->count() - $deliveryToConfirm->merchantOrderCancellations->count();

    	$alreadyPicked = $deliveryToConfirm->collections()->where('is_collected', 1)->count();

    	return $netMerchantsToPick === $alreadyPicked ? true : false;
    }

    private function makeAdminOrderCancellationReason(Order $orderToCancel, $reasonId)
    {
    	$currentUser = \Auth::guard('admin')->user();

    	$orderToCancel->adminOrderCancellation()->create([
    		'cancellation_reason_id' => $reasonId,
    		'canceller_type' => get_class($currentUser),
    		'canceller_id' => $currentUser->id,
    	]);
    }

    private function makeRiderDeliveryCancellationReason(Order $orderToCancel, $reasonId)
    {
    	$orderToCancel->riderOrderCancellations()->create([
    		'cancellation_reason_id' => $reasonId,
    		'canceller_type' => 'App\Models\Rider',
    		'canceller_id' => $orderToCancel->riderAssigned->rider_id,
    	]);
    }

    private function cancelRiderDeliveryOrder(Order $orderToCancel)
    {
    	$orderToCancel->riderAssigned()->update([
    		'is_accepted' => 0
    	]);
    }

    private function updateRiderEvaluation($rider)
    {
    	$totalDeliveryRequestReceived = RiderDelivery::where('rider_id', $rider)->where('is_accepted', 1)->count();
    	$totalDeliveryRequestAchieved = RiderDelivery::where('rider_id', $rider)->count();

    	Rider::findOrFail($rider)->update([
    		'order_acceptance_percentage' => ($totalDeliveryRequestReceived / $totalDeliveryRequestAchieved)*100
    	]);
    }

    private function notifyAdmin(Order $order) 
    {
    	event(new UpdateAdmin(Order::with(['merchants.products.merchantProduct', 'merchants.products.variation.merchantProductVariation.variation', 'merchants.products.addons.merchantProductAddon.addon', 'merchants.products.customization', 'riderAssigned', 'collections.merchant', 'serve', 'merchantOrderCancellations.canceller'])->find($order->id)));
    }

    private function notifyMerchant(MerchantOrder $merchantOrder)
    {
    	event(new UpdateMerchant(MerchantOrder::with(['products.merchantProduct', 'products.variation.merchantProductVariation.variation', 'products.addons.merchantProductAddon.addon', 'products.customization', 'order.orderer', 'serve'])
    		->with(['merchantOrderCancellations' => function ($query) use ($merchantOrder) {
    			$query->where('canceller_id', $merchantOrder->merchant_id);
    		}])
    		->find($merchantOrder->id)));
    }

    private function notifyOrderMerchants(Order $order)
    {
    	foreach ($order->merchants as $merchantOrderKey => $merchantOrder) {
    		$this->notifyMerchant($merchantOrder);
    	}
    }

    private function notifyRider(RiderDelivery $riderNewDeliveryRecord)
    {
    	event(new UpdateRider($riderNewDeliveryRecord));
    }

    private function makeMerchantCalls(Order $order, \Illuminate\Database\Eloquent\Collection $merchantOrders)
    {
		// checking for order confirmation and if already has made
    	if ($order->customer_confirmation===1 && ! $merchantOrders->isEmpty()) {

    		foreach ($merchantOrders as $merchantOrder) {

    			$merchantOrder->update([
                 	'is_accepted' => -1, // ringing
                 	'ringing_started_at' => now(),
                 	// 'merchant_id' => $merchantOrder->merchant_id,
                 ]);

              	// Broadcast for merchant
    			$this->notifyMerchant($merchantOrder);

    		}

    	}
    }

    private function makeCustomerOrderCancellationReason(Order $orderToCancel, $reasonId)
    {
		// if same order hasn't already been cancelled by customer
    	if (! $orderToCancel->customerOrderCancellation()->exists()) {

		 	// reason of the cancelled order
    		$customerOrderCancellation = $orderToCancel->customerOrderCancellation()->create([
    			'cancellation_reason_id' => $reasonId,
    			'canceller_type' => 'App\Models\Customer',
    			'canceller_id' => $orderToCancel->orderer->id,
    		]);

    	}
    }

    private function cancelMerchantOrderRequest(Order $orderToCancel, $merchant)
    {
		// Cancelling merchant order acceptance for this order
    	$merchantCancelledAcceptance = $orderToCancel->merchants()
    	->where('merchant_id', $merchant)
    	->update([
    		'is_accepted' => 0,
    		'answered_at' => now(),
    		'in_progress' => 0,
    		'is_completed' => 0
    	]);
    }

    private function makeMerchantOrderCancellationReason(Order $orderToCancel, $reasonId, $merchant)
    {
		// if same restaurnat hasn't already cancelled the same order
    	if (! $orderToCancel->merchantOrderCancellations()->where('canceller_id', $merchant)->exists()) {

		 	// reason of the cancelled order
    		$merchantOrderCancellation = $orderToCancel->merchantOrderCancellations()->create([
    			'cancellation_reason_id' => $reasonId,
    			'canceller_type' => 'App\Models\Merchant',
    			'canceller_id' => $merchant,
    		]);

    	}
    }

    private function updateMerchantEvaluation($merchant)
    {
    	$totalRequestReceived = MerchantOrder::where('merchant_id', $merchant)->count();

    	$totalRequestAccepted = MerchantOrder::where('merchant_id', $merchant)
    	->where('is_accepted', 1)
    	->count();

		// Avoiding O exception
    	if ($totalRequestReceived) {

			// Updating merchant evaluation
    		Merchant::find($merchant)->update([
    			'order_acceptance_percentage' => (($totalRequestAccepted/$totalRequestReceived)*100)
    		]);

    	}
    }

    private function updateMerchantOrderAcceptanceStatus(MerchantOrder $merchantOrderToAccept)
    {
    	$merchantOrderToAccept->update([
    		'is_accepted' => 1,
    		'answered_at' => now(),
    		'in_progress' => 1,
    	]);
    }

    private function orderIsReady(MerchantOrder $merchantOrder)
    {
    	return $merchantOrder->is_ready==1;
    }

    private function updateMerchantOrderReadyStatus(MerchantOrder $merchantOrder)
    {	
    	$merchantOrder->update([
    		'is_ready' => 1,
    		'ready_at' => now()
    	]);
    }

    private function updateMerchantOrderTakenStatus(MerchantOrder $merchantOrder, $confirmerClassName, $confirmerId)
    {
		// if this is self delivery order

 		$merchantOrder->take()->firstOrCreate(
 			[
 				'merchant_id' => $merchantOrder->merchant_id,
				// 'order_id' => $merchantOrder->order_id,
 			],
 			[
 				'is_taken' => 1,
 				'taken_at' => now(),
 				'confirmer_id' => $confirmerId,
 				'confirmer_type' => $confirmerClassName,
 			]
 		);
    	
    }

    private function updateMerchantOrderSelfDeliveryStatus(MerchantOrder $merchantOrder, $confirmerClassName, $confirmerId)
    {
		// if this is self delivery order
    	if ($merchantOrder->is_self_delivery == 1) {

    		$merchantOrder->selfDelivery()->firstOrCreate(
    			[
    				'merchant_id' => $merchantOrder->merchant_id,
					// 'order_id' => $merchantOrder->order_id,
    			],
    			[
    				'is_delivered' => 1,
    				'delivered_at' => now(),
    				'confirmer_id' => $confirmerId,
    				'confirmer_type' => $confirmerClassName,
    			]
    		);
    	}
    }

    private function makeMerchantOrderServed(Order $order, $className, $id)
    {
		// if not already entered for this order & merchant
    	// if (! $order->serve()->exists()) {
    		$order->serve()->updateOrCreate(
    			[
    				'order_id' => $order->id
    			],
    			[
	    			'is_served' => 1,
	    			'confirmer_id' => $id,
	    			'confirmer_type' => $className,
	    		]
    		);
    	// }
    }

	// broadcasting for riders
    private function makeRiderOrderCall(Order $order)
    {	
    	$applicationSettings = ApplicationSetting::firstOrCreate(["id" => 1]);
    	$riderNumberToCall = $applicationSettings->rider_searching_time / $applicationSettings->rider_call_receiving_time;

		// to calculate the nearest rider laterly
    	$allNearestAvailableRiders = $this->findNearestAvailableRiders($order, $riderNumberToCall);

		if ($allNearestAvailableRiders->isEmpty()) {		// no rider found
			
			foreach ($order->merchants()->where('is_self_delivery', 0)->get() as $merchantOrder) {
				
				$this->disableMerchantOrder($merchantOrder);

				// UpdateCustomer();

			}

			if (! $order->merchants()->where('is_self_delivery', 1)->exists()) {		// no other merchant left
				
				$this->disableOrderStatus($order);
				
				// Broadcast to admin for merchant order cancellation
				$this->notifyAdmin($order);

			}

		}
		else {

			$delay = 0;

			$riderCallReceivingTime = $applicationSettings->rider_call_receiving_time;	// 30 seconds
			
			foreach ($allNearestAvailableRiders as $rider) {
				
				NotifyRiders::dispatch($order, $rider, $riderCallReceivingTime)->delay(now()->addSeconds($delay));

				$delay += $applicationSettings->rider_call_receiving_time;	// 30 seconds

			}

			// Setting a job to check if riders found after certain period
			MonitorOrderProgression::dispatch($order)->delay(now()->addSeconds($delay));

		}

	}

	private function notifyMerchantAgents(MerchantOrder $merchantOrder, $merchantId)
	{
		// checking if merchant has any approved agent
		if (Merchant::find($merchantId)->agents()->where('is_approved', 1)->exists()) {
			
			// broadcast for agent with MerchantOrder
			event(new UpdateAgents($merchantOrder));

		}
	}

	// should calculate the nearest rider and snoozing time laterly (now 1 minute)
	private function findNearestAvailableRiders(Order $order, $riderNumberToCall) 
	{
		$alreadyRequestedRiders = $order->riders->pluck('id')->toArray();

		return Rider::whereNull('current_lat') 	// is gonna compare nearest locations among merchants
		->whereNull('current_lang')
		->where('is_approved', true)
		->where('is_available', true)
		->where('is_engaged', false)
		->whereNotIn('id', $alreadyRequestedRiders)
		->orderBy('order_acceptance_percentage', 'desc')
		->take($riderNumberToCall)
		->get();
	}

	private function makeMerchantCollections(RiderDelivery $riderDelivery)
	{	
		foreach ($riderDelivery->merchantsAccepted as $merchantOrder) {

	    	// if not already created order-pick-progression for the same order-delivery
			if (! $riderDelivery->collections()->where('merchant_id', $merchantOrder->merchant_id)->exists()) {

				$riderDelivery->collections()->create([
		            // 'is_collected' => -1,
					'merchant_id'=>$merchantOrder->merchant_id,
					'rider_id'=>$riderDelivery->rider_id,
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
	 		'success_rate' => -1		// -1 (incomplete) / 1 (complete) / 0 (incomplete)
	 	]);
	}

	private function cancelCustomerOrderRequest(Order $orderToCancel)
	{
		// Cancelling customer order request
		$orderToCancel->update([
			'customer_confirmation' => 0,
			'in_progress' => 0,
	 		'success_rate' => 0		// -1 (pending) / 1 (complete) / 0 (incomplete)
	 	]);
	}

	private function updateMerchantOrderAccomplishmentStatus(MerchantOrder $merchantOrder)
	{
		// Confirming merchant-order accomplishment
		$merchantOrder->update([
			'in_progress' => 0,
    		'is_completed' => 1,		// -1 (pending) / 1 (complete) / 0 (incomplete)
    	]);
	}

	private function accomplishAllMerchantRiderDeliveryOrders($merchantOrders)
	{
		foreach ($merchantOrders as $merchantOrder) {
			
			$this->updateMerchantOrderAccomplishmentStatus($merchantOrder);

		}

	}

	private function stopOrderProgression(Order $order)
	{
		$totalMerchantOrders = $order->merchants()->count();
		$acchomplishedMerchantOrders = $order->merchants()->where('is_completed', 1)->count();

		// Confirming merchant-order failure
		$order->update([
			'in_progress' => 0,
    		'success_rate' => ($acchomplishedMerchantOrders * 100 / $totalMerchantOrders),		// -1 (pending) / 1 (complete) / 0 (incomplete)
    	]);
	}

	private function allMerchantOrderProgressionIsStopped(Order $order)
	{
		$totalAvailableMerchants = $order->merchants->count();		// total merchant-orders

		$nonProgressiveMerchantOrders = $order->merchants()			// stopped merchant-orders
		->where('in_progress', 0)
		->count();

		return $totalAvailableMerchants === $nonProgressiveMerchantOrders;
	}

	private function allMerchantOrderIsAccomplished(Order $order)
	{
		$totalAvailableMerchants = $order->merchants->count() - $order->orderCancellations->count();

		// for non-delivery orders
		$nonDeliveryOrderMerchants = $order->merchants()
		->whereNull('is_self_delivery')
		->count();		
		
		// for self-delivery orders
		$selfDeliveryOrderMerchants = $order->merchants()
		->where('is_self_delivery', 1)
		->whereHas('selfDelivery', function ($query) {
			$query->where('is_delivered', 1);
		})
		->count();		

		// for rider-delivery orders
		$riderDeliveryOrderMerchants = $order->merchants()
		->where('is_self_delivery', 0)
		->whereHas('riderAssigned', function ($query) {
			$query->where('is_delivered', 1);
		})
		->count();

		return $totalAvailableMerchants === ($nonDeliveryOrderMerchants + $selfDeliveryOrderMerchants + $riderDeliveryOrderMerchants);
	}

	private function disableOrderStatus(Order $order)
	{
		$order->update([
			'in_progress' => 0,
			'success_rate' => 0		// -1 (pending) / 1 (complete) / 0 (incomplete)
		]);
	}

	private function disableMerchantOrder(MerchantOrder $merchantOrder)
	{
		$merchantOrder->update([
			'is_rider_available' => 0
		]);
	}

	private function updateAllMerchantRiderAvailablityStatus(\Illuminate\Database\Eloquent\Collection $merchantOrders)
	{
		foreach ($merchantOrders as $merchantOrder) {
			
			$merchantOrder->update([
				'is_rider_available' => 1
			]);

		}

	}

}
