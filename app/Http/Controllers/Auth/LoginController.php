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
        $this->middleware('guest')->except('logout', 'adminLogout', 'restaurantLogout');
        $this->middleware('guest:admin')->except('logout', 'adminLogout', 'restaurantLogout');
        $this->middleware('guest:restaurant')->except('logout', 'adminLogout', 'restaurantLogout');
    }

    public function showAdminLoginForm()
    {
        return view('auth.login', ['url' => 'admin']);
    }

    public function showRestaurantLoginForm()
    {
        return view('auth.login', ['url' => 'resto']);
    }

    // Customer Login
    protected function attemptLogin(Request $request) 
    {
        if ($this->attemptCustomerLoginWithMobile($request) || $this->attemptCustomerLoginWithUsername($request)) {
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

        if ($this->attemptAdminLoginWithEmail($request) || $this->attemptAdminLoginWithMobile($request)) {

            return $this->sendAdminLoginResponse($request);
        }

        // If the login attempt was unsuccessful we will increment the number of attempts
        // to login and redirect the user back to the login form. Of course, when this
        // user surpasses their maximum number of attempts they will get locked out.
        $this->incrementLoginAttempts($request);

        return $this->sendFailedLoginResponse($request);
    }

    // Restaurant Login 
    public function restaurantLogin(Request $request)
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

        if ($this->attemptRestaurantLoginWithMobile($request) || $this->attemptRestaurantLoginWithUsername($request)) {

            return $this->sendRestaurantLoginResponse($request);
        }

        // If the login attempt was unsuccessful we will increment the number of attempts
        // to login and redirect the user back to the login form. Of course, when this
        // user surpasses their maximum number of attempts they will get locked out.
        $this->incrementLoginAttempts($request);

        return $this->sendFailedLoginResponse($request);
    }

    public function adminLogout(Request $request)
    {
        Auth::guard('admin')->logout();

        $request->session()->invalidate();

        return $this->loggedOut($request) ?: redirect('/');
    }

    public function restaurantLogout(Request $request)
    {
        Auth::guard('restaurant')->logout();

        $request->session()->invalidate();

        return $this->loggedOut($request) ?: redirect('/');
    }

    protected function attemptCustomerLoginWithMobile(Request $request)
    {
        return $this->guard()->attempt(
            ['mobile'=>$request->usernameOrEmailOrMobile, 'password'=>$request->password, 'active'=>1], $request->filled('remember')
        );   
    }

    protected function attemptCustomerLoginWithUsername(Request $request)
    {
        return $this->guard()->attempt(
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

    protected function attemptRestaurantLoginWithMobile(Request $request)
    {
        return Auth::guard('restaurant')->attempt(
            ['mobile'=>$request->usernameOrEmailOrMobile, 'password'=>$request->password, 'admin_approval'=>1], $request->filled('remember')
        );   
    }

    protected function attemptRestaurantLoginWithUsername(Request $request)
    {
        return Auth::guard('restaurant')->attempt(
            ['user_name'=>$request->usernameOrEmailOrMobile, 'password'=>$request->password, 'admin_approval'=>1], $request->filled('remember')
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
    protected function sendRestaurantLoginResponse(Request $request)
    {
        $request->session()->regenerate();

        $this->clearLoginAttempts($request);

        return $this->authenticated($request, Auth::guard('restaurant')->user())
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
