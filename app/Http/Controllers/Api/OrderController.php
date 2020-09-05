<?php

namespace App\Http\Controllers\Api;

use App\Models\Order;
use App\Models\Restaurant;
use App\Events\UpdateAdmin;
use Illuminate\Http\Request;
use App\Models\CustomerAddress;
use App\Events\UpdateRestaurant;
use App\Models\OrderedRestaurant;
use App\Models\TableBookingDetail;
use App\Models\RestaurantMenuItem;
use App\Models\RiderDeliveryRecord;
// use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\OrderRequest;
use App\Http\Requests\Api\ReservationRequest;
use App\Http\Requests\Api\ReservationConfirmationRequest;

class OrderController extends Controller
{
    public function createNewOrder(OrderRequest $request)
    {        
        $newOrder = Order::create([
            'order_type' => $request->order_type,
            'is_asap_order' => $request->is_asap_order ?? false,
            'order_schedule' => $request->order_schedule ?? NULL,
            'order_price' => $request->order_price,
            'vat' => $request->vat,
            'discount' => $request->discount,
            'delivery_fee' => $request->delivery_fee,
            'net_payable' => $request->net_payable,
            'payment_method' => $request->payment_method,
            'cutlery_addition' => $request->cutlery_addition ?? false,
            'orderer_type' => $request->orderer_type=='customer' ? "App\Models\Customer" : "App\Models\Waiter",
            'orderer_id' => $request->orderer_id,
            'customer_confirmation' => ($request->orderer_type==='customer' && $request->payment_method==='cash') ? -1 : 1, 
        ]);

        if ($request->payment_method !=='cash' && $request->payment_id) {
            $newOrderPayment = $newOrder->payment()->create([
                'payment_id'=>$request->payment_id
            ]);
        }

        $request->orderItems = json_decode(json_encode($request->orderItems));

        foreach ($request->orderItems as $orderItem) {
            
            $orderedNewRestaurant = $newOrder->restaurants()->create([
                'restaurant_id' => $orderItem->restaurant_id,
            ]);

            $request->menuItems = json_decode(json_encode($orderItem->menuItems));

            foreach ($request->menuItems as $menuItem) {

                $orderedNewItem = $orderedNewRestaurant->items()->create([
                     'restaurant_menu_item_id' => $menuItem->id,
                     'quantity' => $menuItem->quantity,
                ]);

                $addedMenuItem = RestaurantMenuItem::find($menuItem->id);

                if ($addedMenuItem->has_variation && !empty($menuItem->itemVariations) && !empty($menuItem->itemVariations->id)) {
                    $orderedNewItem->selectedItemVariation()->create([
                        'restaurant_menu_item_variation_id'=>$menuItem->itemVariations->id
                    ]);
                }

                if ($addedMenuItem->has_addon && !empty($menuItem->itemAddons)) {
                    foreach ($menuItem->itemAddons as $itemAddon) {
                        $orderedNewItem->additionalOrderedAddons()->create([
                            'restaurant_menu_item_addon_id'=>$itemAddon->id,
                            'quantity'=>$itemAddon->quantity,
                        ]);
                    }
                }

                if ($addedMenuItem->customizable && !empty($menuItem->customization)) {
                    $orderedNewItem->orderedItemCustomization()->create([
                        'custom_instruction'=>$menuItem->customization,
                    ]);
                }
            }
        }

        // if ($request->orderer_type==='customer') {
            
        if ($request->order_type==='home-delivery') {

            if ($request->delivery_new_address) {
                
                $request->delivery_new_address = json_decode(json_encode($request->delivery_new_address));

                $customerNewAddress = CustomerAddress::create([
                    'house' => $request->delivery_new_address->house,
                    'road' => $request->delivery_new_address->road,
                    'additional_hint' => $request->delivery_new_address->additional_hint ?? NULL,
                    'lat' => $request->delivery_new_address->lat,
                    'lang' => $request->delivery_new_address->lang,
                    'address_name' => $request->delivery_new_address->address_name,
                    'customer_id' => $request->orderer_id,
                ]);
            }
        
            $newOrderAddress = $newOrder->delivery()->create([
                'additional_info'=>$request->delivery_additional_info,
                'delivery_address_id'=>$request->delivery_address_id ?? $customerNewAddress->id,
            ]);
        }

        // }
        
        if ($newOrder->customer_confirmation==1) {
            $this->makeRestaurantOrderCalls($newOrder);
        }

        $this->notifyAdmin($newOrder);

        return response()->json([
            'success' => 'Order has been taken successfully'
        ], 201);
    }

    public function createNewReservation(ReservationRequest $request)
    {   
        $request->order = json_decode(json_encode($request->order));
        $request->payment = json_decode(json_encode($request->payment));
        $request->reservation = json_decode(json_encode($request->reservation));

        $expectedRestaurant = Restaurant::find($request->reservation->restaurant_id);

        $newOrder = $this->createReservationOrder($request);
        $this->createTableReservation($expectedRestaurant, $request, $newOrder->id);
        $this->updateRestaurantBookingStatus($expectedRestaurant, $request->reservation);
        $orderedRestaurant = $this->createOrderedRestaurant($newOrder, $request->reservation->restaurant_id);

        $reservationMsg = 'Reservation request has been accepted';

        if ($newOrder->customer_confirmation==1 && !empty($request->menuItems)) {

            $this->saveNewPayment($newOrder, $request->payment->payment_id);
            $this->makeRestaurantOrderRecord($newOrder, $request->reservation->restaurant_id);
            $this->makeOrderItems($orderedRestaurant, json_decode(json_encode($request->menuItems)));
            // $this->confirmReservation($newOrder, $orderedRestaurant, $request);

            $reservationMsg = 'Reservation request has been confirmed';

        }

        // Broadcast for admin
        $this->notifyAdmin($newOrder);
        // Broadcast for restaurant
        $this->notifyRestaurant($orderedRestaurant);

        return response()->json([
            'success' => $reservationMsg
        ], 201);
    }

    public function confirmReservation(ReservationConfirmationRequest $request)
    {
        $request->reservation = json_decode(json_encode($request->reservation));

        $expectedReservation = TableBookingDetail::find($request->reservation->reservation_id);

        if (!$expectedReservation->booking_confirmation && $expectedReservation->max_payment_time->lessThan(now())) {
            return response()->json([
                'message' => 'Payment time is over'
            ], 403);
        }
        else if ($expectedReservation->booking_confirmation) {
            return response()->json([
                'message' => 'Already confirmed reservation'
            ], 403);
        }
        
        $expectedOrder = Order::find($request->reservation->order_id);
        $request->payment = json_decode(json_encode($request->payment));
        $expectedOrderedRestaurant = $expectedOrder->restaurants->first();
        // $expectedOrderedRestaurant = OrderedRestaurant::find($request->reservation->ordered_restaurant_id);

        $this->confirmOrder($expectedOrder);
        $this->confirmTableReservation($expectedReservation);
        $this->saveNewPayment($expectedOrder, $request->payment->payment_id);
        $this->makeRestaurantOrderRecord($expectedOrder, $request->reservation->restaurant_id);
        $this->makeOrderItems($expectedOrderedRestaurant, json_decode(json_encode($request->menuItems)));

        // Broadcast for admin
        $this->notifyAdmin($expectedOrder);
        // Broadcast for restaurant
        $this->makeRestaurantOrderCalls($expectedOrder);

        return response()->json([
            'success' => 'Reservation has been confirmed'
        ], 201);
    }

    private function confirmTableReservation(TableBookingDetail $table)
    {
        $table->update([
            'booking_confirmation' => 1
        ]);
    }

    private function confirmOrder(Order $order)
    {
        $order->update([
            'customer_confirmation' => 1
        ]);
    }

    private function createReservationOrder(Request $request)
    {
        return Order::create([
            'order_type' => $request->order->order_type,
            'is_asap_order' => false,
            'order_schedule' => $request->reservation->arriving_time,
            'order_price' => $request->order->order_price,
            'vat' => $request->order->vat,
            'discount' => $request->order->discount,
            'delivery_fee' => 0,
            'net_payable' => $request->order->net_payable,
            'payment_method' => $request->payment->payment_method,
            'cutlery_addition' => $request->order->cutlery_addition ?? false,
            'orderer_type' => "App\Models\Customer",
            'orderer_id' => $request->order->orderer_id,
            'customer_confirmation' => ($request->payment->payment_method!=='cash' && $request->payment->payment_id) ? 1 : -1, 
        ]);
    }

    private function createTableReservation(Restaurant $restaurant, Request $request, $orderId)
    {
        $newReservation = $restaurant->reservations()->create([
            'guest_number'=>$request->reservation->guest_number,
            'mobile'=>$request->reservation->mobile,
            'booking_confirmation'=>($request->payment->payment_method!=='cash' && $request->payment->payment_id) ? 1 : 0,   // cancelled by default
            'order_id'=>$orderId,
            'max_payment_time'=> now()->addMinutes(60),          // delay time should be as per restaurant choice
        ]);
    }

    private function updateRestaurantBookingStatus(Restaurant $restaurant, $reservation)
    {
        $restaurant->booking()->update([
            'engaged_seat' => ($restaurant->booking->engaged_seat + $reservation->guest_number),
            'seat_left' => ($restaurant->booking->seat_left - $reservation->guest_number),
        ]);
    }

    private function saveNewPayment(Order $order, $paymentId)
    {
        $newOrderPayment = $order->payment()->create([
            'payment_id'=>$paymentId,
        ]);
    }

    private function createOrderedRestaurant(Order $order, $restaurantId)
    {
        return  $orderedNewRestaurant = $order->restaurants()->create([
                    'restaurant_id' => $restaurantId,
                ]); 
    }

    private function makeOrderItems(OrderedRestaurant $orderedNewRestaurant, $menuItems) 
    {
        // foreach ($orderItems as $orderItem) {            

            // $menuItems = json_decode(json_encode($menuItems));

            foreach ($menuItems as $menuItem) {

                $orderedNewItem = $orderedNewRestaurant->items()->create([
                     'restaurant_menu_item_id' => $menuItem->id,
                     'quantity' => $menuItem->quantity,
                ]);

                $addedMenuItem = RestaurantMenuItem::find($menuItem->id);

                if ($addedMenuItem->has_variation && !empty($menuItem->itemVariations) && !empty($menuItem->itemVariations->id)) {
                    $orderedNewItem->selectedItemVariation()->create([
                        'restaurant_menu_item_variation_id'=>$menuItem->itemVariations->id
                    ]);
                }

                if ($addedMenuItem->has_addon && !empty($menuItem->itemAddons)) {
                    foreach ($menuItem->itemAddons as $itemAddon) {
                        $orderedNewItem->additionalOrderedAddons()->create([
                            'restaurant_menu_item_addon_id'=>$itemAddon->id,
                            'quantity'=>$itemAddon->quantity,
                        ]);
                    }
                }

                if ($addedMenuItem->customizable && !empty($menuItem->customization)) {
                    $orderedNewItem->orderedItemCustomization()->create([
                        'custom_instruction'=>$menuItem->customization,
                    ]);
                }

            }

        // }

    }

    private function makeRestaurantOrderRecord(Order $order, $restaurantId)
    {
        $order->restaurantAcceptances()->create([
            'food_order_acceptance' => -1, // ringing
            'restaurant_id' => $restaurantId,
        ]); 
    }

    private function makeRestaurantOrderCalls(Order $order)
    {
        // checking if already has made
        if (!$order->restaurantAcceptances()->exists()) {
           
            foreach ($order->restaurants as $orderedRestaurant) {

                // make restaurant new order record
                $this->makeRestaurantOrderRecord($order, $orderedRestaurant->restaurant_id);
              
                // Broadcast for restaurant
                $this->notifyRestaurant($orderedRestaurant);

            }

        }
    }

    private function notifyRestaurant(OrderedRestaurant $orderedRestaurant)
    {
        // Log::info('UpdateRestaurant');
        event(new UpdateRestaurant($orderedRestaurant));
    }

    private function notifyAdmin(Order $order) 
    {
        // Log::info('UpdateAdmin');
        event(new UpdateAdmin($order));

    }

}
