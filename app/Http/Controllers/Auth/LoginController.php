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
        $this->middleware('guest')->except('logout');
        $this->middleware('guest:admin')->except('logout');
        $this->middleware('guest:restaurant')->except('logout');
    }

    public function showAdminLoginForm()
    {
        return view('auth.login', ['url' => 'admin']);
    }

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

            //return $this->sendLoginResponse($request);
        }

        // If the login attempt was unsuccessful we will increment the number of attempts
        // to login and redirect the user back to the login form. Of course, when this
        // user surpasses their maximum number of attempts they will get locked out.
        $this->incrementLoginAttempts($request);

        return $this->sendFailedLoginResponse($request);
    }

    public function showRestaurantLoginForm()
    {
        return view('auth.login', ['url' => 'resto']);
    }

    protected function attemptAdminLoginWithEmail(Request $request)
    {
        return Auth::guard('admin')->attempt(
            ['email'=>$this->username(), 'password'=>$request->password, 'active'=>1], $request->filled('remember')
        );
    }

    protected function attemptAdminLoginWithMobile(Request $request)
    {
        return Auth::guard('admin')->attempt(['mobile' => $request->usernameOrEmailOrMobile, 'password' => $request->password, 'active' => 1], $request->filled('remember'));
    }

    protected function attemptLogin(Request $request) // Customer Login 
    {
        if ($this->attemptCustomerLoginWithMobile($request) || $this->attemptCustomerLoginWithUsername($request)) {
            return true;
        }
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
