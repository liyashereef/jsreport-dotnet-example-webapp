<?php

namespace Modules\Osgc\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Osgc\Repositories\OsgcUserRepository;
use Modules\Osgc\Repositories\OsgcCourseRepository;
use Modules\Osgc\Http\Requests\OsgcUserRequest;
use Modules\Osgc\Http\Requests\OsgcChangePasswordRequest;
use \Carbon\Carbon;
class OsgcUserController extends Controller
{
    protected $osgcUserrepository;
    public function __construct(OsgcUserRepository $osgcUserrepository,OsgcCourseRepository $osgcCourseRepository){
        $this->osgcUserrepository = $osgcUserrepository;
        $this->osgcCourseRepository = $osgcCourseRepository;

    }
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function login()
    {
        return view('osgc::login');
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function registration()
    {
        $referralArr =config('globals.referral');
        return view('osgc::registration',compact('referralArr'));
    }

    /**
     * Store a newly created resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function store(OsgcUserRequest $request)
    {
        $userid = $this->osgcUserrepository->addUsers($request);
        if($userid>0){
            $content["code"]=200;
            $content["message"]="User added successfully";
            $content["success"] ="success";
        }else{
            $content["code"] =406;
            $content["message"] ="System issue";
            $content["success"] ="warning";
        }
        return json_encode($content,true);
    }
    /**
     * Activating Account
     *
     * @param  $request
     * @return json
     */
    public function activateAccount($token)
    {
      $checkLink=$this->osgcUserrepository->checkUserActivationLink($token);
      if($checkLink)
      {
        return view('osgc::userActivation');
      }else{
        return view('osgc::invalidUserActivation');
      }

    }
    /**
     * login
     *
     * @param  $request
     * @return json
     */
    public function checkLoginUser(Request $request)
    {
        $request->validate([
            'email'   => 'required|email',
            'password' => 'required|min:6'
        ]);
        $content["code"] = 406;
        $content["success"] = false;
        $content["message"] = "Invalid user";
        $content["courseId"] =0;
        $userDetails=$this->osgcUserrepository->getUserByEmail($request->email);
        if($userDetails){
            if($userDetails->email_verified == 1 && $userDetails->active == 1)
            {
                if (\Auth::guard('osgcuser')->attempt(['email' => $request->email, 'password' => $request->password], true)) {
                        $userDetails->last_login=Carbon::now();
                        $userDetails->save();
                        $content["code"] = 200;
                        $content["success"] = true;
                        $content["message"] = "Welcome " . \Auth::guard('osgcuser')->user()->first_name . " " . \Auth::guard('osgcuser')->user()->last_name;
                }
            }else{
                $content["code"] = 406;
                $content["success"] = false;
                $content["message"] = "Account is Not Activated";
            }
        }else{
            $content["code"] = 406;
            $content["success"] = false;
            $content["message"] = "Please check credentials and try again";
        }
        return json_encode($content, true);

    }
    public function logout()
    {
        if (\Auth::guard('osgcuser')->logout()) {
            return redirect('osgc/login');
        } else {
            return redirect('osgc/login');
        }
        return redirect('osgc/login');
    }
    /**
     * Forgot password
     *
     * @param  $request
     * @return json
     */
    public function forgotPassword()
    {
        return view('osgc::forgot-password');
    }
    /**
     * Resetting password
     *
     * @param  $request
     * @return json
     */
    public function resetPassword(Request $request)
    {
        $email = $request->post('email');
        $content = $this->osgcUserrepository->resetPassword($email);
        return response()->json($content);
    }
    /**
     * Resetting password
     *
     * @param  $request
     * @return json
     */
    public function changePassword()
    {
        return view('osgc::change-password');
    }
    /**
     * Resetting password
     *
     * @param  $request
     * @return json
     */
    public function updatePassword(OsgcChangePasswordRequest $request)
    {
        $content = $this->osgcUserrepository->updatePassword($request);
        return response()->json($content);
    }
}
