<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\LoginLog;
use Illuminate\Support\Facades\Route;
use App\Repositories\LoginValidationLogRepository;

class LoginValidationMiddleware
{

    public function __construct(LoginValidationLogRepository $loginLogRepository)
    {
        $this->loginLogRepository = $loginLogRepository;
    }
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


       return $this->loginLogRepository->SaveLoginLog($request, $response);

    }
}
