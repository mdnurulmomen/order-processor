<?php

namespace App\Http\Controllers\Api;

use App\Models\Order;
use App\Models\Restaurant;
use App\Events\UpdateAdmin;
use Illuminate\Http\Request;
use App\Models\CustomerAddress;
use App\Events\UpdateRestaurant;
use App\Models\OrderedRestaurant;
use App\Models\RestaurantMenuItem;
use App\Models\RiderDeliveryRecord;
// use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\OrderRequest;

class OrderController extends Controller
{
    public function createNewOrder(OrderRequest $request)
    {        
        $newOrder = Order::create([
            'order_type' => $request->order_type,
            'is_asap_order' => $request->is_asap_order ?? 0,
            'delivery_datetime' => $request->delivery_datetime ?? NULL,
            'order_price' => $request->order_price,
            'vat' => $request->vat,
            'discount' => $request->discount,
            'delivery_fee' => $request->delivery_fee,
            'net_payable' => $request->net_payable,
            'payment_method' => $request->payment_method,
            'cutlery_addition' => $request->cutlery_addition ?? 0,
            'orderer_type' => $request->orderer_type=='customer' ? "App\Models\Customer" : "App\Models\Waiter",
            'orderer_id' => $request->orderer_id,
            'call_confirmation' => ($request->orderer_type==='customer' && $request->payment_method==='cash') ? -1 : 1, 
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

                if ($addedMenuItem->customizable && !empty($menuItem->customization)) {
                    $orderedNewItem->orderedItemCustomization()->create([
                        'custom_instruction'=>$menuItem->customization,
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
        
        if ($newOrder->call_confirmation==1) {
            $this->makeRestaurantOrderCalls($newOrder);
        }

        $this->notifyAdmin($newOrder);

        return response()->json([
            'success' => 'Order has been taken successfully'
        ], 201);
    }

    private function makeRestaurantOrderCalls(Order $order)
    {
        // checking if already has made
        if (!$order->restaurantAcceptances()->exists()) {
           
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
