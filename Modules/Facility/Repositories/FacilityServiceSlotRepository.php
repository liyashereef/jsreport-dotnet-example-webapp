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


class FacilityServiceSlotRepository
{
    public function __construct(){

    }

    public function getFacilitySlot($facilityid){
        $data = [];
        $facilitydata = FacilityServiceSlot::where(["model_id"=>$facilityid,
        "model_type"=>"Modules\Facility\Models\Facility"])->whereNull("expiry_date")->first()->toArray();
        
        return $facilitydata;
    }
}