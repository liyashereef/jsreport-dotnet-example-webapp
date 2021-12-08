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

    public function SaveLoginLog($request, $response){

         $routeURI = Route::current()->uri();
         $routeName = \Request::route()->getName();
         $username = "--";
         $success_value = 0;

         if($routeName == "osgc.check-login-user"){
             $loginType = 'OSGCLOGIN';
             if($response){
                $res = $response->getContent();
                $user = json_decode($res, true);
                $username = $user['username'];
                $success_value = $user['success'];
            }
         }else if($routeName == "facility.user-login"){
             $loginType = 'FACILITYLOGIN';
             if($response){
                $res = $response->getContent();
                $user = json_decode($res, true);
                $username = $user['username'];
                $success_value = $user['success'];
            }
         }else if($routeName == "app.login"){
             $loginType = 'APPLOGIN';
             if($response){
                $user = $response->getdata();
                $username = $user->content->user->full_name;
                $success_value = 1;
            }
         }else if($routeURI == "login"){
            $loginType = 'WEBLOGIN';
            if($response){
                $username = $response['username'];
                $success_value = 1;
            }
         }

        $saveLoginLog = [
            'ip' => $request->ip(),
            'login_type' => config('globals.login_type')[$loginType],
            'user_agent' => $request->header('user-agent'),
            'success' => $success_value,
            'username' => $username,
        ];

        LoginLog::create($saveLoginLog);

    }

}
