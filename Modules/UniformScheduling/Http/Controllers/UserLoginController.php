<?php

namespace Modules\UniformScheduling\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Modules\UniformScheduling\Http\Requests\UserLoginRequest;

class UserLoginController extends Controller
{
    /**
     * This trait has all the login throttling functionality.
     */
    use ThrottlesLogins;
    /**
     * Max login attempts allowed.
     */
    public $maxAttempts = 10;

    /**
     * Number of minutes to lock the login.
     */
    public $decayMinutes = 5;

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        if (\Auth::check()) {
            return redirect()->route('uniform.booking-page');
            // return view('uniformscheduling::public.login');
        }else{
             return view('uniformscheduling::public.login');
        }
    }

     /**
     * Login.
     * @return Response
     */
    public function login(UserLoginRequest $request)
    {

        //check if the user has too many login attempts.
        if ($this->hasTooManyLoginAttempts($request)) {
            //Fire the lockout event
            $this->fireLockoutEvent($request);

            //redirect the user back after lockout.
            return $this->sendLockoutResponse($request)->json();
        }
        // dd($request->all());
        if (\Auth::attempt(['username' => $request->input('username'), 'password' => $request->input('password'), 'active' => 1])) {
            // if (\Auth::guard('facilityuser')->attempt($request->only('username', 'password'))) {
            $return["code"] = 200;
            $return["success"] = true;
            $return["message"] = "Success";
        } else {

            //keep track of login attempts from the user.
            $this->incrementLoginAttempts($request);

            $return["code"] = 406;
            $return["success"] = false;
            $return["message"] = "Invalid Credentials/Account inactive";
        }
        return response()->json($return);
    }

    /**
     * Logout.
     * @return Response
     */

    public function logout()
    {
        \Auth::logout();
        return redirect()->route('uniform.login');
    }


    /**
     * Username used in ThrottlesLogins trait
     *
     * @return string
     */
    public function username()
    {
        return 'username';
    }

}
