<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Modules\Admin\Models\User;
use App\Models\LoginLog;

class LoginController extends Controller {
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
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {
        $this->middleware('guest')->except('logout');
    }

    /**
     * Get the login username to be used by the controller.
     *
     * @return string
     */
    public function username() {
        return 'log';
    }

    /**
     * Get the needed authorization credentials from the request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    protected function credentials(Request $request) {
        $credentialsArr = array();
        $logValue = $request->input($this->username());
        $logKey = filter_var($logValue, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
        //$field = filter_var($request->get($this->username()), FILTER_VALIDATE_EMAIL) ? $this->username() : 'username';
        $user = User::where($logKey, $logValue)->whereActive(true)->first();
        $saveLoginLog = [
            'username' => $logValue,
            'ip' => $request->ip(),
            'success' => 0,
        ];
        $saveLoginLog['success'] = 0;
        if (isset($user) && $user->hasPermissionTo('login')) {
            //$logKey = filter_var($logValue, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
            $credentialsArr = [
                $logKey => $logValue,
                'password' => $request->input('password'),
                'active' => 1,
            ];
            $saveLoginLog = [
                'username' => $logValue,
                'ip' => $request->ip(),
                'success' => 1,
            ];
        }

        LoginLog::create($saveLoginLog);

        return $credentialsArr;
    }

}
