<?php

namespace Modules\Hranalytics\Http\Controllers;

use App\Services\LocationService;
use Carbon\Carbon;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Admin\Models\Customer;
use Modules\Admin\Models\EmployeeRatingLookup;
use Modules\Admin\Models\ScheduleMaximumHour;
use Modules\Admin\Models\ShiftTiming;
use Modules\Admin\Models\StcReportingTemplateRule;
use Modules\Admin\Models\User;
use Modules\Admin\Repositories\CustomerEmployeeAllocationRepository;
use Modules\Admin\Repositories\CustomerRepository;
use Modules\Admin\Repositories\EmployeeAllocationRepository;
use Modules\Admin\Repositories\EmployeeRatingPolicyRepository;
use Modules\Admin\Repositories\IndustrySectorLookupRepository;
use Modules\Admin\Repositories\RegionLookupRepository;
use Modules\Admin\Repositories\ScheduleAssignmentTypeLookupRepository;
use Modules\Admin\Repositories\ScheduleShiftTimingsRepository;
use Modules\Admin\Repositories\SecurityClearanceLookupRepository;
use Modules\Admin\Repositories\StatusLogLookupRepository;
use Modules\Admin\Repositories\UserRepository;
use Modules\Hranalytics\Http\Requests\EventLogRequest;
use Modules\Hranalytics\Http\Requests\ScheduleCustomerRequirementRequest;
use Modules\Hranalytics\Models\Candidate;
use Modules\Hranalytics\Models\EventLogEntry;
use Modules\Hranalytics\Models\ScheduleCustomerMultipleFillShifts;
use Modules\Hranalytics\Models\ScheduleCustomerRequirement;
use Modules\Hranalytics\Models\UserRating;
use Modules\Hranalytics\Repositories\CandidateRepository;
use Modules\Hranalytics\Repositories\ScheduleCustomerMultipleFillShiftsRepository;
use Modules\Hranalytics\Repositories\ScheduleCustomerRequirementRepository;
use Modules\Timetracker\Models\EmployeeAvailability;
use Modules\Timetracker\Models\EmployeeTimeoff;
use Modules\Timetracker\Models\EmployeeUnavailability;
use Session;
use View;
use Illuminate\Support\Facades\Auth;

class CandidateScheduleController extends Controller
{
    /**
     * Repository instance.
     *
     * @var \App\Repositories\ScheduleCustomerRequirementRepository
     * @var \App\Repositories\CandidateRepository
     * @var \App\Repositories\RegionLookupRepository
     * @var \App\Repositories\IndustrySectorLookupRepository
     * @var \App\Repositories\SecurityClearanceLookupRepository
     * @var \App\Repositories\StatusLogLookupRepository
     * @var \App\Repositories\ScheduleAssignmentTypeLookupRepository
     * @var \App\Repositories\CustomerRepository
     * @var \App\Modules\Admin\Repositories\ScheduleShiftTimingsRepository
     * @var \App\Modules\Admin\Repositories\ScheduleCustomerMultipleFillShiftsRepository
     *
     */
    protected $scheduleCustomerRequirementRepository, $userRepository, $regionLookupRepository, $industrySectorLookupRepository, $securityClearanceLookupRepository, $statusLogLookupRepository, $customerRepository, $candidateRepository, $scheduleAssignmentTypeLookupRepository, $scheduleShiftTimingsRepository, $scheduleCustomerMultipleFillShiftsRepository, $securityClearanceLookupModel, $employee_allocation, $employee_availability;

    /**
     * Create Repository instance.
     *
     * @param  \App\Repositories\ScheduleCustomerRequirementRepository $scheduleCustomerRequirementRepository
     * @param  \App\Repositories\CandidateRepository $candidateRepository
     * @param  \App\Repositories\RegionLookupRepository $regionLookupRepository
     * @param  \App\Repositories\IndustrySectorLookupRepository $industrySectorLookupRepository
     * @param  \App\Repositories\SecurityClearanceLookupRepository $securityClearanceLookupRepository
     * @param  \App\Repositories\StatusLogLookupRepository $statusLogLookupRepository
     * @param  \App\Repositories\ScheduleAssignmentTypeLookupRepository $scheduleAssignmentTypeLookupRepository
     * @param  \App\Repositories\CustomerRepository $customerRepository
     * @param  \App\Modules\Admin\Repositories\ScheduleShiftTimingsRepository $scheduleShiftTimingsRepository
     * @param  \App\Modules\Admin\Repositories\ScheduleCustomerMultipleFillShiftsRepository $scheduleCustomerMultipleFillShiftsRepository
     * @param LocationService $locationService
     * @return void
     */
    public function __construct(
        UserRepository $userRepository,
        ScheduleCustomerRequirementRepository $scheduleCustomerRequirementRepository,
        CandidateRepository $candidateRepository,
        RegionLookupRepository $regionLookupRepository,
        IndustrySectorLookupRepository $industrySectorLookupRepository,
        SecurityClearanceLookupRepository $securityClearanceLookupRepository,
        StatusLogLookupRepository $statusLogLookupRepository,
        ScheduleAssignmentTypeLookupRepository $scheduleAssignmentTypeLookupRepository,
        CustomerRepository $customerRepository,
        ScheduleShiftTimingsRepository $scheduleShiftTimingsRepository,
        ScheduleCustomerMultipleFillShiftsRepository $scheduleCustomerMultipleFillShiftsRepository,
        EmployeeAllocationRepository $employee_allocation,
        EmployeeAvailability $employee_availability,
        EmployeeRatingPolicyRepository $employeeRatingPolicyRepository,
        CustomerEmployeeAllocationRepository $customerEmployeeAllocationRepository,
        LocationService $locationService
    ) {
        $this->scheduleCustomerRequirementRepository = $scheduleCustomerRequirementRepository;
        $this->candidateRepository = $candidateRepository;
        $this->regionLookupRepository = $regionLookupRepository;
        $this->industrySectorLookupRepository = $industrySectorLookupRepository;
        $this->securityClearanceLookupRepository = $securityClearanceLookupRepository;
        $this->statusLogLookupRepository = $statusLogLookupRepository;
        $this->scheduleAssignmentTypeLookupRepository = $scheduleAssignmentTypeLookupRepository;
        $this->customerRepository = $customerRepository;
        $this->scheduleShiftTimingsRepository = $scheduleShiftTimingsRepository;
        $this->scheduleCustomerMultipleFillShiftsRepository = $scheduleCustomerMultipleFillShiftsRepository;
        $this->employee_allocation = $employee_allocation;
        $this->employee_availability = $employee_availability;
        $this->employeeRatingPolicyRepository = $employeeRatingPolicyRepository;
        $this->customerEmployeeAllocationRepository = $customerEmployeeAllocationRepository;
        $this->userRepository = $userRepository;
        $this->locationService = $locationService;
    }

    /**
     * Function to calculate array of available schedules
     *
     * @return array
     */
    private function initialiseScheduleArr()
    {
        $arr_schedule = array();
        $arr_all_days = config("globals.array_day");
        $arr_all_shifts = config("globals.array_shift");
        $clearence = $this->securityClearanceLookupRepository->getList();
        array_push($clearence, 'All');
        array_push($clearence, 'All');
        foreach ($arr_all_shifts as $shift) {
            foreach ($arr_all_days as $day) {
                foreach ($clearence as $clr) {
                    $arr_schedule[$shift][$day][$clr] = array();
                }
            }
        }
        return $arr_schedule;
    }

    public function updateMultiFillParent(Request $request)
    {
        $shiftId = $request->id;
        $parentShiftId = $request->parentId;
        $execution = 0;
        try {
            \DB::beginTransaction();

            $selfQuery = ScheduleCustomerMultipleFillShifts::find($shiftId)->update([
                "parent_id" => 0
            ]);
            if ($selfQuery) {
                $execution++;
            }

            $parentQuery = ScheduleCustomerMultipleFillShifts::find($parentShiftId)->update([
                "parent_id" => $shiftId
            ]);
            if ($parentQuery) {
                $execution++;
            }
            $elseQuery = ScheduleCustomerMultipleFillShifts::where("parent_id", $parentShiftId)->update([
                "parent_id" => $shiftId
            ]);

            if ($elseQuery) {
                $execution++;
            }
            if ($execution >= 2) {
                \DB::commit();
            } else {
                \DB::rollback();
            }
        } catch (\Throwable $th) {
            \DB::rollback();
            // throw $th;
        }
        if ($execution > 0) {
            $successContent['success'] = true;
            $successContent['message'] = 'Updated successfully';
            $successContent['code'] = 200;
        } else {
            $successContent['success'] = false;
            $successContent['message'] = 'Not Updated';
            $successContent['code'] = 406;
        }
        return json_encode($successContent, true);
    }

    /*
     * Schedule a candidate
     *
     * @param  Request $request, $customer_id, $requirement_id, $customer_contract_type
     * @return json
     */
    public function schedule(Request $request, $customer_id = 0, $requirement_id = 0, $customer_contract_type = 0, $security_clearence_id = 0)
    {
        // dd($customer_contract_type);
        $previouspage = str_replace(url('/'), '', url()->previous());
        $arr_schedule = array();
        $arr_all_days = config("globals.array_shift_day");
        $arr_all_shifts = ShiftTiming::all()->pluck('display_name', 'id');
        $employee_id_array = array();
        $unavailable_employees = array();
        $timeoffarray = [];
        $employeetimeoffreference = 0;
        $timeoff_id = 0;
        $timeoff_customer = 0;
        $timeoff_customerstc = 0;
        $timeoff_startdate = 0;
        $timeoff_starttime = 0;
        $timeoff_enddate = 0;
        $timeoff_endtime = 0;
        $timeoff_payrate = 0;
        $employeename = "";
        $timeoff_formattedstartdate = "";
        $timeoff_formattedenddate = "";
        if ($previouspage != "/timetracker/employeeTimeoff") {
            $request->session()->forget('timeoff_requestid');
            $request->session()->forget('timeoff_customer');
            $request->session()->forget('timeoff_customerstc');
            $request->session()->forget('timeoff_startdate');
            $request->session()->forget('timeoff_starttime');
            $request->session()->forget('timeoff_enddate');
            $request->session()->forget('timeoff_endtime');
            $request->session()->forget('timeoff_payrate');
            $request->session()->forget('timeoff_employeename');
            $request->session()->forget('timeoff_formattedstartdate');
            $request->session()->forget('timeoff_formattedenddate');
        }
        if ($request->session()->has('timeoff_customer')) {
            $timeoff_id = $request->session()->get('timeoff_requestid');
            $timeoff_customer = $request->session()->get('timeoff_customer');
            $timeoff_customerstc = $request->session()->get('timeoff_customerstc');
            $timeoff_startdate = $request->session()->get('timeoff_startdate');
            $timeoff_starttime = $request->session()->get('timeoff_starttime');
            $timeoff_enddate = $request->session()->get('timeoff_enddate');
            $timeoff_endtime = $request->session()->get('timeoff_endtime');
            $timeoff_payrate = $request->session()->get('timeoff_payrate');
            $employeename = $request->session()->get('timeoff_employeename');
            $timeoff_formattedstartdate = $request->session()->get('timeoff_formattedstartdate');
            $timeoff_formattedenddate = $request->session()->get('timeoff_formattedenddate');

            $request->session()->reflash();

            $timeoffarray = [$timeoff_customer, $timeoff_customerstc, $timeoff_startdate, $timeoff_starttime, $timeoff_enddate, $timeoff_endtime];
        }

        if ($requirement_id != 0) {
            $requirement_id = $requirement_id;

            $employeetimeoffreference = EmployeeTimeoff::where('backfillstatus', $requirement_id)->count();
        } elseif ($request->get('requirement_id') != 0) {
            $requirement_id = $request->get('requirement_id');

            $employeetimeoffreference = EmployeeTimeoff::where('backfillstatus', $requirement_id)->count();
        }

        if ($employeetimeoffreference > 0) {
            $employeetimeoff = EmployeeTimeoff::select('*', \DB::raw('(select concat_ws(" " ,first_name,last_name) from users where id=employee_timeoff.employee_id) as employeename'))
                ->with(['user', 'reasons', 'customer', 'employee', 'employee.trashedUser', 'cpidRate'])
                ->where('backfillstatus', $requirement_id)->first();
            $employeename = $employeetimeoff->user->first_name . " " . $employeetimeoff->user->last_name;
            $timeoff_formattedstartdate = date("l, F d, Y", strtotime($employeetimeoff->start_date));
            $timeoff_formattedenddate = date("l, F d, Y", strtotime($employeetimeoff->end_date));
        }

        if ($requirement_id != 0) {
            $requirement = $this->scheduleCustomerRequirementRepository->getScheduleRequirement($requirement_id);
            $unavailable_employees = EmployeeUnavailability::select('employee_id')
                ->where('from', '<=', $requirement->start_date)
                ->where('to', '>=', $requirement->end_date)->get()->toArray();
            // ->whereBetween('from', [$requirement->start_date, $requirement->end_date])
            // ->whereBetween('to', [$requirement->start_date, $requirement->end_date])->get()->toArray();
        }
        foreach ($arr_all_shifts as $shift_key => $shift) {
            foreach ($arr_all_days as $day_key => $day) {
                $query = $this->employee_availability
                    ->where('shift_timing_id', '=', $shift_key)
                    ->where('week_day', '=', $day_key)
                    ->whereNotIn('employee_id', $unavailable_employees);

                /* Case when Ajax loading of page and checkbox value is not null */
                if (($request->get('isajax') == true) && ($request->get('flag') == 0)) {
                    $current_availability = $request->get('checkedvalue');
                    $query->whereHas('employee', function ($query) use ($current_availability) {
                        $query->whereNotNull('work_type_id')->whereIn('work_type_id', $current_availability);
                    });
                }
                $query->whereHas('employee', function ($query) {
                    $query->whereNotNull('work_type_id');
                })->whereHas('employee.user', function ($q) {
                    $q->where('active', 1);
                });
                if ($security_clearence_id != 0 && $security_clearence_id != null) {
                    $query->whereHas('employee.user.securityClearanceUser', function ($q) use ($security_clearence_id) {
                        $q->where('security_clearance_lookup_id', $security_clearence_id);
                    });
                }
                $avail_employees = $query->get()->toArray();
                $arr_schedule[$shift_key][$day_key] = $avail_employees;
                if ($avail_employees != null) {
                    foreach ($avail_employees as $key => $value) {
                        $testset[] = $value['employee_id'];
                    }
                    $employee_id_array[$shift_key][$day_key] = implode(',', $testset);
                    unset($testset);
                }
            }
        }

        $lookups['industrySectorLookup'] = $this->industrySectorLookupRepository->getList();
        $lookups['regionLookup'] = $this->regionLookupRepository->getList();

        $lookups['securityClearanceLookup'] = $this->securityClearanceLookupRepository->getList();
        $lookups['shiftTiming'] = $this->scheduleShiftTimingsRepository->getDisplayableLookup();
        $lookups['shiftTimingFrom'] = $this->scheduleShiftTimingsRepository->getShiftTimeFrom();
        $lookups['shiftTimingTo'] = $this->scheduleShiftTimingsRepository->getShiftTimeTo();
        $lookups['maximum_overtime_hours'] = ScheduleMaximumHour::value('hours');
        $schedule_assignment_type = $this->scheduleAssignmentTypeLookupRepository->getList();
        $arr_all_days = config("globals.array_shift_day");
        $lookups['requesterLookup'] = $this->userRepository->getUserLookup(null, ['super_admin', 'admin']);

        /* Case when candidate query returns value and is ajax request and checkbox returning value */
        if (($request->get('isajax') == true) && ($request->get('flag') == 0)) {
            // when any of the checkbox is checked (onchange of checkbox)
            $htmlview = View::make('hranalytics::schedule.schedule', array('arr_all_days' => $arr_all_days, 'arr_schedule' => $arr_schedule, 'lookups' => $lookups, 'schedule_assignment_type' => $schedule_assignment_type, 'customer_id' => $customer_id, 'requirement_id' => $requirement_id, 'customer_contract_type' => $customer_contract_type, 'security_clearence_id' => $security_clearence_id, 'arr_all_shifts' => $arr_all_shifts, 'employee_id_array' => $employee_id_array, 'timeoff_id' => $timeoff_id, 'timeoff_customer' => $timeoff_customer, 'timeoff_customerstc' => $timeoff_customerstc, 'timeoff_startdate' => $timeoff_startdate, 'timeoff_starttime' => $timeoff_starttime, 'timeoff_enddate' => $timeoff_enddate, 'timeoff_endtime' => $timeoff_endtime, 'timeoff_payrate' => $timeoff_payrate, 'employeetimeoffreference' => $employeetimeoffreference, 'employeename' => $employeename, 'timeoff_formattedstartdate' => $timeoff_formattedstartdate, 'timeoff_formattedenddate' => $timeoff_formattedenddate))->render();
            return json_encode(compact('htmlview', 'arr_all_days', 'arr_schedule', 'lookups', 'schedule_assignment_type', 'security_clearence_id', 'arr_all_shifts', 'employee_id_array', 'timeoffarray', 'timeoff_id', 'timeoff_customer', 'timeoff_customerstc', 'timeoff_startdate', 'timeoff_starttime', 'timeoff_enddate', 'timeoff_endtime', 'timeoff_payrate', 'employeetimeoffreference', 'employeename', 'timeoff_formattedstartdate', 'timeoff_formattedenddate'));
        } else {
            return view('hranalytics::schedule.schedule', compact('arr_all_days', 'arr_schedule', 'lookups', 'schedule_assignment_type', 'customer_id', 'requirement_id', 'customer_contract_type', 'security_clearence_id', 'arr_all_shifts', 'employee_id_array', 'timeoffarray', 'timeoff_id', 'timeoff_customer', 'timeoff_customerstc', 'timeoff_startdate', 'timeoff_starttime', 'timeoff_enddate', 'timeoff_endtime', 'timeoff_payrate', 'employeetimeoffreference', 'employeename', 'timeoff_formattedstartdate', 'timeoff_formattedenddate'));
        }

        /* Case when candidate query returns value */
        /*   if (!$candidates->isEmpty()) {
    $arr_schedule = $this->initialiseScheduleArr();
    foreach ($candidate_arr as $key => $candidate) {
    if (!empty(json_decode($candidate['days_required'])) && json_decode($candidate['shifts'])) {
    $arr_candidate_days = json_decode($candidate['days_required']);
    $arr_candidate_shifts = json_decode($candidate['shifts']);
    $candidate['security_clearence'] = isset($candidate['security_clearence']) ? $candidate['security_clearence'] : array();
    array_push($candidate['security_clearence'], 'All');
    $arr_security_clearence = isset($candidate['security_clearence']) ? $candidate['security_clearence'] : [];
    if (($key = array_search('Weekends', $arr_candidate_shifts)) !== false) {
    unset($arr_candidate_shifts[$key]);
    }
    if (count($arr_candidate_days) == config("globals.candidate_day_count")) {
    array_unshift($arr_candidate_days, "Any Day");
    }
    if (count($arr_candidate_shifts) == config("globals.candidate_shift_count")) {
    array_unshift($arr_candidate_shifts, "All");
    }

    foreach ($arr_candidate_shifts as $shift) {
    foreach ($arr_candidate_days as $day) selected_project_no{
    foreach ($arr_security_clearence as $clr) {
    $arr_schedule[$shift][$day][$clr][] = $candidate['candidate_id'];
    }
    }
    }

    }
    }

    if (($request->get('isajax') == true) && ($request->get('flag') == 0)) {
    // when any of the checkbox is checked (onchange of checkbox)
    $htmlview = View::make('hranalytics::schedule.schedule', array('arr_all_days' => $arr_all_days, 'arr_schedule' => $arr_schedule, 'arr_candidate_days' => $arr_candidate_days, 'lookups' => $lookups, 'schedule_assignment_type' => $schedule_assignment_type, 'customer_id' => $customer_id, 'requirement_id' => $requirement_id, 'customer_contract_type' => $customer_contract_type))->render();

    return json_encode(compact('htmlview', 'arr_all_days', 'arr_schedule', 'arr_candidate_days', 'candidates', 'lookups', 'schedule_assignment_type'));
    }

    else if ($request->get('flag') == 1) {
    // when none of check box is checked (onchange of checkbox)
    $arr_schedule = $this->initialiseScheduleArr();
    $arr_candidate_days = config("globals.array_day");
    $htmlview = View::make('hranalytics::schedule.schedule', array('arr_all_days' => $arr_all_days, 'arr_schedule' => $arr_schedule, 'arr_candidate_days' => $arr_candidate_days, 'lookups' => $lookups, 'schedule_assignment_type' => $schedule_assignment_type, 'customer_id' => $customer_id, 'requirement_id' => $requirement_id, 'customer_contract_type' => $customer_contract_type))->render();

    return json_encode(compact('htmlview', 'arr_all_days', 'arr_schedule', 'arr_candidate_days', 'candidates', 'lookups', 'schedule_assignment_type'));
    }
    else {

    return view('hranalytics::schedule.schedule', compact('arr_all_days', 'arr_schedule', 'arr_candidate_days', 'shifts', 'candidates', 'lookups', 'schedule_assignment_type', 'customer_id', 'requirement_id', 'customer_contract_type'));
    }
    }
    else {
    $arr_schedule = $this->initialiseScheduleArr();
    $arr_candidate_days = config("globals.array_day");

    if (($request->get('isajax') == true)) {
    $htmlview = View::make('hranalytics::schedule.schedule', array('arr_all_days' => $arr_all_days, 'arr_schedule' => $arr_schedule, 'arr_candidate_days' => $arr_candidate_days, 'lookups' => $lookups, 'schedule_assignment_type' => $schedule_assignment_type, 'customer_id' => $customer_id, 'requirement_id' => $requirement_id, 'customer_contract_type' => $customer_contract_type))->render();
    return json_encode(compact('htmlview', 'arr_all_days', 'arr_schedule', 'arr_candidate_days', 'candidates', 'lookups', 'schedule_assignment_type'));
    } /* Case when candidate query returns null value and not ajax request
    else {
    return view('hranalytics::schedule.schedule', compact('arr_all_days', 'arr_schedule', 'arr_candidate_days', 'candidates', 'lookups', 'schedule_assignment_type', 'customer_id', 'requirement_id', 'customer_contract_type'));
    }
    }*/
    }

    public function setScheduleRequirementstimeoff(Request $request)
    {
        $timeoff_id = $request->get('timeoff_id');

        $requirement_id = $request->get('requirement_id');
        $emptimeoff = EmployeeTimeoff::find($timeoff_id);

        $emptimeoff->backfillstatus = $requirement_id;
        $emptimeoff->save();
    }

    /**
     * Redirect to view schedule summary page
     * @return view
     */
    public function scheduleSummary()
    {
        $customer_details_arr_short_term = array_combine(data_get($this->scheduleCustomerRequirementRepository->scheduleRequirementList(1), '*.customer.id'), data_get($this->scheduleCustomerRequirementRepository->scheduleRequirementList(1), '*.customer.client_name'));
        $customer_details_arr_permanent = array_combine(data_get($this->scheduleCustomerRequirementRepository->scheduleRequirementList(0), '*.customer.id'), data_get($this->scheduleCustomerRequirementRepository->scheduleRequirementList(0), '*.customer.client_name'));
        return view('hranalytics::schedule.schedule-summary', compact('customer_details_arr_short_term', 'customer_details_arr_permanent'));
    }

    /**
     * Get the schedule summary datatable list
     * @param  $type
     * @return void
     */
    public function getscheduleRequirementList($type, $client_id = null)
    {

        return datatables()->of($this->scheduleCustomerRequirementRepository->scheduleRequirementList($type, $client_id))->toJson();
    }

    /**
     * Store Schedule Requirements
     * @param  ScheduleCustomerRequirementRequest $request
     * @return
     */
    public function storeScheduleRequirements(ScheduleCustomerRequirementRequest $request)
    {
        $scheduleCustomerRequirement = $this->scheduleCustomerRequirementRepository->store($request);
        if ($request->get('type') == config('globals.multiple_fill_id')) {
            $result = $this->scheduleCustomerMultipleFillShiftsRepository->store($request, $scheduleCustomerRequirement);
        }
        return response()->json(array('success' => true, 'result' => $scheduleCustomerRequirement));
    }

    public function updateScheduleRequirements(Request $request)
    {
        $requirement_id = $request->reqId;
        $expiry_date = $request->expiry_date;
        $expiry_time = $request->expiry_time;
        $expiry_datetime = null;
        if ($expiry_date != "") {
            $expiry_datetime = \Carbon::parse($expiry_date . " " . $expiry_time);
        }
        $updatedData = ScheduleCustomerRequirement::find($requirement_id)->update([
            "expiry_date" => $expiry_datetime
        ]);
        if ($updatedData) {
            $successContent['success'] = true;
            $successContent['message'] = 'Updated successfully';
            $successContent['code'] = 200;
        } else {
            $successContent['success'] = false;
            $successContent['message'] = 'Not Updated';
            $successContent['code'] = 406;
        }
        return json_encode($successContent, true);
    }

    /**
     * Get the Schedule Details Page based on project id and reqirement id
     *
     * @param  $project_id, $requirement_id
     * @return view
     */
    public function scheduleDetails($project_id, $requirement_id)
    {
        $stcprojectdetails = $this->scheduleCustomerRequirementRepository->prepareScheduleRecords($project_id, $requirement_id);
        return view('hranalytics::schedule.schedule-details', compact('stcprojectdetails', 'project_id', 'requirement_id'));
    }

    /**
     * To get the event log based on the customer & requirement
     *
     * @return void
     */
    public function scheduleEventLog($project_id, $requirement_id)
    {

        return datatables()->of($this->scheduleCustomerRequirementRepository->getEventLog($requirement_id))->toJson();
    }

    /**
     * Get the Permanent summary list
     *
     * @return void
     */
    public function getPermanentSummaryList()
    {
        return datatables()->of($this->scheduleCustomerRequirementRepository->scheduleRequirementPermanentList())->toJson();
    }

    /**
     * Function to get the Map of Candidates and Customer
     *
     * @param  Request $request
     * @return view
     */
    public function plotJobschdeuleCandidatesMap(Request $request)
    {
        $candidates = $this->customerRepository->getAllCandidatesMap('Applied', $request);
        $customer = Customer::find($request->get('selected_project_no'));
        return view('hranalytics::schedule.schedule-map', compact('candidates', 'customer'));
    }

    /**
     * Function to get the Map of Employee and Customer
     *
     * @param  Request $request
     * @return view
     */
    public function plotJobschdeuleEmployeeMap(Request $request)
    {
        $employees = $this->customerRepository->getAllEmployeesMap('Applied', $request);
        $customer = Customer::find($request->get('selected_project_no'));
        return view('hranalytics::schedule.schedule-map', compact('employees', 'customer'));
    }

    /**
     * Function to populate customer and requirement details
     *
     * @param  , $id
     * @return json
     */
    public function getRequirementDetails($id)
    {
        $requirement = $this->scheduleCustomerRequirementRepository->get($id);
        // dd($requirement);
        return response()->json(['success' => true, 'data' => $requirement]);
    }

    /**
     * Function to get the project number list based on project type(Permanent or STC)
     *
     * @param  $stc
     * @return json
     */
    public function projectList()
    {
        $stcprojectlist = $this->customerRepository->getProjects(null);
        return response()->json(['success' => true, 'data' => $stcprojectlist]);
    }

    /*
     * Function to get customer details in the readonly fields based on project number chosen
     *
     * @param  Request $request
     * @return json
     */
    public function getCustomer(Request $request)
    {
        $stcprojectdetails = $this->customerRepository->getSingleCustomer($request->id);
        return response()->json(['success' => true, 'data' => $stcprojectdetails]);
    }

    /**
     * Function to store STC
     *
     * @param  Request $request
     * @return json
     */
    public function stcStore(Request $request)
    {
        $stcStore = $this->candidateRepository->storeStcProject($request);
        if ($stcStore == 'true') {
            return response()->json(array('success' => true));
        } else {
            return response()->json(array('success' => false));
        }
    }

    /**
     * Function to get EventLog Details of a candidate
     *
     * @param  $candidate_id, $requirement_id
     * @return view
     */
    public function eventLogForm($requirement_id, $shift_id, $user_id)
    {
        $result_arr = $this->scheduleCustomerRequirementRepository->getScheduleRequirementDetails($requirement_id);
        $scheduleRequirement = $result_arr[0];
        $user = User::with('employee')->where('id', $user_id)->first();
        $logDetails = EventLogEntry::where('schedule_customer_requirement_id', $requirement_id)->where('multiple_shift_id', $shift_id)->where('user_id', $user_id)->orderBy('created_at', 'desc')->first();
        $callLogLookup = $this->statusLogLookupRepository->statusLogLookupList();
        return view('hranalytics::schedule.schedule-event-log-entry', compact('user', 'shift_id', 'callLogLookup', 'scheduleRequirement', 'logDetails'));
    }

    /**
     * Function to store EventLog
     *
     * @param  Request $request
     * @return json
     */
    public function eventLogSave(EventLogRequest $request)
    {
        //enough hours between shifts.
        $shiftsFoundBwEnoughHours = 0;
        if ($request->get('status') == config('globals.called_accepted_shift_id')) {
            $shiftsFoundBwEnoughHours = $this->scheduleCustomerMultipleFillShiftsRepository->checkForAnyApprovedShiftWithinRestrictedHours($request->get('shift_id'), $request->get('user_id'));
        }
        if ($shiftsFoundBwEnoughHours > 0) {
            return response()->json(['success' => false, 'msg' => 'Cannot apply for this shift as you do not have enough hours between shifts.']);
        } else {
            $eventlog = $this->scheduleCustomerRequirementRepository->saveEventLog($request);
        }
        return response()->json(['success' => true]);
    }

    /**
     * Function to get Available Candidate Schedule Details list in Datatable
     *
     * @param  Request $request
     * @return json
     */
    public function getCandidatesBasedOnSchedule(Request $request)
    {
        return datatables()->of($this->scheduleCustomerRequirementRepository->getScheduleCandidates($request))->toJson();
    }

    /**
     * Function to get Available Employees Schedule Details list in Datatable
     *
     * @param  Request $request
     * @return json
     */
    public function getEmployeesBasedOnSchedule(Request $request)
    {
        return datatables()->of($this->scheduleCustomerRequirementRepository->getScheduleEmployees($request))->toJson();
    }

    /**
     * Function to generate dynamic rows based on date difference and total no of shifts
     *
     * @param  Request $request
     * @return json
     */
    public function multifillGenerateRows(Request $request)
    {
        $data = $this->scheduleCustomerRequirementRepository->getScheduleRequirementDetails($request->id);
        $preparedData = $this->scheduleCustomerMultipleFillShiftsRepository->getRequirementData($data[0]);
        return datatables()->of($preparedData)->toJson();
    }

    /**
     * Function to delete candidate assigned for multifill shift
     *
     * @param  id
     * @return json
     */
    public function deleteCandidateforMultifill($id)
    {
        $destroy_assigned = $this->scheduleCustomerMultipleFillShiftsRepository->deleteAllocated($id);
        return response()->json(['success' => true]);
    }

    /**
     * Function to get Stc report-employee summary page
     *
     * @param  null
     * @return view
     */
    public function stcEmployeeSummary()
    {
        $template_rule_arr = StcReportingTemplateRule::with('color')->get()->toArray();
        $ratingLookups = EmployeeRatingLookup::orderBy('score', 'ASC')->pluck('rating', 'id')->toArray();
        $project_list = $this->customerEmployeeAllocationRepository->getAllocatedCustomersList(\Auth::user());
        $employeeList = $this->userRepository->getUserList(true, null, null, null)->sortBy('full_name')->pluck('full_name', 'id')->toArray();
        return view('hranalytics::schedule.stc-employee-summary', compact('template_rule_arr', 'ratingLookups', 'project_list', 'employeeList'));
    }

    /**
     * Function to get Stc report-employee summary datatable list
     *
     * @param  null
     * @return view
     */
    public function stcEmployeeSummaryList($userId = 0, $spare = 1)
    {
        $userId = (int) $userId;
        $spare = (int) $spare;
        $logged_in_user_id = \Auth::id();
        $flag = false;
        $logged_in_user = User::find($logged_in_user_id);
        if ($logged_in_user->hasPermissionTo('view_all_stc_schedule_summary') || $logged_in_user_id->hasAnyPermission(['admin', 'super_admin'])) {
            $allocated_employees = null;
        } else {
            $allocated_employees = $this->employee_allocation->getEmployeeIdAssigned($logged_in_user_id)->toArray();
            $flag = true;

            if ($userId != 0) {
                if (!in_array($userId, $allocated_employees)) {
                    $userId = 0;
                }
            }

            //Allocated list
        }
        return datatables()->of($this->scheduleCustomerRequirementRepository->getStcSummaryEmployees($allocated_employees, $flag, $userId, $spare))->toJson();
    }

    /**
     * Function to delete multifill shift
     *
     * @param  id
     * @return json
     */
    public function deleteMultifillShift($id)
    {
        $multipleFillObject = ScheduleCustomerMultipleFillShifts::find($id);
        $destroyStatus = $this->scheduleCustomerMultipleFillShiftsRepository->deleteShift($id);
        if (!$destroyStatus['status']) {
            $message = 'Please unassign shift to delete';
            $status = false;
            $icon = '';
        } else {
            $message = 'Shift has been deleted';
            $status = true;
            $icon = $destroyStatus['delete_icon_parent'];
        }
        return response()->json([
            'success' => $status,
            'msg' => $message,
            'parent_id' => $icon,
        ]);
    }

    /**
     * Function to get Availability and unavailability shift
     *
     * @param  id
     * @return json
     */
    public function getAvailabilityandUnavailabilityDates($id, $requirement_id)
    {
        $result_arr = $this->scheduleCustomerRequirementRepository->getScheduleRequirementDetails($requirement_id);
        $requirement = $result_arr[0];
        $date_range = $this->scheduleCustomerMultipleFillShiftsRepository->createDateRange($requirement->start_date, $requirement->end_date);
        $availability = EmployeeAvailability::where('employee_id', $id)->get();
        $shiftarray = config("globals.array_shift_day");
        foreach ($availability as $key => $value) {
            $shiftarray_name[] = ($shiftarray[$value->week_day]);
        }
        foreach ($date_range as $key => $each_date) {
            $unavailable_employees = EmployeeUnavailability::where('from', '<=', $each_date['date'])
                ->where('to', '>=', $each_date['date'])->where('employee_id', $id)->get();
            if ($unavailable_employees->first()) {
                $date[$each_date['date']] = 'Unavailable';
            } else {
                $day_value = new DateTime($each_date['date']);
                if (in_array($day_value->format('l'), $shiftarray_name)) {
                    $date[$each_date['date']] = 'Available';
                }
            }
        }
        return response()->json(['success' => true, 'result' => $date, 'employeeid' => $id]);
    }

    /**
     * Get Employees Performance Log.
     *
     * @param $id
     * @return \Illuminate\Http\Response
     */
    public function getEmployeeRatings($id)
    {
        //  $user = $this->user_repository->getUserDetails($id);
        $employee_rating = UserRating::with('user', 'userRating')->where('employee_id', $id)->orderBy('created_at', 'desc')->get();
        $html = \View::make('hranalytics::schedule.partials.employee-ratings')
            ->with(compact(['employee_rating']))
            ->render();
        return $html;
    }

    /**
     * Get policy by rating
     *
     * @return \Illuminate\Http\Response
     */
    public function getPolicyByRating(Request $request)
    {
        return $this->employeeRatingPolicyRepository->getRatingPolicyDetails($request->id);
        //  return response()->json(['vehicles'=> $policies]);
    }

    public function distanceWithTimeByPositionCoordinates(Request $request)
    {
        $custCoordnts = $request->get('customer_coordinates');
        $empCoordnts = $request->get('employee_coordinates');
        $userId = $request->get('user_id');

        $lastUpdateDate = null;
        if (!empty($userId)) {
            $employeeAvailability = EmployeeAvailability::withTrashed()->where('employee_id', $userId)
                ->latest('updated_at')->first();
            $employeeUnAvailability = EmployeeUnavailability::withTrashed()->where('employee_id', $userId)
                ->latest('updated_at')->first();

            if (!empty($employeeUnAvailability) && !empty($employeeAvailability)) {
                if ($employeeUnAvailability->updated_at > $employeeAvailability->updated_at) {
                    $lastUpdateDate = $employeeUnAvailability->updated_at;
                } else {
                    $lastUpdateDate = $employeeAvailability->updated_at;
                }
            } elseif (!empty($employeeUnAvailability)) {
                $lastUpdateDate = $employeeUnAvailability->updated_at;
            } elseif (!empty($employeeAvailability)) {
                $lastUpdateDate = $employeeAvailability->updated_at;
            }
        }

        $distanceDetails = ['distance' => '-', 'duration' => '-'];
        if (
            !empty($empCoordnts['lat'])
            && !empty($empCoordnts['lng'])
            && !empty($custCoordnts['lat'])
            && !empty($custCoordnts['lng'])
        ) {
            $inputArray = [
                'origins' => [
                    0 => [
                        'lat' => $empCoordnts['lat'],
                        'long' => $empCoordnts['lng'],
                    ],
                ],
                'destinations' => [
                    0 => [
                        'lat' => $custCoordnts['lat'],
                        'long' => $custCoordnts['lng'],
                    ],
                ],
            ];
            $response = $this->locationService->getDrivingDistance($inputArray);
            if (!empty($response) && isset($response['distanceMatrix']) && isset($response['distanceMatrix']->rows[0]) && isset($response['distanceMatrix']->rows[0]->elements[0])) {
                $matrixData = $response['distanceMatrix']->rows[0]->elements[0];
                if (!empty($matrixData) && isset($matrixData->distance) && isset($matrixData->duration)) {
                    $distanceDetails = [
                        'distance' => $matrixData->distance->text,
                        'duration' => $matrixData->duration->text,
                    ];
                }
            }
        }

        return response()->json([
            'distance' => $distanceDetails,
            'last_update_date' => ($lastUpdateDate != null) ? Carbon::parse($lastUpdateDate)->format('M d, Y') : '--',
        ]);
    }

    public function getScheduleOverview(Request $request)
    {
        $type = $request->get('type');
        $keyDate = $request->get('key-date');
        $userId = $request->get('user-id');
        $records = $this->scheduleCustomerRequirementRepository->getScheduleOverViewByParams($userId, $type, $keyDate);
        return response()->json([
            'records' => $records,
        ]);
    }
}
