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
            'type' => 'required|string|in:phone,gmail,facebook',
            'apk_key' => 'required|string|max:255',
            'username_or_email_or_mobile' => 'required',
            // 'otp_code' => 'required_if:type,phone|string|max:6',
        ]);

        if ($request->type === 'phone' && preg_match("/(^(\+8801|8801|01))[0-9]{9}$/", $request->username_or_email_or_mobile)) {
            
            $request['username_or_email_or_mobile'] = $this->getFilteredMobileNumber($request->username_or_email_or_mobile);

            $existingUser = Customer::whereMobile($request->username_or_email_or_mobile)->first();

            // check otp-code
            if ($request->otp_code && $existingUser && $existingUser->otp_code) {
                return $this->sendMobileOTPResponse($request, $existingUser);
            }
            // send otp-code
            else {
                return $this->sendMobileOTP($request);
            }

        }

        else if (($request->type === 'gmail' || $request->type === 'facebook') && filter_var($request->username_or_email_or_mobile, FILTER_VALIDATE_EMAIL)) {

            return $this->sendEmailLoginResponse($request);

        }

        return $this->sendFailedLoginResponse($request);
    }

    // check otp-code
    private function sendMobileOTPResponse(Request $request, Customer $user)
    {
        if ($user->otp_code === $request->otp_code) {
            
            $user->update([ 'otp_code'=>NULL ]);

            return $this->sendUserLoginResponse($user);

        }

        else {

            return $this->sendFailedMobileOTPResponse($request);

        }        
    }

    // Send Mobile OTP
    private function sendMobileOTP(Request $request)
    {
        $user = Customer::updateOrCreate(
            [ 'mobile' => $request->username_or_email_or_mobile ],
            [ 'otp_code'=> random_int(100000, 999999) ]
        );

        return $this->sendNewUserMobileLoginResponse($user);

        /*
        if ($this->attemptUserLoginWithMobile($request) || $this->attemptUserLoginWithUsername($request) || $this->attemptUserLoginWithEmail($request)) {

            return $this->sendUserLoginResponse(Customer::find(1));

        }
        // return $this->sendFailedLoginResponse($request);
        */
    }

    private function sendEmailLoginResponse(Request $request)
    {
        $user = Customer::firstOrCreate([ 
            'email' => $request->username_or_email_or_mobile 
        ]);

        return $user->wasRecentlyCreated ? $this->sendNewUserEmailLoginResponse($user) : $this->sendUserLoginResponse($user);
    }

    /*
    private function attemptUserLoginWithMobile(Request $request)
    {
        return \Auth::guard()->attempt(
            [ 'mobile'=>$request->username_or_email_or_mobile, 'password'=>$request->password, 'active'=>1 ], $request->filled('remember')
        );   
    }

    private function attemptUserLoginWithUsername(Request $request)
    {
        return \Auth::guard()->attempt(
            [ 'user_name'=>$request->username_or_email_or_mobile, 'password'=>$request->password, 'active'=>1 ], $request->filled('remember')
        );   
    }

    private function attemptUserLoginWithEmail(Request $request)
    {
        return \Auth::guard()->attempt(
            [ 'email'=>$request->username_or_email_or_mobile, 'password'=>$request->password, 'active'=>1 ], $request->filled('remember')
        );   
    }
    */

    private function sendNewUserMobileLoginResponse(Customer $user)
    {
        return [ 'id'=>$user->id, 'is_new_user'=>$user->wasRecentlyCreated, 'otp_code'=>$user->otp_code ];
    }

    private function sendNewUserEmailLoginResponse(Customer $user)
    {
        return [ 'id'=>$user->id, 'is_new_user'=>$user->wasRecentlyCreated, 'email'=>$user->email ];
    }

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
        /*
        throw ValidationException::withMessages([
            $this->username() => [trans('auth.failed')],
        ]);
        */
       
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

    /**
     * Get the login username to be used by the controller.
     *
     * @return string
     */
    private function username()
    {
        return 'username_or_email_or_mobile';
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
