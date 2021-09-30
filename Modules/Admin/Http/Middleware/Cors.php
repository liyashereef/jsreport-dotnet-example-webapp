<?php

# File: app\Http\Middleware\Cors.php
# Create file with below code in above location. And at the end of the file there are other instructions also.
# Please check.

namespace App\Http\Middleware;

use Closure;

class Cors
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
//        return $next($request);
//        return $next($request);
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Methods: POST, GET, OPTIONS, PUT, DELETE");
        header("Access-Control-Allow-Headers: Content-Type, X-Auth-Token, Origin, Authorization, sentry-trace");
        ob_clean();
//        // ALLOW OPTIONS METHOD
//        $headers = [
//            'Access-Control-Allow-Methods' => 'POST, GET, OPTIONS, PUT, DELETE',
//            'Access-Control-Allow-Headers' => 'Content-Type, X-Auth-Token, Origin, Authorization',
//        ];
        $headers = [];
//        if ($request->getMethod() == "OPTIONS") {
//            // The client-side application can set only headers allowed in Access-Control-Allow-Headers
//            return \Response::make('OK', 200, $headers);
//        }
        $response = $next($request);
        foreach ($headers as $key => $value)
        // $response->header($key, $value);
        {
            $response->headers->set($key, $value);
        }

        return $response;
    }

}