<?php

namespace Modules\Facility\Http\Controllers\FacilityUser;

use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Facility\Http\Requests\FacilityUserUpdateRequest;
use Modules\Facility\Http\Requests\FacilityUserUpdatePasswordRequest;
use Modules\Facility\Http\Requests\FacilityUserLoginRequest;
use Modules\Facility\Repositories\FacilityUserRepository;
use Modules\Facility\Repositories\FacilityBookingRepository;

class FacilityUserController extends Controller
{
    /**
     * This trait has all the login throttling functionality.
     */
    use ThrottlesLogins;
    protected $facilityuserrepository;
    protected $facilityBookingRepository;
    /**
     * Max login attempts allowed.
     */
    public $maxAttempts = 10;

    /**
     * Number of minutes to lock the login.
     */
    public $decayMinutes = 5;

    public function __construct(
        FacilityUserRepository $facilityuserrepository,
        FacilityBookingRepository $facilityBookingRepository
    ) {
        $this->facilityuserrepository = $facilityuserrepository;
        $this->facilityBookingRepository = $facilityBookingRepository;
    }
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        return view('facility::FacilityUser.login');
    }

    /**
     * Login.
     * @return Response
     */
    public function login(FacilityUserLoginRequest $request)
    {

        //check if the user has too many login attempts.
        if ($this->hasTooManyLoginAttempts($request)) {
            //Fire the lockout event
            $this->fireLockoutEvent($request);

            //redirect the user back after lockout.
            return $this->sendLockoutResponse($request)->json();
        }
        // dd($request->all());
        if (\Auth::guard('facilityuser')->attempt(['username' => $request->input('username'), 'password' => $request->input('password'), 'active' => 1])) {
            // if (\Auth::guard('facilityuser')->attempt($request->only('username', 'password'))) {
            $return["code"] = 200;
            $return["success"] = true;
            $return["username"] = $request->input('username');
            $return["message"] = "Success";
        } else {

            //keep track of login attempts from the user.
            $this->incrementLoginAttempts($request);

            $return["code"] = 406;
            $return["success"] = false;
            $return["username"] = $request->input('username');
            $return["message"] = "Invalid Credentials/Account inactive";
        }

        return json_encode($return, true);
    }

    /**
     * Logout.
     * @return Response
     */

    public function logout()
    {
        \Auth::guard('facilityuser')->logout();
        return redirect()->route('facility.login');
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

    /**
     * Get profile page.
     * @return page
     */
    public function getProfilePage()
    {
        // $user = \Auth::guard('facilityuser')->user();
        return view('facility::FacilityUser.profile');
    }

    public function getProfile()
    {
        return $this->facilityuserrepository->getLoggedUserProfile();
    }
    /**
     * Update profile.
     * @return Response
     */
    public function updateProfile(FacilityUserUpdateRequest $request)
    {
        return $this->facilityuserrepository->editProfile($request);
    }

    /**
     * Update profile password.
     * @return Response
     */
    public function resetPassword(FacilityUserUpdatePasswordRequest $request)
    {
        if (\Hash::check($request->input('current_password'), \Auth::guard('facilityuser')->user()->password)) {
            $input['id'] = \Auth::guard('facilityuser')->user()->id;
            $input['password'] = \Hash::make($request->input('confirm_password'));
            $this->facilityuserrepository->updatePassword($input);
            $result['success'] = true;
            $result['message'] = '';
        } else {
            $result['message'] = 'Incorrect password';
            $result['success'] = false;
        }
        return $result;
    }



    public function getBookingHistory(Request $request)
    {
        $inputs['facility_user_id'] = \Auth::guard('facilityuser')->user()->id;
        $bookingdata = [];

        $content = $this->facilityBookingRepository->userBookedDetails($inputs);
        $i = 0;
        foreach ($content as $value) {

            $bookingdata[$i]["model_type"] = $value->model_type;
            $bookingdata[$i]["model_id"] = $value->model_id;
            $bookingdata[$i]["booking_date_end"] = $value->booking_date_end;
            $bookingdata[$i]["booking_date_start"] = $value->booking_date_start;
            $bookingdata[$i]["created_at"] = $value->created_at;
            $bookingdata[$i]["facility_user_id"] = $value->facility_user_id;
            $bookingdata[$i]["id"] = $value->id;
            if ($value->model_type == "Modules\Facility\Models\FacilityService") {
                $bookingdata[$i]["facility_id"] = $value->model->getFacilitytrashed->id;
                $bookingdata[$i]["facility_name"] = $value->model->getFacilitytrashed->facility;

                $bookingdata[$i]["service_id"] = $value->model->id;
                $bookingdata[$i]["service_name"] = $value->model->service;
            } else {
                $bookingdata[$i]["facility_id"] = $value->model->id;
                $bookingdata[$i]["facility_name"] = $value->model->facility;

                $bookingdata[$i]["service_id"] = null;
                $bookingdata[$i]["service_name"] = null;
            }


            $i++;
        }
        return  $bookingdata;
    }
}
