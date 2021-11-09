<?php

namespace App\Http\Controllers\Api;

use App\Models\Customer;
use Illuminate\Http\Request;
use App\Models\CustomerAddress;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\UserAddressResource;

class ProfileController extends Controller
{
    public function updateUserSetting(Request $request, $user)
    {
        $request->validate([
            'app_notification'=>'required|boolean',
            'email_notification'=>'required|boolean'
        ]);
        
        $userToUpdate = Customer::findOrFail($user);

        $userToUpdate->app_notification = $request->app_notification ?? false;
        $userToUpdate->email_notification = $request->email_notification ?? false;

        $userToUpdate->save();

        return response()->json([
            'data' => $userToUpdate
        ], 200);
    }

    public function updateUserProfile(Request $request, $user)
    {
        $userToUpdate = Customer::findOrFail($user);
        
        $request->validate([
            'first_name'=>'nullable|string|max:255',
            'last_name'=>'nullable|string|max:255',
            'user_name'=>'string|required|max:255|bail|unique:customers,user_name,'.$userToUpdate->id,
            'mobile'=>'string|required|max:13|bail|unique:customers,mobile,'.$userToUpdate->id,
            'email'=>'email|required|bail|unique:customers,email,'.$userToUpdate->id,
            // 'profile_picture'=>'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $userToUpdate->first_name = $request->first_name;
        $userToUpdate->last_name = $request->last_name;
        $userToUpdate->user_name = $request->user_name;
        $userToUpdate->mobile = $request->mobile;
        $userToUpdate->email = $request->email;
        // $userToUpdate->profile_picture = $request->profile_picture;

        $userToUpdate->save();

        return response()->json([
            'data' => $userToUpdate
        ], 200);
    }

    public function updateUserPassword(Request $request)
    {
        $request->validate([
            'user'=>'numeric|required|exists:customers,id|bail',
            'current_password'=>'string|required|max:255|bail',
            'password'=>'string|required|confirmed|max:255|bail',
        ]);

        $userToUpdate = Customer::findOrFail($request->user);

        if (Hash::check($request->current_password, $userToUpdate->password)) {
            
            $userToUpdate->update([
                'password' => Hash::make($request->password)
            ]);
            
            return response()->json([
               'success' => 'Password has been updated'
            ], 200);
           }

         return response()->json([
            'warning' => "Password doesn't match"
         ], 401);
    }

    public function addUserAddress(Request $request)
    {
        $request->validate([
            'user'=>'string|required|exists:customers,id|bail',
            'address'=>'required',
            'address.house'=>'nullable|string|max:255',
            'address.road'=>'nullable|string|max:255',
            'address.additional_hint'=>'nullable|string|max:255',
            'address.lat'=>'string|required|max:255',
            'address.lang'=>'string|required|max:255',
            'address.address_name'=>'string|required|max:255'
        ]);
        
        $customer = Customer::findOrFail($request->user);

        if ($customer->addresses->count() > 3) {
            return response()->json([
                'warning' => "Customer has already maximum addresses"
             ], 401);
        }

        $customer->addresses()->create([
            'house' => $request->address['house'],
            'road' => $request->address['road'],
            'additional_hint' => $request->address['additional_hint'],
            'lat' => $request->address['lat'],
            'lang' => $request->address['lang'],
            'address_name' => $request->address['address_name']
        ]);

        return response()->json([
            'data' => UserAddressResource::collection($customer->addresses)
        ], 200);
    }

    public function updateUserAddress(Request $request, $user, $address)
    {
        $addressToUpdate = CustomerAddress::findOrFail($address);

        if ($addressToUpdate->customer_id != $user) {
            return response()->json([
                'warning' => "Address doesn't belong to the user"
            ], 401);
        }
        
        $request->validate([
            'house'=>'nullable|string|max:255',
            'road'=>'nullable|string|max:255',
            'additional_hint'=>'nullable|string|max:255',
            'lat'=>'string|required|max:255',
            'lang'=>'string|required|max:255',
            'address_name'=>'string|required|max:255'
        ]);

        $addressToUpdate->house = $request->house;
        $addressToUpdate->road = $request->road;
        $addressToUpdate->additional_hint = $request->additional_hint;
        $addressToUpdate->lat = $request->lat;
        $addressToUpdate->lang = $request->lang;
        $addressToUpdate->address_name = $request->address_name;

        $addressToUpdate->save();

        return response()->json([
            'data' => $addressToUpdate
        ], 200);
    }
}
