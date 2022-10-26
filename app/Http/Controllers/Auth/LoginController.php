<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Providers\RouteServiceProvider;
use Illuminate\Validation\ValidationException;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout', 'adminLogout', 'ownerLogout', 'merchantLogout');
        $this->middleware('guest:admin')->except('logout', 'adminLogout', 'ownerLogout', 'merchantLogout');
        $this->middleware('guest:owner')->except('logout', 'adminLogout', 'ownerLogout', 'merchantLogout');
        $this->middleware('guest:merchant')->except('logout', 'adminLogout', 'ownerLogout', 'merchantLogout');
    }

    public function showAdminLoginForm()
    {
        return view('auth.login', ['url' => 'admin']);
    }

    public function showOwnerLoginForm()
    {
        return view('auth.login', ['url' => 'owner']);
    }

    public function showMerchantLoginForm()
    {
        return view('auth.login', ['url' => 'merchant']);
    }

    // Customer Login
    protected function attemptLogin(Request $request) 
    {
        if ($this->attemptCustomerLoginWithUsername($request) || $this->attemptCustomerLoginWithEmail($request) || $this->attemptCustomerLoginWithMobile($request)) {
            return true;
        }
    }

    // Admin Login
    public function adminLogin(Request $request)
    {
        $this->validateLogin($request);

        // If the class is using the ThrottlesLogins trait, we can automatically throttle
        // the login attempts for this application. We'll key this by the username and
        // the IP address of the client making these requests into this application.
        if (method_exists($this, 'hasTooManyLoginAttempts') &&
            $this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);

            return $this->sendLockoutResponse($request);
        }

        if ($this->attemptAdminLoginWithUsername($request) || $this->attemptAdminLoginWithEmail($request) || $this->attemptAdminLoginWithMobile($request)) {

            return $this->sendAdminLoginResponse($request);
        }

        // If the login attempt was unsuccessful we will increment the number of attempts
        // to login and redirect the user back to the login form. Of course, when this
        // user surpasses their maximum number of attempts they will get locked out.
        $this->incrementLoginAttempts($request);

        return $this->sendFailedLoginResponse($request);
    }

    // Owner Login 
    public function ownerLogin(Request $request)
    {
        $this->validateLogin($request);

        // If the class is using the ThrottlesLogins trait, we can automatically throttle
        // the login attempts for this application. We'll key this by the username and
        // the IP address of the client making these requests into this application.
        if (method_exists($this, 'hasTooManyLoginAttempts') &&
            $this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);

            return $this->sendLockoutResponse($request);
        }

        if ($this->attemptOwnerLoginWithUsername($request) || $this->attemptOwnerLoginWithEmail($request) || $this->attemptOwnerLoginWithMobile($request)) {

            return $this->sendOwnerLoginResponse($request);
        }

        // If the login attempt was unsuccessful we will increment the number of attempts
        // to login and redirect the user back to the login form. Of course, when this
        // user surpasses their maximum number of attempts they will get locked out.
        $this->incrementLoginAttempts($request);

        return $this->sendFailedLoginResponse($request);
    }

    // Merchant Login 
    public function merchantLogin(Request $request)
    {
        $this->validateLogin($request);

        // If the class is using the ThrottlesLogins trait, we can automatically throttle
        // the login attempts for this application. We'll key this by the username and
        // the IP address of the client making these requests into this application.
        if (method_exists($this, 'hasTooManyLoginAttempts') &&
            $this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);

            return $this->sendLockoutResponse($request);
        }

        if ($this->attemptMerchantLoginWithUsername($request) || $this->attemptMerchantLoginWithEmail($request) || $this->attemptMerchantLoginWithMobile($request)) {

            return $this->sendMerchantLoginResponse($request);
        }

        // If the login attempt was unsuccessful we will increment the number of attempts
        // to login and redirect the user back to the login form. Of course, when this
        // user surpasses their maximum number of attempts they will get locked out.
        $this->incrementLoginAttempts($request);

        return $this->sendFailedLoginResponse($request);
    }

    // Admin Logout
    public function adminLogout(Request $request)
    {
        Auth::guard('admin')->logout();

        $request->session()->invalidate();

        return $this->loggedOut($request) ?: redirect('/');
    }

    // Owner Logout
    public function ownerLogout(Request $request)
    {
        Auth::guard('owner')->logout();

        $request->session()->invalidate();

        return $this->loggedOut($request) ?: redirect('/');
    }

    // Merchant Logout
    public function merchantLogout(Request $request)
    {
        Auth::guard('merchant')->logout();

        $request->session()->invalidate();

        return $this->loggedOut($request) ?: redirect('/');
    }

    // Customer
    protected function attemptCustomerLoginWithUsername(Request $request)
    {
        return $this->guard()->attempt(
            ['user_name'=>$request->usernameOrEmailOrMobile, 'password'=>$request->password, 'active'=>1], $request->filled('remember')
        );   
    }

    protected function attemptCustomerLoginWithEmail(Request $request)
    {
        return $this->guard()->attempt(
            ['email'=>$request->usernameOrEmailOrMobile, 'password'=>$request->password, 'active'=>1], $request->filled('remember')
        );   
    }

    protected function attemptCustomerLoginWithMobile(Request $request)
    {
        return $this->guard()->attempt(
            ['mobile'=>$request->usernameOrEmailOrMobile, 'password'=>$request->password, 'active'=>1], $request->filled('remember')
        );   
    }

    // Admin
    protected function attemptAdminLoginWithUsername(Request $request)
    {
        return $this->guard('admin')->attempt(
            ['user_name'=>$request->usernameOrEmailOrMobile, 'password'=>$request->password, 'active'=>1], $request->filled('remember')
        );   
    }

    protected function attemptAdminLoginWithEmail(Request $request)
    {
        return Auth::guard('admin')->attempt(
            ['email'=>$request->usernameOrEmailOrMobile, 'password'=>$request->password, 'active'=>1], $request->filled('remember')
        );
    }

    protected function attemptAdminLoginWithMobile(Request $request)
    {
        return Auth::guard('admin')->attempt(
            ['mobile'=>$request->usernameOrEmailOrMobile, 'password'=>$request->password, 'active'=>1], $request->filled('remember'));
    }

    // Owner
    protected function attemptOwnerLoginWithUsername(Request $request)
    {
        return Auth::guard('owner')->attempt(
            ['user_name'=>$request->usernameOrEmailOrMobile, 'password'=>$request->password], $request->filled('remember')
        );   
    }

    protected function attemptOwnerLoginWithEmail(Request $request)
    {
        return Auth::guard('owner')->attempt(
            ['email'=>$request->usernameOrEmailOrMobile, 'password'=>$request->password, 'active'=>1], $request->filled('remember')
        );
    }

    protected function attemptOwnerLoginWithMobile(Request $request)
    {
        return Auth::guard('owner')->attempt(
            ['mobile'=>$request->usernameOrEmailOrMobile, 'password'=>$request->password], $request->filled('remember')
        );   
    }


    // Merchant
    protected function attemptMerchantLoginWithUsername(Request $request)
    {
        return Auth::guard('merchant')->attempt(
            ['user_name'=>$request->usernameOrEmailOrMobile, 'password'=>$request->password], $request->filled('remember')
        );   
    }

    protected function attemptMerchantLoginWithEmail(Request $request)
    {
        return Auth::guard('merchant')->attempt(
            ['email'=>$request->usernameOrEmailOrMobile, 'password'=>$request->password, 'active'=>1], $request->filled('remember')
        );
    }

    protected function attemptMerchantLoginWithMobile(Request $request)
    {
        return Auth::guard('merchant')->attempt(
            ['mobile'=>$request->usernameOrEmailOrMobile, 'password'=>$request->password], $request->filled('remember')
        );   
    }

    /**
     * Send the response after the user was authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    protected function sendAdminLoginResponse(Request $request)
    {
        $request->session()->regenerate();

        $this->clearLoginAttempts($request);

        return $this->authenticated($request, Auth::guard('admin')->user())
                ?: redirect()->intended($this->redirectPath());
    }

    /**
     * Send the response after the user was authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    protected function sendOwnerLoginResponse(Request $request)
    {
        $request->session()->regenerate();

        $this->clearLoginAttempts($request);

        return $this->authenticated($request, Auth::guard('owner')->user())
                ?: redirect()->intended($this->redirectPath());
    }

    /**
     * Send the response after the user was authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    protected function sendMerchantLoginResponse(Request $request)
    {
        $request->session()->regenerate();

        $this->clearLoginAttempts($request);

        return $this->authenticated($request, Auth::guard('merchant')->user())
                ?: redirect()->intended($this->redirectPath());
    }

    /**
     * Get the login username to be used by the controller.
     *
     * @return string
     */
    public function username()
    {
        return 'usernameOrEmailOrMobile';
    }
}
