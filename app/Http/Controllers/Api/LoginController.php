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

    // User / Customer Login
    public function userLogin(Request $request)
    {
        $request->validate([
            'username_or_email_or_mobile' => 'required|string',
            'password' => 'required|string',
        ]);

        if ($this->attemptUserLoginWithMobile($request) || $this->attemptUserLoginWithUsername($request) || $this->attemptUserLoginWithEmail($request)) {

            return $this->sendUserLoginResponse(Customer::find(1));

        }

        return $this->sendFailedLoginResponse($request);
    }

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
            $this->username() => [trans('auth.failed')],
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

}
