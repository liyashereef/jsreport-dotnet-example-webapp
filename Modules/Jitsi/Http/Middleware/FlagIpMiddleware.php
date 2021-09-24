<?php

namespace Modules\Jitsi\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class flagIpMiddleware
{
    public $whiteIps = ['127.0.0.1', '3.139.2.67', '3.130.232.249', '18.217.118.92', '18.220.50.21', '3.136.38.210', '3.18.96.178'];

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (!in_array($request->ip(), $this->whiteIps)) {
            /*
                 You can redirect to any error page.
            */
            abort(404);
        }

        return $next($request);
    }
}
