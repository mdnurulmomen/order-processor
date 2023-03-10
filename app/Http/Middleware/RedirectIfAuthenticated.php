<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        if (Auth::guard($guard)->check()) {

            switch ($guard) {
                case 'admin':
                    return redirect(RouteServiceProvider::HOME);
                    break;

                case 'owner':
                    return redirect(RouteServiceProvider::HOME);
                    break;

                case 'merchant':
                    return redirect(RouteServiceProvider::HOME);
                    break;
                
                default:
                    return redirect()->route('customer.home');
                    break;
            }

        }

        return $next($request);
    }
}
