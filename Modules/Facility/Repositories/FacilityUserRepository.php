<?php

namespace Modules\Facility\Repositories;

use App\Services\HelperService;
use Carbon\Carbon;
use Auth;
use Modules\Facility\Models\FacilityServiceSlot;
use Modules\Facility\Models\FacilityServiceTiming;
use Modules\Facility\Models\FacilityServiceData;
use Modules\Facility\Models\FacilityServiceLockdown;
use Modules\Facility\Models\FacilityServiceUserAllocation;
use Modules\Facility\Models\FacilityUserWeekendDefinition;
use Modules\Facility\Models\FacilityUser;
use Modules\Facility\Models\Facility;
use Modules\Facility\Models\FacilityService;
use App\Repositories\MailQueueRepository;


class FacilityUserRepository
{
    protected $helperService,$faciltyuserrelation;
    private $mailQueueRepository;
    public function __construct(MailQueueRepository $mailQueueRepository){
        $this->helperService = new HelperService();
        $this->mailQueueRepository = $mailQueueRepository; 
    }

    public function editProfile($request){

        try{
            // if (Auth::once(["id"=>Auth::guard("facilityuser")->user()->id,"password"=>$request->password]))
            // {
                $user = FacilityUser::find(Auth::guard("facilityuser")->user()->id);
                $user->first_name = $request->first_name;
                $user->last_name = $request->last_name;
                $user->email = $request->email;
                $user->alternate_email = $request->alternate_email;
                $user->phoneno = $request->phoneno;
                $user->save();
                if($user){
                    $content["code"] =200; 
                    $content["message"] ="Profile Updated Successfully.";
                    $content["success"] =true;
                    // $content["user"] =FacilityUser::find(Auth::guard("facilityuser")->user()->id);
                }else{
                    $content["code"] =406; 
                    $content["message"] ="Something went wrong.Try again.";
                    $content["success"] =false;
                }
            // }else{
            //     $content["code"] =406; 
            //     $content["message"] ="Your password do not match.";
            //     $content["success"] =false;
            // }
        }catch(\Exception $e){
            $content["code"] =406; 
            $content["message"] ="System Error ".$e;
            $content["success"] =null;
        }
        
        return $content;
    }

    public function getLoggedUserProfile(){
        return FacilityUser::where('id',Auth::guard("facilityuser")->user()->id)
        ->select('id','first_name','last_name','email','alternate_email','phoneno','username')
        ->first();
    }

    public function updatePassword($inputs){
        return FacilityUser::where('id',$inputs['id'])->update(['password'=>$inputs['password']]);
    }
}