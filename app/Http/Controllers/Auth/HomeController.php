<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     **/
    public function __construct()
    {
        $this->middleware('auth')->only('index');
        $this->middleware('auth:admin')->only('showAdminHome');
        $this->middleware('auth:owner')->only('showOwnerHome');
        $this->middleware('auth:merchant')->only('showMerchantHome');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('customer.home');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function showAdminHome()
    {
        return view('layouts.admin');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function showOwnerHome()
    {
        return view('layouts.owner');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function showMerchantHome()
    {
        return view('layouts.merchant');
    }    
}
