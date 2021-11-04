<?php

namespace App\Http\Controllers\Api;

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;

class ProfileController extends Controller
{
    public function updateUserProfile(Request $request, $user)
    {
        $userToUpdate = Customer::findOrFail($user);
        
        $request->validate([
            'first_name'=>'nullable|string|max:50',
            'last_name'=>'nullable|string|max:50',
            'user_name'=>'string|required|max:13|bail|unique:customers,user_name,'.$userToUpdate->id,
            'mobile'=>'string|required|max:13|bail|unique:customers,mobile,'.$userToUpdate->id,
            'email'=>'email|required|bail|unique:customers,email,'.$userToUpdate->id,
            // 'profile_picture'=>'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $userToUpdate->first_name = $request->first_name;
        $userToUpdate->last_name = $request->last_name;
        $userToUpdate->mobile = $request->mobile;
        $userToUpdate->email = $request->email;
        $userToUpdate->user_name = $request->user_name;
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
}
