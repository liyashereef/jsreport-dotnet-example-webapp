<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\LoginLog;
use Illuminate\Support\Facades\Route;

class LoginValidationMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        return $next($request);
    }

    /**
     * Handle tasks after the response has been sent to the browser.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Http\Response  $response
     * @return void
     */
    public function terminate($request, $response)
    {
        //$routeURI = Route::current()->uri();
        $routeName = \Request::route()->getName();
        if($routeURI == "osgc.login"){
            $loginType = 'OSGCLOGIN';
        }else if($routeName == "facility.login"){
            $loginType = 'FACILITYLOGIN';
        }else if($routeName == "app.login"){
            $loginType = 'APPLOGIN';
        }

        $saveLoginLog = [
            'username' => \Auth::user()->email,
            'ip' => $request->ip(),
            'login_type' => config('globals.login_type')[$loginType],
            'user_agent' => $request->header('user-agent'),
            'success' => 0,
        ];

        if($response->status() == 200){
            $saveLoginLog['success'] = 1;
        }
        LoginLog::create($saveLoginLog);

    }
}
