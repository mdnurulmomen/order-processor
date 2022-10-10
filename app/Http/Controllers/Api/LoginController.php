<?php

namespace App\Http\Controllers\Api;

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use App\Http\Resources\Api\UserLoginResponseResource;

class LoginController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        /*
        $this->middleware('guest')->except('logout', 'adminLogout', 'restaurantLogout');
        $this->middleware('guest:admin')->except('logout', 'adminLogout', 'restaurantLogout');
        $this->middleware('guest:restaurant')->except('logout', 'adminLogout', 'restaurantLogout');
        */
    }

    public function login(Request $request)
    {
        $request->validate([
            'type' => 'required|string|in:mobile,gmail,facebook',
            'apk_key' => 'required|string|max:255',
            'mobile' => ['required', 'regex:/(^(\+8801|8801|01))[0-9]{9}$/'],
            'email' => 'required_if:type,gmail,facebook|email',
            // 'otp_code' => 'required_if:type,mobile|string|max:6',
            'first_name' => 'nullable|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'user_name' => 'nullable|string|max:255',
        ]);

        if ($request->type === 'mobile') {

            $user = Customer::firstOrCreate([ 
                'mobile' => $this->getFilteredMobileNumber($request->mobile) 
            ]);

            // check otp-code
            if ($request->otp_code && $user->otp_code) {
                return $this->matchMobileOTPResponse($request, $user);
            }
            // send otp-code
            else {
                return $this->sendMobileOTP($request, $user);
            }

        }

        else if (($request->type === 'gmail' || $request->type === 'facebook')) {

            $emailExists = Customer::whereEmail($request->email)->exists();
            $userNameExists = Customer::where('user_name', $request->user_name)->exists();

            $user = Customer::firstOrCreate(
                [ 'mobile' => $this->getFilteredMobileNumber($request->mobile) ],
                [ 
                    'first_name' => $request->first_name, 
                    'last_name' => $request->last_name, 
                    'email' => $emailExists ? : $request->email, 
                    'user_name' => $userNameExists ? : $request->user_name, 
                ]
            );

            return $this->sendEmailLoginResponse($request, $user);

        }

        return $this->sendFailedLoginResponse($request);
    }

    // Send Mobile OTP
    private function sendMobileOTP(Request $request, Customer $user)
    {
        $user->update([
            'otp_code'=> random_int(100000, 999999)
        ]);

        return $this->sendNewUserMobileLoginResponse($user);
    }

    // check otp-code
    private function matchMobileOTPResponse(Request $request, Customer $user)
    {
        if ($user->otp_code === $request->otp_code) {
            
            $user->update([ 'otp_code'=>NULL ]);

            return $this->sendUserLoginResponse($user);

        }

        else {

            return $this->sendFailedMobileOTPResponse($request);

        }        
    }

    private function sendEmailLoginResponse(Request $request, Customer $user)
    {
        // return $user->wasRecentlyCreated ? $this->sendNewUserEmailLoginResponse($user) : $this->sendUserLoginResponse($user);
        
        return $this->sendUserLoginResponse($user);
    }

    private function sendNewUserMobileLoginResponse(Customer $user)
    {
        return [ 'id'=>$user->id, 'is_new_user'=>$user->wasRecentlyCreated, 'otp_code'=>$user->otp_code ];
    }

    /*
    private function sendNewUserEmailLoginResponse(Customer $user)
    {
        return [ 'id'=>$user->id, 'is_new_user'=>$user->wasRecentlyCreated, 'email'=>$user->email ];
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

    /**
     * Get the failed login response instance.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    private function sendFailedLoginResponse(Request $request)
    {  
        throw ValidationException::withMessages([
            'bad_request' => ['Wrong type or credentials'],
        ]);
    }

    private function sendFailedMobileOTPResponse(Request $request)
    {
        throw ValidationException::withMessages([
            'otp_code' => ['Wrong OTP code. Please try again'],
        ]);
    }

    private function getFilteredMobileNumber($mobile)
    {
        if (substr($mobile, 0, 2) == '88') {
            
            return substr($mobile, 2);

        }
        else if (substr($mobile, 0, 3) == '+88') {
            
            return substr($mobile, 3);

        }
        else {

            return $mobile;

        }
    }

}
