<?php

namespace Modules\Employeescheduling\Http\Controllers;

use App\Repositories\LandingWidgetRepository;
use Auth;
use DateTime;
use DB;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Admin\Models\User;
use Modules\Admin\Repositories\CustomerEmployeeAllocationRepository;
use Modules\Admin\Repositories\CustomerRepository;
use Modules\Admin\Repositories\PayPeriodRepository;
use Modules\Admin\Repositories\UserRepository;
use Modules\Contracts\Repositories\ContractsRepository;
use Modules\Employeescheduling\Models\EmployeeScheduleTemporaryStorage;
use Modules\Employeescheduling\Models\EmployeeSchedule;
use Modules\Employeescheduling\Models\EmployeeScheduleTimeLog;
use Modules\Employeescheduling\Repositories\SchedulingRepository;
use Session;
use Spatie\Permission\Models\Permission;
use View;
use \Carbon\Carbon;

class EmployeeschedulingController extends Controller
{

    const PAYPERIOD_PAST = 5;
    const PAYPERIOD_FUTURE = 12;

    protected $customerrepository, $userRepository, $payPeriodRepository, $CustomerEmployeeAllocationRepository, $SchedulingRepository, $schedulekey, $contractsrepository;
    protected $landingWidgetRepository;
    public function __construct(CustomerRepository $customerrepository, LandingWidgetRepository $landingWidgetRepository, UserRepository $userRepository, ContractsRepository $contractsrepository, PayPeriodRepository $payPeriodRepository, CustomerEmployeeAllocationRepository $CustomerEmployeeAllocationRepository, SchedulingRepository $SchedulingRepository)
    {
        $this->customerrepository = $customerrepository;
        $this->payPeriodRepository = $payPeriodRepository;
        $this->CustomerEmployeeAllocationRepository = $CustomerEmployeeAllocationRepository;
        $this->SchedulingRepository = $SchedulingRepository;
        $this->contractsrepository = $contractsrepository;
        $this->userRepository = $userRepository;
        $this->landingWidgetRepository = $landingWidgetRepository;
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        return view('employeescheduling::index');
    }

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function createschedule(Request $request)
    {
        $uiParameters = ['customerId' => null, 'payperiodIds' => null];
        $rejectedcustomerid = "";
        $rejectedpayperiods = null;
        $initialrequirementid = 0;
        $updatePastScheduleAllowed = false;
        try {
            $spares = User::with(['employee_training' => function ($q) {
                return $q->where('completed', true);
            }])
                ->whereHas('roles.permissions', function ($q) {
                    return $q->where('name', "Spares pool");
                })
                // ->role(['Spares pool'])
                ->get();
        } catch (\Throwable $th) {
            $spares = [];
        }

        if ($request->rejected_id) {
            $updatePastScheduleAllowed = $this->SchedulingRepository->updatePastScheduleAllowed($request->rejected_id);
            $initialrequirementid = $request->rejected_id;
            $uiParameters = $this->SchedulingRepository->rejectPopulatedata($request->rejected_id);
            $rejectedcustomerid = $uiParameters["customerId"];
            $rejectedpayperiods = json_encode($uiParameters["payperiodIds"], true);
        }

        $this->SchedulingRepository->removepreviouslogs();
        $this->schedulekey = \Auth::user()->id . "-" . date("Y-m-d");
        $logged_in_user = \Auth::user();
        $role = $logged_in_user->roles[0]->name;
        $customerarray = [];
        $request->session()->forget('schedulegrid');
        $customers = $this->SchedulingRepository->getCustomerList();
        $customers = collect($customers)->sortBy('project_number')->toArray();

        $payperiods = $this->payPeriodRepository->getAllActivePayPeriodsabovedate();
        return view("employeescheduling::employeeschedule", compact(
            'customers',
            'spares',
            'payperiods',
            'rejectedcustomerid',
            'rejectedpayperiods',
            'initialrequirementid',
            'updatePastScheduleAllowed'
        ));
    }

    public function prepopulatelogs(Request $request)
    {
        $key = Auth::user()->id . "-" . date("Y-m-d");
        $payperiod = $request->get('payperiod');
        $project = $request->get('projectid');
        $oncart = $this->SchedulingRepository->prepopulatelogs($key, $payperiod, $project);
        return json_encode($oncart);
    }

    public function getProcessedblock(Request $request)
    {
        $blockcount = $request->session()->get('schedule_block');
        $project = $request->get('projectid');
        $payperiod = $request->get('payperiod');
        $initialrequirementid = $request->get('initialrequirementid');
        $payperioddates = $this->payPeriodRepository->getPayperiodByArray($payperiod);

        $loopdates = [];
        $loopstart_date = "";
        $loopend_date = "";
        $payperiodarray = [];
        $scheduleDate = "";
        foreach ($payperioddates as $key => $value) {
            $loopstart_date = $value["start_date"];
            $loopend_date = $value["end_date"];
            if ($scheduleDate == "") {
                $scheduleDate = $loopstart_date;
            }

            $begin = new DateTime($loopstart_date);
            $end = new DateTime($loopend_date);

            for ($i = $begin; $i <= $end; $i->modify('+1 day')) {
                $payperiodarray[$i->format("Y-m-d")] = $value["id"];
                array_push($loopdates, $i->format("Y-m-d"));
            }
        }
        //dd($payperioddates);
        $request->session()->put('schedule_block', $blockcount + 1);
        $noofdays = count($loopdates);
        $employees = $this->userRepository->allocationUserList($project, ['client'], true, false, true);
        //$employees = $this->CustomerEmployeeAllocationRepository->allocationList($project,['super_admin','admin','client'],true);
        $employeeids = $employees->pluck('id')->toArray();
        $employeestraining = $this->SchedulingRepository->getTrainingdetails($employeeids);

        $traininguserarray = [];
        foreach ($employeestraining as $training) {

            $courses = $training->course_content;
            $contentitle = $training->course_content->content_title . "-" . $training->course_content->completed_date;
            if ($training->completed_date) {
                $contentitle = $training->course_content->content_title . " (" . (date("d M, Y", strtotime($training->completed_date))) . ") ";
            }
            $traininguserarray[$training->user_id][$training->course_content_id] = $contentitle;
        }

        $empoyeetraininguser = $employeestraining->pluck('user_id')->toArray();
        $trainedemployeesarray = array_unique($empoyeetraininguser);
        $employeesarray = $employees->toArray();
        $payperiodsumarray = [];

        $extrausers = collect([]);
        $EmployeeScheduleAveragePayperiodHours = collect([]);
        if ($initialrequirementid > 0) {
            $key = Auth::user()->id . "-" . date("Y-m-d");
            $allocatedemployeeid = $employees->pluck('id')->toArray();
            $extraemployees = [];
            //echo $initialrequirementid;
            /*
            $EmployeeScheduleAveragePayperiodHours = EmployeeScheduleAveragePayperiodHours::selectRaw('*,payperiod_id as payperiod')
            ->where('employee_schedule_id',$initialrequirementid)->get();
             */
            $EmployeeScheduleAveragePayperiodHours = EmployeeScheduleTemporaryStorage::selectRaw('payperiod,week,sum(hours) as average_hours')->where(['scheduleid' => $key, "customer_id" => $project])
                ->groupBy(['payperiod', 'week'])->get();
            foreach ($EmployeeScheduleAveragePayperiodHours as $workhours) {
                $weekcalc = $this->SchedulingRepository->getWeeklycalculation($key, $project, $workhours->week, $payperiod);
                //dd($weekcalc);
                $payperiodsumarray[$workhours->payperiod . "-" . $workhours->week] = $weekcalc[0];
            }

            // dd($allocatedemployeeid);
            $timearray = [];
            $populatedemployees = EmployeeScheduleTemporaryStorage::select('employeeid')->where(["scheduleid" => $key, "customer_id" => $project])
                ->groupBy('employeeid')->get()->pluck('employeeid')->toArray();
            foreach ($populatedemployees as $key => $value) {
                if (in_array($value, $allocatedemployeeid)) {
                    // echo $value;
                } else {
                    array_push($extraemployees, $value);
                }
            }
            $extrausers = User::whereIn('id', $extraemployees)->get();
        }
        $employees = collect($employeesarray)->sortBy('first_name')->toArray();
        $contractDetails = $this->contractsrepository->getContractsBetweenTwoDatesByCustomerId($project, $scheduleDate);
        $contracthours = $contractDetails != null ? $contractDetails["total_hours_perweek"] : "";
        $blockid = $blockcount + 1;
        $contractarray = explode(".", $contracthours);
        $chours = $contractarray[0];
        $cminutes = 0;
        $inpcontracthours = 0;
        if (count($contractarray) > 1) {
            $cminutes = (int) ($contractarray[1] * .6);
            $inpcontracthours = ($chours . "." . $cminutes);
        } else {
            $inpcontracthours = $chours;
        }

        return view('employeescheduling::partials.schedule-block', compact(
            'employees',
            'extrausers',
            'traininguserarray',
            'trainedemployeesarray',
            'contracthours',
            'loopdates',
            'payperiod',
            'noofdays',
            'blockid',
            'payperiodarray',
            'initialrequirementid',
            'payperiodsumarray',
            "inpcontracthours"
        ));
    }

    public function getProcessedblockspares(Request $request)
    {
        $blockcount = $request->session()->get('schedule_block');
        $project = $request->get('projectid');
        $payperiod = $request->get('payperiod');
        $payperioddates = $this->payPeriodRepository->getPayperiodByArray($payperiod);

        $loopdates = [];
        $loopstart_date = "";
        $loopend_date = "";
        $payperiodarray = [];
        $scheduleDate = "";
        foreach ($payperioddates as $key => $value) {
            $loopstart_date = $value["start_date"];
            $loopend_date = $value["end_date"];
            if ($scheduleDate == "") {
                $scheduleDate = $loopstart_date;
            }

            $begin = new DateTime($loopstart_date);
            $end = new DateTime($loopend_date);

            for ($i = $begin; $i <= $end; $i->modify('+1 day')) {
                $payperiodarray[$i->format("Y-m-d")] = $value["id"];
                array_push($loopdates, $i->format("Y-m-d"));
            }
        }
        //dd($payperioddates);
        $noofdays = count($loopdates);
        $employee = $request->get('employee');

        $contracthours = ($this->contractsrepository->getContractsBetweenTwoDatesByCustomerId($project, $scheduleDate))["total_hours_perweek"];

        return view('employeescheduling::partials.schedule-block-spares', compact('employee', 'contracthours', 'loopdates', 'payperiod', 'noofdays', 'payperiodarray'));
    }

    public function resetscheduleprogress(Request $request)
    {
        $customer = $request->get("customer");
        $key = Auth::user()->id . "-" . date("Y-m-d");
        EmployeeScheduleTemporaryStorage::where(["customer_id" => $customer, "scheduleid" => $key])->delete();
    }

    public function saveprecheck(Request $request)
    {
        $customer = $request->get("customer");
        $key = Auth::user()->id . "-" . date("Y-m-d");
        $successflag = $this->SchedulingRepository->saveprecheck($customer, $key);
        return $successflag;
    }

    public function saveSchedule(Request $request)
    {
        $customer = $request->get("customerid");
        $initialscheduleid = $request->get("initialscheduleid");
        /* Weekly hours calculated in weekly hours */
        $payperiodweeklyhours = $request->get("payparray");
        $supervisornotes = $request->supervisornotes;
        $variance = $request->variance;
        $scheduleindicator = $request->scheduleindicator;
        $contractual_hours = $request->contractual_hours;
        if ($scheduleindicator == "true") {
            $scheduleindicator = 1;
        } else {
            $scheduleindicator = 0;
        }
        $avghoursperweek = $request->avghoursperweek;
        DB::beginTransaction();

        $key = Auth::user()->id . "-" . date("Y-m-d");

        //        try {
        if ($initialscheduleid > 0) {

            // $updatePastScheduleAllowed = $this->SchedulingRepository->updatePastScheduleAllowed($initialscheduleid);
            $updatePastScheduleAllowed = true;
            $pendingschedule = $this->SchedulingRepository->getPendingscheduleid($initialscheduleid);
            $scheduleid = $pendingschedule->id;
            $this->SchedulingRepository->updateSchedule(
                $scheduleid,
                $customer,
                $supervisornotes,
                $variance,
                $scheduleindicator,
                $avghoursperweek,
                $contractual_hours
            );
        } else {

            $updatePastScheduleAllowed = false;
            $saveschedule = $this->SchedulingRepository->saveSchedule(
                $key,
                $customer,
                $supervisornotes,
                $variance,
                $scheduleindicator,
                $avghoursperweek,
                $contractual_hours
            );
            $scheduleid = $saveschedule;
        }

        if ($scheduleid > 0) {
            $schedules = $this->SchedulingRepository->getSchedules($key, $customer, $updatePastScheduleAllowed);
            $this->SchedulingRepository->saveSchedulelogs($scheduleid, $key, $customer, $schedules);
            $this->SchedulingRepository->saveWeeklyhours($scheduleid, $payperiodweeklyhours);
            $this->SchedulingRepository->updateVariance($scheduleid);
        }
        $content['success'] = true;
        $content['message'] = 'Saved succcessfully ';
        $content['code'] = 200;
        DB::commit();
        //        } catch (\Throwable $th) {
        //            throw($th);
        //            $content['success'] = false;
        //            $content['message'] = 'Server issue';
        //            $content['code'] = 406;
        //            DB::rollBack();
        //        }
        return json_encode($content, true);
    }

    public function schedulegeneralreport(Request $request)
    {

        $customers = $this->SchedulingRepository->getCustomerList();
        $customers = collect($customers)->sortBy('project_number')->toArray();

        $payperiods = $this->payPeriodRepository->getAllActivePayPeriodsabovedate();
        try {
            $currentpayperiod = ($payperiods[0]->id);
        } catch (\Throwable $th) {
            $currentpayperiod = 0;
        }
        return view('employeescheduling::schedule-general-reports', compact('customers', 'payperiods', 'currentpayperiod'));
    }

    public function schedulegeneralreportresults(Request $request)
    {
        $customer = $request->project;
        $payperiods = $request->payperiod;
        $reports = $this->SchedulingRepository->generalReports($customer, $payperiods);
        return view("employeescheduling::partials.reports-general", compact('reports'));
    }

    public function scheduleaudit(Request $request)
    {

        $customers = $this->SchedulingRepository->getCustomerList();
        $customers = collect($customers)->sortBy('project_number')->toArray();

        $payperiods = $this->payPeriodRepository->getAllActivePayPeriodsabovedate();
        try {
            $currentpayperiod = ($payperiods[0]->id);
        } catch (\Throwable $th) {
            $currentpayperiod = 0;
        }
        return view('employeescheduling::schedule-audit-reports', compact('customers', 'payperiods', 'currentpayperiod'));
    }

    public function scheduleauditresults(Request $request)
    {
        $customer = $request->project;
        $reports = $this->SchedulingRepository->auditReports($customer);
        return view("employeescheduling::partials.reports-audit", compact('reports'));
    }

    public function payPeriodsYearwise(Request $request)
    {
        $years = $request->selectedyear;
        $areamanager = [];
        $supervisors = [];
        $adminroles = User::select('id')
            ->role(['admin', 'super_admin'])->get()->pluck('id')->toArray();

        $supervisor = $this->userRepository->getUserList(true, $role = ['supervisor'], $supervisor_id = null, $role_except = null, $customer_session = true, true);
        $areamanagerarray = User::select('id', 'first_name', 'last_name')
            ->permission(['area_manager'])->where('active', true)->orderBy('first_name')->get();
        $supervisorarray = User::select('id', 'first_name', 'last_name')
            ->permission(['supervisor'])->where('active', true)->orderBy('first_name')->get();
        $i = 0;
        foreach ($areamanagerarray as $areamanagers) {
            $areamanagerid = $areamanagers->id;
            if (!in_array($areamanagerid, $adminroles)) {
                $areamanager[$i] = [$areamanagers->id, $areamanagers->getFullNameAttribute()];
                $i++;
            } else {
            }
        }

        $i = 0;
        foreach ($supervisorarray as $supervisor) {
            $supervisorid = $supervisor->id;
            if (!in_array($supervisorid, $adminroles)) {
                $supervisors[$i] = [$supervisor->id, $supervisor->getFullNameAttribute()];
                $i++;
            } else {
            }
        }
        $returnarray = [0 => $this->payPeriodRepository->getPayperiodsyearwise($years), 1 => $supervisors, 2 => $areamanager];
        return json_encode($returnarray, true);
    }

    public function SchedulePayperiodReport(Request $request)
    {

        $yearbegin = 2000;
        $yearend = date("Y") + 40;
        $currentyear = date("Y");
        $payperiods = $this->payPeriodRepository->getAllActivePayPeriodsabovedate();
        try {
            $currentpayperiod = ($payperiods[0]->id);
        } catch (\Throwable $th) {
            $currentpayperiod = 0;
        }
        return view('employeescheduling::schedule-payperiod-reports', compact('payperiods', 'currentpayperiod', 'currentyear', 'yearbegin', 'yearend'));
    }

    public function SchedulePayperiodResults(Request $request)
    {
        $areamanager = $request->areamanager;
        $supervisor = $request->supervisor;
        $customers = $this->SchedulingRepository->getCustomerList(true, $areamanager, $supervisor);
        $customers = collect($customers)->sortBy('project_number')->toArray();
        $payperiodsparams = $request->payperiod;
        $years = $request->year;
        if ($payperiodsparams == "") {
            $payperiods = $this->payPeriodRepository->getPayperiodsyearwise($years);
        } else {
            $payperiods = $this->payPeriodRepository->getPayperioddetailsfromarray($payperiodsparams);
        }
        return view("employeescheduling::partials.reports-payperiod", compact('customers', 'payperiods'));
    }

    public function schedulepayperiodreportstatus(Request $request)
    {

        $payperiodsparams = $request->payperiod;
        $years = $request->year;
        if ($payperiodsparams == "") {
            $payperiods = $this->payPeriodRepository->getPayperiodsyearwise($years);
        } else {
            $payperiods = $this->payPeriodRepository->getPayperioddetailsfromarray($payperiodsparams);
        }
        $this->SchedulingRepository->getScheduleStatus($payperiods);
    }

    public function removecartentry(Request $request)
    {
        $customer = $request->get('customer');
        $employeeid = $request->get('employeeid');
        $scheduledate = $request->get('scheduledate');
        $starttime = $request->get('starttime');
        $endtime = $request->get('endtime');
        $payperiod = $request->get('payperiod');
        $content = EmployeeScheduleTemporaryStorage::where([
            "scheduleid" => \Auth::user()->id . "-" . date("Y-m-d"),
            "customer_id" => $customer,
            "payperiod" => $payperiod,
            "employeeid" => $employeeid,
            "scheduledate" => $scheduledate,
        ])->first();
        $week = $content->week;
        $hours = $content->hours;
        $response = $this->SchedulingRepository->removecartentry($customer, $employeeid, $scheduledate, $starttime, $endtime, $payperiod);
        $returnarray = ["week" => $week, "payperiod" => $payperiod, "hours" => $hours, "response" => $response];
        return json_encode($returnarray, true);
    }

    public function precheck(Request $request)
    {

        $customer = $request->get('projectid');
        $payperiod = $request->get('payperiod');
        $initialrequirementid = $request->get('initialrequirementid');
        $employeeallocationcount = $this->CustomerEmployeeAllocationRepository->allocationList($customer)->count();
        $schedulescheck = $this->SchedulingRepository->checkScheduleexist($customer, $payperiod);
        $key = \Auth::user()->id . "-" . date("Y-m-d");
        if ($initialrequirementid < 1) {
            $removalCount = $this->SchedulingRepository->removeTempLogStorageByParams($customer, $payperiod);
        }
        if ($employeeallocationcount < 1) {
            $content['success'] = false;
            $content['message'] = 'No Employee allocated to the project';
            $content['code'] = 406;
        } else if ($schedulescheck > 0) {
            $content['success'] = false;
            $content['message'] = 'Schedule request already exist';
            $content['code'] = 406;
        } else {

            $content['success'] = true;
            $content['message'] = 'Success';
            $content['code'] = 200;
        }
        return json_encode($content, true);
    }

    /**
     * Set schedule data.
     * @return Response
     */
    public function setScheduledata(Request $request)
    {
        $key = \Auth::user()->id . "-" . date("Y-m-d");
        $customer = $request->get('customer');
        $employeeid = $request->get('employeeid');
        $scheduledate = $request->get('scheduledate');
        $starttime = $request->get('starttime');
        $endtime = $request->get('endtime');
        $payperiod = $request->get('payperiod');
        $schedules = $request->get('schedules');
        $editflag = $request->get('editflag');
        $weekdata = $this->SchedulingRepository->getWhichweek($scheduledate);

        $scheduledates = $this->SchedulingRepository->processDates($scheduledate, $starttime, $endtime);
        $overlapping = $this->SchedulingRepository->checkOverlappingschedules($key, $customer, $employeeid, $scheduledate, $payperiod, $scheduledates[0], $scheduledates[1]);
        $week = $weekdata;
        $hours = $scheduledates[2];

        if ($overlapping["code"] == "200") {
            $insertdata = $this->SchedulingRepository->processCart($key, $customer, $employeeid, $week, $hours, $scheduledate, $payperiod, $scheduledates[0], $scheduledates[1]);
            if ($insertdata) {
                $weeklyhours = $this->SchedulingRepository->getWeeklycalculation($key, $customer, $week, $payperiod);
                $weekhours = str_replace(":", ".", $weeklyhours[0]);
                $totalworkhours = $weeklyhours[1];
                $floattoweekhours = str_replace(".", ":", $weekhours);
                $content['success'] = true;
                $content['message'] = 'Schedule added';
                $content['code'] = 200;
                $content['extras'] = [
                    "hours" => $scheduledates[2],
                    "weekhours" => $weekhours,
                    "week" => $week,
                    "payperiod" => $payperiod,
                    "floattoweekhours" => $floattoweekhours,
                    "tothours" => $totalworkhours,
                ];
            } else {
                $content['success'] = false;
                $content['message'] = 'There is some server issue ';
                $content['code'] = 406;
                $content['extras'] = [];
            }
        } else {
            $content['success'] = false;
            $content['message'] = $overlapping["message"];
            $content['code'] = 406;
            $content['extras'] = [];
        }
        return json_encode($content, true);
    }

    /*
     * show schedule approval page
     * @return \Illuminate\Http\Response
     */

    public function scheduleApprovalPage()
    {
        $haveDeletePermission = false;
        if (Auth::user()->can('employee_schedule_requests_delete')) {
            $haveDeletePermission = true;
        }

        $regionalManager = User::whereHas("roles", function ($q) {
            return $q->where("name", "area_manager")->whereNotIn("name", ["admin", "super_admin"]);
        })->orderBy("first_name", "asc")->get();
        $payperiods = $this->payPeriodRepository->getRecentPeriods(EmployeeschedulingController::PAYPERIOD_PAST, EmployeeschedulingController::PAYPERIOD_FUTURE);
        $lastFewPayperiods = $payperiods->where("start_date", '<=', Carbon::now()->addDays(365))->pluck("id")->toArray();
        return view('employeescheduling::schedule-approval-page', compact(
            'payperiods',
            'haveDeletePermission',
            'lastFewPayperiods',
            'regionalManager'
        ));
    }

    /*
     * fetch schedule by status and payperiods
     * @param  DocumentRequest $request
     * @return datatable object
     */

    public function getScheduleByStatus(Request $request)
    {
        $status = $request->get('status');
        $payperiods = $request->get('payperiods');
        $payperioIdArray = isset($payperiods) ? $payperiods : [];

        //find customers by permission
        $allocatedCustomersArray = [];
        if (Auth::user()->can('view_all_employee_schedule_requests')) {
            $allocatedCustomersArray = $this->customerrepository->getAllCustomers();
        } else if (Auth::user()->can('view_allocated_employee_schedule_requests')) {
            $allocatedCustomersArray = $this->SchedulingRepository->getAllocatedCustomerIds();
        }
        return datatables()->of($this->SchedulingRepository->getSchedulesByStatus($status, $payperioIdArray, $allocatedCustomersArray))->addIndexColumn()->toJson();
    }

    /*
     * show schedule approval detailed grid view
     * @param  DocumentRequest $request
     * @return \Illuminate\Http\Response
     */

    public function scheduleApprovalGridView(Request $request)
    {
        $scheduleId = $request->request->get('id');
        $this->SchedulingRepository->updateVariance($scheduleId);
        $scheduleFound = false;
        $enableApproveReject = false;
        $rejectApprovedSchedules = false;
        $customers = [];
        $currentPayperiodId = '';
        $payperiods = [];
        $scheduleObj = '';
        $scheduleLastStatusNotes = '';
        if (!empty($scheduleId)) {
            $payperiods = $this->SchedulingRepository->getPayperiodsByScheduleId($scheduleId);
            $scheduleObj = $this->SchedulingRepository->getScheduleById($scheduleId);

            if (!empty($scheduleObj)) {
                $parentSchedule = $this->SchedulingRepository->getParentScheduleRequest($scheduleObj);
                if (!empty($parentSchedule)) {
                    $scheduleLastStatusNotes = $parentSchedule->status_notes;
                }
            }

            if (!empty($scheduleObj) && !empty($scheduleObj->customer)) {
                $customers[$scheduleObj->customer->id] = $scheduleObj->customer->client_name . ' (' . $scheduleObj->customer->project_number . ')';
            }

            $scheduleFound = true;
            if (isset($scheduleObj->status) && $scheduleObj->status == 0) {
                $enableApproveReject = true;
            }

            if ((Auth::user()->can('reject_approved_employee_schedule_requests')) && (isset($scheduleObj->status) && $scheduleObj->status == 1)) {
                $rejectApprovedSchedules = true;
            }
        } else {
            $currentPayperiod = $this->payPeriodRepository->getCurrentPayperiod();
            if (!empty($currentPayperiod)) {
                $currentPayperiodId = $currentPayperiod->id;
            }

            //find customers by permission
            $customers = [];
            if (Auth::user()->can('view_all_employee_schedule_requests')) {
                $customers = $this->customerrepository->getProjectsDropdownList('all');
            } else if (Auth::user()->can('view_allocated_employee_schedule_requests')) {
                $customers = $this->customerrepository->getProjectsDropdownList('allocated');
            }
        }
        return view(
            'employeescheduling::partials.employee-schedule-view',
            compact(
                'payperiods',
                'scheduleFound',
                'customers',
                'scheduleId',
                'enableApproveReject',
                'currentPayperiodId',
                'scheduleObj',
                'scheduleLastStatusNotes',
                'rejectApprovedSchedules'
            )
        );
    }

    /*
     * show schedule detailes by schedule_id
     * @param  DocumentRequest $request
     * @return json
     */

    public function getSheduleDetails(Request $request)
    {
        //input parameters
        $scheduleId = $request->get('schedule_id');
        $payperiodIds = $request->get('payperiod_id');

        $noSearchValueMissMatch = true;
        if (empty($scheduleId)) {
            $customerIds = session()->get('customer_ids');

            if ((!empty($customerIds)) || (!empty($request->get('customer_id')))) {
                $arrayDifference1 = array_diff($request->get('customer_id'), $customerIds);
                $arrayDifference2 = array_diff($customerIds, $request->get('customer_id'));
                if (!empty($arrayDifference1) || !empty($arrayDifference2)) {
                    $noSearchValueMissMatch = false;
                }
            }
        } else {
            $customerIds = $request->get('customer_id');
        }

        //        try {
        $allocatedCustomersArray = [];
        if (Auth::user()->can('view_all_employee_schedule_requests')) {
            $allocatedCustomersArray = $this->customerrepository->getAllCustomers();
        } else if (Auth::user()->can('view_allocated_employee_schedule_requests')) {
            $allocatedCustomersArray = $this->SchedulingRepository->getAllocatedCustomerIds();
        } else {
            return response()->json([
                'success' => false,
                'header' => '',
                'body' => '',
                'scheduleApprovalButton' => false,
                'noSearchValueMissMatch' => $noSearchValueMissMatch,
            ]);
        }

        if (empty($allocatedCustomersArray)) {
            return response()->json([
                'success' => false,
                'header' => '',
                'body' => '',
                'scheduleApprovalButton' => false,
                'noSearchValueMissMatch' => $noSearchValueMissMatch,
            ]);
        }

        $scheduleObj = null;
        if (!empty($scheduleId)) {
            $scheduleObj = $this->SchedulingRepository->getScheduleById($scheduleId);
            if (empty($scheduleObj)) {
                return response()->json([
                    'success' => false,
                    'header' => '',
                    'body' => '',
                    'scheduleApprovalButton' => false,
                    'noSearchValueMissMatch' => $noSearchValueMissMatch,
                ]);
            }

            //check for approval privilage -start
            $scheduleApprovalButton = true;
            $allocatedCustomersArrayForApproval = [];
            if (Auth::user()->can('approve_all_employee_schedule_requests')) {
                $allocatedCustomersArrayForApproval = $this->customerrepository->getAllCustomers();
            } else if (Auth::user()->can('approve_allocated_employee_schedule_requests')) {
                $allocatedCustomersArrayForApproval = $this->SchedulingRepository->getAllocatedCustomerIds();
            }
            if (!in_array($scheduleObj->customer_id, $allocatedCustomersArrayForApproval)) {
                $scheduleApprovalButton = false;
            }
        } else {
            $scheduleApprovalButton = false;
        }
        //check for approval privilage -end

        if (!empty($customerIds) && !empty($allocatedCustomersArray)) {
            $allocatedCustomersArray = array_intersect($customerIds, $allocatedCustomersArray);
        }
        $customerIds = $allocatedCustomersArray;
        $records = $this->SchedulingRepository->getScheduleByParams($scheduleId, $customerIds, $payperiodIds);
        $schedules = $records['schedules'];
        $tableHeaderRow = $records['headerData'];

        $employeehtml = View::make('employeescheduling::partials.employee-schedule-table-body-employee-view')
            ->with(compact(['schedules', 'scheduleId']))->render();

        //bind to blade view
        $headerHtml = View::make('employeescheduling::partials.employee-schedule-table-header-row-view')
            ->with(compact(['tableHeaderRow', 'scheduleId']))->render();

        $bodyHtml = View::make('employeescheduling::partials.employee-schedule-table-body-row-view')
            ->with(compact(['schedules', 'scheduleId']))->render();

        $summaryView = View::make('employeescheduling::partials.schedule-summary')
            ->with(compact(['scheduleObj', 'scheduleId']))->render();

        //free garbage collection
        unset($userIdArray);
        unset($schedules);
        unset($payperioddates);
        unset($detail);
        unset($scheduleTimeLogDetails);
        unset($payperiodIds);

        return response()->json([
            'success' => true,
            'header' => $headerHtml,
            'body' => $bodyHtml,
            'employee' => $employeehtml,
            'scheduleApprovalButton' => $scheduleApprovalButton,
            'summary' => $summaryView,
            'noSearchValueMissMatch' => $noSearchValueMissMatch,
        ]);
        //        } catch (\Throwable $th) {
        //            return response()->json([
        //                        'success' => false,
        //                        'header' => '',
        //                        'body' => '',
        //                        'scheduleApprovalButton' => $scheduleApprovalButton
        //            ]);
        //        }
    }

    /*
     * fetch recent payperiods
     * @return json
     */

    public function getPayperiodByLastAndPast()
    {
        $currentPayperiodId = '';
        $currentPayperiod = $this->payPeriodRepository->getCurrentPayperiod();
        if (!empty($currentPayperiod)) {
            $currentPayperiodId = $currentPayperiod->id;
        }
        $data = $this->payPeriodRepository->getRecentPeriods(EmployeeschedulingController::PAYPERIOD_PAST, EmployeeschedulingController::PAYPERIOD_FUTURE);

        return response()->json([
            'success' => true,
            'data' => $data,
            'currentPayperiodId' => $currentPayperiodId,
        ]);
    }

    /*
     * approve schedule
     * @param  DocumentRequest $request
     * @return json
     */

    public function approveSchedule(Request $request)
    {
        $scheduleId = $request->get('schedule_id');
        $statusNote = $request->get('status_note');

        if (empty($statusNote)) {
            return response()->json([
                'success' => false,
                'msg' => 'Empty note',
            ]);
        }

        if (empty($scheduleId)) {
            return response()->json([
                'success' => false,
                'msg' => 'Invalid schedule',
            ]);
        }

        $scheduleObj = $this->SchedulingRepository->getScheduleById($scheduleId);
        if (empty($scheduleObj)) {
            return response()->json([
                'success' => false,
                'msg' => 'Invalid schedule',
            ]);
        } elseif ($scheduleObj->status != 0) {
            return response()->json([
                'success' => false,
                'msg' => 'Schedule not in pending status to approve',
            ]);
        }

        $allocatedCustomersArray = [];
        if (Auth::user()->can('approve_all_employee_schedule_requests')) {
            $allocatedCustomersArray = $this->customerrepository->getAllCustomers();
        } else if (Auth::user()->can('approve_allocated_employee_schedule_requests')) {
            $allocatedCustomersArray = $this->SchedulingRepository->getAllocatedCustomerIds();
        }

        if (!in_array($scheduleObj->customer_id, $allocatedCustomersArray)) {
            return response()->json([
                'success' => false,
                'msg' => 'Not enough permissions',
            ]);
        }

        try {
            DB::beginTransaction();
            $status = $this->SchedulingRepository->approveScheduleById($scheduleId, $statusNote);
            DB::commit();

            if ($status) {
                $isAdmin = Auth::user()->can('reject_approved_employee_schedule_requests') ? true : false;
                return response()->json(['success' => true, 'msg' => 'Schedule has been approved successfully', 'reject_approved_schedules' => $isAdmin]);
            }
            return response()->json(['success' => false, 'msg' => 'Failed to approve schedule']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'msg' => $e->getMessage()]);
        }
    }

    /*
     * reject schedule
     * @param  DocumentRequest $request
     * @return json
     */

    public function rejectSchedule(Request $request)
    {
        $scheduleId = $request->get('schedule_id');
        $statusNote = $request->get('status_note');

        if (empty($statusNote)) {
            return response()->json([
                'success' => false,
                'msg' => 'Empty note',
            ]);
        }

        if (empty($scheduleId)) {
            return response()->json([
                'success' => false,
                'msg' => 'Invalid schedule',
            ]);
        }

        $scheduleObj = $this->SchedulingRepository->getScheduleById($scheduleId);
        if (empty($scheduleObj)) {
            return response()->json([
                'success' => false,
                'msg' => 'Invalid schedule',
            ]);
        } elseif (($scheduleObj->status != 0) && (!Auth::user()->can('reject_approved_employee_schedule_requests'))) {
            return response()->json([
                'success' => false,
                'msg' => 'Schedule not in pending status to reject',
            ]);
        }

        $allocatedCustomersArray = [];
        if (Auth::user()->can('approve_all_employee_schedule_requests')) {
            $allocatedCustomersArray = $this->customerrepository->getAllCustomers();
        } else if (Auth::user()->can('approve_allocated_employee_schedule_requests')) {
            $allocatedCustomersArray = $this->SchedulingRepository->getAllocatedCustomerIds();
        }

        if (!in_array($scheduleObj->customer_id, $allocatedCustomersArray)) {
            return response()->json([
                'success' => false,
                'msg' => 'Not enough permissions',
            ]);
        }

        if ($scheduleObj->status == 1) {
            $statusNote .= ' (Rejection after approval, approval time status note :' . $scheduleObj->status_notes . ')';
        }

        try {
            DB::beginTransaction();
            $status = $this->SchedulingRepository->rejectScheduleById($scheduleId, $statusNote);
            DB::commit();

            if ($status) {
                return response()->json(['success' => true, 'msg' => 'Schedule has been rejected successfully', 'status_note' => $statusNote]);
            }
            return response()->json(['success' => false, 'msg' => 'Failed to reject schedule']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'msg' => $e->getMessage()]);
        }
    }

    /*
     * re-schedule rejected entries
     * @param  DocumentRequest $request
     * @return json
     */

    public function reScheduleRejectedSchedule(Request $request)
    {
        $scheduleId = $request->get('schedule_id');

        if (empty($scheduleId)) {
            return response()->json([
                'success' => false,
                'msg' => 'Invalid schedule',
            ]);
        }

        $scheduleObj = $this->SchedulingRepository->getScheduleById($scheduleId);
        if (empty($scheduleObj)) {
            return response()->json([
                'success' => false,
                'msg' => 'Invalid schedule',
            ]);
        } elseif ($scheduleObj->status != 2) {
            return response()->json([
                'success' => false,
                'msg' => 'Schedule not in reject status to re-schedule',
            ]);
        }

        try {
            DB::beginTransaction();
            $status = $this->SchedulingRepository->reScheduleById($scheduleId);
            DB::commit();

            if ($status == 0) {
                return response()->json(['success' => true, 'msg' => 'Schedule has been re-scheduled successfully', 'rejected_id' => $scheduleObj->id]);
            } elseif ($status == 1) {
                $rescheduleObject = $this->SchedulingRepository->checkReScheduleExistence($scheduleObj);
                return response()->json(['success' => true, 'msg' => 'Already re-scheduled', 'rejected_id' => $rescheduleObject->initial_schedule_id]);
            } elseif ($status == 2) {
                return response()->json(['success' => false, 'msg' => 'Invalid schedule', 'rejected_id' => '']);
            } else {
                return response()->json(['success' => false, 'msg' => 'Failed to re-schedule', 'rejected_id' => '']);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'msg' => $e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     * @return Json
     */
    public function deleteSchedule(Request $request)
    {
        $scheduleId = $request->get('schedule_id');

        if (empty($scheduleId)) {
            return response()->json(['success' => false, 'msg' => 'Invalid schedule']);
        }

        $scheduleObj = $this->SchedulingRepository->getScheduleById($scheduleId);
        if (empty($scheduleObj)) {
            return response()->json(['success' => false, 'msg' => 'Invalid schedule']);
        }

        try {
            $result = ['success' => false, 'msg' => 'Failed to delete schedule'];

            if (!Auth::user()->can('employee_schedule_requests_delete')) {
                return response()->json(['success' => false, 'msg' => 'Access denied, You may not have the appropriate permissions to delete']);
            }

            DB::beginTransaction();
            $delete = $this->SchedulingRepository->deleteSchedule($scheduleId);
            DB::commit();

            if ($delete) {
                $result = ['success' => $delete, 'msg' => 'Schedule has been deleted successfully'];
            }

            return response()->json($result);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'msg' => $e->getMessage()]);
        }
    }

    public function getCompliancewidgetdata(Request $request)
    {
        $startdate = $request->startdate;
        return $this->landingWidgetRepository->getScheduletimesheetcomparison($request);
    }

    /*
     *populate non-compliance report page with filter values
     *@return View
     */
    public function scheduleNonComplianceReport()
    {
        $scheduleNonComplianceTypes = config('globals.schedule_non_compliance_type');
        $payperiods = $this->payPeriodRepository->getRecentPeriods(EmployeeschedulingController::PAYPERIOD_PAST, EmployeeschedulingController::PAYPERIOD_FUTURE);
        return view('employeescheduling::schedule-non-compliance-report', compact('payperiods', 'scheduleNonComplianceTypes'));
    }

    /*
     *populate non-compliance report based on filter parameters
     *@param  Request $request
     *@return json
     */
    public function scheduleNonComplianceReportApplyFilter(Request $request)
    {
        //parameters
        $payperiodIds = $request->pay_period_id;
        $customerIds = $request->customer_id;
        $employeeIds = $request->employee_id;
        $regionalManagerIds = $request->manager_id;
        $type = $request->type;
        $start = $request->start;
        $limit = $request->limit;

        //fetch payperiod by start,end date
        $startDate = null;
        $endDate = null;
        if (isset($request->start_date) && isset($request->end_date)) {
            $startDate = Carbon::createFromFormat('Y-m-d H:i:s', Carbon::parse($request->start_date)->format('Y-m-d') . ' 00:00:00');
            $endDate = Carbon::createFromFormat('Y-m-d H:i:s', Carbon::parse($request->end_date)->format('Y-m-d') . ' 23:59:59');
        }

        //fetch prepared data by filter
        $scheduleData = $this->SchedulingRepository->fetchScheduleComplianceByPayperiods($start, $limit, $startDate, $endDate, $payperiodIds, $customerIds, $employeeIds, $regionalManagerIds, $type);
        return response()->json([
            'success' => true,
            'new_start' => $scheduleData['start'],
            'total_rows' => $scheduleData['totalRows'],
            'data' => $scheduleData['records'],
        ]);
    }

    /*
     *populate area manager select box values by customer array
     *@param  Request $request
     *@return json
     */
    public function getRegionalManagersByCustomer(Request $request)
    {
        return response()->json([
            'success' => true,
            'data' => $this->SchedulingRepository->getRegionalManagersByCustomer($request->get('customer_id')),
        ]);
    }

    /*
     *populate customer select box values by area manager array
     *@param  Request $request
     *@return json
     */
    public function getCustomersByAreaManager(Request $request)
    {
        return response()->json([
            'success' => true,
            'data' => $this->SchedulingRepository->getCustomersByAreaManager($request->get('area_manager_id')),
        ]);
    }

    /*
     *fetch employee select box values by customer array
     *@param  Request $request
     *@return json
     */
    public function getAllocatedEmployeesByCustomer(Request $request)
    {
        $empArr = [];
        $allocList = $this->CustomerEmployeeAllocationRepository->allocationList($request->get('customer_id'));
        foreach ($allocList as $key => $empList) {
            $empArr[] = [
                'id' => $empList->id,
                'name' => $empList->full_name . ' (' . $empList->employee->employee_no . ')',
            ];
        }

        if (!empty($empArr)) {
            $users = array_column($empArr, 'name');
            array_multisort($users, SORT_ASC, $empArr);
        }

        return response()->json([
            'success' => true,
            'data' => $empArr,
        ]);
    }
}
