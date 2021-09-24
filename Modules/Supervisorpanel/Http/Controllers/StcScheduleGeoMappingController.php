<?php

namespace Modules\Supervisorpanel\Http\Controllers;

use App\Services\HelperService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Admin\Models\Customer;
use Modules\Admin\Models\SecurityClearanceLookup;
use Modules\Admin\Repositories\ScheduleAssignmentTypeLookupRepository;
use Modules\Supervisorpanel\Repositories\StcScheduleGeoMappingRepository;

class StcScheduleGeoMappingController extends Controller
{
    protected $stcScheduleGeoMappingRepository, $scheduleAssignmentTypeLookupRepository;

    public function __construct(StcScheduleGeoMappingRepository $stcScheduleGeoMappingRepository, ScheduleAssignmentTypeLookupRepository $scheduleAssignmentTypeLookupRepository
    ) {
        $this->helperService = new HelperService();
        $this->stcScheduleGeoMappingRepository = $stcScheduleGeoMappingRepository;
        $this->scheduleAssignmentTypeLookupRepository = $scheduleAssignmentTypeLookupRepository;
    }

    public function index(Request $request)
    {
        $customers = Customer::pluck('client_name', 'id');
        $cityList = Customer::groupBy('city')->pluck('city', 'city')->toArray();
        $securityClearance = SecurityClearanceLookup::get()->pluck('security_clearance', 'id')->toArray();
        $customerTypes = [0 => 'All', 1 => 'STC', 2 => 'Permanent'];
        $assignmentTypes = $this->scheduleAssignmentTypeLookupRepository->getList();
        if (!empty($assignmentTypes) && isset($assignmentTypes[config('globals.multiple_fill_id')])) {
            unset($assignmentTypes[config('globals.multiple_fill_id')]);
        }
        return view('supervisorpanel::stc-schedule-geo-mapping', compact('assignmentTypes', 'request', 'customers', 'securityClearance', 'cityList', 'customerTypes'));
    }

    public function fetchStcSiteDetails(Request $request)
    {
        return response()->json($this->stcScheduleGeoMappingRepository->getStcSiteDetails($request));
    }

    public function showStcScheduleDetails(Request $request)
    {
        return response()->json($this->stcScheduleGeoMappingRepository->getStcGeoMappingDetailsByCustomer($request));
    }
}
