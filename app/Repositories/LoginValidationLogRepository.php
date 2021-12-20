<?php

namespace App\Repositories;

use App\Services\AppId;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\LoginLog;
use Illuminate\Support\Facades\Route;

class LoginValidationLogRepository
{
    public function __construct()
    {

    }

    public function saveLoginLog($request, $response){
         $routeURI = Route::current()->uri();
         $routeName = \Request::route()->getName();
         $userName = "--";
         $successValue = 0;

         if($routeName == "osgc.check-login-user"){
             $loginType = 'OSGCLOGIN';
             if($response){
                $res = $response->getContent();
                $user = json_decode($res, true);
                $userName = $user['username'];
                $successValue = $user['success'];
            }
         }else if($routeName == "facility.user-login"){
             $loginType = 'FACILITYLOGIN';
             if($response){
                $res = $response->getContent();
                $user = json_decode($res, true);
                $userName = $user['username'];
                $successValue = $user['success'];
            }
         }else if($routeName == "app.login"){
             $loginType = 'APPLOGIN';
             if($response){
                $res = $response->getContent();
                $user = json_decode($res, true);
                $userName = $user['content']['loggedInUsername'];
                $successValue = $user['content']['success'];
            }
         }else if($routeURI == "login"){
            $loginType = 'WEBLOGIN';
            $parameter = $request->request->all();
            $userName = $parameter['log'];
            if($response){
                $userName = $response['username'];
                $successValue = 1;
            }
         }
         else if($routeName == "visitorlog-login"){
            $loginType = 'VISITORAPPLOGIN';
            if($response){
                $res = $response->getContent();
                $user = json_decode($res, true);
                $userName = $user['user_name'];
                $successValue = $user['success'];
            }
         }

        $saveLoginLog = [
            'ip' => $request->ip(),
            'login_type' => config('globals.login_type')[$loginType],
            'user_agent' => $request->header('user-agent'),
            'success' => $successValue,
            'username' => $userName,
        ];

        LoginLog::create($saveLoginLog);

    }

}
