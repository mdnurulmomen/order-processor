<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Models\ApplicationSetting;
use App\Http\Controllers\Controller;

class HomeController extends Controller
{
    protected $applicationSettings;

    /**
     * Create a new controller instance.
     *
     * @return void
     **/
    public function __construct()
    {
        $this->applicationSettings = ApplicationSetting::first();

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
        return view('layouts.admin', ['applicationSettings' => $this->applicationSettings]);
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function showOwnerHome()
    {
        return view('layouts.owner', ['applicationSettings' => $this->applicationSettings]);
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function showMerchantHome()
    {
        return view('layouts.merchant', ['applicationSettings' => $this->applicationSettings]);
    }    
}
