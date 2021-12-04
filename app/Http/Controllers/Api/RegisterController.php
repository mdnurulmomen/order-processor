<?php

namespace App\Http\Controllers\Api;

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\UserLoginResponseResource;

class RegisterController extends Controller
{
    /*
    public function userRegistration(Request $request)
    {
        $request->validate([
            'first_name' => ['string', 'max:255'],
            'last_name' => ['string', 'max:255'],
            'user_name' => ['required', 'string', 'unique:customers,user_name', 'max:255'],
            'mobile' => ['required', 'string', 'unique:customers,mobile', 'max:11'],
            'email' => ['required', 'email', 'unique:customers,email', 'max:50'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $newCustomer = Customer::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'user_name' => $request->user_name,
            'mobile' => $request->mobile,
            'email' => $request->email,
            'password' => Hash::make($request->password)
        ]);

        
        // event(new Registered($user = $this->create($request->all())));

        // $this->guard()->login($user);

        // return $this->registered($request, $user)
        //                 ?: redirect($this->redirectPath());
        

        return $this->sendUserLoginResponse($newCustomer);
    }
    */

    /**
     * Send the response after the user was authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    private function sendUserLoginResponse(Customer $user)
    {  
        return new UserLoginResponseResource($user);
    }
}
