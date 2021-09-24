<?php

namespace Modules\Timetracker\Repositories;

use App\Repositories\AttachmentRepository;
use App\Repositories\MailQueueRepository;
use App\Services\HelperService;
use Carbon\Carbon;
use Config;
use DateTime;
use DB;
use Exception;
use Illuminate\Support\Facades\Auth;
use Log;
use Modules\Admin\Models\CpidLookup;
use Modules\Admin\Models\Customer;
use Modules\Admin\Models\CustomerEmployeeAllocation;
use Modules\Admin\Models\CustomerQrcodeLocation;
use Modules\Admin\Models\EmployeeAllocation;
use Modules\Admin\Models\Holiday;
use Modules\Admin\Models\PayPeriod;
use Modules\Admin\Models\SiteSettings;
use Modules\Admin\Models\User;
use Modules\Admin\Repositories\CustomerRepository;
use Modules\Admin\Repositories\EmployeeAllocationRepository;
use Modules\Admin\Repositories\GeofenceRepository;
use Modules\Admin\Repositories\PayPeriodRepository;
use Modules\Admin\Rules\CpidAndCustomer;
use Modules\Admin\Rules\ActivityTypeCustomerType;
use Modules\Admin\Rules\ActivityCodeToActivityTypeCustomerType;
use Modules\Employeescheduling\Models\EmployeeScheduleTimeLog as ModelsEmployeeScheduleTimeLog;
use Modules\Supervisorpanel\Repositories\GuardTourRepository;
use Modules\Supervisorpanel\Repositories\ShiftJournalRepository;
use Modules\Timetracker\Models\CustomerQrcodeAttachment;
use Modules\Timetracker\Models\CustomerQrcodeHistory;
use Modules\Timetracker\Models\CustomerQrcodeSummary;
use Modules\Timetracker\Models\CustomerQrcodeWithShift;
use Modules\Timetracker\Models\EmployeeShift;
use Modules\Timetracker\Models\EmployeeShiftCpid;
use Modules\Timetracker\Models\EmployeeShiftPayperiod;
use Modules\Timetracker\Models\EmployeeShiftReportEntry;
use Modules\Timetracker\Models\EmployeeShiftWeeklyPerformance;
use Modules\Timetracker\Models\ShiftMeetingNote;
use function GuzzleHttp\json_encode;
use Modules\Employeescheduling\Repositories\SchedulingRepository;
use phpDocumentor\Reflection\Types\True_;
use PhpParser\Node\Stmt\Foreach_;

class EmployeeShiftRepository
{

    /**
     * The Model instance.
     * ''
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $model;
    private $week_pay_period_sec = 158400; // in seconds - 44*60*60
    private $biweek_pay_period_sec = 316800; // in seconds - 88*60*60
    private $triweek_pay_period_sec = 475200; // in seconds - 132*60*60
    private $regular_wrk_hours = 0;
    private $json_shift_details_array;
    protected $imageRepository;
    protected $guardTourRepository;
    protected $customer, $notificationRepository;
    protected $customerRepository;
    protected $geofenceRepository;
    protected $employeeShiftCpidReportRepository;

    /**
     * Create a new EmployeeShiftRepository instance.
     *
     * @param EmployeeShift $employeeShift
     * @param \Modules\Timetracker\Repositories\ImageRepository $imageRepository
     * @param GuardTourRepository $guardTourRepository
     * @param ShiftJournalRepository $shiftJournalRepository
     * @param \Modules\Timetracker\Repositories\TripRepository $tripRepository
     * @param \Modules\Timetracker\Repositories\EmployeeShiftApprovalLogRepository $employeeShiftApprovalLogRepository
     * @param \Modules\Timetracker\Repositories\EmployeeShiftCpidRepository $employeeShiftCpidRepository
     * @param HelperService $helperService
     * @param Customer $customer
     * @param EmployeeAllocationRepository $employeeAllocationrepository
     * @param \Modules\Timetracker\Repositories\NotificationRepository $notificationRepository
     * @param \Modules\Timetracker\Repositories\QrcodeLocationRepository $qrcodeLocationRepository
     * @param CustomerRepository $customerRepository
     * @param GeofenceRepository $geofenceRepository
     * @param MobileSecurityPatrolFenceDataRepository $fenceDataRepository
     * @param CustomerQrcodeAttachment $customerQrcodeAttachment
     * @param AttachmentRepository $attachmentRepository
     * @param SchedulingRepository $schedulingRepository
     */
    public function __construct(
        EmployeeShift $employeeShift,
        ImageRepository $imageRepository,
        GuardTourRepository $guardTourRepository,
        ShiftJournalRepository $shiftJournalRepository,
        TripRepository $tripRepository,
        EmployeeShiftApprovalLogRepository $employeeShiftApprovalLogRepository,
        EmployeeShiftCpidRepository $employeeShiftCpidRepository,
        EmployeeShiftCpidReportRepository $employeeShiftCpidReportRepository,
        HelperService $helperService,
        Customer $customer,
        EmployeeAllocationRepository $employeeAllocationrepository,
        NotificationRepository $notificationRepository,
        QrcodeLocationRepository $qrcodeLocationRepository,
        CustomerRepository $customerRepository,
        GeofenceRepository $geofenceRepository,
        MobileSecurityPatrolFenceDataRepository $fenceDataRepository,
        CustomerQrcodeAttachment $customerQrcodeAttachment,
        AttachmentRepository $attachmentRepository,
        PayPeriodRepository $payPeriodRepository,
        SchedulingRepository $schedulingRepository,
        MailQueueRepository $mailQueueRepository
    ) {
        $this->model = $employeeShift;
        $this->imageRepository = $imageRepository;
        $this->guardTourRepository = $guardTourRepository;
        $this->shiftJournalRepository = $shiftJournalRepository;
        $this->tripRepository = $tripRepository;
        $this->employeeShiftApprovalLogRepository = $employeeShiftApprovalLogRepository;
        $this->employeeShiftCpidRepository = $employeeShiftCpidRepository;
        $this->employeeShiftCpidReportRepository = $employeeShiftCpidReportRepository;
        $this->helperService = $helperService;
        $this->customer = $customer;
        $this->helper_service = new HelperService();
        $this->employeeAllocationRepository = $employeeAllocationrepository;
        $this->notificationRepository = $notificationRepository;
        $this->qrcodeLocationRepository = $qrcodeLocationRepository;
        $this->customerRepository = $customerRepository;
        $this->geofenceRepository = $geofenceRepository;
        $this->fenceDataRepository = $fenceDataRepository;
        $this->attachmentRepository = $attachmentRepository;
        $this->customerQrcodeAttachment = $customerQrcodeAttachment;
        $this->payPeriodRepository = $payPeriodRepository;
        $this->schedulingRepository = $schedulingRepository;
        $this->mailQueueRepository = $mailQueueRepository;

    }

    /**
     * Get getAllActiveGuards
     *
     * @param \App\Models\User $user
     * @return resultset
     */
    public function getAllActiveGuards($user, $request = null)
    {
        $all_active_guards = EmployeeShiftPayperiod::whereActive(true)
            ->with(array(
                'trashed_user',
                'trashed_user.employee_profile',
                'trashed_user.allocatedSupervisor',
                'trashed_customer',
                'payperiod'
            ))
            ->whereHas('trashed_user.allocatedSupervisor', function ($query) use ($user) {
                $query->where('supervisor_id', '=', $user->id);
            })
            ->select('employee_id', 'created_at')
            ->where(function ($query) use ($request) {
                if (!is_null($request)) {
                    $employee_id = (int)$request->get('employeeId');
                    $employee_name = $request->get('employeeName');
                    $project_no = $request->get('projectNo');
                    $client_name = $request->get('clientName');
                    $payperiod_start = $request->get('payperiodStart');
                    $payperiod_end = $request->get('payperiodEnd');
                    $approved = (int)$request->get('approved');
                    $query->whereSubmitted(true);
                    $query->whereApproved($approved);
                    if ($employee_id > 0) {
                        $query->whereHas('trashed_user.trashed_employee_profile', function ($query) use ($employee_id) {
                            $query->where('employee_no', 'like', '%' . $employee_id . '%');
                        });
                    }
                    if (isset($employee_name)) {
                        $query->whereHas('trashed_user', function ($query) use ($employee_name) {
                            $query->where('first_name', 'like', '%' . $employee_name . '%');
                        });
                    }
                    if (isset($project_no) || isset($client_name)) {
                        $query->whereHas('trashed_customer', function ($query) use ($project_no, $client_name) {
                            $query->where('project_number', 'like', '%' . $project_no . '%');
                            $query->where('client_name', 'like', '%' . $client_name . '%');
                        });
                    }
                    if (isset($payperiod_start) || isset($payperiod_end)) {
                        $query->whereHas('payperiod', function ($query) use ($payperiod_start, $payperiod_end) {
                            $query->where('start_date', '>=', $payperiod_start);
                            $query->where('end_date', '<=', $payperiod_end);
                        });
                    }
                    return $query;
                }
            })
            ->get();
        // convert the result to array
        //$arr_guards_all = data_get($all_active_guards,"*.user.allocatedEmployee.*.user_id");
        $arr_guards_all = $all_active_guards->toArray();
        //sorting the array by created date and return array
        usort($arr_guards_all, function ($a, $b) {
            return $a['created_at'] <=> $b['created_at'];
        });
        $arr_guards = array();
        $emp_id_arr = array();
        // fetching unique employee ids.
        foreach ($arr_guards_all as $i => $element) {
            foreach ($element as $key => $val) {
                if (!in_array($val, $emp_id_arr) && ($key == 'employee_id')) {
                    $arr_guards[$i][$key] = $val;
                    array_push($emp_id_arr, $val);
                }
            }
        }
        return $emp_id_arr;
    }

    /**
     * Get All shift details of an employee with other details
     *
     * @param type $name Description
     * @return resultset
     */
    public function getAllShiftDetails($employee_id, $approved = null, $request = null)
    {
        $user = Auth::user()->id;
        $projects = CustomerEmployeeAllocation::where('user_id', $user)->pluck('customer_id');
        $all_shift_details = User::withTrashed()->with(array('trashed_employee_profile' => function ($query) {
            $query->select('user_id', 'employee_no');
        }, 'employee_shift_payperiods' => function ($query) use ($approved, $request, $projects) {
            $query->where(function ($query) use ($request, $projects) {
                if (!is_null($request)) {
                    $employee_id = (int)$request->get('employeeId');
                    $employee_number = $request->get('employeeNumber');
                    $employee_name = $request->get('employeeName');
                    $project_no = $request->get('projectNo');
                    $client_name = $request->get('clientName');
                    $payperiod_start = $request->get('payperiodStart');
                    $payperiod_end = $request->get('payperiodEnd');
                    $approved = (int)$request->get('approved');

                    $query->whereSubmitted(true);
                    $query->whereApproved($approved);
                    if (isset($employee_number)) {
                        $query->whereHas('trashed_user.trashed_employee_profile', function ($query) use ($employee_number) {
                            $query->where('employee_no', 'like', '%' . $employee_number . '%');
                        });
                    }
                    if (isset($employee_name)) {
                        $query->whereHas('trashed_user', function ($query) use ($employee_name) {
                            $query->where('first_name', 'like', '%' . $employee_name . '%');
                        });
                    }
                    if (isset($project_no) || isset($client_name)) {
                        $query->whereHas('trashed_customer', function ($query) use ($project_no, $client_name) {
                            $query->where('project_number', 'like', '%' . $project_no . '%');
                            $query->where('client_name', 'like', '%' . $client_name . '%');
                        });
                    }
                    if (isset($payperiod_start) || isset($payperiod_end)) {
                        $query->whereHas('trashed_payperiod', function ($query) use ($payperiod_start, $payperiod_end) {
                            $query->where('start_date', '>=', $payperiod_start);
                            $query->where('end_date', '<=', $payperiod_end);
                        });
                    }
                    return $query;
                }
            })
                ->whereSubmitted(true)
                ->whereIn('customer_id', $projects);
            $query->select('id', 'employee_id', 'pay_period_id', 'customer_id', 'submitted', 'approved', DB::raw('TIME_FORMAT(total_regular_hours, "%H:%i") as total_regular_hours'), DB::raw('TIME_FORMAT(total_overtime_hours, "%H:%i") as total_overtime_hours'), DB::raw('TIME_FORMAT(total_statutory_hours, "%H:%i") as total_statutory_hours'));
        }, 'employee_shift_payperiods.trashed_payperiod' => function ($query) {
            $query->select('id', 'start_date', 'end_date');
        }, 'employee_shift_payperiods.trashed_customer' => function ($query) {
            $query->select('id', 'project_number', 'client_name');
        }, 'employee_shift_payperiods.shifts' => function ($query) {
            $query->select('id', 'employee_shift_payperiod_id', 'start', 'end', 'work_hours');
        }))
            ->where('id', '=', $employee_id)
            ->select('id', 'first_name', 'last_name')
            ->get();
        return $all_shift_details;
    }

    /**
     *
     * Get pay period details of an employee
     *
     * @param int $payperiod_id ID of payperiod, int $employee_id user_id of employee
     */
    public function getEmployeeShiftPayperiod($pay_period_id, $employee_id, $customer_id = null)
    {
        $employeeShiftPayperiod = EmployeeShiftPayperiod::where('pay_period_id', '=', $pay_period_id)
            ->where('employee_id', '=', $employee_id)
            ->with('user.allocation.supervisor', 'payperiod', 'approved_by_user', 'customer')
            ->where(function ($q) use ($customer_id) {
                if ($customer_id !== null) {
                    $q->where('customer_id', $customer_id);
                }
            })
            ->get();
        return $employeeShiftPayperiod;
    }

    /**
     *
     *Get employee with shift details
     *
     * @param $startDate
     * @param $endDate
     * @param $customer_id
     * @return mixed
     */
    public function getEmployeeShiftDetailsByDate($startDate = null, $endDate = null, $customer_id = null, $employeeId = null)
    {
        // if only start or end date is given, filter for single day
        if ($endDate === null && $startDate !== null) {
            $endDate = $startDate;
        } else if ($startDate === null && $endDate !== null) {
            $startDate = $endDate;
        }
        // convert date to SQL datetime
        $start = date("Y-m-d H:i:s", strtotime($startDate . " 00:00:00"));
        $end = date("Y-m-d H:i:s", strtotime($endDate . " 23:59:59"));
        // if any date filter preset get payperiod
        if ($startDate !== null) {
            // Get payperiod id between start date and end date
            $pay_period_id = data_get(
                $this->payPeriodRepository->getAllActivePayPeriodsByDate($startDate, $endDate),
                "*.id"
            );
        } else {
            $pay_period_id = null;
        }
        if ($startDate === null && $endDate === null) {
            $shiftsIdInDate = null;
            $shiftsPayperiodIdInDate = null;
        } else {
            $shiftsInDate = EmployeeShift::when(
                ($startDate != null),
                function ($q) use ($start, $end) {
                    $q->whereBetween(
                        'start',
                        [$start, $end]
                    );
                }
            )->get();
            $shiftsIdInDate = array_unique(data_get($shiftsInDate, "*.id"));
            $shiftsPayperiodIdInDate = array_unique(data_get($shiftsInDate, "*.employee_shift_payperiod_id"));
        }
        $shiftsPayperiodIdFiltered = $shiftsPayperiodIdInDate;
        $shiftsPayperiodInDate = EmployeeShiftPayperiod::when(($pay_period_id !== null), function ($q) use ($pay_period_id) {
            $q->whereIn('pay_period_id', $pay_period_id);
        })->select('id', 'employee_id', 'customer_id')
            ->when(($customer_id !== null), function ($q) use ($customer_id) {
                $q->whereIn('customer_id', $customer_id);
            })
            ->when(($shiftsPayperiodIdFiltered !== null), function ($q) use ($shiftsPayperiodIdFiltered) {
                $q->whereIn('id', $shiftsPayperiodIdFiltered);
            })
            ->get();
        $shiftsPayperiodIdFiltered = data_get($shiftsPayperiodInDate, '*.id');
        if (count($shiftsPayperiodIdFiltered) > 0) {
            $shiftsPayperiodIdFiltered = array_unique(array_intersect($shiftsPayperiodIdInDate, $shiftsPayperiodIdFiltered));
        }
        $employeesDoneShiftInDate = data_get($shiftsPayperiodInDate, '*.employee_id');

        if (null !== $employeeId) {
            $filteredEmployeeId = array_unique($employeeId);
        } else {
            $filteredEmployeeId = array_unique($employeesDoneShiftInDate);
        }

        if ($shiftsPayperiodIdInDate !== null && empty($shiftsPayperiodIdInDate)) {
            $employeeShiftPayperiod = null;
        } else {
            $employeeShiftPayperiod = User::with([
                'employee_shift_payperiods' => function ($q) use ($shiftsPayperiodIdFiltered, $shiftsIdInDate) {
                    $q->when(($shiftsPayperiodIdFiltered !== null), function ($q) use ($shiftsPayperiodIdFiltered, $shiftsIdInDate) {
                        $q->with(['shifts' => function ($shiftQ) use ($shiftsIdInDate) {
                            return $shiftQ->whereIn('id', $shiftsIdInDate);
                        }])->whereIn('id', $shiftsPayperiodIdFiltered);
                    });
                },
            ])
                ->whereIn('id', $filteredEmployeeId)
                ->get();
        }
        return $employeeShiftPayperiod;
    }

    /**
     * Saving each shift details of Guards/Supervisors
     *
     * @param type $name Description
     * @return type Description     *
     */
    public function saveShiftDetails($user, $request)
    {
        $reg_working_hours;
        $ovr_working_hours;
        $sta_working_hours;
        $existing_working_hours;
        $response = array();
        $mobileSecurityPatrol_status = false;

        //$json_shift_details_array = json_decode($request->get('details'));
        $params = json_decode($request->getContent());
        if (is_string($params->details)) {
            $this->json_shift_details_array = json_decode($params->details);
        } else {
            $this->json_shift_details_array = $params->details;
        }
        foreach ($this->json_shift_details_array as $json_shift_details) {
            $payPeriod = Payperiod::whereActive(true)->where('start_date', '<=', date('Y-m-d', strtotime($json_shift_details->startTime)))
                ->where('end_date', '>=', date('Y-m-d', strtotime($json_shift_details->startTime)))->first();
            if (is_null($payPeriod)) {
                throw new Exception("No Payperiod Found");
            }

            $week = null;
            $user_id = $user->id;
            $customer_id = (int)$json_shift_details->projectId;
            $startTime = $json_shift_details->startTime;
            $employeeShiftId = $this->getCurrentShiftId($user_id, $payPeriod, $customer_id, $startTime);
            $check_active_users = $this->updateShiftStatus($user_id, $payPeriod, $customer_id);
            /*****Checking the time sheet comes in which week (first or second) of the payperiod - Start */
            $pay_period_arr = $this->checkEmployeeshiftPayperiod($json_shift_details->startTime, $payPeriod, $user_id, $customer_id);
            $employee_shift_payperiod = $pay_period_arr['employee_shift_payperiod'];
            $week = $pay_period_arr['week'];
            /*****Checking the time sheet comes in which week (first or second) of the payperiod - End */
            $holiday_list = Holiday::whereActive(true)
                ->where('holiday', date('Y-m-d', strtotime($json_shift_details->startTime)))
                ->count();

            $work_week = date('W', strtotime($json_shift_details->startTime));
            $date_obj = new DateTime($json_shift_details->startTime);
            $monday_week = $date_obj->modify('Monday this week');
            $saturday_week = clone $monday_week;
            $saturday_week->modify('Saturday this week');

            /* $sum_work_hours = EmployeeShift::selectRaw('SUM(TIME_TO_SEC(`work_hours`)) AS work_hours')
            ->whereBetween('start', [$monday_week->format('Y-m-d'), $saturday_week->format('Y-m-d')])
            ->join('employee_shift_payperiods', 'employee_shift_payperiods.id', '=', 'employee_shifts.employee_shift_payperiod_id')
            ->where(['employee_shift_payperiods.employee_id' => $user->id])
            ->first(); */

            $this->regular_wrk_hours = $this->getMaxRegularHours($payPeriod->id, $week);
            $pay_period_id = $payPeriod->id;
            if ($week) {
                $sum_work_hours = EmployeeShiftPayperiod::selectRaw('SUM(TIME_TO_SEC(`total_regular_hours`)) AS total_regular_hours')
                    ->whereActive(true)
                    ->where(['employee_id' => $user->id, 'pay_period_id' => $payPeriod->id, 'payperiod_week' => $week, 'customer_id' => $customer_id])
                    ->first();
            } else {
                $sum_work_hours = EmployeeShiftPayperiod::selectRaw('SUM(TIME_TO_SEC(`total_regular_hours`)) AS total_regular_hours')
                    ->whereActive(true)
                    ->where(['employee_id' => $user->id, 'pay_period_id' => $payPeriod->id, 'customer_id' => $customer_id])
                    ->first();
            }

            $existing_reg_hours_sec = $sum_work_hours->total_regular_hours;

            if (is_null($existing_reg_hours_sec)) {
                $existing_reg_hours_sec = 0;
            } else {
                $existing_reg_hours_sec = (int)$existing_reg_hours_sec;
            }

            $new_hours = "";
            // sterilising input
            $new_hours = $this->sterilizeTime($json_shift_details->hours);
            // Calculating total working hours for checking overtime

            $new_hours_sec = $this->getSecondsFromStr($new_hours);
            // Get new hours worked by adding existing and new
            $curr_working_hours_str = $this->addTime($existing_reg_hours_sec, $new_hours_sec, true);
            $curr_working_hours_sec = $this->getSecondsFromStr($curr_working_hours_str);
            //dd($existing_reg_hours_sec , $this->regular_wrk_hours);
            // Conditions for determining working hours
            if ($holiday_list > 0) {
                // if holiday
                $reg_working_hours = "00:00:00";
                $ovr_working_hours = "00:00:00";
                $sta_working_hours = $new_hours;
            } else if ($existing_reg_hours_sec > $this->regular_wrk_hours) {
                // overtime
                $reg_working_hours = "00:00:00";
                $ovr_working_hours = $new_hours;
                $sta_working_hours = "00:00:00";
            } else if ($curr_working_hours_sec > $this->regular_wrk_hours) {
                $remaining_reg_hrs_sec = $this->regular_wrk_hours - $existing_reg_hours_sec;
                $curr_ovr_hours_sec = $curr_working_hours_sec - $this->regular_wrk_hours;
                $reg_hr_arr = $this->getHourMinSec(0, 0, $remaining_reg_hrs_sec);
                $reg_hr_str = $this->formatedTime($reg_hr_arr['hours'], $reg_hr_arr['minutes'], $reg_hr_arr['seconds']);

                $ovr_hr_arr = $this->getHourMinSec(0, 0, $curr_ovr_hours_sec);
                $ovr_hr_str = $this->formatedTime($ovr_hr_arr['hours'], $ovr_hr_arr['minutes'], $ovr_hr_arr['seconds']);

                /* dd("regular ".$this->regular_wrk_hours,
                "already ".$existing_working_hours_sec,
                "new ".$new_hours_sec,
                "remaining reg ".$remaining_reg_hrs_sec." ".$reg_hr_str,
                "overtime ".$curr_ovr_hours_sec." ".$ovr_hr_str); */

                $reg_working_hours = $reg_hr_str;
                $ovr_working_hours = $ovr_hr_str;
                $sta_working_hours = "00:00:00";
            } else {
                // else regular
                $reg_working_hours = $new_hours;
                $ovr_working_hours = "00:00:00";
                $sta_working_hours = "00:00:00";
            }
            if (empty($employee_shift_payperiod)) {
                $employee_shift_payperiod = new EmployeeShiftPayperiod;
                $employee_shift_payperiod->assigned = (int)$json_shift_details->assigned;
                $employee_shift_payperiod->customer_id = (int)$json_shift_details->projectId;
                $employee_shift_payperiod->employee_id = $user->id;
                $employee_shift_payperiod->pay_period_id = $payPeriod->id;
                $employee_shift_payperiod->payperiod_week = $week;
                $employee_shift_payperiod->total_regular_hours = $reg_working_hours;
                $employee_shift_payperiod->total_overtime_hours = $ovr_working_hours;
                $employee_shift_payperiod->total_statutory_hours = $sta_working_hours;
                $employee_shift_payperiod->submitted = 1;
            } else {
                $existing_reg_time = $employee_shift_payperiod->total_regular_hours;
                $changed_reg_time = $reg_working_hours;
                $existing_ovr_time = $employee_shift_payperiod->total_overtime_hours;
                $changed_ovr_time = $ovr_working_hours;
                $existing_sta_time = $employee_shift_payperiod->total_statutory_hours;
                $changed_sta_time = $sta_working_hours;
                $employee_shift_payperiod->submitted = 1;
                $employee_shift_payperiod->approved = 0;

                //$employee_shift_payperiod->total_regular_hours = date("H:i:s", ($time1 + $time2));
                $employee_shift_payperiod->total_regular_hours = $this->addTime($existing_reg_time, $changed_reg_time);
                $employee_shift_payperiod->total_overtime_hours = $this->addTime($existing_ovr_time, $changed_ovr_time);
                $employee_shift_payperiod->total_statutory_hours = $this->addTime($existing_sta_time, $changed_sta_time);
            }
            $employee_shift_payperiod->notes = $json_shift_details->notes;

            if ($employee_shift_payperiod->save()) {
                $employee_shift_arr = array();
                $employee_shift_arr['employee_shift_payperiod_id'] = $employee_shift_payperiod->id;
                $employee_shift_arr['start'] = date('Y-m-d H:i:s', strtotime($json_shift_details->startTime));
                $employee_shift_arr['end'] = date('Y-m-d H:i:s', strtotime($json_shift_details->endTime));
                $employee_shift_arr['submitted'] = 1;

                $start_date = new DateTime($employee_shift_arr['start']);
                $since_start = $start_date->diff(new DateTime($employee_shift_arr['end']));

                $start_date_carbon = Carbon::createFromFormat("Y-m-d H:i:s", $employee_shift_arr['start']);
                $end_date_carbon = Carbon::createFromFormat("Y-m-d H:i:s", $employee_shift_arr['end']);
                $since_start_carbon = $start_date_carbon->diffInHours($end_date_carbon);

                $employee_shift_arr['work_hours'] =
                    str_pad($since_start_carbon, 2, '0', STR_PAD_LEFT) . ':' .
                    str_pad($since_start->i, 2, '0', STR_PAD_LEFT) . ':' .
                    str_pad($since_start->s, 2, '0', STR_PAD_LEFT);
                //$employee_shift_arr['work_hours'] = $json_shift_details->hours;
                $employee_shift_arr['notes'] = $json_shift_details->notes;
                $employee_shift_arr['live_status_id'] = UNAVAILABLE;
                $employee_shift_arr['given_end_time'] = $json_shift_details->givenEndTime;
                $employee_shift_arr['mobile_security_patrol_incident_reported'] = (bool)($json_shift_details->mobileSecurityPatrolIncidentReported ?? false);
                //$employee_shift_arr->work_hours = round(abs(strtotime($employee_shift_arr->end) - strtotime($employee_shift_arr->start)) / 60, 2);
                //$employee_shift_arr->work_hours = date('h:i:s', $diff);

                //$employee_shift_arr->work_hours = $json_shift_details->hours;
                if (isset($json_shift_details->shiftid) && !empty($json_shift_details->shiftid)) {
                    $shiftid = $json_shift_details->shiftid;
                } else if (isset($employeeShiftId) && !empty($employeeShiftId)) {
                    $shiftid = $employeeShiftId;
                    \Log::info("Offline shift handled EmployeeShiftID: " . $employeeShiftId);
                } else {
                    $shiftid = null;
                }
                $employee_shift = EmployeeShift::updateOrCreate(['id' => $shiftid], $employee_shift_arr);
                /*save meeting notes*/
                $meetingnotes = $json_shift_details->meetingNotes ?? [];
                $this->saveMeetingNotes($employee_shift->id, $meetingnotes);
                /*save meeting notes*/
                $customer_details = $this->customer->where('id', $customer_id)->first();
                if ($customer_details->time_shift_enabled == 1) {
                    $this->shiftJournalRepository->shiftEndJournal($customer_id, date('Y-m-d H:i:s', strtotime($json_shift_details->startTime)), $employee_shift->id);
                    // $this->shiftJournalRepository->shiftEndModule($customer_id, date('Y-m-d H:i:s', strtotime($json_shift_details->startTime)), $employee_shift->id);
                }
                $this->shiftJournalRepository->shiftEndModule($customer_id, date('Y-m-d H:i:s', strtotime($json_shift_details->startTime)), $employee_shift->id);

                $guardTour = $this->guardTourRepository->saveGuardTour($json_shift_details->guardTour, $employee_shift);
                if (isset($json_shift_details->shiftJournal)) {
                    $shiftJournal = $this->shiftJournalRepository->saveShiftJournal($json_shift_details->shiftJournal, $employee_shift, $customer_id, $user->id);
                }
                if (isset($json_shift_details->mobileSecurityPatrol)) {
                    $mobileSecurityPatrol_status = true;
                    $response['vehicle_id'] = isset($json_shift_details->mobileSecurityPatrol->vehicleId) ? $json_shift_details->mobileSecurityPatrol->vehicleId : null;
                    $trip = $this->tripRepository->identifyTrip($json_shift_details->mobileSecurityPatrol, $employee_shift);
                    $customer_fence_details = $this->geofenceRepository->getFenceByCustomer($customer_id);

                    $fence_arr = $this->geofenceRepository->prepareFenceCount(
                        $json_shift_details->mobileSecurityPatrol->coordinates,
                        $customer_fence_details,
                        $employee_shift->id,
                        $customer_id
                    );
                    foreach ($fence_arr as $each_fence_data) {
                        $this->fenceDataRepository->store($each_fence_data);
                    }
                    // Set the fence summary
                    $this->fenceDataRepository->setMSPFenceDetailsByShift($employee_shift->id);
                }

                if (isset($json_shift_details->qrcodeDetails)) {
                    $this->saveQrcode($employee_shift->id, $json_shift_details->qrcodeDetails);
                }
                $user = Auth::user();
                $employeeShiftPayperiods = $this->getEmployeeShiftPayperiod($pay_period_id, $user->id, $customer_id)->first();
                $this->notificationRepository->createNotification($employeeShiftPayperiods, "TIME_SHEET_SUBMITTED");
            } else {
                return false;
            }
        }
        $response['shift_id'] = $employee_shift->id;
        $response['mobileSecurityPatrol'] = $mobileSecurityPatrol_status;
        return $response;
    }

    /**
     * Submit employee shift notes
     *
     * @param type $name Description
     * @return type Description
     */
    public function saveShiftNotes($shift_id, $request)
    {
        $is_meeting = $request->isMeeting;
        $meeting_notes = $request->meetingNotes ?? [];
        if ($is_meeting == 1) {
            $updateDetails = EmployeeShift::where('id', $shift_id)->update([
                'live_status_id' => MEETING,
            ]);
        } else {
            $updateDetails = EmployeeShift::where('id', $shift_id)->update([
                'live_status_id' => AVAILABLE,
            ]);
        }
        $this->saveMeetingNotes($shift_id, $meeting_notes);
        return true;
    }

    /**
     * Submit time sheet for approval by employee
     *
     * @param type $name Description
     * @return type Description
     */
    public function submitShiftDetails($user, $payperiod_id)
    {
        $shift_payperiod_arr = EmployeeShiftPayperiod::where('pay_period_id', '=', $payperiod_id)
            ->where('employee_id', $user->id)->pluck('id');

        EmployeeShiftPayperiod::where('pay_period_id', '=', $payperiod_id)
            ->where('employee_id', $user->id)
            ->update(['submitted' => 1]);
        EmployeeShift::whereIn('employee_shift_payperiod_id', $shift_payperiod_arr)
            ->update(['submitted' => 1]);
    }

    /**
     * Approve time sheet by supervisor
     *
     * @param type $name Description
     * @return type Description
     */
    public function approveShiftDetails($request)
    {
        $request->validate([
            "employeeId.*"  => "required|exists:employees,id",
            "payPeriodId" => 'required|exists:pay_periods,id',
            "customerId" => 'required|exists:customers,id',
            "cpid.*"  => [
                'required',
                'exists:cpid_lookups,id',
                new CpidAndCustomer($request->get("customerId"))
            ],
            "cpidWorkType.*"  => [
                'required',
                'exists:employee_shift_work_hour_types,id',
                new ActivityTypeCustomerType($request->get("customerId"))
            ],
            "cpidTimes.*"  => "required|string",
            "activCode.*" => 'required|exists:work_hour_activity_code_customers,id'

        ]);
        //todo::modified function
        $payperiod_id_str_arr = $request->get('employee_shift_payperiod_ids') ?? null;
        if (isset($payperiod_id_str_arr)) {
            $payperiod_id_arr = array_map('intval', $payperiod_id_str_arr);
        }

        $arr_toapprove = array();
        $arr_approved = array();
        $arr_not_toapprove = array();
        $approve_result_obj = (object)[];
        $overtime_hrs_str = "00:00";
        $stattime_hrs_str = "00:00";
        $reportData = [];
        // declare all variables
        if (isset($payperiod_id_arr)) {
            // Fetch all details from id
            $espp_obj = EmployeeShiftPayperiod::withIds($payperiod_id_arr)->get();
            foreach ($espp_obj as $espp) {
                $emp_shift_payperiod_arr = array();
                $overtime_hrs_str = "00:00";
                $stattime_hrs_str = "00:00";
                $to_approve = true;
                $emp_shift_payperiod_arr = $espp->toArray();

                if ($emp_shift_payperiod_arr['total_overtime_hours'] !== "00:00:00") {
                    $arr_hrs = explode(':', $emp_shift_payperiod_arr['total_overtime_hours']);
                    $overtime_hrs_str = $arr_hrs[0] . ":" . $arr_hrs[1];
                    $to_approve = false;
                }

                if ($emp_shift_payperiod_arr['total_statutory_hours'] !== "00:00:00") {
                    $arr_hrs = explode(':', $emp_shift_payperiod_arr['total_statutory_hours']);
                    $stattime_hrs_str = $arr_hrs[0] . ":" . $arr_hrs[1];
                    $to_approve = false;
                }

                // if the details contain additional hours, fetch the name and hours dont add to the array
                if ($to_approve) {
                    // assign others to corresponding variables in an array

                    $req_arr = array();
                    $req_arr['payperiod_id'] = $emp_shift_payperiod_arr['pay_period_id'];
                    $req_arr['customer_id'] = $emp_shift_payperiod_arr['customer_id'];
                    $req_arr['employee_id'] = $emp_shift_payperiod_arr['employee_id'];
                    $req_arr['regular_hours'] = $this->sterilizeTime($emp_shift_payperiod_arr['total_regular_hours']);
                    $req_arr['overtime_hours'] = $this->sterilizeTime($emp_shift_payperiod_arr['total_overtime_hours']);
                    $req_arr['statutory_hours'] = $this->sterilizeTime($emp_shift_payperiod_arr['total_statutory_hours']);
                    $req_arr['approved_overtime'] = 0;
                    $req_arr['approved_stat'] = 0;
                    array_push($arr_toapprove, $req_arr);
                } else {
                    // if not to approve
                    $user_details_obj = User::find($emp_shift_payperiod_arr['employee_id']);
                    $req_arr = array();
                    $req_arr['employee_name'] = $user_details_obj->first_name;
                    $req_arr['overtime_hours'] = $overtime_hrs_str;
                    $req_arr['statutory_hours'] = $stattime_hrs_str;
                    array_push($arr_not_toapprove, $req_arr);
                }
            }
        } else {
            // assign variables from request (API and single approval) as an array
            $req_arr = array();
            $req_arr['employee_shift_payperiod_id'] = $request->get('employee_shift_payperiod_id');
            $req_arr['payperiod_id'] = $request->get('payPeriodId');
            $req_arr['customer_id'] = $request->get('customerId');
            $req_arr['assigned'] = $request->get('assigned');
            $req_arr['employee_id'] = (int)$request->get('employeeId');
            $req_arr['regular_hours'] = $this->sterilizeTime($request->get('totalRegularHours'));
            $req_arr['overtime_hours'] = $this->sterilizeTime($request->get('totalOvertimeHours'));
            $req_arr['statutory_hours'] = $this->sterilizeTime($request->get('totalStatutoryHours'));
            $req_arr['approved_overtime'] = $request->get('clientApprovedBillableOvertime') ?? 0;
            $req_arr['approved_stat'] = $request->get('clientApprovedBillableStatutory') ?? 0;
            //CR
            $req_arr['approved_regular_hours'] = $this->sterilizeTime($request->get('approvedTotalRegularHours'));
            $req_arr['approved_overtime_hours'] = $this->sterilizeTime($request->get('approvedTotalOvertimeHours'));
            $req_arr['approved_statutory_hours'] = $this->sterilizeTime($request->get('approvedTotalStatutoryHours'));
            //End CR
            //billable
            $req_arr['billable_overtime_hours'] = $this->sterilizeTime($request->get('overtimeBillableHours'));
            $req_arr['billable_statutory_hours'] = $this->sterilizeTime($request->get('statBillableHours'));
            //endbillable

            array_push($arr_toapprove, $req_arr);
        }

        $employeeShiftPayperiod = EmployeeShiftPayperiod::find($request->get('employee_shift_payperiod_id'));
        $payperiod_week = $employeeShiftPayperiod->payperiod_week;

        EmployeeShiftReportEntry::where([
            "customer_id" => (int)$request->get('customerId'),
            "user_id" => $request->get('employeeId'),
            "payperiod_id" => $request->get('payPeriodId'),
            "payperiod_week" => $payperiod_week,
            "shift_payperiod_id" => $request->get('employee_shift_payperiod_id'),
        ])->delete();

        // loop through the array
        foreach ($arr_toapprove as $req_toapprove) {
            $employeeShiftPayperiod = EmployeeShiftPayperiod::with('trashed_payperiod', 'trashed_user', 'approved_by_trashed_user')->where([
                // 'employee_id' => (int) $req_toapprove['employee_id'],
                // 'customer_id' => $req_toapprove['customer_id'],
                // 'pay_period_id' => $req_toapprove['payperiod_id'],
                'id' => $req_toapprove['employee_shift_payperiod_id'],
            ])
                ->first();
            $employeeShiftPayperiod->total_regular_hours = $this->sterilizeTime($req_toapprove['regular_hours']);
            $employeeShiftPayperiod->total_overtime_hours = $this->sterilizeTime($req_toapprove['overtime_hours']);
            $employeeShiftPayperiod->total_statutory_hours = $this->sterilizeTime($req_toapprove['statutory_hours']);
            $employeeShiftPayperiod->client_approved_billable_overtime = $req_toapprove['approved_overtime'];
            $employeeShiftPayperiod->client_approved_billable_statutory = $req_toapprove['approved_stat'];
            $employeeShiftPayperiod->approved = 1;
            $employeeShiftPayperiod->approved_by = Auth::user()->id;
            $employeeShiftPayperiod->approved_date = Carbon::now()->format('Y-m-d H:i:s');
            $employeeShiftPayperiod->is_rated = false;
            //CR approved fields.
            $employeeShiftPayperiod->approved_total_regular_hours = $this->sterilizeTime($req_toapprove['approved_regular_hours']);
            $employeeShiftPayperiod->approved_total_overtime_hours = $this->sterilizeTime($req_toapprove['approved_overtime_hours']);
            $employeeShiftPayperiod->approved_total_statutory_hours = $this->sterilizeTime($req_toapprove['approved_statutory_hours']);
            //End CR
            $employeeShiftPayperiod->billable_overtime_hours = $this->sterilizeTime($req_toapprove['billable_overtime_hours']);
            $employeeShiftPayperiod->billable_statutory_hours = $this->sterilizeTime($req_toapprove['billable_statutory_hours']);
            $employeeShiftPayperiod->save();
            array_push($arr_approved, $employeeShiftPayperiod);
            /**
             * Store employee_shift_approval_log table.
             * employee_shift_cpid table store and delete
             *  */
            $this->storeEmployeeShiftApprovalLog($request, $employeeShiftPayperiod);
        }
        $approve_result_obj->approved = $arr_approved;
        $approve_result_obj->not_approved = $arr_not_toapprove;
        $employeeShiftPayperiod = $approve_result_obj;
        return $employeeShiftPayperiod;
    }

    public function addEmployeeShiftReportEntry($approvedData, $employeeDetailBifercation)
    {
        $cpids = $employeeDetailBifercation[0];
        $cpidWorkType = $employeeDetailBifercation[1];
        $cpidTimes = $employeeDetailBifercation[2];
        $es_cpid = $employeeDetailBifercation[3];
        $activCodes = $employeeDetailBifercation[4];
        foreach ($approvedData as $Data) {
            foreach ($cpids as $key => $cpid) {
                $payperiod_id = $Data->pay_period_id;
                $payperiod_week = $Data->payperiod_week;
                $shift_payperiod_id = 0; //not resolved
                $user_id = $Data->employee_id;
                $customer_id = $Data->customer_id;
                $cpid_rate_id = $cpid; //not resolved
                $cpid_function_id = ""; //not resolved
                $cpidworktypeid = $cpidWorkType[$key];
                $work_hour_activity_code_customer_id = $activCodes[$key]; //not resolved
                $hours = "";
                $total_amount = "";
                $is_manual = 0;
                $created_by = \Auth::user()->id;
                $updated_by = \Auth::user()->id;
            }
        }
    }

    /**
     * Function to store values in Employee shift approval log
     *
     */
    public function storeEmployeeShiftApprovalLog($request, $employeeShiftPayperiod)
    {
        /**START* Put cpids & cpidTimes in a single and convet to json to store */
        $this->employeeShiftCpidRepository->deleteByEmpoyeeIdAndShiftId([
            'employee_id' => $employeeShiftPayperiod->employee_id,
            'employee_shift_payperiod_id' => $employeeShiftPayperiod->id,
        ], $request->get('es_cpid'));



        if (is_array($request->get('cpids'))) {
            foreach ($request->get('cpids') as $key => $cpid) {
                $shift_cpid_array = [];
                $shift_cpid_report_array = [];

                //update the fields
                if (isset($request->get('es_cpid')[$key])) {
                    $esCpid = EmployeeShiftCpid::find($request->get('es_cpid')[$key]);
                    if (is_object($esCpid)) {
                        $shift_cpid_array['id'] = $esCpid->id;
                        $shift_cpid_report_array['cpid_rate_id'] = $esCpid->id; //Not sure
                    }
                }
                $shift_cpid_array['employee_id'] = $employeeShiftPayperiod->employee_id;
                $shift_cpid_array['employee_shift_payperiod_id'] = $employeeShiftPayperiod->id;
                $shift_cpid_array['cpid'] = $cpid;
                $shift_cpid_array['hours'] = $request->get('cpidTimes')[$key];
                $shift_cpid_array['work_hour_type_id'] = $request->get('cpidWorkType')[$key];
                $shift_cpid_array['activity_code_id'] = $request->get('activCode')[$key];
                $functionId = CpidLookup::find($cpid)->cpid_function_id;
                $employeeShiftPayperiod = EmployeeShiftPayperiod::find($employeeShiftPayperiod->id);
                $payperiod_week = $employeeShiftPayperiod->payperiod_week;
                //dd($employeeShiftPayperiod->id);
                $shift_cpid_report_array['payperiod_id'] = $request->get('payPeriodId');
                $shift_cpid_report_array['payperiod_week'] = $payperiod_week;
                $shift_cpid_report_array['user_id'] = $request->get('employeeId');
                $shift_cpid_report_array['shift_payperiod_id'] = $employeeShiftPayperiod->id;
                $shift_cpid_report_array['customer_id'] = $request->get('customerId');
                $shift_cpid_report_array['cpid_function_id'] = $functionId;
                $shift_cpid_report_array['work_hour_type_id'] = $request->get('cpidWorkType')[$key];
                $shift_cpid_report_array['work_hour_activity_code_customer_id'] = $request->get('activCode')[$key];
                $shift_cpid_report_array['hours'] = $request->get('cpidTimes')[$key];
                $shift_cpid_report_array['is_manual'] = 0;
                $shift_cpid_report_array['created_by'] = \Auth::user()->id;

                $this->employeeShiftCpidRepository->store($shift_cpid_array);
                $this->employeeShiftCpidReportRepository->store($cpid, $shift_cpid_report_array);

                /**END* Store data to  employee Shift Cpid table */
            }
        }
        //$this->addEmployeeShiftReportEntry($employeeShiftPayperiod->approved, $employeeDetailBifercation);

        $inputs['cpid'] = json_encode($shift_cpid_array);
        /**END* Put cpids & cpidTimes in a single and convet to json to store */
        $inputs['total_regualr_hours'] = $request->get('approvedTotalRegularHours');
        $inputs['total_overtime_hours'] = $request->get('totalOvertimeHours');
        $inputs['total_statutory_hours'] = $request->get('totalStatutoryHours');
        $inputs['approved_by'] = Auth::user()->id;
        $inputs['employee_shift_payperiod_id'] = $employeeShiftPayperiod->id;
        return $this->employeeShiftApprovalLogRepository->store($inputs);
    }

    /**
     * Enter weekly performance by supervisor
     *
     * @param type $name Description
     * @return type Description
     */
    public function addWeeklyReview($weekly_performance_data, $employee_shift_payperiod_id)
    {
        if (is_array($weekly_performance_data)) {
            foreach ($weekly_performance_data as $key => $each_record) {
                $weekly_performance_data[$key]['employee_shift_payperiod_id'] = $employee_shift_payperiod_id;
            }
            EmployeeShiftWeeklyPerformance::where('employee_shift_payperiod_id', '=', $employee_shift_payperiod_id)->delete();
            EmployeeShiftWeeklyPerformance::insert($weekly_performance_data);
        }
    }

    /**
     * Approve time sheet by supervisor and enter weekly performance
     *
     * @param type $name Description
     * @return type Description
     */
    public function approveShiftDetailsAndReview($request)
    {
        // \DB::enableQueryLog();
        $payperiod_id_str_arr = null;
        $employeeShiftPayperiod = $this->approveShiftDetails($request);
        $payperiod_id_str_arr = $request->get('employee_shift_payperiod_ids') ?? null;

        //Removed weekly review
        /*if (!isset($payperiod_id_str_arr)) {
        foreach ($employeeShiftPayperiod->approved as $eachEmployeeShiftPayperiod) {
        $weekly_performance_data = json_decode($request->get('weeklyPerformance'), true);
        $this->addWeeklyReview($weekly_performance_data, $eachEmployeeShiftPayperiod->id);
        }
        }*/

        // dd(\DB::getQueryLog());

        return $employeeShiftPayperiod;
    }

    /**
     * Function to get the count of approved requests
     * @param type $request
     */
    public function getApprovedRequests()
    {
        $role_name = auth()->user()->roles->first()->name;
        $supervisor_id = auth()->user()->id;

        $allocatedcustomers = $this->customerRepository->getAllAllocatedCustomerId([auth()->user()->id]);
        $allocated_employee_ids = EmployeeAllocation::where('supervisor_id', $supervisor_id)
            ->pluck('user_id')
            ->toArray();

        $guard_requests = EmployeeShiftPayperiod::whereActive(true)
            ->when(!auth()->user()->hasAnyPermission(['admin', 'super_admin']), function ($q)
            use ($allocatedcustomers, $allocated_employee_ids, $role_name) {
                //check user hierarcy
                $q->whereIn('customer_id', $allocatedcustomers);

                if (auth()->user()->hasAnyPermission(['supervisor'])) {
                    //include recods of supervisror
                    array_push($allocated_employee_ids, auth()->user()->id);
                    $q->whereIn('employee_id', $allocated_employee_ids);
                }
            })->whereSubmitted(true);

        $total_requests = clone $guard_requests;
        $approved_requests = $guard_requests->where('approved', true)
            ->count();
        $total_requests = $total_requests->count();
        $content['approved'] = $approved_requests;
        $content['total'] = $total_requests;
        return $content;
    }

    /**
     * Function to get maximum regular hours
     * @param int $payperiod_id
     * @param default null, integer week - specifies the week of payperiod
     * @return int
     */
    public function getMaxRegularHours($payperiod_id, $week = null)
    {
        $max_regular_hours = 0;
        // Check if first week of payperiod
        if ($week == 1) {
            $payperiod_duration = PayPeriod::selectRaw('(ABS(DATEDIFF(week_one_end_date,start_date))+1) AS duration')
                ->whereActive(true)
                ->find($payperiod_id);
        } elseif ($week == 2) { // Check if second week of payperiod
            $payperiod_duration = PayPeriod::selectRaw('(ABS(DATEDIFF(end_date,week_two_start_date))+1) AS duration')
                ->whereActive(true)
                ->find($payperiod_id);
        } else { // If no week end dates are mentioned (for previous payperiods)
            $payperiod_duration = PayPeriod::selectRaw('(ABS(DATEDIFF(end_date,start_date))+1) AS duration')
                ->whereActive(true)
                ->find($payperiod_id);
        }

        if (1 <= $payperiod_duration->duration && $payperiod_duration->duration <= 7) {
            $max_regular_hours = $this->week_pay_period_sec;
        } else if (8 <= $payperiod_duration->duration && $payperiod_duration->duration <= 14) {
            $max_regular_hours = $this->biweek_pay_period_sec;
        } else if ($payperiod_duration->duration >= 15) {
            $max_regular_hours = $this->triweek_pay_period_sec;
        }
        return $max_regular_hours;
    }

    public function sterilizeTime($hours)
    {
        $time_arr = explode(':', $hours);
        if (count($time_arr) == 3) {
            $new_hours = $hours;
        } else {
            $new_hours = $hours . ":00";
        }
        return $new_hours;
    }

    /**
     * Function to add time
     * @param String $time1 - eg: "12:34:45" or 45285
     * @param String $time2 - eg: "23:34:45" or 84885
     * @param type $time_in_sec - optional - set true if input is in seconds
     * @return String HH:mm:ss
     */
    public function addTime($time1, $time2, $time_in_sec = false)
    {
        $t_arr = array();

        if ($time_in_sec) {
            $s_time_sum = $time1 + $time2;
            $t_arr = $this->getHourMinSec(0, 0, $s_time_sum);
        } else {
            $t1_arr = explode(":", $time1);
            $t2_arr = explode(":", $time2);

            $t_h_sum = $t1_arr[0] + $t2_arr[0];
            $t_m_sum = $t1_arr[1] + $t2_arr[1];
            $t_s_sum = $t1_arr[2] + $t2_arr[2];

            $t_arr = $this->getHourMinSec($t_h_sum, $t_m_sum, $t_s_sum);
        }
        return $this->formatedTime($t_arr['hours'], $t_arr['minutes'], $t_arr['seconds']);
    }

    /**
     * Function to get time in seconds from hh:mm:ss
     * @param String $s_time
     *  Time string in hh:mm:ss format
     * @return Integer
     *  Time integer in seconds
     */
    public function getSecondsFromStr($s_time)
    {
        $t_arr = explode(":", $s_time);
        $hr = ((int)$t_arr[0]) * 60 * 60;
        $min = ((int)$t_arr[1]) * 60;
        $t_sec = ((int)$t_arr[2]) + $min + $hr;
        return $t_sec;
    }

    /**
     * Get array of hours, minutes and seconds from
     * hours or minutes or seconds or combination
     * -- Can be used to convert time in any one unit to hour-minute-second
     * -- Eg:getHourMinSec(0,0,3600)
     *       returns arr['hours'] = 1 arr['minutes'] = 0 arr['seconds'] = 0
     *
     * @param int $t_hr
     *  Optional - Hours integer
     * @param type $t_min
     *  Optional - Minutes integer
     * @param type $t_sec
     *  Optional - Seconds integer
     * @return Array
     *  arr['hours'], arr['minutes'], arr['seconds']
     */
    public function getHourMinSec($t_hr = 0, $t_min = 0, $t_sec = 0)
    {
        $t_add_h = 0;
        $t_add_m = 0;

        if ($t_sec >= 60) {
            $t_add_m = floor($t_sec / 60);
            $t_sec = $t_sec % 60;
        }
        $t_min = $t_min + $t_add_m;
        if ($t_min >= 60) {
            $t_add_h = floor($t_min / 60);
            $t_min = $t_min % 60;
        }
        $t_hr = (int)floor($t_hr + $t_add_h);
        return array('hours' => $t_hr, 'minutes' => $t_min, 'seconds' => $t_sec);
    }

    /**
     * The string in HH:mm:ss format
     * @param int $hour_num
     * @param int $min_num
     * @param int $sec_num
     * @return string
     */
    public function formatedTime($hour_num, $min_num, $sec_num)
    {

        $hour_str = (string)$hour_num;
        $min_str = (string)$min_num;
        $sec_str = (string)$sec_num;

        if ($hour_num < 10) {
            $hour_str = "0" . $hour_num;
        }
        if ($min_num < 10) {
            $min_str = "0" . $min_num;
        }
        if ($sec_num < 10) {
            $sec_str = "0" . $sec_num;
        }
        return $hour_str . ":" . $min_str . ":" . $sec_str;
    }

    public function getPositionWiseGrossTime($pay_period_ids)
    {
        $inputs = $this->helper_service->getFMDashboardFilters();
        return EmployeeShiftPayperiod::whereIn('pay_period_id', $pay_period_ids)
            ->with('trashed_employee')
            ->groupBy('employee_id')
            ->where('approved', 1)
            ->where(function ($query) use ($inputs) {
                if (!empty($inputs)) {
                    //For customer_ids
                    $query->whereIn('customer_id', $inputs['customer_ids']);
                }
            })
            ->select(
                'employee_id',
                DB::raw('SUM(TIME_TO_SEC(`total_regular_hours`)) as total_regular_hours'),
                DB::raw('SUM(TIME_TO_SEC(`total_overtime_hours`)) as total_overtime_hours'),
                DB::raw('SUM(TIME_TO_SEC(`total_statutory_hours`)) as total_statutory_hours')
            )
            ->get();
    }

    /**
     * Api for  new shift
     *
     * @param type $userid request
     * @return true
     */
    public function startShift($userid, $request)
    {
        // $request =json_decode($request->getContent());
        $reg_working_hours = "00:00:00";
        $ovr_working_hours = "00:00:00";
        $sta_working_hours = "00:00:00";
        $customerId = $request->customerId;
        $startTime = $request->startTime ?? \Carbon\Carbon::now()->format('Y-m-d H:i');
        $assigned = $request->assigned;
        $payPeriod = Payperiod::whereActive(true)->where('start_date', '<=', date('Y-m-d', strtotime($startTime)))
            ->where('end_date', '>=', date('Y-m-d', strtotime($startTime)))->first();
        if (is_null($payPeriod)) {
            throw new Exception("No Payperiod Found");
        }
        $check_active_users = $this->updateShiftStatus($userid, $payPeriod, $customerId);
        $pay_period_arr = $this->checkEmployeeshiftPayperiod($startTime, $payPeriod, $userid, $customerId, $assigned);
        $employee_shift_payperiod = $pay_period_arr['employee_shift_payperiod'];
        $week = $pay_period_arr['week'];
        // check if employee shift already exist? if yes return
        if (!empty($employee_shift_payperiod)) {
            $employee_shift_exist = EmployeeShift::where('employee_shift_payperiod_id', $employee_shift_payperiod->id)
                ->where('start', date('Y-m-d H:i:s', strtotime($startTime)))
                ->whereHas('shift_payperiod', function ($query) use ($customerId) {
                    $query->where('customer_id', $customerId);
                    $query->where('employee_id', \Auth::id());
                })->first();
            if (!empty($employee_shift_exist)) {
                return $employee_shift_exist;
            }
        }

        if (empty($employee_shift_payperiod)) {
            $employee_shift_payperiod = new EmployeeShiftPayperiod;
            $employee_shift_payperiod->assigned = $assigned;
            $employee_shift_payperiod->customer_id = $customerId;
            $employee_shift_payperiod->employee_id = $userid;
            $employee_shift_payperiod->pay_period_id = $payPeriod->id;
            $employee_shift_payperiod->payperiod_week = $week;
            $employee_shift_payperiod->total_regular_hours = $reg_working_hours;
            $employee_shift_payperiod->total_overtime_hours = $ovr_working_hours;
            $employee_shift_payperiod->total_statutory_hours = $sta_working_hours;
            $employee_shift_payperiod->notes = '';
        }
        if ($employee_shift_payperiod->save()) {

            $employee_shift = new EmployeeShift;
            $employee_shift->employee_shift_payperiod_id = $employee_shift_payperiod->id;
            $employee_shift->start = date('Y-m-d H:i:s', strtotime($startTime));
            $employee_shift->end = null;
            $employee_shift->work_hours = '00:00:00';
            $employee_shift->notes = '';
            $employee_shift->live_status_id = AVAILABLE;

            if (isset($request->shiftTypeId)) {
                $employee_shift->shift_type_id = $request->shiftTypeId;
            }
            $employee_shift->save();
            if (($employee_shift->id != 0) && ((isset($request->qrPatrolEnabled)) && $request->qrPatrolEnabled == true)) {
                $this->saveQRcodeSummary($customerId, $employee_shift->id, null);
            }
            if (($employee_shift->id != 0) && ($employee_shift_payperiod->id != 0)) {
                $scheduleDetails = $this->schedulingRepository->fetchScheduleComplianceDetails($employee_shift->start, [$employee_shift_payperiod->pay_period_id], [$customerId], [$userid]);
                $scheduleId = isset($scheduleDetails->employee_schedule_id) ? $scheduleDetails->employee_schedule_id : null;
                $scheduleTimeLogId = isset($scheduleDetails->id) ? $scheduleDetails->id : null;
                $this->schedulingRepository->updateEmployeeShiftPayperiodByEmployeeScheduleId($scheduleId, $employee_shift_payperiod->id);
                $this->schedulingRepository->updateEmployeeShiftByEmployeeScheduleTimeLogId($scheduleTimeLogId, $employee_shift->id);
            }
            return $employee_shift;
        } else {
            return false;
        }
        return true;
    }

    /**
     * Checking the time sheet comes in which week (first or second) of the payperiod
     *
     * @param type $startTime payPeriod userid customerId
     * @return type array     *
     */
    public function checkEmployeeshiftPayperiod($startTime, $payPeriod, $userid, $customerId)
    {

        if (isset($payPeriod->week_one_end_date) && isset($payPeriod->week_one_end_date)) {
            $shift_start_time = strtotime($startTime);
            $start_date = strtotime($payPeriod->start_date);
            $week_one_end_date = strtotime($payPeriod->week_one_end_date . '23:59:59');
            $week_two_start_date = strtotime($payPeriod->week_two_start_date);
            $end_date = strtotime($payPeriod->end_date . '23:59:59');

            if ($shift_start_time >= $start_date && $shift_start_time <= $week_one_end_date) {
                $week = 1;
            }
            if ($shift_start_time >= $week_two_start_date && $shift_start_time <= $end_date) {
                $week = 2;
            }
            $employee_shift_payperiod = EmployeeShiftPayperiod::where([
                'employee_id' => $userid,
                'customer_id' => (int)$customerId,
                'payperiod_week' => $week,
                'pay_period_id' => $payPeriod->id
            ])->orderBy('id', 'desc')->first();
        } else {

            $employee_shift_payperiod = EmployeeShiftPayperiod::where([
                'employee_id' => $userid,
                'customer_id' => (int)$customerId,
                'pay_period_id' => $payPeriod->id
            ])->orderBy('id', 'desc')->first();
        }
        $pay_period_arr = ['employee_shift_payperiod' => $employee_shift_payperiod, 'week' => $week];
        return $pay_period_arr;
    }

    /**
     * Api for end  shift
     *
     * @param type $endtime shiftid
     * @return true
     */
    public function shiftEntryDetails($customer_id = null)
    {
        $user = \Auth::user();
        $employees = [];
        if ($customer_id != null) {
            $today = EmployeeShift::wherein('live_status_id', [AVAILABLE, MEETING])
                ->whereHas('shift_payperiod', function ($query) use ($customer_id) {
                    return $query->where('customer_id', $customer_id);
                })->with(['shift_payperiod', 'shift_payperiod.user'])->get();
            $todays_shift = data_get($today, "*.shift_payperiod.user.id");
            $allocated_employees = CustomerEmployeeAllocation::where('customer_id', $customer_id)->pluck('user_id')->toArray();
            $employees = array_merge($todays_shift, $allocated_employees);
        }

        $result = User::whereIn('id', $employees)
            ->with(['employee_shift_payperiods' => function ($shift_payperiod_query) use ($customer_id) {
                $shift_payperiod_query->when(($customer_id != null), function ($shift_payperiod_query) use ($customer_id) {
                    $shift_payperiod_query->where('customer_id', $customer_id);
                });
                $shift_payperiod_query->whereHas('availableShift', function ($query) {
                    return $query->wherein('live_status_id', [AVAILABLE, MEETING])->with('availableShift');
                });
                $shift_payperiod_query->orderBy('created_at', 'DESC')->with('availableShift');
            }])->get();

        $shift_available = $shift_notin_emp = $shift_meeting = $shift_unavailable = [];
        foreach ($result as $data) {
            if (count($data['employee_shift_payperiods']) > 0) {
                $unique_arr = array();
                foreach ($data['employee_shift_payperiods'] as $val) {

                    if ($val['availableShift']['live_status_id'] == 1) {
                        if (!in_array($data['id'], $unique_arr)) {
                            $unique_arr[] = $data['id'];
                            $arr['id'] = $data['id'];
                            $arr['first_name'] = $data['first_name'] . ' ' . $data['last_name'];
                            $arr['live_status'] = $val['availableShift']['live_status_id'];
                            $shift_available[] = $arr;
                        }
                    }
                    if ($val['availableShift']['live_status_id'] == 2) {
                        if (!in_array($data['id'], $unique_arr)) {
                            $unique_arr[] = $data['id'];
                            $arr2['id'] = $data['id'];
                            $arr2['first_name'] = $data['first_name'] . ' ' . $data['last_name'];
                            $arr2['live_status'] = $val['availableShift']['live_status_id'];
                            $shift_meeting[] = $arr2;
                        }
                    }
                    if ($val['availableShift']['live_status_id'] == 3) {
                        if (!in_array($data['id'], $unique_arr)) {
                            $unique_arr[] = $data['id'];
                            $arr3['id'] = $data['id'];
                            $arr3['first_name'] = $data['first_name'] . ' ' . $data['last_name'];
                            $arr3['live_status'] = $val['availableShift']['live_status_id'];
                            $shift_unavailable[] = $arr3;
                        }
                    }
                }
            } else {
                $arr1['id'] = $data['id'];
                $arr1['first_name'] = $data['first_name'] . ' ' . $data['last_name'];
                $arr1['live_status'] = '';
                $shift_notin_emp[] = $arr1;
            }
        }
        $result = array_merge($shift_available, $shift_meeting, $shift_unavailable, $shift_notin_emp);
        return $result;
    }

    /**
     * Api for end  shift
     *
     * @param type $endtime shiftid
     * @return true
     */

    public function dailyShiftDetails($customerid)
    {

        $user = \Auth::user();
        $employees = [];
        $shift_today = [];

        // if ($user->can('view_allocated_employees_live_status')) {
        //         $employees = $this->employeeAllocationRepository->getEmployeeIdAssigned($user->id);
        // }else if($user->can('view_all_employees_live_status')) {
        //         $employees = User::pluck('id');
        // }else{
        //     $employees = [];
        // }
        //if(isset($employees) && !empty($employees)){
        $shift_today = $this->model->orderBy(DB::raw('live_status_id IS NULL, live_status_id'), 'asc')
            ->with(['latest_meeting_note'])
            ->whereHas('shift_payperiod', function ($q) use ($customerid) {
                // $q->whereIn('employee_id',$employees);
                $q->where('customer_id', $customerid);
            })->with(['shift_payperiod', 'shift_payperiod.user'])
            ->get();
        //}
        //dd($employees);
        return $shift_today;
    }

    /**
     * Api for end  shift
     *
     * @param type $endtime shiftid
     * @return true
     */
    public function endShift($shift_id)
    {
        $end_time = \Carbon\Carbon::now()->format('Y-m-d H:i');
        $arr = ['end' => $end_time, 'live_status_id' => UNAVAILABLE];
        EmployeeShift::where('id', $shift_id)->update($arr);
        $shift = $this->get($shift_id);
        return $shift;
    }

    /**
     * Save meeting notes
     *
     * @param type $meeting_note array
     * @return type true
     */
    public function saveMeetingNotes($shift_id, $notes)
    {
        if (!empty($notes)) {
            foreach ($notes as $each_note_obj) {
                if (isset($each_note_obj->notes) && !empty($each_note_obj->notes)) {
                    $notes = $each_note_obj->notes;
                    $time = $each_note_obj->time ?? \Carbon\Carbon::now()->format('Y-m-d H:i:s');
                    ShiftMeetingNote::create([
                        'shift_id' => $shift_id,
                        'time' => $time,
                        'note' => $notes,

                    ]);
                }
            }
        }
        return true;
    }

    /**
     * Display details of single shift
     *
     * @param $id
     * @return object
     */
    public function get($id)
    {
        return $this->model->find($id);
    }

    /**
     * Save qrcode
     *
     * @param type $qrcode array
     * @return type true
     */
    public function saveQrcode($shift_id, $request)
    {
        try {
            $qrDetails = $request;
            $status = 0;
            $user_id = \Auth::user()->id;
            if (!empty($qrDetails)) {
                foreach ($qrDetails as $each_qrcode) {
                    $customerId = $each_qrcode->customer_id;
                    $qrcode_details = CustomerQrcodeLocation::select('id')->where('customer_id', $each_qrcode->customer_id)->where('qrcode', $each_qrcode->qrcode_id)->first();
                    if (!empty($qrcode_details)) {
                        $qrCodeAttempts = $this->fetchQrCodeNumberOfAttemptsByShiftId($shift_id, $qrcode_details);
                        $result = CustomerQrcodeWithShift::firstOrCreate([
                            'user_id' => $user_id,
                            'qrcode_id' => $qrcode_details->id,
                            'customer_id' => $each_qrcode->customer_id,
                            'latitude' => isset($each_qrcode->latitude) ? $each_qrcode->latitude : null,
                            'longitude' => isset($each_qrcode->longitude) ? $each_qrcode->longitude : null,
                            'no_of_attempts' => $qrCodeAttempts['numberOfAttempts'],
                            'comments' => $each_qrcode->comments,
                            'shift_id' => $shift_id,
                            'time' => $each_qrcode->time ?? \Carbon\Carbon::now()->format('Y-m-d H:i:s'),
                            // 'image' => isset($each_qrcode->image) ? $this->imageRepository->saveImage($each_qrcode->image, $shift_id) : null
                        ]);
                        if ($result && (!empty($each_qrcode->image))) {
                            foreach ($each_qrcode->image as $eachimage) {

                                $imagefile = $this->imageRepository->imageFromBase64($eachimage);
                                $attachment_id = $this->attachmentRepository->saveBase64ImageFile('qr-patrol', $result, $imagefile);
                                $this->customerQrcodeAttachment->create(
                                    [
                                        'qrcode_with_shift_id' => $result->id,
                                        'attachment_id' => $attachment_id,
                                    ]
                                );
                            }
                        }
                        $status = 1;
                        $this->saveQRcodeSummary($each_qrcode->customer_id, $shift_id, $qrcode_details->id);
                    } else {
                        $status = 2;
                    }
                }

                return $status;
            } else {
                return $status = 0;
            }
        } catch (\Exception $e) {
            \Log::info(" Error : " . $e);
            return $status = 0;
        }
    }

    public function secToTimeString($seconds)
    {
        $hours = floor($seconds / 3600);
        $mins = floor($seconds / 60 % 60);
        if ($hours < 10) {
            $hours = '0' . $hours;
        }
        if ($mins < 10) {
            $mins = '0' . $mins;
        }
        return $hours . ':' . $mins;
    }

    public function prepareCpidDataforExport($cpids)
    {
        $results = [];
        $distinctIds = [];
        foreach ($cpids as $cpid_var) {
            if (!in_array($cpid_var->cpid, $distinctIds)) {
                array_push($distinctIds, $cpid_var->cpid);
            }
        }
        foreach ($distinctIds as $distictId) {
            $regularTime = 0;
            $overTime = 0;
            $statTime = 0;
            $position = '';
            $cpidName = '';
            $out = array_fill(0, 12, null);
            foreach ($cpids as $cpid) {
                if ($distictId !== $cpid->cpid) {
                    continue;
                }

                $cpidName = $cpid->cpid_lookup_with_trash->cpid;
                $position = $cpid->cpid_lookup_with_trash->position->position;

                if ($cpid->work_hour_type_id === 1) {
                    $regularTime += $this->helper_service->strTimeToSeconds($cpid->hours);
                } else if ($cpid->work_hour_type_id === 2) {
                    $overTime += $this->helper_service->strTimeToSeconds($cpid->hours);
                } else if ($cpid->work_hour_type_id === 3) {
                    $statTime += $this->helper_service->strTimeToSeconds($cpid->hours);
                }
            }
            $out = array_merge($out, [
                $cpidName,
                $position,
                $this->secToTimeString($regularTime),
                $this->secToTimeString($overTime),
                $this->secToTimeString($statTime),
            ]);
            $results[] = $out;
        }
        return $results;
    }

    public function prepareTimesheetExport($esps)
    {
        $results = [];
        $body = [];
        $headerMain = [
            'Employee ID',
            'Employee Name',
            'Role',
            'Project Number',
            'Client',
            'Pay Period',
            'Start Date',
            'End Date',
            'Total Regular Hours',
            'Total Ovetime Hours',
            'Total Stat Hours',
            'Notes',
            'CPID',
            'Position',
            'Regular Hours',
            'Overtime Hours',
            'Stat Hours',
        ];
        foreach ($esps as $esp) {
            $body[] = [
                $esp->trashed_user->trashed_employee_profile->employee_no,
                $esp->trashed_user->first_name . ' ' . $esp->trashed_user->last_name,
                $esp->trashed_user->roles->first()->name,
                $esp->trashed_customer->project_number,
                $esp->trashed_customer->client_name,
                $esp->trashed_payperiod->pay_period_name,
                $esp->trashed_payperiod->start_date,
                $esp->trashed_payperiod->end_date,
                $esp->total_regular_hours,
                $esp->total_overtime_hours,
                $esp->total_statutory_hours,
                $esp->notes,
            ];
            if (!empty($esp->cpids)) {
                $body = array_merge($body, $this->prepareCpidDataforExport($esp->cpids));
            }
        }
        $results[] = $headerMain;
        $results = array_merge($results, $body);
        return $results;
    }
    public function prepareTimesheetVisionExport($filters = [])
    {
        $qry = EmployeeShiftReportEntry::with(
            'user',
            'user.roles',
            'user.trashedEmployee',
            'customer',
            'cpidRate',
            'cpidFunction',
            'workHourActivityCodeCustomer',
            'payPeriod'
        );

        //Filter by customer
        if (!empty($filters['customer'])) {
            $qry->where('customer_id', '=', $filters['customer']);
        }

        //Filter by payperiod
        if (!empty($filters['payperiod'])) {
            $qry->where('payperiod_id', '=', $filters['payperiod']);
        }

        //Filter by week
        if (!empty($filters['week'])) {
            $qry->where('payperiod_week', '=', $filters['week']);
        }

        //Filter by employee
        if (!empty($filters['employee'])) {
            $qry->where('user_id', '=', $filters['employee']);
        }

        //Filter by type
        if (isset($filters['is_manual'])) {
            if ($filters['is_manual'] == 1) {
                $qry->where('is_manual', '=', 1);
            } else if ($filters['is_manual'] == 0) {
                $qry->where('is_manual', '=', 0);
            }
        }

        //Get results
        $esrs = $qry->get();

        return $esrs;
    }

    public function updateShiftStatus($userid, $payPeriod, $customerId)
    {
        // check user start the shift or not , if started then delete the shift and insert new shift //
        $check_active_user = EmployeeShift::whereIn('live_status_id', [AVAILABLE, MEETING])
            ->whereHas('shift_payperiod', function ($query) use ($userid, $customerId, $payPeriod) {
                $query->where('employee_id', $userid);
                $query->where('customer_id', $customerId);
                //$query->where('pay_period_id',$payPeriod->id);
            })->pluck('id');
        EmployeeShift::whereIn('id', $check_active_user)->update(['live_status_id' => UNAVAILABLE]);
    }

    /**
     * @param $userid
     * @param $payPeriod
     * @param $customerId
     */
    public function getCurrentShiftId($userid, $payPeriod, $customerId, $startTime)
    {
        $shiftId = EmployeeShift::whereHas(
            'shift_payperiod',
            function ($query) use ($userid, $customerId, $payPeriod, $startTime) {
                $query->where('employee_id', $userid);
                $query->where('customer_id', $customerId);
                $query->where('pay_period_id', $payPeriod->id);
                $query->whereNull('end');
            }
        )
            ->where('start', $startTime)
            ->pluck('id')
            ->first();
        return $shiftId;
    }

    /**
     * Get user_ids of active shift employes.
     *
     * @param  $customer_id
     * @return user ids
     */

    public function getActiveShiftEmployes($customer_id = null, $shift_type_id = null, $live_status_id = null)
    { //dd($customer_id ,$shift_type_id,$live_status_id);
        $active_shift_employees = EmployeeShift:: //whereDate('start', date('Y-m-d'))
            where('live_status_id', '!=', UNAVAILABLE)
            //TODO : Remove avove commented line, function must return end vale null records.
            ->when(!empty($live_status_id), function ($que) use ($live_status_id) {
                return $que->wherein('live_status_id', $live_status_id);
            })
            ->when(!empty($shift_type_id), function ($q) use ($shift_type_id) {
                return $q->whereIn('shift_type_id', $shift_type_id);
            })->whereHas('shift_payperiod', function ($query) use ($customer_id) {
                if (!empty($customer_id)) {
                    return $query->whereIn('customer_id', $customer_id);
                }
            })->with(['shift_payperiod', 'shift_payperiod.user'])
            ->get();

        $user_ids = data_get($active_shift_employees, "*.shift_payperiod.user.id");

        return $user_ids;
    }

    function saveQRcodeSummary($customerId, $shiftId, $qrCodeID)
    {

        $isWeekDay = false;
        $is_shift_present = CustomerQrcodeSummary::where('qrcode_id', $qrCodeID)->where('shift_id', $shiftId)->first();

        if (!empty($is_shift_present)) {
            $qrCodeAttempts = $this->fetchQrCodeNumberOfAttemptsByShiftId($shiftId, '', $qrCodeID);
            $isWeekDay = $qrCodeAttempts['isWeekDay'];
            $expected_count = $is_shift_present->expected_attempts;
            $curr_count = $is_shift_present->total_count;
            $total_count = $curr_count + 1;
            $missed_percent = ($expected_count > 0) ? (($total_count < $expected_count) ? (($total_count * 100) / $expected_count) : 100) : 0;
            $data = [
                'total_count' => $total_count,
                'missed_count_percentage' => $missed_percent,
            ];
            CustomerQrcodeSummary::where('id', $is_shift_present->id)->update($data);
        } else {
            $check_cus_atemmpts = CustomerQrcodeLocation::where('customer_id', $customerId)->get();

            if (!empty($check_cus_atemmpts)) {
                foreach ($check_cus_atemmpts as $each_row) {

                    //fetch no of attempts by shift
                    $qrCodeAttempts = $this->fetchQrCodeNumberOfAttemptsByShiftId($shiftId, $each_row);
                    $isWeekDay = $qrCodeAttempts['isWeekDay'];

                    CustomerQrcodeSummary::create([
                        'shift_id' => $shiftId,
                        'qrcode_id' => $each_row->id,
                        'expected_attempts' => $qrCodeAttempts['numberOfAttempts'],
                        'total_count' => 0,
                        'missed_count_percentage' => 0,
                    ]);
                }
            }
        }

        $scanned = $this->qrcodeLocationRepository->get_qrcode_count_for_each_shift($shiftId);
        $assigned = $this->qrcodeLocationRepository->total_qrcode_count_by_customer($customerId, $isWeekDay);
        $missed = $assigned - $scanned;
        $status = CustomerQrcodeHistory::updateOrCreate(
            [
                'shift_id' => $shiftId,
            ],
            [
                'scanned' => $scanned,
                'missed' => $missed,
            ]
        );
    }

    /*
     *fetch qr code number of attempts by shift
     */
    public function fetchQrCodeNumberOfAttemptsByShiftId($shiftId, $qrCodeLocationObject = '', $qrCodeLocationId = '', $total = false)
    {
        $result = ['numberOfAttempts' => 0, 'isWeekDay' => false];

        if (empty($qrCodeLocationObject) && empty($qrCodeLocationId)) {
            return $result;
        }

        if (empty($qrCodeLocationObject)) {
            $qrCodeLocationObject = CustomerQrcodeLocation::find($qrCodeLocationId);
        }
        //fetch shift by id
        $employeeShift = EmployeeShift::find($shiftId);
        if (!empty($employeeShift)) {
            //fetch day name
            $dayString = \Carbon\Carbon::parse($employeeShift->start)->format('l');
            if ($dayString == "Saturday" || $dayString == "Sunday") {
                $noOfAttempts = ($total) ? $qrCodeLocationObject->tot_no_of_attempts_week_ends : $qrCodeLocationObject->no_of_attempts_week_ends;
                $result = ['numberOfAttempts' => $noOfAttempts ? $noOfAttempts : 0, 'isWeekDay' => true];
            } else {
                $noOfAttempts = ($total) ? $qrCodeLocationObject->tot_no_of_attempts_week_day : $qrCodeLocationObject->no_of_attempts;
                $result = ['numberOfAttempts' => $noOfAttempts ? $noOfAttempts : 0, 'isWeekDay' => false];
            }
        }
        return $result;
    }


    /*
     *fetch qr code number of attempts by shift
     */
    public function fetchQrCodeNumberOfAttemptsByShiftIdFromCollection(
        $qrCodeLocation,
        $employeeShifts,
        $shiftId,
        $qrCodeLocationObject = '',
        $qrCodeLocationId = '',
        $total = false
    ) {
        $result = ['numberOfAttempts' => 0, 'isWeekDay' => false];

        if (empty($qrCodeLocationObject) && empty($qrCodeLocationId)) {
            return $result;
        }

        if (empty($qrCodeLocationObject)) {
            $qrCodeLocationObject = $qrCodeLocation;
        }
        //fetch shift by id
        $employeeShift = $employeeShifts;
        if (!empty($employeeShift)) {
            //fetch day name
            $dayString = \Carbon\Carbon::parse($employeeShift->start)->format('l');
            if ($dayString == "Saturday" || $dayString == "Sunday") {
                $noOfAttempts = ($total) ? $qrCodeLocationObject->tot_no_of_attempts_week_ends : $qrCodeLocationObject->no_of_attempts_week_ends;
                $result = ['numberOfAttempts' => $noOfAttempts ? $noOfAttempts : 0, 'isWeekDay' => true];
            } else {
                $noOfAttempts = ($total) ? $qrCodeLocationObject->tot_no_of_attempts_week_day : $qrCodeLocationObject->no_of_attempts;
                $result = ['numberOfAttempts' => $noOfAttempts ? $noOfAttempts : 0, 'isWeekDay' => false];
            }
        }
        return $result;
    }

    public function endAllEmployeeShiftsExceedsDuration()
    {
        $end_time = \Carbon\Carbon::now()->format('Y-m-d H:i');
        $limit = SiteSettings::select('shift_duration_limit')->first();
        if ($limit->shift_duration_limit > Config::get('globals.shift_minimum_duration_limit')) {
            $arr = [
                'end' => $end_time,
                'live_status_id' => UNAVAILABLE,
                'notes' => 'Shift ended by System at ' . $end_time . '. Exceeded shift duration limit (' . $limit->shift_duration_limit . 'hrs).',
            ];

            $employeeShiftIdArr = EmployeeShift::where('end', '=', null)
                ->where(\DB::raw('TIMESTAMPDIFF(HOUR, start, "' . Carbon::now() . '")'), '>=', $limit->shift_duration_limit)
                ->get()
                ->pluck('id');
            $exceeded_shifts = EmployeeShift::whereIn('id', $employeeShiftIdArr)
                ->update($arr);
            return $exceeded_shifts;
        }
        return 0;
    }

    /**
     * Function to prepare and give attachment path array
     * @param $request
     * @return array
     */
    public static function getAttachmentPathArr($request)
    {
        return array(config('globals.qr_patrol_attachment_folder'), $request->id);
    }

    /**
     * Static function to return path as an array when file name is given
     * @param $file_id
     * @return array
     */
    public static function getAttachmentPathArrFromFile($file_id)
    {
        $attachment = CustomerQrcodeAttachment::where('attachment_id', $file_id)->first();

        if (isset($attachment)) {
            $qr_entry_id = $attachment->qrcode_with_shift_id;
        }
        return array(config('globals.qr_patrol_attachment_folder'), $qr_entry_id);
    }

    public function getPayperiodDetailsFromEmployeeShiftPayperiods($id)
    {
        $payperiodDetails = EmployeeShiftPayperiod::where('id', $id)->with(['payperiod'])->first();
        $payperiodIds = isset($payperiodDetails->pay_period_id) ? $payperiodDetails->pay_period_id : null;
        return $payperiodIds;
    }

    public function getEmployeeShiftPayperiodDetailsByEmployeeId($request)
    {
        $payperiodDetails =  EmployeeShiftPayperiod::with(['user','cpids','customer','approved_by_user'])->where([
            ['employee_id', '=', $request['user_id']],
            ['pay_period_id', '=', $request['pay_period_id']],
            ['approved', '=', TRUE]
        ])->get();
        return $payperiodDetails;
    }

    public function getTotalHours($timesheetDetails)
    {
        if(!empty($timesheetDetails)){
            $employee_total_amount = 0;
            $total_hours = 0;
            foreach($timesheetDetails as $key => $details){
                if(!empty($details->cpids)){
                    $sum = 0;
                    $total_hours_sec = 0;
                    foreach ($details->cpids as $item) {
                        $sum += $item->total_amount;
                        $total_hours_sec += $this->helper_service->strTimeToSeconds($item->hours);
                    }
                }
                $total_hours = $total_hours+$total_hours_sec;
                $employee_total_amount += $sum;
            }
            $employee_total_hours = floor($total_hours / 3600).":".floor(($total_hours / 60) % 60);
            return ["total_amount" => number_format((float)$employee_total_amount, 2, '.', ''),"total_hours" => $employee_total_hours,"total_seconds"=>$total_hours];
        }else{
            return false;
        }
    }

    public function getTimesheetCustomerDetails($timesheetDetails)
    {
        if (!empty($timesheetDetails)) {
            $eachArr = [];
            foreach ($timesheetDetails as $key => $value) {
                $totalSum = 0;
                if (!isset($eachArr[$value->trashed_customer->id])) {
                    $eachArr[$value->trashed_customer->id]["id"] = $value->trashed_customer->id;
                    $eachArr[$value->trashed_customer->id]["project_name"] = $value->trashed_customer->client_name;
                    $eachArr[$value->trashed_customer->id]["project_number"] = $value->trashed_customer->project_number;
                    if(!empty($value->cpids)){
                        $sum = 0;
                        $total_hours_sec = 0;
                        foreach ($value->cpids as $item) {
                            $sum += $item->total_amount;
                            $total_hours_sec += $this->helper_service->strTimeToSeconds($item->hours);
                        }
                    }
                    $eachArr[$value->trashed_customer->id]["total_hours"] = (int)floor($total_hours_sec / 3600);
                    $eachArr[$value->trashed_customer->id]["total_minutes"] = floor(($total_hours_sec / 60) % 60);
                    $eachArr[$value->trashed_customer->id]["total_seconds"] = $total_hours_sec;
                    $eachArr[$value->trashed_customer->id]["total_earning"] =  number_format((float)$sum, 2, '.', '');
                }else{
                    if(!empty($value->cpids)){
                        $sum = 0;
                        $total_hours_sec = 0;
                        foreach ($value->cpids as $item) {
                            $sum += $item->total_amount;
                            $total_hours_sec += $this->helper_service->strTimeToSeconds($item->hours);
                        }
                    }
                    $eachArr[$value->trashed_customer->id]["total_hours"] += (int)floor($total_hours_sec / 3600);
                    $eachArr[$value->trashed_customer->id]["total_minutes"] += floor(($total_hours_sec / 60) % 60);
                    $eachArr[$value->trashed_customer->id]["total_seconds"] += $total_hours_sec;
                    $eachArr[$value->trashed_customer->id]["total_earning"] += number_format((float)$sum, 2, '.', '');
                }
            }
            return $eachArr;
        }else{
            return false;
        }
    }
}
