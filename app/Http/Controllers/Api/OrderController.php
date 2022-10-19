<?php

namespace App\Http\Controllers\Api;

use App\Models\Order;
use App\Models\Merchant;
use App\Events\UpdateAdmin;
use App\Models\Reservation;
use Illuminate\Http\Request;
use App\Models\MerchantOrder;
use App\Events\UpdateMerchant;
use App\Models\MerchantProduct;
use App\Models\CustomerAddress;
// use App\Models\RiderDeliveryRecord;
// use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\OrderRequest;
use App\Http\Resources\Api\OrderResource;
use App\Http\Requests\Api\ReservationRequest;
use App\Http\Requests\Api\ReservationConfirmationRequest;

class OrderController extends Controller
{
    public function makeNewOrder(OrderRequest $request)
    {        
        $newOrder = Order::create([
            'type' => $request->order->type,
            'price' => $request->order->price,
            'vat' => $request->order->vat,
            'discount' => $request->order->discount,
            'delivery_fee' => $request->order->delivery_fee,
            'net_payable' => $request->order->net_payable,
            'payment_method' => $request->payment->method,
            'orderer_type' => $request->order->orderer_type=='customer' ? "App\Models\Customer" : "App\Models\Waiter",
            'orderer_id' => $request->order->orderer_id,
            'has_cutlery' => $request->order->has_cutlery ? true : false, 
            'is_asap_order' => $request->order->is_asap_order ? true : false, 
            'customer_confirmation' => ($request->order->orderer_type==='customer' && $request->payment->method==='cash') ? -1 : 1, 
            'in_progress' => ($request->order->orderer_type==='customer' && $request->payment->method==='cash') ? -1 : 1,
        ]);

        //  scheduled
        if (! $request->order->is_asap_order) {

            $this->createScheduleOrder($newOrder, $request->order->schedule); 

        }

        if ($request->payment->method !=='cash' && $request->payment->id) {

            $newOrderPayment = $newOrder->payment()->create([
                'payment_id'=>$request->payment->id
            ]);
            
        }

        foreach ($request->merchants as $merchantOrder) {
            
            $merchantNewOrder = $newOrder->merchants()->create([
                'merchant_id' => $merchantOrder->id,
                'has_delivery_support' => Merchant::find($merchantOrder->id)->need_delivery_support,
            ]);

            $request->products = json_decode(json_encode($merchantOrder->products));

            foreach ($request->products as $product) {

                $orderedNewProduct = $merchantNewOrder->products()->create([
                     'merchant_product_id' => $product->id,
                     'quantity' => $product->quantity,
                ]);

                $addedProduct = MerchantProduct::find($product->id);

                if ($addedProduct->has_variation && !empty($product->variation) && !empty($product->variation->id)) {
                    
                    $orderedNewProduct->variation()->create([
                        'merchant_product_variation_id'=>$product->variation->id
                    ]);

                }

                if ($addedProduct->has_addon && !empty($product->addons)) {
                    
                    foreach ($product->addons as $merchantProductAddon) {

                        $orderedNewProduct->addons()->create([
                            'merchant_product_addon_id'=>$merchantProductAddon->id,
                            'quantity'=>$merchantProductAddon->quantity,
                        ]);

                    }

                }

                if ($addedProduct->customizable && !empty($product->customization)) {
                    
                    $orderedNewProduct->customization()->create([
                        'custom_instruction'=>$product->customization,
                    ]);

                }
            }
        }

        // if ($request->orderer_type==='customer') {
            
        if ($request->order->type==='delivery') {

            if (isset($request->order->delivery_new_address) && empty($request->order->delivery_address_id)) {

                $existingAddress = CustomerAddress::where('customer_id', $request->order->orderer_id)->where('address_name', $request->order->delivery_new_address->address_name)->first();

                if ($existingAddress) {
                    
                    $existingAddress->update([
                        'house' => $request->order->delivery_new_address->house,
                        'road' => $request->order->delivery_new_address->road,
                        'additional_hint' => $request->order->delivery_new_address->additional_hint ?? NULL,
                        'lat' => $request->order->delivery_new_address->lat,
                        'lang' => $request->order->delivery_new_address->lang,
                        'address_name' => $request->order->delivery_new_address->address_name,
                        // 'customer_id' => $request->order->orderer_id
                    ]);

                }
                else {

                    $customerNewAddress = CustomerAddress::create([
                        'house' => $request->order->delivery_new_address->house,
                        'road' => $request->order->delivery_new_address->road,
                        'additional_hint' => $request->order->delivery_new_address->additional_hint ?? NULL,
                        'lat' => $request->order->delivery_new_address->lat,
                        'lang' => $request->order->delivery_new_address->lang,
                        'address_name' => $request->order->delivery_new_address->address_name,
                        'customer_id' => $request->order->orderer_id,
                    ]);

                }

            }
        
            $newOrderAddress = $newOrder->address()->create([
                'additional_info'=>$request->order->delivery_additional_info,
                'customer_address_id'=>$request->order->delivery_address_id ?? $existingAddress->id ?? $customerNewAddress->id,
            ]);
        }
        else if ($request->order->type==='serving') {
            
            $newOrder->serve()->create([
                'guest_number'=>$request->order->guest_number,
            ]);

        }

        // }
        
        if ($newOrder->customer_confirmation==1) {

            $this->makeMerchantOrderCalls($newOrder);

        }

        $this->notifyAdmin($newOrder);

        return response()->json([
            'success' => 'Order has been taken successfully'
        ], 201);
    }

    public function makeNewReservation(ReservationRequest $request)
    {   
        $expectedMerchant = Merchant::find($request->reservation->merchant_id);

        $newOrder = $this->createReservationOrder($request);

        $this->createScheduleOrder($newOrder, $request->reservation->arriving_time);
        $this->createTableReservation($expectedMerchant, $request, $newOrder->id);
        $this->updateMerchantBookingStatus($expectedMerchant, $request->reservation);
        $merchantOrder = $this->createMerchantOrderRecord($newOrder, $request->reservation->merchant_id);

        $reservationMsg = 'Reservation request has been accepted';

        if ($newOrder->customer_confirmation==1 && ! empty($request->products) && ! empty($request->payment->id)) {

            $this->saveNewPayment($newOrder, $request->payment->id);
            $this->updateMerchantOrderRecord($merchantOrder);
            $this->makeProductOrders($merchantOrder, $request->products);
            // $this->confirmReservation($newOrder, $merchantOrder, $request);

            $reservationMsg = 'Reservation request has been confirmed';

        }

        // Broadcast for admin
        $this->notifyAdmin($newOrder);
        // Broadcast for merchant
        $this->notifyMerchant($merchantOrder);

        return response()->json([
            'success' => $reservationMsg
        ], 201);
    }

    public function confirmReservation(ReservationConfirmationRequest $request)
    {
        $expectedReservation = Reservation::find($request->reservation->reservation_id);

        if (/* ! $expectedReservation->booking_confirmation && */ $expectedReservation->max_payment_time->lessThan(now())) {
            return response()->json([
                'message' => 'Payment time is over'
            ], 403);
        }
        
        $expectedOrder = Order::find($request->reservation->order_id);

        if ($expectedOrder->customer_confirmation != -1) {
            return response()->json([
                'message' => 'Already confirmed or cancelled reservation'
            ], 422);
        }
        
        $expectedMerchantOrder = $expectedOrder->merchants->first();
        // $expectedMerchantOrder = MerchantOrder::find($request->reservation->ordered_merchant_id);

        $this->confirmOrder($expectedOrder);
        // $this->confirmTableReservation($expectedReservation);
        $this->saveNewPayment($expectedOrder, $request->payment->id);
        $this->updateMerchantOrderRecord($expectedMerchantOrder);
        $this->makeProductOrders($expectedMerchantOrder, $request->products);

        // Broadcast for admin
        $this->notifyAdmin($expectedOrder);
        // Broadcast for merchant
        $this->makeMerchantOrderCalls($expectedOrder);

        return response()->json([
            'success' => 'Reservation has been confirmed'
        ], 200);
    }

    public function getOrderDetails($order)
    {
        return new OrderResource(Order::with(['schedule', 'payment', 'address', 'riderAssigned', 'collections', 'serve', 'customerOrderCancelation', 'merchants.products.merchantProduct', 'merchants.products.variation.merchantProductVariation.variation', 'merchants.products.addons.merchantProductAddon.addon', 'merchants.merchant'])->findOrFail($order));
    }

    /*
    private function confirmTableReservation(Reservation $table)
    {
        $table->update([
            'booking_confirmation' => 1
        ]);
    }
    */

    private function confirmOrder(Order $order)
    {
        $order->update([
            'customer_confirmation' => 1,
            'in_progress' => 1
        ]);
    }

    private function createReservationOrder(Request $request)
    {
        return Order::create([
            'type' => $request->order->type,
            'price' => $request->order->price,
            'vat' => $request->order->vat,
            'discount' => $request->order->discount,
            'delivery_fee' => 0,
            'net_payable' => $request->order->net_payable,
            'payment_method' => $request->payment->method,
            'orderer_type' => "App\Models\Customer",
            'orderer_id' => $request->order->orderer_id,
            'customer_confirmation' => ($request->payment->method !== 'cash' && $request->payment->id) ? 1 : -1, 
            'has_cutlery' => $request->order->has_cutlery ? true : false, 
            'is_asap_order' => /*$request->order->is_asap_order ? true : */ false,
            'in_progress' => ($request->payment->method !== 'cash' && $request->payment->id) ? 1 : -1,
        ]);
    }

    private function createScheduleOrder(Order $order, $schedule)
    {
        $order->schedule()->create([
            'schedule' => $schedule
        ]);
    }

    private function createTableReservation(Merchant $merchant, Request $request, $orderId)
    {
        $newReservation = $merchant->reservations()->create([
            'guest_number'=>$request->reservation->guest_number,
            'mobile'=>$request->reservation->mobile,
            // 'booking_confirmation'=>($request->payment->method!=='cash' && $request->payment->id) ? 1 : 0,   // cancelled by default
            'order_id'=>$orderId,
            'max_payment_time'=> now()->addMinutes(60),          // delay time should be as per merchant choice
        ]);
    }

    private function updateMerchantBookingStatus(Merchant $merchant, $reservation)
    {
        $merchant->booking()->update([
            'engaged_seat' => ($merchant->booking->engaged_seat + $reservation->guest_number),
            'seat_left' => ($merchant->booking->seat_left - $reservation->guest_number),
        ]);
    }

    private function saveNewPayment(Order $order, $paymentId)
    {
        $newOrderPayment = $order->payment()->create([
            'payment_id'=>$paymentId,
        ]);
    }

    private function createMerchantOrderRecord(Order $order, $merchantId)
    {
        return  $merchantOrder = $order->merchants()->create([
            'merchant_id' => $merchantId,
        ]); 
    }

    private function makeProductOrders(MerchantOrder $merchantOrder, $products) 
    {
        // foreach ($merchants as $merchantOrder) {            

            // $products = json_decode(json_encode($products));

            foreach ($products as $product) {

                $orderedNewProduct = $merchantOrder->products()->create([
                     'merchant_product_id' => $product->id,
                     'quantity' => $product->quantity,
                ]);

                $addedProduct = MerchantProduct::find($product->id);

                if ($addedProduct->has_variation && !empty($product->variation) && !empty($product->variation->id)) {
                    $orderedNewProduct->variation()->create([
                        'merchant_product_variation_id'=>$product->variation->id
                    ]);
                }

                if ($addedProduct->has_addon && !empty($product->addons)) {

                    foreach ($product->addons as $merchantProductAddon) {

                        $orderedNewProduct->addons()->create([
                            'merchant_product_addon_id'=>$merchantProductAddon->id,
                            'quantity'=>$merchantProductAddon->quantity,
                        ]);

                    }

                }

                if ($addedProduct->customizable && !empty($product->customization)) {

                    $orderedNewProduct->customization()->create([
                        'custom_instruction'=>$product->customization,
                    ]);

                }

            }

        // }

    }

    private function updateMerchantOrderRecord(MerchantOrder $merchantOrder)
    {
        $merchantOrder->update([
            'is_accepted' => -1, // ringing
            // 'merchant_id' => $merchantId,
        ]); 
    }

    private function makeMerchantOrderCalls(Order $order)
    {
        // checking if already has made
        // if (! $order->merchantAcceptances()->exists()) {
           
            foreach ($order->merchants as $merchantOrder) {

                // make merchant new order record
                $this->updateMerchantOrderRecord($merchantOrder);
              
                // Broadcast for merchant
                $this->notifyMerchant($merchantOrder);

            }

        // }
    }

    private function notifyMerchant(MerchantOrder $merchantOrder)
    {
        // Log::info('UpdateMerchant');
        event(new UpdateMerchant(MerchantOrder::with(['order', 'products.merchantProduct', 'products.variation.merchantProductVariation.variation', 'products.addons.merchantProductAddon.addon', 'products.customization'])->find($merchantOrder->id)));
    }

    private function notifyAdmin(Order $order) 
    {
        // Log::info('UpdateAdmin');
        event(new UpdateAdmin(Order::with(['merchants.products.merchantProduct', 'merchants.products.variation.merchantProductVariation.variation', 'merchants.products.addons.merchantProductAddon.addon', 'merchants.products.customization', 'riderAssigned', 'collections.merchant', 'serve', 'merchantOrderCancelations.canceller'])->find($order->id)));

    }

}
