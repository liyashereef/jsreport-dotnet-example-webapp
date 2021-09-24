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


class FacilityServiceDataRepository
{
    public function __construct(){
        $this->model =  new FacilityServiceData();
    }

    public function getFacilityData($facilityid){
        $data = [];
        $facilitydata = FacilityServiceData::select("weekend_booking","maxbooking_perday",
        "tolerance_perslot","booking_window")->where(["model_id"=>$facilityid,
        "model_type"=>"Modules\Facility\Models\Facility"])->whereNull("expiry_date")->first()->toArray();
        
        return $facilitydata;
    }
    /**
     * Fetching active entries.
     * @param model_id,model_type
     */
    public function getActiveData($inputs){
        return  $this->model
        ->when(!empty($inputs) && isset($inputs['model_id']), function ($que) use($inputs){
            return $que->where('model_id',$inputs['model_id']);
        }) ->when(!empty($inputs) && isset($inputs['model_type']), function ($que) use($inputs){
            return $que->where('model_type',$inputs['model_type']);
        })
        ->whereNull('expiry_date')
        ->orderBy('id','DESC')
        ->first();
    }
    
}