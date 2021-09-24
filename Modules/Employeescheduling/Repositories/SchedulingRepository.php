<?php

namespace Modules\Employeescheduling\Repositories;

use App\Repositories\AttachmentRepository;
use App\Repositories\MailQueueRepository;
use Auth;
use Carbon\Carbon;
use DateTime;
use Illuminate\Support\Facades\DB;
use Modules\Admin\Models\Customer;
use Modules\Admin\Models\CustomerEmployeeAllocation;
use Modules\Admin\Models\ScheduleSettings;
use Modules\Admin\Models\SiteSettings;
use Modules\Admin\Models\User;
use Modules\Admin\Repositories\BusinessLineRepository;
use Modules\Admin\Repositories\BusinessSegmentRepository;
use Modules\Admin\Repositories\ContractBillingCycleRepository;
use Modules\Admin\Repositories\ContractBillingRateChangeRepository;
use Modules\Admin\Repositories\ContractCellphoneProviderRepository;
use Modules\Admin\Repositories\ContractDeviceAccessRepository;
use Modules\Admin\Repositories\ContractPaymentMethodRepository;
use Modules\Admin\Repositories\CustomerEmployeeAllocationRepository;
use Modules\Admin\Repositories\CustomerRepository;
use Modules\Admin\Repositories\DivisionLookupRepository;
use Modules\Admin\Repositories\EmployeeAllocationRepository;
use Modules\Admin\Repositories\HolidayPaymentAllocationRepository;
use Modules\Admin\Repositories\HolidayRepository;
use Modules\Admin\Repositories\OfficeAddressRepository;
use Modules\Admin\Repositories\ParentCustomerRepository;
use Modules\Admin\Repositories\PayPeriodRepository;
use Modules\Admin\Repositories\PositionLookupRepository;
use Modules\Admin\Repositories\ReasonForSubmissionRepository;
use Modules\Admin\Repositories\UserRepository;
use Modules\Client\Repositories\ClientRepository;
use Modules\Contracts\Models\Cmuf;
use Modules\Contracts\Repositories\ContractsRepository;
use Modules\Employeescheduling\Models\EmployeeSchedule;
use Modules\Employeescheduling\Models\EmployeeScheduleAveragePayperiodHours;
use Modules\Employeescheduling\Models\EmployeeScheduleTemporaryStorage;
use Modules\Employeescheduling\Models\EmployeeScheduleTimeLog;
use Modules\Employeescheduling\Models\ScheduledEmployeeWorkHour;
use Modules\LearningAndTraining\Models\TrainingUserContent;
use Modules\Timetracker\Models\EmployeeShift;
use Modules\Timetracker\Models\EmployeeShiftPayperiod;

class SchedulingRepository
{

    protected $customerModel, $payPeriodRepository, $contractdeviceaccessrepository, $contractcellphoneproviderrepository, $CustomerEmployeeAllocationRepository, $cmuf, $contractbillingcyclerepository, $contractpaymentmethodrepository, $holidayrepository, $holidaypaymentallocationrepository, $positionlookuprepository, $lineofbusiness, $businesssegmentrepository, $contractbillingratechangerepository, $userrepository, $officeaddressrepository, $parentcustomerrepository, $reasonformsubmissionrepository, $DocumentcategoryModel, $DocumenttypeModel, $userModel, $attachmentRepository, $customerRepository, $model, $timeLogModel, $contractsrepository, $EmployeeScheduleTemporaryStorage, $MailQueueRepository;

    /**
     * The Model instance.
     * ''
     * @var \Illuminate\Database\Eloquent\Model
     */
    public function __construct(
        Customer $customerModel,
        ContractDeviceAccessRepository $contractdeviceaccessrepository,
        ContractCellphoneProviderRepository $contractcellphoneproviderrepository,
        PayPeriodRepository $payPeriodRepository,
        ContractBillingCycleRepository $contractbillingcyclerepository,
        ContractPaymentMethodRepository $contractpaymentmethodrepository,
        Cmuf $cmuf,
        HolidayPaymentAllocationRepository $holidaypaymentallocationrepository,
        HolidayRepository $holidayrepository,
        PositionLookupRepository $positionlookuprepository,
        OfficeAddressRepository $officeaddressrepository,
        BusinessLineRepository $lineofbusinessRepository,
        ParentCustomerRepository $parentcustomerrepository,
        BusinessSegmentRepository $businesssegmentrepository,
        ClientRepository $clientRepository,
        DivisionLookupRepository $divisionlookuprepository,
        User $userModel,
        AttachmentRepository $attachmentRepository,
        ContractBillingRateChangeRepository $contractbillingratechangerepository,
        CustomerRepository $customerRepository,
        EmployeeAllocationRepository $employeeAllocationrepository,
        UserRepository $userrepository,
        ReasonForSubmissionRepository $reasonforsubmissionRepository,
        EmployeeSchedule $employeeSchedule,
        EmployeeScheduleTimeLog $timeLogModel,
        ContractsRepository $contractsrepository,
        EmployeeScheduleTemporaryStorage $EmployeeScheduleTemporaryStorage,
        CustomerEmployeeAllocationRepository $CustomerEmployeeAllocationRepository,
        MailQueueRepository $MailQueueRepository
    ) {

        $this->cmuf = $cmuf;
        $this->customerModel = $customerModel;
        $this->clientRepository = $clientRepository;

        $this->usermodel = $userModel;

        $this->attachmentRepository = $attachmentRepository;
        $this->customerRepository = $customerRepository;
        $this->employeeAllocationRepository = $employeeAllocationrepository;
        $this->reasonforsubmissionRepository = $reasonforsubmissionRepository;
        $this->lineofbusinessRepository = $lineofbusinessRepository;
        $this->businesssegmentrepository = $businesssegmentrepository;
        $this->divisionlookuprepository = $divisionlookuprepository;
        $this->parentcustomerrepository = $parentcustomerrepository;
        $this->officeaddressrepository = $officeaddressrepository;
        $this->userrepository = $userrepository;
        $this->contractbillingratechangerepository = $contractbillingratechangerepository;
        $this->positionlookuprepository = $positionlookuprepository;
        $this->holidaypaymentallocationrepository = $holidaypaymentallocationrepository;
        $this->holidayrepository = $holidayrepository;
        $this->contractbillingcyclerepository = $contractbillingcyclerepository;
        $this->contractpaymentmethodrepository = $contractpaymentmethodrepository;
        $this->contractdeviceaccessrepository = $contractdeviceaccessrepository;
        $this->contractcellphoneproviderrepository = $contractcellphoneproviderrepository;
        $this->CustomerEmployeeAllocationRepository = $CustomerEmployeeAllocationRepository;

        $this->model = $employeeSchedule;
        $this->timeLogModel = $timeLogModel;
        $this->contractsrepository = $contractsrepository;
        $this->EmployeeScheduleTemporaryStorage = $EmployeeScheduleTemporaryStorage;
        $this->MailQueueRepository = $MailQueueRepository;
        $this->payPeriodRepository = $payPeriodRepository;
    }

    public function processCart($key, $customer, $employeeid, $week, $hours, $scheduledate, $payperiod, $startdatetime, $enddatetime, $overlaps = false)
    {
        $employeeschedulecart = new EmployeeScheduleTemporaryStorage();
        return $employeeschedulecart->insertData($key, $customer, $payperiod, $employeeid, $week, $hours, $scheduledate, $startdatetime, $enddatetime, \Auth::user()->id, $overlaps);
    }

    public function updateVariance($scheduleId)
    {
        $scheduleData = EmployeeScheduleTimeLog::select("hours", "payperiod_id")
            ->where("employee_schedule_id", $scheduleId)->get();
        $employeeSchedule = EmployeeSchedule::find($scheduleId);
        $schedHours = 0;
        $totPayperiod = 0;
        $contractHours = isset($employeeSchedule->contractual_hours) ?
            floatval($employeeSchedule->contractual_hours) : 0; // In hours
        $contractHoursFloat = 0;
        $paypArray = [];
        if ($contractHours > 0) {
            $contracthour = intval($contractHours);
            $contractminute = intval(($contractHours - floatval($contracthour)) * 100) / 60;
            $contractHoursFloat = $contracthour + $contractminute;
        }
        foreach ($scheduleData as $data) {
            $wHours = $data->hours;
            $noOfTotHours = intval($wHours);
            $noOfTotMinutes = ($wHours) - $noOfTotHours;

            $schedHours = $schedHours + (($noOfTotHours * 60) + ($noOfTotMinutes * 100));
            $paypArray[$data->payperiod_id] = $data->payperiod_id;
        }
        $totPayperiod = count($paypArray);
        $schedHours = $schedHours  / 60;
        $floatAverageHoursPerweek = $schedHours > 0 ? $schedHours / ($totPayperiod * 2) : 0;

        $noOfHours = intval($floatAverageHoursPerweek);
        $noOfMinutes = ($floatAverageHoursPerweek) - $noOfHours;
        $perCentageval = 0;
        $minsFormat = 0;
        if ($noOfMinutes != 0) {
            $noOfMinutes = ($noOfMinutes * 60) / 100;
            $minsFormat = $noOfMinutes * 100;
        }

        $minToHour = intval($noOfMinutes * 10) / 60;
        // if ($noOfMinutes > 0) {
        //     $noOfMinutes = $noOfMinutes * 10;
        //     if ($noOfMinutes > 0) {
        //         $noOfMinutes =  round($noOfMinutes / 60, 2) * 10;
        //     }
        //     // $minsFormat = intval(60 * ($noOfMinutes / 10));
        // }
        $averageWorkHour = $noOfHours + $noOfMinutes;
        $averageWorkHour = str_replace(".", ":", $averageWorkHour);

        $contractVariance = 0;
        $schedIndicator = 0;
        if ($contractHours > 0) {
            $contractVariance = $floatAverageHoursPerweek - $contractHoursFloat;
            if ($contractVariance === 0) {
                $schedIndicator = 1;
            }
        }

        $averageVarianceWorkHour = 0;
        $symbol = "";
        if ($contractVariance != 0) {
            $contractVariancesplit = explode(".", $contractVariance);
            $minutePart = 0;
            if (count($contractVariancesplit) > 1) {
                if ($contractVariancesplit[1] > 0) {
                    $minutePart = intval(60 * ($contractVariancesplit[1] / 10));
                }
            }

            $averageVarianceWorkHour = floatval($contractVariancesplit[0] . "." . $minutePart);
        }
        if ($employeeSchedule) {
            $employeeSchedule->update([
                "avgworkhours" => str_replace(":", ".", $averageWorkHour),
                "variance" => ($symbol . str_replace(":", ".", $averageVarianceWorkHour)),
                "schedindicator" => $schedIndicator
            ]);
        }
    }
    public function getWeeklycalculation($key, $customer, $week, $payperiod)
    {

        $noofpayperiods = EmployeeScheduleTemporaryStorage::where(["scheduleid" => $key, "customer_id" => $customer])
            ->select(DB::raw('(select count(week)) as noofweek'))->groupBy(['payperiod', 'week'])->first();
        $noofweek = $noofpayperiods->noofweek;
        $timearray = [];
        $employeeschedulecart = EmployeeScheduleTemporaryStorage::select('hours')->where(["scheduleid" => $key, "customer_id" => $customer, "week" => $week, "payperiod" => $payperiod])->get();
        foreach ($employeeschedulecart as $cart) {
            $hours = str_replace(".", ":", $cart->hours);
            array_push($timearray, $hours);
        }
        $employeeschedulecartsum = DB::table('employee_schedule_temporary_storages')
            ->where(["scheduleid" => $key, "customer_id" => $customer, "payperiod" => $payperiod])
            ->select(DB::raw("SUM(time_to_sec(timediff(endtime, starttime)) / 3600) as result"))->get(['result']);
        //dd($employeeschedulecartsum[0]->result);
        $tothours = round($employeeschedulecartsum[0]->result, 0, 2);
        $average_time = ($tothours / $noofweek);

        $returnarray[0] = $this->AddPlayTime($timearray);
        $returnarray[1] = $average_time;
        return $returnarray;
    }

    public function AddPlayTime($times)
    {
        $minutes = 0; //declare minutes either it gives Notice: Undefined variable
        // loop throught all the times
        foreach ($times as $time) {
            list($hour, $minute) = explode(':', $time);
            $minutes += $hour * 60;
            $minutes += $minute;
        }

        $hours = floor($minutes / 60);
        $minutes -= $hours * 60;

        // returns the time already formatted
        return sprintf('%02d.%02d', $hours, $minutes);
    }

    public function processDates($scheduledate, $starttime, $endtime)
    {
        $schedulestartdatetime = date("Y-m-d  H:i", strtotime($scheduledate . " " . $starttime));
        $scheduleenddatetime = date("Y-m-d  H:i", strtotime($scheduledate . " " . $endtime));
        $diff = strtotime($scheduleenddatetime) - strtotime($schedulestartdatetime);
        $datedifference = $diff / (60 * 60);
        if ($datedifference < 0) {
            $scheduleenddatetime = date("Y-m-d  H:i", strtotime("+1 day", strtotime($scheduledate . " " . $endtime)));
        }
        $differenceinhours = strtotime($scheduleenddatetime) - strtotime($schedulestartdatetime);
        $date1 = Carbon::parse($schedulestartdatetime);
        $date2 = Carbon::parse($scheduleenddatetime);
        $diff = $date1->diffInSeconds($date2);
        if ($diff > 0) {
            $explodetime = explode(":", gmdate('H:i:s', $diff));
            $differenceinhours = $explodetime[0] . "." . $explodetime[1];
        } else {
            $differenceinhours = 0;
        }

        // $differenceinhours = $differenceinhours / ( 60 * 60 );
        return [$schedulestartdatetime, $scheduleenddatetime, $differenceinhours];
    }

    public function getWhichweek($scheduledate)
    {

        $week1query = "select * from pay_periods where '" . $scheduledate . "' between start_date and week_one_end_date";
        $week2query = "select * from pay_periods where '" . $scheduledate . "' between week_two_start_date  and end_date";
        //select * from pay_periods where '2020-12-01' between start_date and week_one_end_date
        //echo $week2query;
        $week1 = count(DB::select($week1query));

        if ($week1 > 0) {
            return 1;
        } else {
            $week2 = count(DB::select($week2query));
        }

        if ($week2 > 0) {
            return 2;
        }
    }

    public function checkOverlappingschedules($key, $customer, $employeeid, $scheduledate, $payperiod, $startdatetime, $enddatetime)
    {
        $internalcount = 0;
        $employeeid = (int) $employeeid;

        $previousday = date('Y-m-d', strtotime($scheduledate . ' -1 day'));
        $nextday = date('Y-m-d', strtotime($scheduledate . ' +1 day'));
        $whereinarray = [$previousday, $scheduledate, $nextday];

        $internalcount = EmployeeScheduleTemporaryStorage::whereIn('scheduledate', $whereinarray)->whereRaw('employeeid =? and scheduledate !=?
                            and (( starttime between ? and  ?)
                            or ( endtime  between ? and  ?))', [
            $employeeid, $scheduledate, $startdatetime, $enddatetime,
            $startdatetime, $enddatetime,
        ])
            ->count();

        $actualschedule = EmployeeScheduleTimeLog::select('*')
            ->addselect(DB::raw('(select status from employee_schedules where id=employee_schedule_time_logs.employee_schedule_id) as status'))
            ->whereIn('schedule_date', $whereinarray)
            ->whereRaw('user_id =? and schedule_date =? and ((( start_datetime between ? and  ?)
        or ( end_datetime  between ? and  ?) or (? between start_datetime and end_datetime) or (? between start_datetime and end_datetime)) and (select status from employee_schedules where id=employee_schedule_time_logs.employee_schedule_id) < 2 )', [
                $employeeid, $scheduledate, $startdatetime, $enddatetime,
                $startdatetime, $enddatetime, $startdatetime, $enddatetime,
            ])
            ->whereHas('schedule', function ($q) {
                return $q->where('status', '<', '2');
            })
            ->count();
        //dd($actualschedule);
        $actcount = $actualschedule;

        if ($internalcount > 0) {
            $content['success'] = false;
            $content['message'] = 'Schedule overlapping';
            $content['code'] = 406;
        } else if ($actcount > 0) {
            $content['success'] = false;
            $content['message'] = 'Employee already have shift between the same time slot';
            $content['code'] = 406;
        } else {
            $content['success'] = true;
            $content['message'] = 'Schedule ';
            $content['code'] = 200;
        }
        // dd($scheduletemplogcount);

        return $content;
    }

    public function saveprecheck($customer, $key)
    {
        $employeecount = EmployeeScheduleTemporaryStorage::where(["scheduleid" => $key, "customer_id" => $customer])->count();
        $settings = ScheduleSettings::where('id', '1')->count();
        if ($employeecount < 1) {
            $content['success'] = false;
            $content['message'] = 'No employees assigned';
            $content['code'] = 406;
        } else if ($settings < 1) {
            $content['success'] = false;
            $content['message'] = 'No valid settings in admin panel';
            $content['code'] = 406;
        } else {
            $content['success'] = true;
            $content['message'] = 'Schedule ';
            $content['code'] = 200;
        }
        return json_encode($content, true);
    }

    public function getPendingscheduleid($initialscheduleid)
    {
        return EmployeeSchedule::where("initial_schedule_id", $initialscheduleid)->first();
    }

    public function getTrainingdetails($employees)
    {
        return TrainingUserContent::with(['course_content'])->whereIn("user_id", $employees)->where('completed', true)->get();
    }

    public function auditReports($customer)
    {
        $customers = $this->getCustomerList();

        $customeridarray = array_column($customers, 'id');

        $result = EmployeeSchedule::when($customer, function ($q) use ($customer) {
            return $q->where('customer_id', $customer);
        })
            ->when($customer == null, function ($q) use ($customer) {
                $today = date('Y-m-d', strtotime("-1 months", strtotime(date("Y-m-d"))));
                return $q->where('created_at', '>', $today);
            })
            ->whereIn('customer_id', $customeridarray)
            ->select("*", \DB::raw('(select start_datetime from employee_schedule_time_logs where employee_schedule_id=employee_schedules.id order by id asc limit 0,1) as contractstartdate'))
            ->addSelect(\DB::raw('(select total_hours_perweek from cmufs where contract_name=employee_schedules.customer_id and DATE(contract_startdate)<contractstartdate and DATE(contract_enddate)>contractstartdate order by contract_startdate desc limit 0,1) as cmuf_total_hours_perweek'))
            ->with(['scheduleTimeLogs'], function ($q) {
            })
            ->orderBy('id', 'desc')
            ->get();
        return $result;
    }

    public function generalReports($customer, $payperiod)
    {
        $customers = $this->getCustomerList();

        $customeridarray = array_column($customers, 'id');
        $result = EmployeeSchedule::when($customer, function ($q) use ($customer) {
            return $q->where('customer_id', $customer);
        })
            ->whereIn('customer_id', $customeridarray)
            ->select("*", \DB::raw('(select start_datetime from employee_schedule_time_logs where employee_schedule_id=employee_schedules.id order by id asc limit 0,1) as contractstartdate'))
            ->addSelect(\DB::raw('(select total_hours_perweek from cmufs where contract_name=employee_schedules.customer_id and DATE(contract_startdate)<contractstartdate and DATE(contract_enddate)>contractstartdate order by contract_startdate desc limit 0,1) as cmuf_total_hours_perweek'))
            ->with(['scheduleTimeLogs'], function ($q) {
            })
            ->when($payperiod, function ($q) use ($payperiod) {
                return $q->whereHas('scheduleTimeLogs', function ($query) use ($payperiod) {
                    return $query->whereIn('payperiod_id', $payperiod);
                });
            })
            ->orderBy('id', 'desc')
            ->get();
        return $result;
    }

    public function getScheduleStatus($payperiod)
    {
        $payp = array_column($payperiod, 'id');
        $returnarray = [];
        $scheduleids = EmployeeScheduleTimeLog::select(
            'employee_schedule_id',
            \DB::raw("(select customer_id from employee_schedules where id=employee_schedule_time_logs.employee_schedule_id) as customer_ids"),
            \DB::raw("(select status from employee_schedules where id=employee_schedule_time_logs.employee_schedule_id) as status")
        )
            ->whereIn('payperiod_id', $payp)->groupBy('employee_schedule_id')->get();
        foreach ($scheduleids as $schedule) {
            $customer_id = $schedule->customer_ids;
            $schedules = $schedule->employee_schedule_id;
            $status = $schedule->status;
            $payperiodinsideschecules = EmployeeScheduleTimeLog::select('payperiod_id')
                ->where('employee_schedule_id', $schedules)->groupBy('payperiod_id')->get()->pluck('payperiod_id')->toArray();
            foreach ($payperiodinsideschecules as $key => $value) {
                $returnarray[$customer_id . "-" . $value] = $status;
            }
        }
        echo json_encode($returnarray, true);
    }

    public function saveSchedule(
        $key,
        $customer,
        $supervisornotes,
        $variance,
        $scheduleindicator,
        $avghoursperweek,
        $contractual_hours
    ) {

        $schedule = new EmployeeSchedule();
        $schedule->customer_id = $customer;
        $schedule->initial_schedule_id = 0;
        $schedule->contractual_hours = $contractual_hours;
        $schedule->supervisornotes = $supervisornotes;
        $schedule->variance = $variance;
        $schedule->schedindicator = $scheduleindicator;
        $schedule->avgworkhours = $avghoursperweek;
        $schedule->status = 0;
        $schedule->update_by = 0;
        $schedule->created_by = Auth::user()->id;
        $schedule->save();
        return $schedule->id;
    }

    public function updateSchedule(
        $key,
        $customer,
        $supervisornotes,
        $variance,
        $scheduleindicator,
        $avghoursperweek,
        $contractual_hours
    ) {
        try {
            $schedule = EmployeeSchedule::find($key);
            $schedule->contractual_hours = $contractual_hours;
            $schedule->supervisornotes = $supervisornotes;
            $schedule->variance = $variance;
            $schedule->schedindicator = $scheduleindicator;
            $schedule->avgworkhours = $avghoursperweek;
            $schedule->created_by = Auth::user()->id;
            $schedule->save();
            return true;
        } catch (\Throwable $th) {
            return false;
        }
    }

    public function saveWeeklyhours($scheduleid, $payperiodweeklyhours)
    {
        $payperiodweeklyhours = json_decode($payperiodweeklyhours, true);
        $insertarray = [];
        $i = 0;

        foreach ($payperiodweeklyhours as $weeklyhours) {
            $insertarray[$i]["employee_schedule_id"] = $scheduleid;
            $insertarray[$i]["payperiod_id"] = $weeklyhours["payperiod"];
            $insertarray[$i]["week"] = $weeklyhours["week"];
            $insertarray[$i]["average_hours"] = $weeklyhours["weeklyhours"];
            $insertarray[$i]["contractual_hours"] = 0.0;
            $i++;
        }

        EmployeeScheduleAveragePayperiodHours::insert($insertarray);
    }

    public function getSchedules($key, $customer, $considerAllInsteadOfFutureDatesOnly = false)
    {
        if ($considerAllInsteadOfFutureDatesOnly) {
            $scheduleslog = EmployeeScheduleTemporaryStorage::where(['scheduleid' => $key, "customer_id" => $customer])->get();
        } else {
            $date = today()->format('Y-m-d');
            $scheduleslog = EmployeeScheduleTemporaryStorage::where(['scheduleid' => $key, "customer_id" => $customer])->where('scheduledate', '>=', $date)->get();
        }
        return $scheduleslog;
    }

    public function getSchedulelogcount($key, $customer)
    {
        $scheduleslog = EmployeeScheduleTemporaryStorage::where(['scheduleid' => $key, "customer_id" => $customer])->get();
        return $scheduleslog;
    }

    public function checkScheduleexist($customer, $payperiod)
    {

        $schedules = 0;
        $schedulesarray = EmployeeScheduleTimeLog::select('employee_schedule_id')
            ->addselect(DB::raw("(select customer_id from employee_schedules where id=employee_schedule_id) as customer "))
            ->addselect(DB::raw("(select status from employee_schedules where id=employee_schedule_id) as status "))
            ->whereIn('payperiod_id', $payperiod)->get();

        foreach ($schedulesarray as $value) {

            $customer_id = $value->customer;
            $status = $value->status;
            if ($customer_id == $customer && $status != "2") {
                $schedules = $schedules + 1;
            }
        }

        return $schedules;
    }

    public function removepreviouslogs()
    {
        $date = date("Y-m-d");
        EmployeeScheduleTemporaryStorage::where('created_at', '<', $date)->delete();
    }

    public function getCustomerList($status = ACTIVE, $areamanager = [], $supervisor = [])
    {
        $customers = [];
        $customerarray = [];
        $perm = 1;
        if (\Auth::user()->can('create_schedule_all_customer') || \Auth::user()->hasAnyPermission('admin', 'super_admin')) {
            $perm = 1;
            $customers = Customer::with('employeeCustomerAreaManager')
                ->where('active', $status)->when($areamanager, function ($q) use ($areamanager) {
                    $q->whereHas('employeeCustomerAreaManager', function ($query) use ($areamanager) {
                        return $query->whereIn('user_id', $areamanager);
                    });
                })
                ->when($supervisor, function ($q) use ($supervisor) {
                    $q->whereHas('employeeCustomerSupervisor', function ($query) use ($supervisor) {
                        return $query->whereIn('user_id', $supervisor);
                    });
                })->get();
        } else if (\Auth::user()->can('create_schedule_allocated_customer')) {
            $perm = 2;
            $customers = CustomerEmployeeAllocation::where(['user_id' => \Auth::user()->id])->when($areamanager, function ($q) use ($areamanager) {
                $q->whereHas('customer.employeeCustomerAreaManager', function ($query) use ($areamanager) {
                    return $query->whereIn('user_id', $areamanager);
                });
            })
                ->when($supervisor, function ($q) use ($supervisor) {
                    $q->whereHas('customer.employeeCustomerSupervisor', function ($query) use ($supervisor) {
                        return $query->whereIn('user_id', $supervisor);
                    });
                })->get();
        } else {
            $perm = 2;
            $customers = CustomerEmployeeAllocation::where(['user_id' => \Auth::user()->id])
                ->when($areamanager, function ($q) use ($areamanager) {
                    $q->whereHas('customer.employeeCustomerAreaManager', function ($query) use ($areamanager) {
                        return $query->whereIn('user_id', $areamanager);
                    });
                })
                ->when($supervisor, function ($q) use ($supervisor) {
                    $q->whereHas('customer.employeeCustomerSupervisor', function ($query) use ($supervisor) {
                        return $query->whereIn('user_id', $supervisor);
                    });
                })
                ->get();
        }

        $i = 0;
        foreach ($customers as $customer) {

            if ($perm == 1) {
                if ($customer->active == 1) {
                    $customerarray[$i]["id"] = $customer->id;
                    $customerarray[$i]["project_number"] = $customer->project_number;
                    $customerarray[$i]["client_name"] = $customer->client_name;
                    try {
                        $managername = "";
                        foreach ($customer->employeeCustomerAreaManager as $aremanagersarray) {
                            if ($managername == "") {
                                $managername .= $aremanagersarray->trashedUser->getFullNameAttribute();
                            } else {
                                $managername .= " , " . $aremanagersarray->trashedUser->getFullNameAttribute();
                            }
                        }

                        $full_name = $managername;
                        $customerarray[$i]["areamanager"] = $full_name;
                    } catch (\Throwable $th) {
                        $customerarray[$i]["areamanager"] = "";
                    }

                    try {
                        $supervisorname = "";
                        foreach ($customer->employeeCustomerSupervisor as $supervisorarray) {
                            if ($supervisorname == "") {
                                $supervisorname .= $supervisorarray->trashedUser->getFullNameAttribute();
                            } else {
                                $supervisorname .= " , " . $supervisorarray->trashedUser->getFullNameAttribute();
                            }
                        }
                        $customerarray[$i]["supervisor"] = $supervisorname;
                    } catch (\Throwable $th) {
                        $customerarray[$i]["supervisor"] = "";
                    }
                }
            } else if ($perm == 2) {
                if ($customer->customer->active == 1) {
                    $customerarray[$i]["id"] = $customer->customer->id;
                    $customerarray[$i]["project_number"] = $customer->customer->project_number;
                    $customerarray[$i]["client_name"] = $customer->customer->client_name;

                    try {
                        $customerarray[$i]["areamanager"] = $customer->customer->employeeLatestCustomerAreaManager->trashedUser->getFullNameAttribute();
                    } catch (\Throwable $th) {
                        $customerarray[$i]["areamanager"] = "";
                    }

                    try {
                        $customerarray[$i]["supervisor"] = $customer->customer->employeeLatestCustomerSupervisor->trashedUser->getFullNameAttribute();
                    } catch (\Throwable $th) {
                        $customerarray[$i]["supervisor"] = "";
                    }
                }
            }
            $i++;
        }
        return ($customerarray);
    }

    public function getThresholdvalues()
    {
        return $thresholdsettings = ScheduleSettings::find('1');
    }

    /*
     * fetch payperiods,customers from schedule by id
     */

    public function rejectPopulatedata($initialschedule)
    {
        $result = [];

        $scheduleObject = $this->getScheduleById($initialschedule);
        $payperiodarray = [];
        if (!empty($scheduleObject)) {
            $result['payperiodIds'] = [];
            $result['customerId'] = $scheduleObject->customer_id;
            $payperiodArray = $this->getPayperiodsByScheduleId($initialschedule);
            if (!empty($payperiodArray)) {
                foreach ($payperiodArray as $key => $value) {
                    array_push($payperiodarray, $key);
                }
                $result['payperiodIds'] = $payperiodarray;
            }
        }
        return $result;
    }

    public function saveSchedulelogs($scheduleid, $key, $customer, $logs)
    {
        $thresholdsettings = $this->getThresholdvalues();
        $dataarray = [];
        $definedweeklythreshold = $thresholdsettings->weekly_threshold;
        $definedbiweeklythreshold = $thresholdsettings->bi_weekly_threshold;
        $schedlogsarray = [];
        $userwithweeklythreshold = [];
        $userwithbiweeklythreshold = [];

        $employee_schedule_id = $scheduleid;

        $payperiods = EmployeeScheduleTemporaryStorage::select('payperiod')->where(['scheduleid' => $key, "customer_id" => $customer])
            ->groupBy('payperiod')
            ->get();
        /*
        Section to check Threshold and other variable
         */
        $usersvariable = EmployeeScheduleTemporaryStorage::select('employeeid')->where(['scheduleid' => $key, "customer_id" => $customer])
            ->groupBy('employeeid')
            ->get();
        foreach ($usersvariable as $users) {
            $userid = $users->employeeid;
            foreach ($payperiods as $payperiod) {
                $payperiodid = $payperiod->payperiod;
                $week1threshold = EmployeeScheduleTemporaryStorage::where(['scheduleid' => $key, "customer_id" => $customer, "employeeid" => $userid, "week" => "1", "payperiod" => $payperiodid])
                    ->sum('hours');
                $week2threshold = EmployeeScheduleTemporaryStorage::where(['scheduleid' => $key, "customer_id" => $customer, "employeeid" => $userid, "week" => "2", "payperiod" => $payperiodid])
                    ->sum('hours');
                if ($week1threshold > $definedweeklythreshold || $week2threshold > $definedweeklythreshold) {
                    array_push($userwithweeklythreshold, $userid);
                }
                $biweeklythreshold = $week1threshold + $week2threshold;
                if ($biweeklythreshold > $definedbiweeklythreshold) {
                    array_push($userwithbiweeklythreshold, $userid);
                }
            }
        }
        // dd($userwithweeklythreshold);
        /*
        Section to check Threshold and other variable
         */
        foreach ($logs as $schedlogs) {
            $user_id = $schedlogs->employeeid;
            $payperiod_id = $schedlogs->payperiod;
            $week = $schedlogs->week;
            $schedule_date = $schedlogs->scheduledate;
            $start_datetime = $schedlogs->starttime;
            $end_datetime = $schedlogs->endtime;
            $hours = $schedlogs->hours;
            $approved_by = 0;
            $approved_Date = null;
            $created_by = $schedlogs->employeeid;
            $approved = 0;
            if (!in_array($user_id . $schedule_date . $start_datetime, $schedlogsarray)) {
                $schedlogsarray[] = $user_id . $schedule_date . $start_datetime;
                $dataarray[] = [
                    "employee_schedule_id" => $employee_schedule_id, "user_id" => $user_id, "payperiod_id" => $payperiod_id, "week" => $week, "schedule_date" => $schedule_date,
                    "start_datetime" => $start_datetime, "end_datetime" => $end_datetime, "hours" => $hours, "approved_by" => $approved_by, "approved_Date" => $approved_Date,
                    "created_by" => $created_by, "approved" => $approved,
                ];
            }
        }
        $saved = EmployeeScheduleTimeLog::insert($dataarray);
        $includedusers = [];
        if ($saved) {
            $totalhourdata = [];
            $scheduledworkhoursdata = EmployeeScheduleTimeLog::where("employee_schedule_id", $employee_schedule_id)
                ->select('employee_schedule_id', 'payperiod_id', 'user_id', 'week')
                ->addselect(DB::raw("SUM(hours) as total_hours"))
                ->groupBy('employee_schedule_id', 'payperiod_id', 'user_id', 'week')
                ->get();
            foreach ($scheduledworkhoursdata as $scheduledworkhours) {
                $total_hours = $scheduledworkhours->total_hours;
                $payper = $scheduledworkhours->payperiod_id;
                $user = $scheduledworkhours->user_id;
                $cweek = $scheduledworkhours->week;
                if (!in_array($user, $includedusers)) {
                    array_push($includedusers, $user);
                }

                $totalhourdata[] = [
                    "employee_schedule_id" => $employee_schedule_id, "payperiod_id" => $payper, "user_id" => $user, "week" => $cweek, "workhours" => $total_hours,
                ];
            }
            ScheduledEmployeeWorkHour::insert($totalhourdata);
            EmployeeScheduleTemporaryStorage::where(['scheduleid' => $key, "customer_id" => $customer])->delete();
            $includedpayperiod = $payperiods->toArray();
            if (count($includedusers) > 0) {
                EmployeeScheduleTemporaryStorage::whereIn('employeeid', $includedusers)
                    ->whereIn('payperiod', $includedpayperiod)->delete();
            }
            $employeeschedule = EmployeeSchedule::find($scheduleid);
            $employeeschedule->pending_with = null;
            $employeeschedule->save();

            $customerObject = $this->customerRepository->getSingleCustomer($customer);
            $userIds = array_merge($userwithweeklythreshold, $userwithbiweeklythreshold);
            //dd($userIds);
            if (count($userIds) > 0) {

                $userIds = array_unique($userIds);
                $userNames = $this->getUserNameWithEmployeeCodeByIdArray($userIds);
                $thresoldnames = "";
                if (!empty($userNames)) {
                    $thresoldnames = "<b>Weekly/Bi-weekly threshold exceeded for the following employees <br/>" . $userNames . "</b>";
                }
                $helper_variables = array(
                    '{receiverFullName}' => 'Sir/Mam',
                    '{loggedInUserEmployeeNumber}' => Auth::user()->employee->employee_no,
                    '{loggedInUser}' => Auth::user()->getFullNameAttribute(),
                    '{client}' => $customerObject->client_name,
                    '{projectNumber}' => $customerObject->project_number,
                    '{thresholdexceededlist}' => $thresoldnames,
                );

                $this->MailQueueRepository->prepareMailTemplate("scheduling_request_notification", $customer, $helper_variables, "Modules\Employeescheduling\Models\EmployeeSchedule", 0);
            } else {
                $helper_variables = array(
                    '{receiverFullName}' => 'Sir/Mam',
                    '{loggedInUserEmployeeNumber}' => Auth::user()->employee->employee_no,
                    '{loggedInUser}' => Auth::user()->getFullNameAttribute(),
                    '{client}' => $customerObject->client_name,
                    '{projectNumber}' => $customerObject->project_number,
                    '{thresholdexceededlist}' => '',
                );
                $this->MailQueueRepository->prepareMailTemplate("scheduling_request_notification", $customer, $helper_variables, "Modules\Employeescheduling\Models\EmployeeSchedule", 0);
            }
            $helper_variable = array(
                '{receiverFullName}' => '',
                '{loggedInUserEmployeeNumber}' => Auth::user()->employee->employee_no,
                '{loggedInUser}' => Auth::user()->getFullNameAttribute(),
                '{client}' => $customerObject->client_name,
                '{projectNumber}' => $customerObject->project_number,
            );

            $this->MailQueueRepository->prepareMailTemplate("scheduling_request_notification_creator", '', $helper_variable, "Modules\Employeescheduling\Models\EmployeeSchedule", Auth::user()->id);
        }
    }

    public function removecartentry($customer, $employeeid, $scheduledate, $starttime, $endtime, $payperiod)
    {
        return EmployeeScheduleTemporaryStorage::where([
            "scheduleid" => \Auth::user()->id . "-" . date("Y-m-d"),
            "customer_id" => $customer,
            "payperiod" => $payperiod,
            "employeeid" => $employeeid,
            "scheduledate" => $scheduledate,
        ])->delete();
    }

    public function prepopulatelogs($key, $payperiod, $project)
    {
        $previouscart = [];
        $cartset = EmployeeScheduleTemporaryStorage::where(["scheduleid" => $key, "customer_id" => $project])->whereIn("payperiod", $payperiod)->get();
        $i = 0;
        foreach ($cartset as $cart) {
            $previouscart[$i]["scheduleid"] = $cart->scheduleid;
            $previouscart[$i]["customer_id"] = $cart->customer_id;
            $previouscart[$i]["payperiod"] = $cart->payperiod;
            $previouscart[$i]["employee"] = $cart->employeeid;
            $previouscart[$i]["week"] = $cart->week;
            $previouscart[$i]["hours"] = $cart->hours;
            $previouscart[$i]["scheduledate"] = $cart->scheduledate;
            $previouscart[$i]["starttime"] = date("h:ia", strtotime($cart->starttime));
            $previouscart[$i]["endtime"] = date("h:ia", strtotime($cart->endtime));
            $previouscart[$i]["overlaps"] = $cart->overlaps;
            $i++;
        }
        return $previouscart;
    }

    /**
     * fetch schedule data for listing by status, paperiod, customer.
     * @param  string, array, array
     * @return collection
     */
    public function getSchedulesByStatus($status, $payperioIdArray, $allocatedCustomersArray)
    {
        $resultArray = [];
        $reScheduledObjectCollections = $this->model
            ->select(
                "id",
                "initial_schedule_id"
            )
            ->with("reScheduleTimeLogs")
            ->whereNotNull('initial_schedule_id')->where('status', '!=', 2)->orderBy("id")->get();
        $allReschedulesArray = [];
        foreach ($reScheduledObjectCollections as $reScheduledObjectCollection) {
            if ($reScheduledObjectCollection->initial_schedule_id > 0) {
                $allReschedulesArray[$reScheduledObjectCollection->initial_schedule_id]
                    = [
                        "id" => $reScheduledObjectCollection->id,
                        "count" => $reScheduledObjectCollection->reScheduleTimeLogs->count()
                    ];
            }
        }
        $timeLogScheduleArray = $this->timeLogModel->with([
            'payperiod',
            'schedule' => function ($q) {
                return $q->with([
                    "createdUser",
                    "updatedUser",
                    "customer",
                    "statusUpdatedUser"
                ])->select(
                    "id",
                    "customer_id",
                    "contractual_hours",
                    "initial_schedule_id",
                    "status_updated_by",
                    "status_update_date",
                    "created_by",
                    "update_by",
                    "created_at",
                    "updated_at",
                    "status"
                );
            }
        ])
            ->when(!empty($payperioIdArray), function ($query) use ($payperioIdArray) {
                $query->whereIn('payperiod_id', $payperioIdArray);
            })
            ->orderBy('id', 'DESC')
            ->select('*')
            // ->where("start_datetime", '>=', \Carbon::now()->subDays(90))
            ->get();

        if (empty($timeLogScheduleArray)) {
            return [];
        }

        $userIdArray = [];
        $keyArrayForDuplicationCheck = [];
        foreach ($timeLogScheduleArray as $timeLogSchedule) {
            $scheduleObject = $timeLogSchedule->schedule;
            $scheduleId = $timeLogSchedule->employee_schedule_id;
            $customerId = $scheduleObject->customer_id;
            $scheduleDate = $timeLogSchedule->schedule_date;
            $timestring = strtotime($scheduleDate);

            if (in_array($customerId, $allocatedCustomersArray) && ($scheduleObject->status == $status)) {
                if ($status == 2) {
                    $key = $timeLogSchedule->user_id . "_" . $timeLogSchedule->payperiod_id . "_" . $scheduleDate;
                    if (in_array($key, $keyArrayForDuplicationCheck)) {
                        continue;
                    } else {
                        $keyArrayForDuplicationCheck[] = $key;
                    }
                    $reScheduledObjectcount = 0;
                    $reScheduledObject = null;
                    if (isset($allReschedulesArray[$scheduleId])) {
                        $reScheduledObject = $allReschedulesArray[$scheduleId]["id"];
                        $reScheduledObjectcount = $allReschedulesArray[$scheduleId]["count"];
                    }

                    if ($reScheduledObject > 0) {
                        // dd($allReschedulesArray, $reScheduledObject);
                        // $reScheduleDetails = EmployeeScheduleTimeLog::where('employee_schedule_id', '=', $reScheduledObject)->count();
                        // $reScheduleDetails = $this->getScheduleTimeLogByScheduleId($reScheduledObject->id);
                        // dd($reScheduleDetails);
                        if ($reScheduledObjectcount > 0) {
                            continue;
                        }
                    }
                }

                $resultArray[$scheduleId]['status'] = $scheduleObject->status;
                $resultArray[$scheduleId]['created_at'] = isset($scheduleObject->created_at) ?
                    $scheduleObject->created_at->format("d-m-Y") : "";
                $resultArray[$scheduleId]['is_rescheduled'] = !empty($scheduleObject->initial_schedule_id) ? true : false;
                $resultArray[$scheduleId]['id'] = $scheduleObject->id;
                // dd($scheduleObject);
                $resultArray[$scheduleId]['created_by'] = $scheduleObject->createdUser->getFullNameAttribute();
                // dd($scheduleObject);
                $resultArray[$scheduleId]['updated_by'] = ($scheduleObject->statusUpdatedUser ? $scheduleObject->statusUpdatedUser->getFullNameAttribute() : ($scheduleObject->updatedUser ? $scheduleObject->updatedUser->getFullNameAttribute() : ''));
                $resultArray[$scheduleId]['updated_date'] = (($scheduleObject->statusUpdatedUser && $scheduleObject->status_update_date) ? $scheduleObject->status_update_date : (($scheduleObject->updatedUser && $scheduleObject->updated_at) ? $scheduleObject->updated_at->format("d-m-Y") : ''));
                $resultArray[$scheduleId]['customer'] = $scheduleObject->customer->client_name . ' /' . $scheduleObject->customer->project_number;

                $userIdArray[$scheduleId][] = $timeLogSchedule->user_id;
                $resultArray[$scheduleId]['number_of_employees'] = count(array_unique($userIdArray[$scheduleId]));
                $hours = $this->getTimeDifference($timeLogSchedule->start_datetime, $timeLogSchedule->end_datetime);
                if (array_key_exists('bi_weekly_total_hours1', $resultArray[$scheduleId])) {
                    $resultArray[$scheduleId]['bi_weekly_total_hours1'] += ($this->explodeTime($hours));
                } else {
                    $resultArray[$scheduleId]['bi_weekly_total_hours1'] = ($this->explodeTime($hours));
                }

                $resultArray[$scheduleId]['bi_weekly_total_hours'] = $this->secondTohhmm($resultArray[$scheduleId]['bi_weekly_total_hours1']);

                if ((!array_key_exists('timestring', $resultArray[$scheduleId])) || ((array_key_exists('timestring', $resultArray[$scheduleId]) && ($timestring < $resultArray[$scheduleId]['timestring'])))) {
                    $resultArray[$scheduleId]['timestring'] = $timestring;
                    $resultArray[$scheduleId]['contractual_hours'] = $scheduleObject->contractual_hours;
                }
            }
        }

        if (!empty($resultArray)) {
            usort($resultArray, function ($a, $b) {
                return (strtotime($b["created_at"]) - strtotime($a["created_at"]));
            });
        }

        return $resultArray;
    }

    /*
     * fetch payperiods by schedule
     * @param  id
     * @return array
     */

    public function getPayperiodsByScheduleId($id)
    {
        return $this->timeLogModel
            ->join('pay_periods', 'employee_schedule_time_logs.payperiod_id', '=', 'pay_periods.id')
            ->where('employee_schedule_id', '=', $id)
            ->groupBy('payperiod_id', 'pay_periods.pay_period_name', 'pay_periods.short_name', 'pay_periods.start_date')
            ->orderBy('pay_periods.start_date', 'ASC')
            ->select(DB::raw("CONCAT(pay_periods.pay_period_name,' (',pay_periods.short_name,')') AS name"), 'payperiod_id')
            ->pluck('name', 'payperiod_id')->toArray();
    }

    /*
     * fetch customers by schedule
     * @param  id
     * @return object
     */

    public function getScheduleById($id)
    {
        return $this->model->with(['customer', 'statusUpdatedUser', 'createdUser', 'updatedUser', 'scheduleTimeLogs'])->find($id);
    }

    /*
     * fetch schedule time log
     * @param  id, array, array
     * @return collection
     */

    public function getScheduleTimeLogByScheduleId($id = '', $customerIdArray = [], $payperiodIdParams = [])
    {
        if (!empty($payperiodIdParams) && (!is_array($payperiodIdParams))) {
            $payperiodIdArray[] = $payperiodIdParams;
        } else {
            $payperiodIdArray = $payperiodIdParams;
        }

        return $this->timeLogModel->with('schedule', 'user', 'employee')
            ->join('employee_schedules', 'employee_schedule_time_logs.employee_schedule_id', '=', 'employee_schedules.id')
            ->join('users', 'users.id', '=', 'employee_schedule_time_logs.user_id')
            ->when((!empty($customerIdArray)), function ($query) use ($customerIdArray) {
                $query->whereIn('employee_schedules.customer_id', $customerIdArray);
            })
            ->when(!empty($payperiodIdArray), function ($query) use ($payperiodIdArray) {
                $query->whereIn('employee_schedule_time_logs.payperiod_id', $payperiodIdArray);
            })
            ->when(!empty($id), function ($query) use ($id) {
                $query->where('employee_schedule_id', '=', $id);
            })
            ->where('employee_schedules.deleted_at', null)
            ->orderBy('users.first_name', 'ASC')
            ->get();
    }

    /*
     * approve schedule by id
     * @param  id, string
     * @return boolean
     */

    public function approveScheduleById($id, $statusNote)
    {
        $scheduleObj = $this->getScheduleById($id);
        if (empty($scheduleObj)) {
            return false;
        }

        $date = Carbon::now();
        $authUserId = Auth::user()->id;
        $status = $this->model->where('id', $id)->update(['status' => 1, 'status_update_date' => $date, 'status_updated_by' => $authUserId, 'status_notes' => $statusNote, 'schedule_overlaps' => false]);
        if ($status) {
            $status = $this->timeLogModel->where('employee_schedule_id', $id)->update(['approved' => 1, 'approved_date' => $date, 'approved_by' => $authUserId, 'overlaps' => false]);

            $customerObject = $this->customerRepository->getSingleCustomer($scheduleObj->customer_id);
            if (!empty($customerObject)) {
                $helper_variable = array(
                    '{receiverFullName}' => '',
                    '{loggedInUserEmployeeNumber}' => Auth::user()->employee->employee_no,
                    '{scheduleReasonNote}' => '',
                    '{thresholdexceededlist}' => '',
                    '{loggedInUser}' => Auth::user()->getFullNameAttribute(),
                    '{client}' => $customerObject->client_name,
                    '{projectNumber}' => $customerObject->project_number,
                    '{scheduleStatus}' => 'approved',
                );
                $this->MailQueueRepository->prepareMailTemplate("scheduling_approved_request_notification", $scheduleObj->customer_id, $helper_variable, "Modules\Employeescheduling\Models\EmployeeSchedule", Auth::user()->id);
            }
        } else {
            return false;
        }

        return $status;
    }

    /*
     * reject schedule by id
     * @param  id, string
     * @return boolean
     */

    public function rejectScheduleById($id, $statusNote)
    {
        $scheduleObj = $this->getScheduleById($id);
        if (empty($scheduleObj)) {
            return false;
        }
        $date = Carbon::now();
        $authUserId = Auth::user()->id;
        $status = $this->model->where('id', $id)->update(['status' => 2, 'status_update_date' => $date, 'status_updated_by' => $authUserId, 'status_notes' => $statusNote]);
        if ($status) {
            $logStatus = $this->timeLogModel->where('employee_schedule_id', $id)->update(['approved' => 0]);
            $customerObject = $this->customerRepository->getSingleCustomer($scheduleObj->customer_id);

            if (!empty($customerObject)) {
                $helper_variable = array(
                    '{receiverFullName}' => '',
                    '{loggedInUserEmployeeNumber}' => Auth::user()->employee->employee_no,
                    '{scheduleReasonNote}' => $statusNote,
                    '{thresholdexceededlist}' => '',
                    '{loggedInUser}' => Auth::user()->getFullNameAttribute(),
                    '{client}' => $customerObject->client_name,
                    '{projectNumber}' => $customerObject->project_number,
                    '{scheduleStatus}' => 'rejected',
                );
                $this->MailQueueRepository->prepareMailTemplate("scheduling_approved_request_notification", $scheduleObj->customer_id, $helper_variable, "Modules\Employeescheduling\Models\EmployeeSchedule", Auth::user()->id);
            }
            return true;
        }
        return false;
    }

    /*
     * re-schedule rejected schedule
     * @param  id
     * @return integer
     */

    public function reScheduleById($id)
    {
        $scheduleObj = $this->getScheduleById($id);
        if (empty($scheduleObj)) {
            return 2;
        }
        $checkExists = $this->checkReScheduleExistence($scheduleObj);
        if (empty($checkExists) || (!empty($checkExists) && (count($checkExists->scheduleTimeLogs) == 0))) {
            $authUserId = \Auth::user()->id;
            $cartKey = $authUserId . "-" . date("Y-m-d");
            $date = Carbon::now();

            //fetch time log details
            $scheduleLogDetails = $this->getScheduleTimeLogByScheduleId($id);

            //validate cart entries
            foreach ($scheduleLogDetails as $scheduleLogDetail) {
                $startTime = $scheduleLogDetail->start_datetime;
                $endTime = $scheduleLogDetail->end_datetime;
                $customerId = $scheduleObj->customer_id;
                $scheduleDate = $scheduleLogDetail->schedule_date;
                $employeeId = $scheduleLogDetail->user_id;
                $hours = $scheduleLogDetail->hours;
                $payperiodId = $scheduleLogDetail->payperiod_id;

                //remove already found entries from cart
                $overlappingCount = $this->EmployeeScheduleTemporaryStorage
                    ->where('scheduleid', $cartKey)
                    ->where('customer_id', $customerId)
                    ->where('payperiod', $payperiodId)
                    ->where('employeeid', $employeeId)
                    ->where('scheduledate', $scheduleDate)
                    ->delete();
            }

            //check schedule already existing with same initial_schedule_id
            $newScheduleObj = $this->model
                ->where('initial_schedule_id', $scheduleObj->id)
                ->where('status', '=', 0)
                ->first();

            if (empty($newScheduleObj)) {
                //create new schedule
                $newScheduleId = $this->saveSchedule($cartKey, $scheduleObj->customer_id, "", 0, 1, 0, 0);
                if (empty($newScheduleId)) {
                    return 3;
                }

                $newScheduleObj = $this->getScheduleById($newScheduleId);
            }

            //set initail schedule id
            $newScheduleObj->initial_schedule_id = $scheduleObj->id;
            $newScheduleObj->pending_with = $authUserId;
            $newScheduleObj->created_by = $authUserId;
            $newScheduleObj->update_by = $authUserId;
            $newScheduleObj->created_at = $date;
            $newScheduleObj->updated_at = $date;
            $newScheduleObj->save();

            $i = $j = 0;
            foreach ($scheduleLogDetails as $scheduleLogDetail) {
                $startTime = $scheduleLogDetail->start_datetime;
                $endTime = $scheduleLogDetail->end_datetime;
                $customerId = $scheduleObj->customer_id;
                $scheduleDate = $scheduleLogDetail->schedule_date;
                $employeeId = $scheduleLogDetail->user_id;
                $hours = $scheduleLogDetail->hours;
                $payperiodId = $scheduleLogDetail->payperiod_id;
                $overlaps = $scheduleLogDetail->overlaps;

                $weekdata = $this->getWhichweek($scheduleDate);

                //escape multi creation of already existing schedule with in between dates
                $exists = $this->checkScheduleLogExistance($employeeId, $scheduleDate, $payperiodId, $startTime, $endTime);
                if ($exists) {
                    continue;
                }

                $insertdata = $this->processCart($cartKey, $customerId, $employeeId, $weekdata, $hours, $scheduleDate, $payperiodId, $startTime, $endTime, $overlaps);
                if ($insertdata) {
                    $i++;
                }
                $j++;
            }

            if ($i == $j) {
                return 0;
            }
        } elseif (!empty($checkExists) && !empty($checkExists->scheduleTimeLogs)) {
            return 1;
        }
        return 3;
    }

    public function checkScheduleLogExistance($employeeId, $scheduleDate, $payperiodId, $startdatetime, $enddatetime)
    {
        $actualschedule = EmployeeScheduleTimeLog::select('*')
            ->addselect(DB::raw('(select status from employee_schedules where id=employee_schedule_time_logs.employee_schedule_id) as status'))
            ->whereRaw('user_id =? and schedule_date =? and ((( start_datetime between ? and  ?)
        or ( end_datetime  between ? and  ?) or (? between start_datetime and end_datetime) or (? between start_datetime and end_datetime)) and (select status from employee_schedules where id=employee_schedule_time_logs.employee_schedule_id) < 2 )', [
                $employeeId, $scheduleDate, $startdatetime, $enddatetime,
                $startdatetime, $enddatetime, $startdatetime, $enddatetime,
            ])
            ->whereHas('schedule', function ($q) {
                return $q->where('status', '<', '2');
            })
            ->count();

        if ($actualschedule > 0) {
            return true;
        }
        return false;
    }

    /*
     * check for re-scheduled netry for the rejected schedule
     * @param  object
     * @return object
     */

    public function checkReScheduleExistence($scheduleObj)
    {
        if (empty($scheduleObj)) {
            return [];
        }

        $reScheduledObject = $this->model->where('initial_schedule_id', $scheduleObj->id)->where('status', '!=', 2)->first();

        if (!empty($reScheduledObject)) {
            return $reScheduledObject;
        }

        return [];
    }

    /**
     * Remove the specified schedule from storage.
     *
     * @param  id
     * @return object
     */
    public function deleteSchedule($id)
    {
        $scheduleObj = $this->getScheduleById($id);
        if (!empty($scheduleObj)) {
            $scheduleTimeLogs = $scheduleObj->scheduleTimeLogs;

            if (!empty($scheduleTimeLogs)) {
                foreach ($scheduleTimeLogs as $scheduleTimeLog) {
                    $scheduleTimeLog->delete();
                }
            }
            return $scheduleObj->delete();
        }
        return false;
    }

    /*
     * get parent schedule from initial id
     * @param  object
     * @return object
     */

    public function getParentScheduleRequest($scheduleObj)
    {
        if (empty($scheduleObj)) {
            return [];
        }

        $parentObject = $this->model->where('id', $scheduleObj->initial_schedule_id)->where('status', '=', 2)->first();
        return $parentObject;
    }

    /*
     * fecth time diffrence between two datetime
     * @param  datetime
     * @return string
     */

    public function getTimeDifference($startTime, $endTime, $returnArray = 0)
    {
        $date1 = date_create($startTime);
        $date2 = date_create($endTime);
        $diff = date_diff($date1, $date2);
        $hours = sprintf('%02d', $diff->h);
        $min = sprintf('%02d', $diff->i);

        if ($returnArray) {
            return ['hours' => $diff->h, 'min' => $diff->i];
        }
        return $hours . ':' . $min;
    }

    /*
     * convert decimal to hours format
     * @param  decimal
     * @return string
     */

    public function replaceDecimalPointToColon($num_hours, $returnString = 0)
    {
        $timeString = explode(".", $num_hours);
        $hours = (int) $timeString[0];
        $mins = (int) $timeString[1];
        $time = (($hours * 3600) + ($mins * 60));
        return $this->secondTohhmm($time, $returnString);
    }

    /*
     * fetch user name and employee code from user id array
     * @param  array
     * @return string (separated by comma)
     */

    public function getUserNameWithEmployeeCodeByIdArray($userIds)
    {
        $result = '';
        $userDetails = $this->userrepository->getUsersDropdownList($userIds);
        if (!empty($userDetails)) {
            foreach ($userDetails as $userDetail) {
                $empArr[] = $userDetail['name'];
            }
            $result = implode(",<br/>", $empArr);
        }
        return $result;
    }

    /*
     * convert to seconds from hours:minutes string
     * @param  string
     * @return string
     */

    public function explodeTime($time)
    { //explode time and convert into seconds
        $time = explode(':', $time);
        $time = (($time[0] * 3600) + ($time[1] * 60));
        return $time;
    }

    /*
     * convert seconds to hours:minutes
     * @param  string
     * @return string
     */

    public function secondTohhmm($time, $returnString = 0)
    { //convert seconds to hh:mm
        $hour = floor($time / 3600);
        $minute = strval(floor(($time % 3600) / 60));

        if ($minute == 0) {
            $minute = "00";
        } else {
            $minute = $minute;
        }

        if ($returnString) {
            return $hour . "." . $minute;
        }
        $hour = sprintf('%02d', $hour);
        $minute = sprintf('%02d', $minute);
        return $hour . ":" . $minute;
    }

    public function calculatePayperiodHours($payperiodId, $userId, $week, $scheduleId)
    {
        $logs = $this->timeLogModel
            ->where('employee_schedule_id', $scheduleId)
            ->where('payperiod_id', $payperiodId)
            ->where('user_id', $userId)
            ->where('week', $week)
            ->get();

        $totalHours = 0;
        $totalMinutes = 0;
        foreach ($logs as $log) {
            $data = explode(".", $log->hours);
            $totalHours += $data[0];
            $totalMinutes += $data[1];
        }
        $time = (($totalHours * 3600) + ($totalMinutes * 60));
        return $this->secondTohhmm($time, 0);
    }

    public function getAllocatedCustomerIds()
    {
        $customerIds = [];
        $customers = $this->CustomerEmployeeAllocationRepository->getUserallocatedcustomers();
        foreach ($customers as $customer) {
            if ($customer->customer->active == 1) {
                $customerIds[] = $customer->customer->id;
            }
        }
        return $customerIds;
    }

    public function removeTempLogStorageByParams($customerId, $payperiodIdArray)
    {
        $key = \Auth::user()->id . "-" . date("Y-m-d");
        $count = $this->EmployeeScheduleTemporaryStorage
            ->where('customer_id', $customerId)
            ->where('scheduleid', $key)
            ->delete();
        return $count;
    }

    public function getScheduleByParams($scheduleId = '', $customerIds = [], $payperiodIds = [])
    {
        //declare variables
        $temporaryArr = [];
        $schedules = [];
        $userIdArray = [];
        $tableHeaderRow = [];
        $payperiodWeekArray = [];
        $payperiodArray = [];
        $test = [];

        //fetch schedules by input parameters
        $scheduleTimeLogDetails = $this->getScheduleTimeLogByScheduleId($scheduleId, $customerIds, $payperiodIds);
        foreach ($scheduleTimeLogDetails as $scheduleTimeLog) {
            $scheduleObject = $scheduleTimeLog->schedule;
            if (empty($scheduleId) && $scheduleObject->status != 1) {
                continue;
            }
            $startTime = \Carbon::createFromFormat('Y-m-d H:i:s', $scheduleTimeLog->start_datetime);
            $endTime = \Carbon::createFromFormat('Y-m-d H:i:s', $scheduleTimeLog->end_datetime);

            $userId = $scheduleTimeLog->user->id;
            $userName = ucwords(strtolower($scheduleTimeLog->user->first_name)) . ' ' . ucwords(strtolower($scheduleTimeLog->user->last_name));
            $schedules[$userId]['user_name'] = $scheduleTimeLog->employee->employee_no ? $userName . ' (' . $scheduleTimeLog->employee->employee_no . ')' : $userName;

            $schedules[$userId]['user_name'] = $scheduleTimeLog->employee->employee_no ? $userName . ' (' . $scheduleTimeLog->employee->employee_no . ')' : $userName;
            $schedules[$userId]['title'] = $scheduleTimeLog->employee->employee_no ? $userName . ' (' . $scheduleTimeLog->employee->employee_no . ')' : $userName;
            $schedules[$userId]['role'] = $scheduleTimeLog->user->getFormatedRoleNameAttribute();

            $schedules[$userId]['training_details'] = '';
            $triningDetails = $this->getTrainingdetails([$userId]);
            if (!empty($triningDetails)) {
                $userTrainingDetails = [];
                $i = 0;
                foreach ($triningDetails as $val) {
                    $userTrainingDetails[$i] = trim($val->course_content->content_title);

                    if (!empty($val->course_content->completed_date)) {
                        $userTrainingDetails[$i] .= ' (' . $val->course_content->completed_date . ') ';
                    }
                    $i++;
                }
                if (!empty($userTrainingDetails)) {
                    $schedules[$userId]['training_details'] = implode("|", $userTrainingDetails);
                }
            }

            $detail['is_data'] = true;
            $detail['overlaps'] = $scheduleTimeLog->overlaps;
            $detail['start_datetime'] = $startTime->format("h:i A");
            $detail['end_datetime'] = $endTime->format("h:i A");
            $detail['hours'] = $scheduleTimeLog->hours;
            $detail['user_name'] = $scheduleTimeLog->user->full_name;

            $a = \Carbon::createFromFormat('Y-m-d H:i:s', $scheduleTimeLog->start_datetime);
            $dateToString = ($a->format("Y-m-d"));
            $dateToCustomString = ($a->format("M d Y"));
            $temporaryArr[$scheduleTimeLog->payperiod_id] = $scheduleTimeLog->payperiod->end_date;
            $tableHeaderRow[$scheduleTimeLog->payperiod_id][$scheduleTimeLog->week][$dateToString]['value'] = ($a->format("l"));
            $tableHeaderRow[$scheduleTimeLog->payperiod_id][$scheduleTimeLog->week][$dateToString]['display'] = $dateToCustomString;
            $detail['date_string'] = ($a->format("d-m-Y"));
            $detail['day'] = $tableHeaderRow[$scheduleTimeLog->payperiod_id][$scheduleTimeLog->week][$dateToString]['value'];
            $schedules[$userId]['schedule_data'][$scheduleTimeLog->payperiod_id][$scheduleTimeLog->week][$dateToString] = $detail;
            $weeklyHoursInHoursFormat = $this->calculatePayperiodHours($scheduleTimeLog->payperiod_id, $scheduleTimeLog->user_id, $scheduleTimeLog->week, $scheduleTimeLog->employee_schedule_id);
            $schedules[$userId]['week_' . $scheduleTimeLog->payperiod_id . '_' . $scheduleTimeLog->week] = $weeklyHoursInHoursFormat;
            $payperiodArray[] = $scheduleTimeLog->payperiod_id;
            if ((!in_array($userId . '_' . $scheduleTimeLog->payperiod_id . '_' . $scheduleTimeLog->week, $payperiodWeekArray)) && array_key_exists($scheduleTimeLog->payperiod_id, $schedules[$userId])) {
                $schedules[$userId][$scheduleTimeLog->payperiod_id] += $this->explodeTime($weeklyHoursInHoursFormat);
                $payperiodWeekArray[] = $userId . '_' . $scheduleTimeLog->payperiod_id . '_' . $scheduleTimeLog->week;
                $schedules[$userId][$scheduleTimeLog->payperiod_id . '_display'] = $this->secondTohhmm($schedules[$userId][$scheduleTimeLog->payperiod_id]);
            } elseif (!in_array($userId . '_' . $scheduleTimeLog->payperiod_id . '_' . $scheduleTimeLog->week, $payperiodWeekArray)) {
                $schedules[$userId][$scheduleTimeLog->payperiod_id] = $this->explodeTime($weeklyHoursInHoursFormat);
                $payperiodWeekArray[] = $userId . '_' . $scheduleTimeLog->payperiod_id . '_' . $scheduleTimeLog->week;
                $schedules[$userId][$scheduleTimeLog->payperiod_id . '_display'] = $this->secondTohhmm($schedules[$userId][$scheduleTimeLog->payperiod_id]);
            }

            $userIdArray[] = $userId;

            //make empty for next iteration.
            $detail = [];
        }
        // dd($schedules);
        $payperiodArray = array_unique($payperiodArray);
        $userIdArray = array_unique($userIdArray);

        //fetch payperiod by schedule
        if (!empty($scheduleId) && empty($payperiodIds)) {
            $payperiods = $this->getPayperiodsByScheduleId($scheduleId);
            if (!empty($payperiods)) {
                $payperiodIds = array_keys($payperiods);
                $payperiodIds = array_unique($payperiodIds);
            }
        }

        if (empty($payperiodIds)) {
            //show payperiods above date
            $payperioddates = $this->payPeriodRepository->getAllActivePayPeriodsabovedate();
        } else {
            if (!empty($payperiodIds) && (!is_array($payperiodIds))) {
                $payperiodIdArray[] = $payperiodIds;
            } else {
                $payperiodIdArray = $payperiodIds;
            }

            $payperioddates = $this->payPeriodRepository->getPayperiodByArray($payperiodIdArray);
        }

        //loop through pending payperiod dates
        $loopstart_date = "";
        $loopend_date = "";
        foreach ($payperioddates as $ky => $value) {
            $loopstart_date = $value["start_date"];
            $loopend_date = $value["end_date"];
            $payperiodId = $value["id"];

            $begin = new DateTime($loopstart_date);
            $end = new DateTime($loopend_date);

            for ($i = $begin; $i <= $end; $i->modify('+1 day')) {
                $selectedDateToString = ($i->format("Y-m-d"));
                $dateToCustomFormat = ($i->format("M d Y"));
                $selectedWeek = $this->getWhichweek($selectedDateToString);

                //make empty array for no data
                foreach ($userIdArray as $userId) {
                    $schedules[$userId]['total_hours'] = 0;
                    foreach ($payperiodArray as $value) {
                        if (array_key_exists($userId, $schedules) && array_key_exists($value, $schedules[$userId])) {
                            $schedules[$userId]['total_hours'] += $schedules[$userId][$value]; // this fucntion will convert all hh:mm to seconds
                            $timeString = $this->secondTohhmm($schedules[$userId]['total_hours']);
                            $schedules[$userId]['total_hours_display'] = $timeString;
                        }
                    }

                    if (!array_key_exists($userId, $schedules) || (array_key_exists($userId, $schedules) && ((!array_key_exists($payperiodId, $schedules[$userId]['schedule_data'])) || (!array_key_exists($selectedWeek, $schedules[$userId]['schedule_data'][$payperiodId])) || (!array_key_exists($selectedDateToString, $schedules[$userId]['schedule_data'][$payperiodId][$selectedWeek]))))) {
                        $day = $i->format("l");
                        $schedules[$userId]['schedule_data'][$payperiodId][$selectedWeek][$selectedDateToString] = ['is_data' => false, 'overlaps' => false, 'start_datetime' => '', 'end_datetime' => '', 'hours' => '', 'date_string' => $selectedDateToString, 'day' => $day];
                        $tableHeaderRow[$payperiodId][$selectedWeek][$selectedDateToString]['value'] = $day;
                        $tableHeaderRow[$payperiodId][$selectedWeek][$selectedDateToString]['display'] = $dateToCustomFormat;
                        $temporaryArr[$payperiodId] = $end;
                    }

                    if (!array_key_exists('week_' . $payperiodId . '_' . $selectedWeek, $schedules[$userId])) {
                        $schedules[$userId]['week_' . $payperiodId . '_' . $selectedWeek] = '00:00';
                    }

                    if (array_key_exists($payperiodId, $schedules[$userId]['schedule_data'])) {
                        if (array_key_exists($selectedWeek, $schedules[$userId]['schedule_data'][$payperiodId])) {
                            ksort($schedules[$userId]['schedule_data'][$payperiodId][$selectedWeek]);
                        }
                        ksort($schedules[$userId]['schedule_data'][$payperiodId]);
                    }

                    ksort($schedules[$userId]['schedule_data']);
                }

                if (!empty($tableHeaderRow) && array_key_exists($payperiodId, $tableHeaderRow) && array_key_exists($selectedWeek, $tableHeaderRow[$payperiodId])) {
                    ksort($tableHeaderRow[$payperiodId][$selectedWeek]);
                    ksort($tableHeaderRow[$payperiodId]);
                    ksort($tableHeaderRow);
                }
            }
        }

        $temporaryArr1 = $temporaryArr;
        if (!empty($temporaryArr1)) {
            asort($temporaryArr1);
            $temporaryArr = $temporaryArr1;
        }

        foreach ($userIdArray as $userId) {
            if (array_key_exists($userId, $schedules)) {
                $schedules[$userId]['schedule_data'] = array_replace($temporaryArr1, $schedules[$userId]['schedule_data']);
            }
            $temporaryArr1 = $temporaryArr;
        }

        if (!empty($temporaryArr) && !empty($tableHeaderRow)) {
            $tableHeaderRow = array_replace($temporaryArr, $tableHeaderRow);
        }

        return [
            'schedules' => $schedules,
            'headerData' => $tableHeaderRow,
        ];
    }

    public function updatePastScheduleAllowed($scheduleId)
    {
        $date = today()->format('Y-m-d');
        $count = $this->timeLogModel->where('employee_schedule_id', $scheduleId)->where('approved_by', '>', 0)->where('schedule_date', '>=', $date)->count();
        return ($count > 0) ? true : false;
    }

    public function getCustomerScheduleComplienceByDate($customerId, $processDate)
    {
        $succesfulShift = 0;
        $percentage = 0;

        //Get employee schedule and timings of a customer
        //(employee_schedule , employee_schedule_time_logs)
        $estQuery = EmployeeScheduleTimeLog::whereDate('schedule_date', '=', $processDate);
        $estQuery->whereHas('schedule', function ($query) use ($customerId) {
            $query->where('status', 1);
            $query->where('customer_id', $customerId);
        });
        $totalSchedule = $estQuery->count();

        //All shifts of a specific date
        $es = EmployeeShift::whereDate('start', $processDate)
            // ->where('end', '<=', $signOutEndDate)
            ->groupBy('employee_shift_payperiod_id')
            ->whereHas('shift_payperiod', function ($q) use ($customerId, $estQuery) {
                return $q->where('customer_id', '=', $customerId)
                    ->whereIn('employee_id', $estQuery->pluck('user_id')->toArray());
            })->select('employee_shift_payperiod_id')->get();

        $succesfulShift = $es->count();
        //calculate the percentage of compliance
        // satisfied count/total schedule count
        if ($totalSchedule > 0) {
            $percentage = ($succesfulShift / $totalSchedule) * 100;
        }

        //Return values
        return [
            'total' => $totalSchedule,
            'completed' => $succesfulShift,
            'percentage' => $percentage,
        ];
    }

    public function fetchScheduleComplianceByPayperiods($start, $limit, $startDate = null, $endDate = null, $payPeriodIds = null, $customerIds = null, $employeeIds = null, $regionalManagerIds = null, $typeIds = null, $exemptlimit = 0, $region = 0)
    {
        $scheQry = EmployeeScheduleTimeLog::with(['schedule', 'user']);
        if (!empty($startDate) && !empty($endDate)) {
            $scheQry->whereBetween('schedule_date', [$startDate, $endDate]);
        }

        if (!empty($payPeriodIds)) {
            $scheQry->whereIn('payperiod_id', $payPeriodIds);
        }

        if (!empty($employeeIds)) {
            $scheQry->whereIn('user_id', $employeeIds);
        }

        $scheQry->whereHas('schedule', function ($query) use ($customerIds, $regionalManagerIds,$region) {
            $query->where('status', 1);
            $query->when(!empty($customerIds), function ($q) use ($customerIds) {
                $q->whereIn('customer_id', $customerIds);
            })->when(!empty($regionalManagerIds), function ($q) use ($regionalManagerIds) {
                $q->whereHas('customer', function ($qry) use ($regionalManagerIds) {
                    $qry->whereHas('employeeCustomerAreaManager', function ($qy) use ($regionalManagerIds) {
                        $qy->whereIn('user_id', $regionalManagerIds);
                    });
                });
            });
            $query->when($region != 0, function ($qr) use ($region) {
                $qr->whereHas('customer', function ($qr) use ($region) {
                        $qr->where('region_lookup_id', $region);
                });
            });
        });

     /*   $scheQry->when($region != null, function ($qr) use ($region) {
            $qr->whereHas('customer', function ($qr) use ($region) {
                    $qr->whereIn('region_lookup_id', $region);
            });
        });*/

        $scheduleCnt = $scheQry->count();
        $scheduleData = $scheQry->when($exemptlimit == 0, function ($q) use ($scheduleCnt, $start) {
            return $q->orderBy('start_datetime', 'ASC')->skip($start)->limit($scheduleCnt);
        })->get();
        if (empty($scheduleData)) {
            return ['start' => 0, 'records' => []];
        }
        $siteSettings = SiteSettings::find(1);
        $maximumShiftStartTolerance = !empty($siteSettings) ? (($siteSettings->shift_start_time_tolerance + 1)) : 0;
        $maximumShiftEndTolerance = !empty($siteSettings) ? (($siteSettings->shift_end_time_tolerance + 1)) : 0;
        $resultArr = [];
        $i = $start;
        foreach ($scheduleData as $key => $scheduleVal) {
            $i++;
            $customerId = $scheduleVal->schedule->customer_id;
            $signInStartDate = \Carbon::parse($scheduleVal->start_datetime)->subHours(8);
            $signOutEndDate = \Carbon::parse($scheduleVal->end_datetime)->addHours(8);
            $shiftPayperiodIds = EmployeeShiftPayperiod::where('pay_period_id', $scheduleVal->payperiod_id)
                ->where('employee_id', $scheduleVal->user_id)
                ->where('customer_id', $customerId)
                ->pluck('id')->toArray();

            $shiftLateIn = EmployeeShift::select('start', \DB::raw('abs(floor((TIMESTAMPDIFF(SECOND,start,"' . $scheduleVal->start_datetime . '"))/60)) as diffminutes'))
                ->whereIn('employee_shift_payperiod_id', $shiftPayperiodIds)
                ->where('start', '>=', $signInStartDate)->where('start', '<=', $signOutEndDate)
                ->orderBy(\DB::raw('abs(TIMESTAMPDIFF(SECOND,start,"' . $scheduleVal->start_datetime . '"))'), 'asc')
                ->take(1)->get();

            $shiftEarlyOut = EmployeeShift::select('end', \DB::raw('abs(floor((TIMESTAMPDIFF(SECOND,"' . $scheduleVal->end_datetime . '",end))/60)) as diffminutes'))
                ->whereIn('employee_shift_payperiod_id', $shiftPayperiodIds)
                ->where('end', '>=', $signInStartDate)->where('end', '<=', $signOutEndDate)
                ->orderBy(\DB::raw('abs(TIMESTAMPDIFF(SECOND,"' . $scheduleVal->end_datetime . '",end))'), 'asc')
                ->take(1)->get();

            $result['late_in_minutes'] = (isset($shiftLateIn[0]) && ($shiftLateIn[0]->start > $scheduleVal->start_datetime)) ? $shiftLateIn[0]->diffminutes : 0;
            $result['early_out_minutes'] = (isset($shiftEarlyOut[0]) && ($shiftEarlyOut[0]->end < $scheduleVal->end_datetime)) ? $shiftEarlyOut[0]->diffminutes : 0;
            $actualIn = (isset($shiftLateIn[0])) ? \Carbon::parse($shiftLateIn[0]->start)->format('h:i A') : '-';
            $actualOut = (isset($shiftEarlyOut[0])) ? \Carbon::parse($shiftEarlyOut[0]->end)->format('h:i A') : '-';
            // $result['actual_in'] = $actualIn;
            // $result['actual_out'] = $actualOut;
            $result['noShow'] = false;

            //type based conditions
            if (!empty($typeIds)) {
                $popArray = $typeIds;
                if (in_array(1, $typeIds) && (($actualIn == "-") || ($result['late_in_minutes'] == 0) || ($result['late_in_minutes'] <= $maximumShiftStartTolerance))) {
                    $popArray = array_diff($popArray, array("1"));
                }

                if (in_array(2, $typeIds) && (($actualIn == "-") || ($result['late_in_minutes'] == 0) || ($result['late_in_minutes'] >= $maximumShiftStartTolerance))) {
                    $popArray = array_diff($popArray, array("2"));
                }

                if (in_array(6, $typeIds) && (($actualOut == "-") || ($result['early_out_minutes'] == 0) || ($result['early_out_minutes'] <= $maximumShiftEndTolerance))) {
                    $popArray = array_diff($popArray, array("6"));
                }

                if (in_array(7, $typeIds) && (($actualOut == "-") || ($result['early_out_minutes'] == 0) || ($result['early_out_minutes'] >= $maximumShiftEndTolerance))) {
                    $popArray = array_diff($popArray, array("7"));
                }

                if (in_array(3, $typeIds) && (($actualIn == "-") || ($actualOut == "-") || ($result['late_in_minutes'] != 0) || ($result['early_out_minutes'] != 0))) {
                    $popArray = array_diff($popArray, array("3"));
                }

                if (in_array(4, $typeIds)) {
                    if (($actualIn == "-") || ($actualOut == "-")) {
                        $popArray = array_diff($popArray, array("4"));
                    } else {
                        $actualTimeDifference = \Carbon::parse($shiftEarlyOut[0]->end)->diffInMinutes(\Carbon::parse($shiftLateIn[0]->start));
                        $scheduleTimeDifference = \Carbon::parse($scheduleVal->end_datetime)->diffInMinutes(\Carbon::parse($scheduleVal->start_datetime));

                        if ($actualTimeDifference < $scheduleTimeDifference) {
                            $popArray = array_diff($popArray, array("4"));
                        }
                    }
                }
                if (in_array(5, $typeIds) && (($actualIn != "-") || ($actualOut != "-"))) {
                    $popArray = array_diff($popArray, array("5"));
                }
                if (in_array(5, $typeIds)) {
                    $result["noShow"] = true;
                }
                // if (in_array(5, $popArray)) {
                //     $result["noShow"] = true;
                // }

                if (empty($popArray)) {
                    continue;
                }
            }
            if (!isset($shiftLateIn[0]->start) && !isset($shiftLateIn[0]->end)) {
                $result["noShow"] = true;
            }
            $result['id'] = $scheduleVal->id;
            $result['late_in_color'] = (isset($shiftLateIn[0]) && ($shiftLateIn[0]->start >= $scheduleVal->start_datetime)) ? (($shiftLateIn[0]->diffminutes == 0) ? 0 : (($shiftLateIn[0]->diffminutes < $maximumShiftStartTolerance) ? 1 : 2)) : 3;
            $result['early_out_color'] = (isset($shiftEarlyOut[0]) && ($shiftEarlyOut[0]->end <= $scheduleVal->end_datetime)) ? (($shiftEarlyOut[0]->diffminutes == 0) ? 0 : (($shiftEarlyOut[0]->diffminutes < $maximumShiftEndTolerance) ? 1 : 2)) : 3;
            $result['date'] = Carbon::parse($scheduleVal->schedule_date)->format('M d, Y');
            $result['date_hidden'] = Carbon::parse($scheduleVal->schedule_date)->format('Y-m-d');
            $result['in_time'] = Carbon::parse($scheduleVal->start_datetime)->format('h:i A');
            $result['out_time'] = Carbon::parse($scheduleVal->end_datetime)->format('h:i A');
            $result['actual_in_time'] = isset($shiftLateIn[0]->start) ? (Carbon::parse($shiftLateIn[0]->start)->format('h:i A')) : "";
            $result['actual_out_time'] = isset($shiftLateIn[0]->end) ? (Carbon::parse($shiftLateIn[0]->end)->format('h:i A')) : "";
            $result['site_no'] = $scheduleVal->schedule->customer->project_number;
            $result['site_name'] = $scheduleVal->schedule->customer->client_name;
            $result['employee'] = $scheduleVal->user->full_name;
            $result['employee_no'] = ($scheduleVal->user->employee && $scheduleVal->user->employee->employee_no) ? $scheduleVal->user->employee->employee_no : '-';
            $result['email'] = $scheduleVal->user->email;
            $result['phone'] = $scheduleVal->user->employee ? $scheduleVal->user->employee->phone : '';
            $result['area_manager'] = $scheduleVal->schedule->customer->employeeLatestCustomerAreaManager ? $scheduleVal->schedule->customer->employeeLatestCustomerAreaManager->user->full_name : '-';
            // $result['area_manager_email'] = $scheduleVal->schedule->customer->employeeLatestCustomerAreaManager ? $scheduleVal->schedule->customer->employeeLatestCustomerAreaManager->user->email : '-';
            // $result['area_manager_phone'] = ($scheduleVal->schedule->customer->employeeLatestCustomerAreaManager && $scheduleVal->schedule->customer->employeeLatestCustomerAreaManager->user && $scheduleVal->schedule->customer->employeeLatestCustomerAreaManager->user->employee) ? $scheduleVal->schedule->customer->employeeLatestCustomerAreaManager->user->employee->phone : '-';
            // $result['supervisor'] = $scheduleVal->schedule->customer->employeeLatestCustomerSupervisor ? $scheduleVal->schedule->customer->employeeLatestCustomerSupervisor->user->full_name : '-';
            // $result['supervisor_email'] = $scheduleVal->schedule->customer->employeeLatestCustomerSupervisor ? $scheduleVal->schedule->customer->employeeLatestCustomerSupervisor->user->email : '-';
            // $result['supervisor_phone'] = ($scheduleVal->schedule->customer->employeeLatestCustomerSupervisor && $scheduleVal->schedule->customer->employeeLatestCustomerSupervisor->user && $scheduleVal->schedule->customer->employeeLatestCustomerSupervisor->user->employee) ? $scheduleVal->schedule->customer->employeeLatestCustomerSupervisor->user->employee->phone : '-';

            //hidden values for data check
            $result['user_id'] = $scheduleVal->user ? $scheduleVal->user->id : '';
            $result['payperiod_id'] = $scheduleVal->payperiod_id;

            $resultArr[] = $result;
            if (count($resultArr) == $limit && $exemptlimit == 0) {
                break;
            }
        }

        return [
            'start' => $i,
            'totalRows' => $scheduleCnt,
            'records' => $resultArr,
        ];
    }

    public function getRegionalManagersByCustomer($customerIds)
    {
        $result = [];
        $qry = Customer::with(['employeeLatestCustomerAreaManager']);
        if (!empty($customerIds)) {
            $qry->whereIn('id', $customerIds);
        }
        $customers = $qry->get();
        $managersId = [];
        foreach ($customers as $customer) {

            if ($customer->employeeLatestCustomerAreaManager && (!in_array($customer->employeeLatestCustomerAreaManager->areaManager->id, $managersId))) {
                $managersId[] = $customer->employeeLatestCustomerAreaManager->areaManager->id;
                $manager = $customer->employeeLatestCustomerAreaManager->areaManager;
                $result[] = [
                    'id' => $manager->id,
                    'name' => $manager->full_name,
                ];
            }
        }

        if (!empty($result)) {
            $managers = array_column($result, 'name');
            array_multisort($managers, SORT_ASC, $result);
        }

        return $result;
    }

    public function getCustomersByAreaManager($areaManagerIds)
    {
        $result = [];
        $qry = CustomerEmployeeAllocation::with(['customer'])->select('customer_id');
        if (!empty($areaManagerIds)) {
            $qry->whereIn('user_id', $areaManagerIds);
        }
        $allocation = $qry->groupBy('customer_id')->get();

        $customersId = [];
        foreach ($allocation as $alloc) {
            if ($alloc->customer && (!in_array($alloc->customer->id, $customersId))) {
                $customersId[] = $alloc->customer->id;

                $result[] = [
                    'id' => $alloc->customer->id,
                    'name' => $alloc->customer->client_name,
                ];
            }
        }

        if (!empty($result)) {
            $customers = array_column($result, 'name');
            array_multisort($customers, SORT_ASC, $result);
        }

        return $result;
    }

    public function fetchScheduleComplianceDetails($startDate = null, $payPeriodIds = null, $customerIds = null, $employeeIds = null, $regionalManagerIds = null)
    {
        if (!empty($startDate)) {
            $shiftStartDate = Carbon::parse($startDate)->subHours(8);
            $shiftEndDate = Carbon::parse($startDate)->addHours(8);
        }
        $scheQry = EmployeeScheduleTimeLog::when($employeeIds != null, function ($query) use ($employeeIds) {
            return $query->whereIn('user_id', $employeeIds);
        })
            ->when($payPeriodIds != null, function ($query) use ($payPeriodIds) {
                return $query->whereIn('payperiod_id', $payPeriodIds);
            })
            ->whereHas('schedule', function ($query) use ($customerIds) {
                return $query->where('status', 1)
                    ->when(!empty($customerIds), function ($q) use ($customerIds) {
                        $q->whereIn('customer_id', $customerIds);
                    });
            })
            ->whereBetween('start_datetime', [$shiftStartDate, $shiftEndDate])
            ->with(['schedule', 'user'])
            ->orderBy("id", "desc")
            ->first();
        return $scheQry;
    }

    public function updateEmployeeShiftPayperiodByEmployeeScheduleId($scheduleId, $employeeShiftPayperiodId)
    {
        return EmployeeShiftPayperiod::where('id', $employeeShiftPayperiodId)->update(['employee_schedule_id' => $scheduleId]);
    }

    public function updateEmployeeShiftByEmployeeScheduleTimeLogId($scheduleTimeLogId, $employeeShiftId)
    {
        return EmployeeShift::where('id', $employeeShiftId)->update(['employee_schedule_time_log_id' => $scheduleTimeLogId]);
    }
}
