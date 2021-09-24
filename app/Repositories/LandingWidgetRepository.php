<?php

namespace App\Repositories;

use Auth;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;
use Modules\Admin\Models\PayPeriod;
use Modules\Admin\Models\SiteSettings;
use Modules\Admin\Models\TemplateSettingRules;
use Modules\Admin\Models\TrainingSettings;
use Modules\Admin\Models\User;
use Modules\Admin\Repositories\CustomerEmployeeAllocationRepository;
use Modules\Admin\Repositories\PayPeriodRepository;
use Modules\Admin\Repositories\ShiftModuleRepository;
use Modules\Admin\Repositories\UserRepository;
use Modules\Contracts\Repositories\ContractsRepository;
use Modules\Employeescheduling\Models\EmployeeSchedule;
use Modules\Employeescheduling\Repositories\SchedulingRepository;
use Modules\LearningAndTraining\Models\TrainingCourse;
use Modules\Sensors\Repositories\SensorTriggerRepository;
use Modules\Supervisorpanel\Models\CustomerPayperiodTemplate;
use Modules\Supervisorpanel\Repositories\CustomerMapRepository;
use Modules\Supervisorpanel\Repositories\CustomerReportRepository;
use Modules\Supervisorpanel\Repositories\IncidentReportRepository;
use Modules\Timetracker\Models\EmployeeShift;
use Modules\Timetracker\Models\EmployeeShiftPayperiod;

class LandingWidgetRepository
{

    protected $customerEmployeeAllocationRepository, $userRepository, $employeeShiftPayperiod, $EmployeeShift, $customerMapRepository, $sensorTriggerRepository;
    protected $customerReportRepository, $payPeriodRepository, $templateSettingRule, $schedulingRepository, $contractsRepository, $incidentReportRepository, $shiftModuleRepository;

    public function __construct(
        CustomerEmployeeAllocationRepository $customerEmployeeAllocationRepository,
        UserRepository $userRepository,
        EmployeeShiftPayperiod $employeeShiftPayperiod,
        EmployeeShift $employeeShift,
        CustomerReportRepository $customerReportRepository,
        PayPeriodRepository $payPeriodRepository,
        ContractsRepository $contractsRepository,
        SchedulingRepository $schedulingRepository,
        IncidentReportRepository $incidentReportRepository,
        CustomerMapRepository $customerMapRepository,
        ShiftModuleRepository $shiftModuleRepository,
        SensorTriggerRepository $sensorTriggerRepository
    ) {
        $this->customerEmployeeAllocationRepository = $customerEmployeeAllocationRepository;
        $this->userRepository = $userRepository;
        $this->employeeShiftPayperiod = $employeeShiftPayperiod;
        $this->employeeShift = $employeeShift;
        $this->customerReportRepository = $customerReportRepository;
        $this->payPeriodRepository = $payPeriodRepository;
        $this->contractsRepository = $contractsRepository;
        $this->schedulingRepository = $schedulingRepository;
        $this->incidentReportRepository = $incidentReportRepository;
        $this->customerMapRepository = $customerMapRepository;
        $this->shiftModuleRepository = $shiftModuleRepository;
        $this->sensorTriggerRepository = $sensorTriggerRepository;
    }

    public function getScheduletimesheetcomparison($request)
    {
        $customer = $request->get('customer-id');
        if ($request->has("startdate")) {
            $startday = $request->startdate;
        } else {
            $startday = date('Y-m-d', strtotime("-14 days"));
        }

        $endday = date('Y-m-d', strtotime("+1 days"));
        $maximumshifttolerance = (SiteSettings::find(1)->shift_start_time_tolerance) + 1;
        $maximumShiftEndTolerance = (SiteSettings::find(1)->shift_end_time_tolerance);
        $customerschedules = EmployeeSchedule::where("customer_id", $customer)
            ->with(["scheduleTimeLogs" => function ($q) use ($customer, $startday, $endday) {

                return $q->whereBetween('start_datetime', [$startday, $endday])->with(["payperiod" => function ($qry) use ($customer) {
                    return $qry
                        ->with(["employeeshiftpayperiods" => function ($q)
                        use ($customer) {
                            return $q->where("customer_id", $customer)
                                ->with(["shifts" => function ($qry) {
                                    //return $qry->whereDate("start", '>=', "scheduleTimeLogs.schedule_date");
                                    //return $qry->whereraw("start>=employee_schedule_time_logs.schedule_date");
                                }]);
                        }]);
                }])->with(["user" => function ($query) {
                    return $query->orderBy("first_name", "asc");
                }]);
            }])
            ->whereHas("scheduleTimeLogs", function ($q) use ($startday, $endday) {
                $q->whereBetween('start_datetime', [$startday, $endday]);
            })
            ->where(["status" => 1])->get()->sortBy("scheduleTimeLogs.user.first_name");

        $shiftcollection = EmployeeShiftPayperiod::with(["shifts" => function ($qry) {
        }])->where("customer_id", $customer)
            ->whereHas("shifts", function ($q) use ($startday, $endday) {
                return $q->whereBetween('start', [$startday, $endday]);
            })->get();
        $filterarray = [];
        $i = 0;
        foreach ($shiftcollection as $scollection) {
            foreach ($scollection->shifts as $sshifts) {
                $filterarray[$i]["employee_shift_payperiod_id"] = $scollection->id;
                $filterarray[$i]["pay_period_id"] = $scollection->pay_period_id;
                $filterarray[$i]["payperiod_week"] = $scollection->payperiod_week;
                $filterarray[$i]["employee_id"] = $scollection->employee_id;
                $filterarray[$i]["customer_id"] = $scollection->customer_id;
                $filterarray[$i]["total_regular_hours"] = $scollection->total_regular_hours;

                $filterarray[$i]["start"] = $sshifts->start;
                $filterarray[$i]["submitted"] = $sshifts->submitted;
                $filterarray[$i]["end"] = $sshifts->end;
                $filterarray[$i]["work_hours"] = $sshifts->work_hours;
                $filterarray[$i]["sdate"] = date("Y-m-d", strtotime($sshifts->start));
                $filterarray[$i]["total_regular_hours"] = $sshifts->total_regular_hours;
                $i++;
            }
        }
        //dd($filterarray);
        $filterarray = collect($filterarray);

        $employeedata = [];
        foreach ($customerschedules as $timelog) {
            $schdcustomer = $timelog->customer_id;

            foreach ($timelog->scheduleTimeLogs as $schlogs) {
                $employee = $schlogs->user_id;

                $employeename = $schlogs->user->getFullNameAttribute();
                $payperiod_id = $schlogs->payperiod_id;
                $schedule_date = $schlogs->schedule_date;
                $start_datetime = $schlogs->start_datetime;
                $end_datetime = $schlogs->end_datetime;
                $customername = $schlogs->schedule->customer->client_name;

                $grace_start_datetime = date('Y-m-d H:i:s', strtotime($schlogs->start_datetime . '-6 hour'));
                $grace_end_datetime = date('Y-m-d H:i:s', strtotime($schlogs->end_datetime . '+6 hour'));
                $toleranceEndTime = strtotime($end_datetime) - ($maximumShiftEndTolerance * 60);

                $afterTolerance = date('Y-m-d H:i:s', $toleranceEndTime);
                if (date("Y-m-d", strtotime($end_datetime)) == "2021-04-06") {
                    // dd($end_datetime, $maximumShiftEndTolerance, $afterTolerance);
                }



                $diff = \Carbon::parse($start_datetime)
                    ->diffInMinutes(\Carbon::parse($end_datetime));
                $diffUnderTolerance = \Carbon::parse($start_datetime)
                    ->diffInMinutes(\Carbon::parse($afterTolerance));

                $expectedworkhours = (gmdate("H:i", $diff * 60));
                $expectedWorkHoursTolerance = (gmdate("H:i", $diffUnderTolerance * 60));

                // $filtershift = $filterarray->where("employee_id", $employee)
                //     ->where("start", '>=', $grace_start_datetime)->where("end", "<=", $grace_end_datetime)->sortBy("start");

                $filtershift = $filterarray->where("employee_id", $employee)->filter(function ($item) use ($grace_start_datetime, $grace_end_datetime) {
                    return (data_get($item, 'start') >= $grace_start_datetime && data_get($item, 'start') <= $grace_end_datetime);
                })->sortBy("start");
                $employeedata["client-" . $schdcustomer]["client_id"] = $schdcustomer;
                $employeedata["client-" . $schdcustomer]["client_name"] = $customername;
                if (count($filtershift) > 0) {
                    // if ($schedule_date == "2021-03-25") {
                    //     //dump($employeedata["client-198"]["Peter Wrobel-274"]);
                    //     dump($employee, $filtershift, $grace_start_datetime, $grace_end_datetime);
                    //     dd("yes data");
                    // }
                    $employeeShiftHours = 0;
                    foreach ($filtershift as $shiftdata) {
                        // $employeeShiftHours = $employeeShiftHours + strtotime($shiftdata["work_hours"]);
                        $minuteArray = explode(":", $shiftdata["work_hours"]);
                        $employeeShiftHours = $employeeShiftHours + ($minuteArray[0] * 60 + $minuteArray[1]);
                        $representionHours = gmdate('H:i', $employeeShiftHours * 60);
                        if (!isset($employeedata["client-" . $schdcustomer][$employeename . "-" . $employee][$schedule_date]["starttime"])) {
                            $employeedata["client-" . $schdcustomer][$employeename . "-" . $employee]["name"] = $employeename;
                            $employeedata["client-" . $schdcustomer][$employeename . "-" . $employee][$schedule_date]["scheduled_starttime"] =
                                $start_datetime;
                            $employeedata["client-" . $schdcustomer][$employeename . "-" . $employee][$schedule_date]["converted_scheduled_starttime"] =
                                date('h:i A', strtotime($start_datetime));
                            $employeedata["client-" . $schdcustomer][$employeename . "-" . $employee][$schedule_date]["starttime"] =
                                $shiftdata["start"];
                            $employeedata["client-" . $schdcustomer][$employeename . "-" . $employee][$schedule_date]["converted_actual_starttime"] =
                                date('h:i A', strtotime($shiftdata["start"]));
                            $employeedata["client-" . $schdcustomer][$employeename . "-" . $employee][$schedule_date]["scheduled_endtime"] =
                                $end_datetime;

                            $employeedata["client-" . $schdcustomer][$employeename . "-" . $employee][$schedule_date]["converted_scheduled_endtime"] =
                                date('h:i A', strtotime($end_datetime));
                            if ($shiftdata["submitted"] == 1 || $grace_end_datetime < date("Y-m-d H:i")) {

                                $employeedata["client-" . $schdcustomer][$employeename . "-" . $employee][$schedule_date]["endtime"] =
                                    $shiftdata["end"];
                                $employeedata["client-" . $schdcustomer][$employeename . "-" . $employee][$schedule_date]["converted_actual_endtime"] =
                                    date('h:i A', strtotime($shiftdata["end"]));
                                $employeedata["client-" . $schdcustomer][$employeename . "-" . $employee][$schedule_date]["actual_work_hours"] =
                                    $representionHours;
                            } else if ($shiftdata["start"] <= date("Y-m-d")) {
                                $employeedata["client-" . $schdcustomer][$employeename . "-" . $employee][$schedule_date]["endtime"] =
                                    $shiftdata["end"];
                                $employeedata["client-" . $schdcustomer][$employeename . "-" . $employee][$schedule_date]["converted_actual_endtime"] =
                                    date('h:i A', strtotime($shiftdata["end"]));
                                $employeedata["client-" . $schdcustomer][$employeename . "-" . $employee][$schedule_date]["actual_work_hours"] =
                                    $representionHours;
                            } else {
                                $employeedata["client-" . $schdcustomer][$employeename . "-" . $employee][$schedule_date]["endtime"] = "Not Submitted";
                                $employeedata["client-" . $schdcustomer][$employeename . "-" . $employee][$schedule_date]["converted_actual_endtime"] =
                                    "Not Submitted";
                                $employeedata["client-" . $schdcustomer][$employeename . "-" . $employee][$schedule_date]["actual_work_hours"] = "";
                            }


                            $employeedata["client-" . $schdcustomer][$employeename . "-" . $employee][$schedule_date]["expectedworkhours"] =
                                $expectedworkhours;
                            $employeedata["client-" . $schdcustomer][$employeename . "-" . $employee][$schedule_date]["expectedworkhoursundertolerance"] =
                                $expectedWorkHoursTolerance;
                            $employeedata["client-" . $schdcustomer][$employeename . "-" . $employee][$schedule_date]["ontime"]
                                = $shiftdata["start"] <= $schlogs->start_datetime ? 1 : 0;
                            $time = new \DateTime($schlogs->start_datetime);
                            $time->add(new \DateInterval('PT' . $maximumshifttolerance . 'M'));
                            $stamp = $time->format('Y-m-d H:i');

                            if ($shiftdata["start"] <= $schlogs->start_datetime) {
                                $employeedata["client-" . $schdcustomer][$employeename . "-" . $employee][$schedule_date]["ontimecolor"]
                                    = "green";
                            } else if ($shiftdata["start"] <= $stamp) {
                                $employeedata["client-" . $schdcustomer][$employeename . "-" . $employee][$schedule_date]["ontimecolor"]
                                    = "yellow";
                            } else {
                                $employeedata["client-" . $schdcustomer][$employeename . "-" . $employee][$schedule_date]["ontimecolor"]
                                    = "red";
                            }
                        }
                        if (isset($employeedata["client-" . $schdcustomer][$employeename . "-" . $employee][$schedule_date]["work_hours"])) {
                            $secs
                                = strtotime($shiftdata["work_hours"]) - strtotime("00:00:00");
                            $result
                                = date("H:i:s", strtotime($employeedata["client-" . $schdcustomer][$employeename . "-" . $employee][$schedule_date]["work_hours"]) + $secs);
                            $whours
                                = $employeedata["client-" . $schdcustomer][$employeename . "-" . $employee][$schedule_date]["work_hours"]
                                = $result;
                            if (strtotime($whours) >= strtotime($expectedworkhours)) {
                                $employeedata["client-" . $schdcustomer][$employeename . "-" . $employee][$schedule_date]["fullshift"]
                                    = 1;
                            } else if (strtotime($shiftdata["work_hours"]) >= strtotime($expectedWorkHoursTolerance)) {
                                $employeedata["client-" . $schdcustomer][$employeename . "-" . $employee][$schedule_date]["fullshift"]
                                    = 2;
                            } else {
                                $employeedata["client-" . $schdcustomer][$employeename . "-" . $employee][$schedule_date]["fullshift"]
                                    = 0;
                            }
                            if ($employee == 956) {
                                // dump($shiftdata, $grace_end_datetime);
                            }
                            if ($shiftdata["submitted"] == 1 || $grace_end_datetime < date("Y-m-d H:i")) {

                                $employeedata["client-" . $schdcustomer][$employeename . "-" . $employee][$schedule_date]["endtime"] =
                                    $shiftdata["end"];
                                $employeedata["client-" . $schdcustomer][$employeename . "-" . $employee][$schedule_date]["converted_actual_endtime"] =
                                    date('h:i A', strtotime($shiftdata["end"]));
                                $employeedata["client-" . $schdcustomer][$employeename . "-" . $employee][$schedule_date]["actual_work_hours"] =
                                    $representionHours;
                            } else if ($shiftdata["start"] <= date("Y-m-d")) {
                                $employeedata["client-" . $schdcustomer][$employeename . "-" . $employee][$schedule_date]["endtime"] =
                                    $shiftdata["end"];
                                $employeedata["client-" . $schdcustomer][$employeename . "-" . $employee][$schedule_date]["converted_actual_endtime"] =
                                    date('h:i A', strtotime($shiftdata["end"]));
                                $employeedata["client-" . $schdcustomer][$employeename . "-" . $employee][$schedule_date]["actual_work_hours"] =
                                    $representionHours;
                            } else {
                                $employeedata["client-" . $schdcustomer][$employeename . "-" . $employee][$schedule_date]["endtime"] = "Not Submitted";
                                $employeedata["client-" . $schdcustomer][$employeename . "-" . $employee][$schedule_date]["converted_actual_endtime"] =
                                    "Not Submitted";
                                $employeedata["client-" . $schdcustomer][$employeename . "-" . $employee][$schedule_date]["actual_work_hours"] = "";
                            }
                        } else {
                            $employeedata["client-" . $schdcustomer][$employeename . "-" . $employee][$schedule_date]["work_hours"] =
                                $shiftdata["work_hours"];
                            $workhourstime = strtotime($shiftdata["work_hours"]);
                            if (strtotime($shiftdata["work_hours"]) >= strtotime($expectedworkhours)) {
                                $employeedata["client-" . $schdcustomer][$employeename . "-" . $employee][$schedule_date]["fullshift"]
                                    = 1;
                            } else if (strtotime($shiftdata["work_hours"]) >= strtotime($expectedWorkHoursTolerance)) {
                                $employeedata["client-" . $schdcustomer][$employeename . "-" . $employee][$schedule_date]["fullshift"]
                                    = 2;
                            } else {
                                $employeedata["client-" . $schdcustomer][$employeename . "-" . $employee][$schedule_date]["fullshift"]
                                    = 0;
                            }
                            if ($shiftdata["submitted"] == 1 || $grace_end_datetime < date("Y-m-d H:i")) {

                                $employeedata["client-" . $schdcustomer][$employeename . "-" . $employee][$schedule_date]["endtime"] =
                                    $shiftdata["end"];
                                $employeedata["client-" . $schdcustomer][$employeename . "-" . $employee][$schedule_date]["converted_actual_endtime"] =
                                    date('h:i A', strtotime($shiftdata["end"]));
                                $employeedata["client-" . $schdcustomer][$employeename . "-" . $employee][$schedule_date]["actual_work_hours"] =
                                    $representionHours;
                            } else if ($shiftdata["start"] <= date("Y-m-d")) {
                                $employeedata["client-" . $schdcustomer][$employeename . "-" . $employee][$schedule_date]["endtime"] =
                                    $shiftdata["end"];
                                $employeedata["client-" . $schdcustomer][$employeename . "-" . $employee][$schedule_date]["converted_actual_endtime"] =
                                    date('h:i A', strtotime($shiftdata["end"]));
                                $employeedata["client-" . $schdcustomer][$employeename . "-" . $employee][$schedule_date]["actual_work_hours"] =
                                    $representionHours;
                            } else {
                                $employeedata["client-" . $schdcustomer][$employeename . "-" . $employee][$schedule_date]["actual_work_hours"] = "";
                                $employeedata["client-" . $schdcustomer][$employeename . "-" . $employee][$schedule_date]["endtime"] = "Not Submitted";
                                $employeedata["client-" . $schdcustomer][$employeename . "-" . $employee][$schedule_date]["converted_actual_endtime"] =
                                    "Not Submitted";
                            }
                        }
                        //dump($employeeShiftHours . "-" . $employeename . $schedule_date);
                    }
                } else {
                    $employeedata["client-" . $schdcustomer][$employeename . "-" . $employee]["name"] = $employeename;
                    $employeedata["client-" . $schdcustomer][$employeename . "-" . $employee][$schedule_date]["starttime"] =
                        "Nil";
                    $employeedata["client-" . $schdcustomer][$employeename . "-" . $employee][$schedule_date]["converted_actual_starttime"] =
                        "Nil";
                    $employeedata["client-" . $schdcustomer][$employeename . "-" . $employee][$schedule_date]["endtime"] =
                        "Nil";
                    $employeedata["client-" . $schdcustomer][$employeename . "-" . $employee][$schedule_date]["converted_actual_endtime"] =
                        "Nil";
                    $employeedata["client-" . $schdcustomer][$employeename . "-" . $employee][$schedule_date]["scheduled_starttime"] =
                        "Nil";
                    $employeedata["client-" . $schdcustomer][$employeename . "-" . $employee][$schedule_date]["converted_scheduled_starttime"] =
                        "Nil";
                    $employeedata["client-" . $schdcustomer][$employeename . "-" . $employee][$schedule_date]["scheduled_endtime"] =
                        "Nil";
                    $employeedata["client-" . $schdcustomer][$employeename . "-" . $employee][$schedule_date]["converted_scheduled_endtime"] =
                        "Nil";
                    $employeedata["client-" . $schdcustomer][$employeename . "-" . $employee][$schedule_date]["expectedworkhours"] =
                        $expectedworkhours;
                    $employeedata["client-" . $schdcustomer][$employeename . "-" . $employee][$schedule_date]["expectedworkhoursundertolerance"] =
                        $expectedWorkHoursTolerance;
                    $employeedata["client-" . $schdcustomer][$employeename . "-" . $employee][$schedule_date]["ontime"] =
                        0;
                    $employeedata["client-" . $schdcustomer][$employeename . "-" . $employee][$schedule_date]["ontimecolor"] =
                        "black";
                    $employeedata["client-" . $schdcustomer][$employeename . "-" . $employee][$schedule_date]["work_hours"] =
                        "Nil";
                    $employeedata["client-" . $schdcustomer][$employeename . "-" . $employee][$schedule_date]["fullshift"] =
                        0;
                    $employeedata["client-" . $schdcustomer][$employeename . "-" . $employee][$schedule_date]["actual_work_hours"] = "";
                }
            }
        }
        $mylist = [];
        foreach ($employeedata as $key => $value) {
            $sortval = $this->ksort_recursive($value);
            $mylist[$key] = $value;
        }

        $begin = new \DateTime($startday);
        $end = new \DateTime(date("Y-m-d"));

        $interval = \DateInterval::createFromDateString('1 day');
        $period = new \DatePeriod($begin, $interval, $end);
        $datearray = [];

        for ($i = $begin; $i <= $end; $i->modify('+1 day')) {
            $datearray[] = ["date" => $i->format("Y-m-d"), "display" => $i->format("d M Y")];
        }
        $returndata[0] = [$startday, date("Y-m-d")];
        $returndata[1] = json_encode($mylist, true);
        $returndata[2] = array_reverse($datearray);
        return $returndata;
    }

    public function ksort_recursive(&$array)
    {
        ksort($array);
        foreach ($array as &$a) {
            is_array($a) && $this->ksort_recursive($a);
        }
    }

    public function getSiteSummaryHoursPerWeekDetails($customerId, $payPeriodId = null, $userId = null)
    {
        // $payPeriodId = 11;
        // $customerId = 63;
        $result = [
            'hours_per_week' => 0,
            'actual_hours' => null,
        ];
        if (empty($payPeriodId)) {
            $payPeriodObject = $this->payPeriodRepository->getCurrentPayperiod();
            $payPeriodId = $payPeriodObject->id;
        }

        if (empty($payPeriodId)) {
            return $result;
        }

        $payPeriodObject = $this->payPeriodRepository->getPayperiodById($payPeriodId);
        $payPeriodId = $payPeriodObject->id;

        if (empty($userId)) {
            $userId = Auth::user()->id;
        }

        $hpw = ($this->contractsRepository->getContractsBetweenTwoDatesByCustomerId($customerId, $payPeriodObject->start_date))["total_hours_perweek"];

        $todayDate = Carbon::today();
        $weekdata = $this->payPeriodRepository->getPayperiodWeekByDate($todayDate);
        $employeeShiftPayperiod = $this->employeeShiftPayperiod->whereActive(true)
            ->whereSubmitted(true)
            ->whereHas('shifts', function ($query) use ($payPeriodId, $customerId, $weekdata) {
                $query->when($payPeriodId, function ($q) use ($payPeriodId, $customerId, $weekdata) {
                    return $q->where('pay_period_id', $payPeriodId)
                        ->where('customer_id', $customerId)
                        ->where("payperiod_week", $weekdata)
                        ->where("approved", 1);
                });
            })->select(
                DB::raw('TIME_FORMAT(SEC_TO_TIME( SUM( TIME_TO_SEC( `approved_total_regular_hours` ) ) ), "%H:%i") as regular_hours'),
                DB::raw('TIME_FORMAT(SEC_TO_TIME( SUM( TIME_TO_SEC( `approved_total_overtime_hours` ) ) ), "%H:%i") as overtime_hours'),
                DB::raw('TIME_FORMAT(SEC_TO_TIME( SUM( TIME_TO_SEC( `approved_total_statutory_hours` ) ) ), "%H:%i") as statutory_hours')
            )
            ->get()->toArray();

        $resultArray = [];
        $actualHours = null;
        if (!empty($employeeShiftPayperiod)) {
            if (isset($employeeShiftPayperiod[0])) {
                if (!empty($employeeShiftPayperiod[0]['regular_hours'])) {
                    $resultArray[] = $employeeShiftPayperiod[0]['regular_hours'];
                }

                if (!empty($employeeShiftPayperiod[0]['overtime_hours'])) {
                    $resultArray[] = $employeeShiftPayperiod[0]['overtime_hours'];
                }

                if (!empty($employeeShiftPayperiod[0]['statutory_hours'])) {
                    $resultArray[] = $employeeShiftPayperiod[0]['statutory_hours'];
                }
            }

            foreach ($resultArray as $resultItem) {
                $actualHours += $this->explodeTime($resultItem); // this fucntion will convert all hh:mm to seconds
            }

            if ($actualHours > 0) {
                $actualHours = $this->secondToHoursMinutes($actualHours);
            }
        }

        $result = [
            'hours_per_week' => (float) ($hpw ? $hpw : 0),
            'actual_hours' => $actualHours ? $actualHours : '00:00',
        ];

        return $result;
    }

    public function getSiteDetailsByQuestionCategory($customerId, $selectedTemplateCategory = null, $payperiodLimit = 4)
    {
        $lastNpayperiodObjects = PayPeriod::orderby('end_date', 'desc')->where('start_date', '<=', today())->where('active', true)->limit($payperiodLimit)->get();
        $templateQuery = $this->customerReportRepository->getCurrentTemplateParentQuestions($selectedTemplateCategory);

        $payPeriods = null;
        $questions = null;
        $answerArray = null;
        $questionCategory = null;
        if (!empty($templateQuery)) {
            $templateId = $templateQuery->id;
            $templateFormIds = [];

            foreach ($templateQuery->templateForm as $template_form) {
                $questionCategory[$template_form->id] = $template_form->questionCategory->description;
                $templateFormIds[] = $template_form['id'];
                $questions[$template_form->questionCategory->description][$template_form->id] = $template_form->question_text;
            }

            if (empty($questions)) {
                return ['pay_periods' => [], 'answers' => []];
            }

            $payperiodIdsTempArray = [];
            foreach ($lastNpayperiodObjects as $lastNpayperiodObject) {
                $payperiodKey = 'payperiod_' . ($lastNpayperiodObject->id);
                $payperiodIdsTempArray[$payperiodKey] = 'empty';
                $payPeriods[] = Carbon::parse($lastNpayperiodObject->start_date)->format('d-M-y');
                $lastNpayperiods[] = $lastNpayperiodObject->id;
            }

            foreach ($questions as $ky => $question) {
                $answerArray[$ky] = array_fill_keys($question, $payperiodIdsTempArray);
            }

            $customerReport = CustomerPayperiodTemplate::where('template_id', $templateId)
                ->whereIn('payperiod_id', $lastNpayperiods)
                ->where('customer_id', $customerId)
                ->with(['customerReport' => function ($query) use ($templateFormIds) {
                    $query->whereIn('element_id', $templateFormIds);
                }])
                ->with(['payperiod'])
                ->orderBy('payperiod_id', 'DESC')
                ->get();

            foreach ($customerReport as $ky => $item) {
                foreach ($item->customerReport as $key => $report) {
                    $color = 'empty';
                    if ($report['score'] !== null) {
                        $color_details = TemplateSettingRules::where('min_value', '<=', $report['score'])
                            ->where('max_value', '>=', $report['score'])
                            ->with(['color'])
                            ->first();
                        $color = ($color_details->color->color_class_name) ? $color_details->color->color_class_name : $color;
                    }
                    $payperiodKey = 'payperiod_' . ($item->payperiod->id);
                    $category = $questionCategory[$report->element_id];
                    $answerArray[$category][$report->question][$payperiodKey] = $color;
                }
            }
        }

        return ['pay_periods' => $payPeriods, 'answers' => $answerArray];
    }

    /**
     * to get trend of a customer
     * @param $request
     * @return chart
     */
    public function getTrendAnalysisDetails(Request $request)
    {
        $dates = [];
        $score = [];
        $paypeioddates = $this->payPeriodRepository
            ->getLastNPayperiodWithCurrent(26);

        foreach ($paypeioddates as $key => $value) {
            $trendReport = $this->customerReportRepository
                ->customerPayperiodTrendReport(
                    $request->get('customer-id'),
                    $value->start_date,
                    $value->end_date
                );
            if (!empty($trendReport['trendchart'] && $trendReport['trendchart'][$value->short_name])) {
                array_push($dates, $value->short_name);
                array_push($score, $trendReport['trendchart'][$value->short_name]);
            }

            // array_push($dates, $value->short_name);
            // array_push($score, !empty($trendReport['trendchart'])
            //     ? $trendReport['trendchart'][$value->short_name]
            //     : '0');
        }

        return ['score' => array_reverse($score), 'dates' => array_reverse($dates)];
    }

    /**
     * to get training widgets permanent and spares with mandatory or optional course
     * @param $request, $mandatory, $spares
     * @return array with employees and course details
     */
    public function trainingWidget(Request $request, $mandatory, $spares)
    {
        $customerId = $request->get('customer-id');
        $sparesEmployeeArray = ['Spares Pool'];
        $permanentEmployeeArray = ['super_admin', 'admin', 'client', 'Spares Pool'];
        $tolerance = number_format(TrainingSettings::where('setting', 'trainingWidgetTolerenceDays')->first()->value);
        $toleranceDays = -1 * $tolerance;
        $cIds = $request->input('cIds');
        $from = $request->input('from');
        $to = $request->input('to');

        //get allocated employees
        $employeeQueryMaster = User::whereHas('allocation', function ($queryCustomer) use ($customerId, $cIds) {
            if (is_array($customerId)) {
                return $queryCustomer->whereIn('customer_id', $customerId);
            }
            if (is_array($cIds) && !empty($cIds)) {
                return $queryCustomer->whereIn('customer_id', $cIds);
            }
            return $queryCustomer->where('customer_id', $customerId);
        })
            ->when($spares, function ($query) use ($sparesEmployeeArray) {
                $query->whereHas('roles', function ($querySpareEmployee) use ($sparesEmployeeArray) {
                    return $querySpareEmployee->whereIn('name', $sparesEmployeeArray);
                });
            }, function ($query) use ($permanentEmployeeArray) {
                $query->whereHas('roles', function ($queryPermanent) use ($permanentEmployeeArray) {
                    return $queryPermanent->whereNotIn('name', $permanentEmployeeArray)
                        ->select('name');
                });
            })
            ->select('id', 'first_name', 'last_name')
            ->get();

        $employeeQuery = $employeeQueryMaster->pluck('full_name', 'id')
            ->toArray();

        $employee_name = $employeeQueryMaster->toArray();

        $employee = collect($employeeQuery);
        $employee_id = $employee->keys()->all();

        //get allocated courses
        $courseDetailsQuery = TrainingCourse::whereHas(
            'TrainingUserCourseAllocation',
            function ($q) use ($employee_id, $mandatory, $from, $to) {
                $q =  $q->whereIn('user_id', $employee_id)
                    ->where('mandatory', $mandatory)
                    ->orderBy('user_id', 'asc');
                if (!empty($from) && !empty($to)) {
                    $q->whereBetween('created_at', [Carbon::parse($from), Carbon::parse($to)]);
                }
                return $q;
            }
        ) //->whereNotNull('course_due_date')
            ->select('id', 'course_due_date', 'course_title')
            ->with(['TrainingUserCourseAllocation' => function ($q) use ($employee_id, $mandatory,$from,$to) {
                $q = $q->whereIn('user_id', $employee_id)
                    ->where('mandatory', $mandatory)
                    ->select('course_id', 'user_id', 'completed', 'completed_date')
                    ->orderBy('user_id', 'asc');
                if (!empty($from) && !empty($to)) {
                    $q->whereBetween('created_at', [Carbon::parse($from), Carbon::parse($to)]);
                }
                return $q;                   
            }])
            ->get()
            ->toArray();

        $results = [];

        foreach ($courseDetailsQuery as $key => $course) {
            $results[$key]['course'] = $course['course_title'];
            $results[$key]['course_due_date'] = isset($course['course_due_date'])
                ? Carbon::parse($course['course_due_date'])->format('d-M-y')
                : "";
            $deadline = $course['course_due_date'];

            $collection = collect($course['training_user_course_allocation']);

            foreach ($employee_id as $userKey => $id) {
                $data = $collection->where('user_id', $id);

                if ($data->isNotEmpty()) {
                    $results[$key]['allocation_data'][$userKey] = $data->collapse()->toArray();
                    $completedDate = $results[$key]['allocation_data'][$userKey]['completed_date'];
                    $results[$key]['allocation_data'][$userKey]['completed_date'] =
                        Carbon::parse($completedDate)->format('d-M-y');

                    if ($completedDate == null) {
                        $results[$key]['allocation_data'][$userKey]['completed_date'] = 'Incomplete';
                        $results[$key]['allocation_data'][$userKey]['color_code'] = '#d2d2d2';
                    } else {
                        $dateDiff = Carbon::parse($completedDate)->diffInDays($deadline, false);

                        if ($dateDiff >= 0) {
                            $results[$key]['allocation_data'][$userKey]['color_code'] = 'green';
                        } else if ($dateDiff > $toleranceDays && $dateDiff < 0) {
                            $results[$key]['allocation_data'][$userKey]['color_code'] = 'yellow';
                        } else {
                            $results[$key]['allocation_data'][$userKey]['color_code'] = 'red';
                        }
                    }
                } else {
                    $results[$key]['allocation_data'][$userKey] = [
                        'course_id' => $course['id'],
                        'user_id' => $id,
                        'completed' => 0,
                        'completed_date' => '00-MMM-00',
                        'color_code' => 'white',
                    ];
                }
            }
        }

        return ['name' => $employee_name, 'details' => $results];
    }

    public function getIncidentKpi($request)
    {
        $content = $this->incidentReportRepository->Kpi($request);
        return $content;
    }

    public function getIncidentCompliance($request)
    {
        $content = $this->incidentReportRepository->getCompliance($request);
        return $content;
    }

    public function getAllPostOrders($request)
    {
        $content = $this->shiftModuleRepository->getShiftJournalSummary($request);
        return $content;
    }

    public function getAllMotionSensors($request)
    {
        $content = $this->sensorTriggerRepository->getAllMotionSensorDetails($request);
        return $content;
    }

    public function getSiteMatrixDetails($customer_id)
    {
        $resultArr = [];
        $payperiod_start = Carbon::now()->addDay(-30)->toDateString();
        $payperiod_end = Carbon::now()->toDateString();
        $pay_periods = array();
        $current_report = null;
        $current_payperiod = $this->payPeriodRepository->getCurrentPayperiod();
        if ($current_payperiod) {
            $pay_periods[] = $current_payperiod->id;
            $current_report = $this->customerMapRepository->getPayperiodAvgReport($customer_id, $pay_periods);
        }
        $currentValue = [];
        if (!empty($current_report) && $current_report != false) {
            $currentValue = array_merge_recursive($current_report['score'], $current_report['color_class']);
        }
        if (!empty($currentValue)) {
            foreach ($currentValue as $key => $currentVal) {
                $resultArr[$key]['current_css'] = isset($currentVal[1]) ? $currentVal[1] : '';
                $resultArr[$key]['current_score'] = isset($currentVal[0]) ? $currentVal[0] : 0;
            }
        }
        $pay_periods = $this->payPeriodRepository->getPayperiodRange($payperiod_start, $payperiod_end);
        $average_report = $this->customerMapRepository->getPayperiodAvgReport($customer_id, $pay_periods);
        if (!empty($average_report)) {
            $averageValue = array_merge_recursive($average_report['score'], $average_report['color_class']);
        }
        if (!empty($averageValue)) {
            foreach ($averageValue as $key => $averageVal) {
                $resultArr[$key]['average_css'] = isset($averageVal[1]) ? $averageVal[1] : '';
                $resultArr[$key]['average_score'] = isset($averageVal[0]) ? $averageVal[0] : 0;
            }
        }

        if (!$current_report) {
            foreach ($resultArr as $key => $result) {
                $resultArr[$key]['current_css'] = $this->customerMapRepository->getDefaultColor();
                $resultArr[$key]['current_score'] = 'not_submitted_score';
            }
        }

        return $resultArr;
    }

    /*
     *convert minutes to hh:mm
     */
    private function secondToHoursMinutes($time)
    {
        $hour = floor($time / 60);
        $minute = strval(floor($time % 60));
        if ($minute == 0) {
            $minute = "00";
        } else {
            $minute = $minute;
        }
        $time = $hour . ":" . $minute;
        return $time;
    }

    /*
     *explode time and convert into minutes
     */
    private function explodeTime($time)
    {
        $time = explode(':', $time);
        $time = $time[0] * 60 + $time[1];
        return $time;
    }
}
