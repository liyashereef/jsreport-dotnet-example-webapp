<?php

namespace Modules\Timetracker\Http\Controllers;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use App\Services\HelperService;
use Illuminate\Routing\Controller;
use Modules\Admin\Models\Customer;
use Modules\Admin\Models\CustomerEmployeeAllocation;
use Modules\Admin\Models\EmployeeAllocation;
use Modules\Admin\Models\PayPeriod;
use Modules\Admin\Repositories\CpidCustomerAllocationRepository;
use Modules\Admin\Repositories\CustomerEmployeeAllocationRepository;
use Modules\Admin\Repositories\CustomerRepository;
use Modules\Timetracker\Models\EmployeeShiftPayperiod;
use Modules\Timetracker\Models\Notification;
use Modules\Timetracker\Models\WorkHourActivityCodeCustomer;
use Modules\Timetracker\Repositories\EmployeeShiftRepository;
use Modules\Timetracker\Repositories\EmployeeShiftWorkHourTypeRepository;
use Modules\Timetracker\Repositories\NotificationRepository;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Modules\Timetracker\Jobs\TimeSheetApprovalRating;
use Modules\Timetracker\Repositories\EmployeeShiftAprovalRatingRepository;
use Modules\Timetracker\Repositories\EmployeeShiftCpidRepository;
use App\Repositories\MailQueueRepository;
use Modules\Timetracker\Jobs\TimeSheetApprovalReminder;
use Modules\Timetracker\Models\TimeSheetApprovalConfiguration;
use App\Exports\CompassExport;
use Illuminate\Support\Facades\Config;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Admin\Repositories\UserRepository;
use Modules\Employeescheduling\Models\EmployeeSchedule;
use Modules\Employeescheduling\Models\EmployeeScheduleTimeLog;

class TimesheetApprovalController extends Controller
{

    /**
     * The NotificationRepository instance.
     *
     * @var \App\Repositories\NotificationRepository
     */
    protected $employeeShiftRepository;
    protected $notificationRepository;
    protected $cpidCustomerAllocationRepository;
    protected $employeeShiftWorkHourTypeRepository;
    protected $customerRepository;
    protected $customerEmployeeAllocationRepository;
    protected $employeeShiftAprovalRatingRepository;
    protected $mailQueueRepository;
    protected $helperService;
    protected $employeeShiftCpidRepository;

    public function __construct(
        EmployeeShiftRepository $employeeShiftRepository,
        NotificationRepository $notificationRepository,
        CpidCustomerAllocationRepository $cpidCustomerAllocationRepository,
        EmployeeShiftWorkHourTypeRepository $employeeShiftWorkHourTypeRepository,
        CustomerRepository $customerRepository,
        CustomerEmployeeAllocationRepository $customerEmployeeAllocationRepository,
        EmployeeShiftAprovalRatingRepository $employeeShiftAprovalRatingRepository,
        MailQueueRepository $mailQueueRepository,
        HelperService $helperService,
        EmployeeShiftCpidRepository $employeeShiftCpidRepository,
        UserRepository $userRepository
    ) {
        $this->employeeShiftRepository = $employeeShiftRepository;
        $this->notificationRepository = $notificationRepository;
        $this->cpidCustomerAllocationRepository = $cpidCustomerAllocationRepository;
        $this->employeeShiftWorkHourTypeRepository = $employeeShiftWorkHourTypeRepository;
        $this->customerRepository = $customerRepository;
        $this->helperService = $helperService;
        $this->customerEmployeeAllocationRepository = $customerEmployeeAllocationRepository;
        $this->employeeShiftAprovalRatingRepository = $employeeShiftAprovalRatingRepository;
        $this->mailQueueRepository = $mailQueueRepository;
        $this->employeeShiftCpidRepository = $employeeShiftCpidRepository;
        $this->userRepository = $userRepository;
    }

    /* Get Timesheet Detailed View */

    public function detailedview($id)
    {
        $supervisor_id = auth()->user()->id;
        $role_name = auth()->user()->roles->first()->name;
        $allocated_customer_ids = $this->customerEmployeeAllocationRepository->getAllocatedCustomers(Auth::user());
        $approved_count = $this->employeeShiftRepository->getApprovedRequests();

        $allocated_employee_ids = EmployeeAllocation::where('supervisor_id', $supervisor_id)
            ->pluck('user_id')
            ->toArray();

        $allocatedcustomers = $this->customerRepository->getAllAllocatedCustomerId([Auth::User()->id]);
        $shift_ids = EmployeeShiftPayperiod::whereSubmitted(true)
            ->whereApproved(false)
            //check user hierarchy
            ->when(
                !in_array($role_name, ['admin', 'super_admin']),
                function ($q) use ($allocatedcustomers) {
                    $q->whereIn('customer_id', $allocatedcustomers);
                }
            )
            //show his own records.
            ->when(Auth::user()->hasAnyPermission(['supervisor']), function ($q) use ($allocated_employee_ids) {
                // $q->orWhere('employee_id','=',$supervisor_id);
                array_push($allocated_employee_ids, auth()->user()->id);
                $q->whereIn('employee_id', $allocated_employee_ids);
            })
            //->orderBy('employee_shift_payperiods.id')
            ->pluck('employee_shift_payperiods.id');

        $shift_ids_arr_values = array_unique($shift_ids->toArray());
        $shift_ids_arr = array_values($shift_ids_arr_values);
        $total_len = sizeof($shift_ids_arr);
        $pos = array_search($id, $shift_ids_arr);
        $prev = "";
        $next = "";
        $timesheet_path = action('\Modules\Timetracker\Http\Controllers\TimesheetApprovalController@timesheet');
        if ($pos === false && $total_len > 0) {
            $next = $timesheet_path . "/view/" . $shift_ids_arr[0];
        } elseif (($pos == 0 && $total_len == 1) || $total_len == 0) {
            $prev = "";
            $next = "";
        } elseif ($pos == 0 && $total_len > 1) {
            $next = $timesheet_path . "/view/" . $shift_ids_arr[$pos + 1];
        } elseif (($pos + 1) == ($total_len)) {
            $prev = $timesheet_path . "/view/" . $shift_ids_arr[$pos - 1];
        } else {
            $next = $timesheet_path . "/view/" . $shift_ids_arr[$pos + 1];
            $prev = $timesheet_path . "/view/" . $shift_ids_arr[$pos - 1];
        }
        $shift_details = EmployeeShiftPayperiod::whereActive(true)
            ->select(
                'id',
                'pay_period_id',
                'employee_schedule_id',
                'employee_id',
                'customer_id',
                DB::raw('TIME_FORMAT(total_overtime_hours, "%H:%i") as total_overtime_hours'),
                DB::raw('TIME_FORMAT(total_statutory_hours, "%H:%i") as total_statutory_hours'),
                DB::raw('TIME_FORMAT(total_regular_hours, "%H:%i") as total_regular_hours'),
                //CR
                DB::raw('TIME_FORMAT(approved_total_overtime_hours, "%H:%i") as approved_total_overtime_hours'),
                DB::raw('TIME_FORMAT(approved_total_statutory_hours, "%H:%i") as approved_total_statutory_hours'),
                DB::raw('TIME_FORMAT(approved_total_regular_hours, "%H:%i") as approved_total_regular_hours'),
                //End CR
                DB::raw('TIME_FORMAT(billable_overtime_hours, "%H:%i") as billable_overtime_hours'),
                DB::raw('TIME_FORMAT(billable_statutory_hours, "%H:%i") as billable_statutory_hours'),
                'approved',
                'approved_by',
                'active',
                'assigned',
                'payperiod_week',
                'client_approved_billable_overtime',
                'client_approved_billable_statutory',
                DB::raw('DATE_FORMAT(updated_at, "%Y-%m-%d %H:%i") as updated')
            )
            ->with('user', 'payperiod', 'user.roles', 'shifts', 'shifts.shift_payperiod', 'trashed_payperiod', 'trashed_user', 'trashed_user.trashed_employee_profile', 'trashed_customer', 'shifts.employeeScheduleTimeLog')
            ->where('id', $id);
        $shift_details_qry = clone $shift_details;
        $shift_details_emp_scheduledata = $shift_details;

        $shift_details = $shift_details->paginate(1);
        $shift_details_qry = $shift_details_qry->first();
        if (isset($shift_details[0])) {
            $shift_details_array = $shift_details[0]->toArray();
        }

        $Customer = Customer::withTrashed()->find($shift_details_array["customer_id"]);
        $customer_type = $Customer->customer_type_id;
        $cPids = $Customer->cpids;
        $allocatedCpidFunctionCodes = [];
        foreach ($cPids as $cpidAllocation) {
            if (isset($cpidAllocation->cpid_lookup->cpidFunction->name)) {
                $allocatedCpidFunctionCodes[$cpidAllocation->cpid] = $cpidAllocation->cpid_lookup->cpidFunction->name;
            }
        }
        // $customer_type = 1;
        $workHourMappingTable = WorkHourActivityCodeCustomer::where("customer_type_id", $customer_type)->get();
        $dataMappingTable = [];
        $dataDescArray = [];
        foreach ($workHourMappingTable as $workHourMapping) {
            $dataMappingTable[$workHourMapping->work_hour_type->id][] = [
                "id" => $workHourMapping->id,
                "code" => $workHourMapping->code,
                "description" => $workHourMapping->description
            ];
            $dataDescArray[$workHourMapping->id] = $workHourMapping->description;
        }
        // $val = $shift_details_array['weekly_performance'];
        $empWorkHourTypes = $this->employeeShiftWorkHourTypeRepository->getAll();
        $workHourArray = ($empWorkHourTypes->pluck("description", "id")->toArray());
        $customer_id = $shift_details_array["customer_id"];

        $total_reg_hours_sec = $this->helperService->strTimeToSeconds($shift_details_qry->total_regular_hours);
        $total_ovr_hours_sec = $this->helperService->strTimeToSeconds($shift_details_qry->total_overtime_hours);
        $total_stat_hours_sec = $this->helperService->strTimeToSeconds($shift_details_qry->total_statutory_hours);

        $total_time_hours_sec = $total_reg_hours_sec + $total_ovr_hours_sec + $total_stat_hours_sec;

        $total_hours = (int)floor($total_time_hours_sec / 3600);
        $userId = $shift_details_qry->trashed_user->id;
        $dateWiseScheduleArray = [];
        $empSchedules = EmployeeScheduleTimeLog::where([
            'payperiod_id' => $shift_details_qry->trashed_payperiod->id,
            "user_id" => $userId,
        ])->with("schedule")
            ->whereHas("schedule", function ($q) {
                return $q->where("status", 1);
            })->get();
        if ($empSchedules) {
            foreach ($empSchedules as $empSchedule) {
                $dateWiseScheduleArray[$empSchedule->schedule_date] = [
                    "id" => $empSchedule->employee_schedule_id,
                    "expected_hours" => str_replace(".", ":", $empSchedule->hours)
                ];
            }
        }
        $scheduleWorkHours = date("Y-m-d H:i:s");
        $endScheduleWorkHours =  date("Y-m-d H:i:s");
        $scheduleDates = [];
        return view('timetracker::detailed_timesheet', compact(
            'shift_details',
            'shift_ids',
            'next',
            'prev',
            'approved_count',
            'empWorkHourTypes',
            'dataMappingTable',
            'dataDescArray',
            'workHourArray',
            'customer_id',
            'allocatedCpidFunctionCodes',
            'total_hours',
            'dateWiseScheduleArray',
            'scheduleWorkHours',
            'endScheduleWorkHours',
            'scheduleDates'
        ));
    }

    /* Store Timesheet Data */

    public function store(Request $request)
    {
        //dd($request->all());
        //can edit approved payperiod
        //todo::move to middleware
        $id = $request->input('employee_shift_payperiod_id');
        $empShiftPayperiod = EmployeeShiftPayperiod::find($id);
        if (is_object($empShiftPayperiod)) {
            if (!$empShiftPayperiod->canEdit()) {
                return response('Unauthorized', 401);
            }
        }
        try {
            $not_approved = array();
            $customerType = 0;
            $customerDetails = Customer::find($request->customerId);
            if ($customerDetails) {
                $customerType = $customerDetails->customer_type_id;
            }
            $workType = $request->cpidWorkType;
            $activType = $request->activCode;
            $invalidEntry = 0;
            foreach ($request->activCode as $key => $activCode) {
                $singleworkType = $workType[$key];
                $singleactivType = $activType[$key];
                $workTypeCount = WorkHourActivityCodeCustomer::where([
                    "id" => $singleactivType,
                    "work_hour_type_id" => $singleworkType,
                    "customer_type_id" => $customerType
                ])->count();
                if ($workTypeCount < 1) {
                    $invalidEntry++;
                }
            }
            if ($invalidEntry > 0) {
                return response()->json(array('success' => false, 'not_approved' => $not_approved, 'payload' => '<div class="alert alert-success"><a href="#" class="close" data-dismiss="alert">&times;</a> Data infringement.</div>'));
            } else {
                DB::beginTransaction();
                $employeeShiftPayperiod = $this->employeeShiftRepository->approveShiftDetailsAndReview($request);
                foreach ($employeeShiftPayperiod->approved as $eachEmployeeShiftPayperiod) {
                    $helper_variables = array(
                        '{receiverFullName}' => $eachEmployeeShiftPayperiod->trashed_user->full_name,
                        '{customerDetails}' =>  $eachEmployeeShiftPayperiod->trashed_customer->getCustomerNameAndNumberAttribute(),
                        '{approvedBy}' => Auth::user()->full_name,
                        '{approvedDate}' =>  Carbon::parse($eachEmployeeShiftPayperiod->approved_date)->format('Y-m-d'),
                        '{payperiod}' =>  $eachEmployeeShiftPayperiod->trashed_payperiod->pay_period_name,
                        '{totalRegularHours}' =>  Carbon::parse($eachEmployeeShiftPayperiod->approved_total_regular_hours)->format('H:i:s'),
                        '{totalOvertimeHours}' =>  Carbon::parse($eachEmployeeShiftPayperiod->approved_total_overtime_hours)->format('H:i:s'),
                        '{totalStatutoryHours}' =>  Carbon::parse($eachEmployeeShiftPayperiod->approved_total_statutory_hours)->format('H:i:s')
                    );
                    $this->mailQueueRepository->prepareMailTemplate(
                        'employee_timesheet_approval_mail_alert',
                        null,
                        $helper_variables,
                        'Modules\Timetracker\Models\EmployeeShiftPayperiod',
                        null,
                        $eachEmployeeShiftPayperiod->employee_id
                    );
                    $this->notificationRepository->createNotification($eachEmployeeShiftPayperiod, "TIME_SHEET_APPROVED");
                }
                if (!empty($employeeShiftPayperiod->not_approved)) {
                    $not_approved = $employeeShiftPayperiod->not_approved;
                }
                DB::commit();
            }
        } catch (\Exception $e) {
            Log::error($e->getMessage() . " at " . $e->getLine() . " in " . $e->getFile());
            Log::error($e);
            DB::rollBack();
        }

        /* $employee_shift_payperiod_ids = json_decode($request->get('employee_shift_payperiod_ids'));
        $supervisor_id = auth()->user()->id;
        foreach ($employee_shift_payperiod_ids as $employee_shift_payperiod) {
        EmployeeShiftPayperiod::where('id', $employee_shift_payperiod)->update(['approved' => 1, 'approved_by' => $supervisor_id]);
        } */

        return response()->json(array('success' => true, 'not_approved' => $not_approved, 'payload' => '<div class="alert alert-success"><a href="#" class="close" data-dismiss="alert">&times;</a> Timesheet has been successfully updated.</div>'));
    }

    /* Get Timesheet Report-Start */

    public function timesheet()
    {
        $customerIds = $this->customerEmployeeAllocationRepository->getAllocatedCustomers(\Auth::user());
        $employeeLookup = $this->userRepository->getUserLookup(null, ['admin', 'super_admin']);
        $customers = Customer::orderBy('client_name')->findMany($customerIds);
        return view('timetracker::timesheet-approval', [
            'payperiod_list' => $this->getAllPayperiods(),
            'current_payperiod' => $this->getCurrentPayperiod(),
            'allocated_customers' => $customers,
            'employeeLookupList' => $employeeLookup
        ]);
    }

    public function getTimesheetCpidData(Request $request)
    {
        $employeeShiftData = EmployeeShiftPayperiod::find($request->shiftId);
        $customerId = $employeeShiftData->customer_id;
        $Customer = Customer::find($customerId);
        $customer_type = $Customer->customer_type_id;
        $data = [];
        $empShiftCpids = $this->employeeShiftCpidRepository->getAllBy([
            'employee_shift_payperiod_id' => $request->shiftId
        ]);
        $workHourTypeID = $empShiftCpids->pluck("activity_code_id")->toArray();
        $workHourMappingTable = WorkHourActivityCodeCustomer::whereIn("id", $workHourTypeID)->get();
        $dataMappingTable = [];
        $dataCodeMappingTable = [];
        $dataDescArray = [];
        foreach ($workHourMappingTable as $workHourMapping) {
            $dataMappingTable[$workHourMapping->work_hour_type->id][] = [
                "id" => $workHourMapping->id,
                "code" => $workHourMapping->code,
                "description" => $workHourMapping->description,
                "type" => $workHourMapping->work_hour_type->name
            ];
            $dataCodeMappingTable[$workHourMapping->id] = $workHourMapping->code;
            $dataDescArray[$workHourMapping->id] = $workHourMapping->description;
        }
        foreach ($empShiftCpids as $empShiftCpid) {
            $explodedTime = explode(":", ($empShiftCpid->hours));
            $formattedTime = $explodedTime[0] . ":" . $explodedTime[1];
            $data[] = [
                "cpids" => $empShiftCpid->cpid_lookup->cpid,
                "function" =>  isset($empShiftCpid->cpid_lookup->cpidFunction) ? $empShiftCpid->cpid_lookup->cpidFunction->name : "",
                "position" => isset($empShiftCpid->cpid_lookup->position) ? $empShiftCpid->cpid_lookup->position->position : "",
                "type" => isset($empShiftCpid->shift_work_hour_type_withtrashed) ?
                    $empShiftCpid->shift_work_hour_type_withtrashed->name : "",
                "code" => isset($empShiftCpid->activity_code_withtrashed) ?
                    $empShiftCpid->activity_code_withtrashed->code : "",
                "hours" => $formattedTime
            ];
        }


        return json_encode($data, true);
    }

    public function getTimesheet(Request $request)
    {
        $arr_user = [Auth::User()->id];
        $allocatedcustomers = $this->customerRepository->getAllAllocatedCustomerId($arr_user);

        $payperiod = request('payperiod');
        $role_name = auth()->user()->roles->first()->name;
        $supervisor_id = auth()->user()->id;
        $projects = CustomerEmployeeAllocation::where('user_id', $supervisor_id)->pluck('customer_id');

        $allocated_employee_ids = EmployeeAllocation::where('supervisor_id', $supervisor_id)
            ->pluck('user_id')
            ->toArray();

        //select datas
        $employee_data = EmployeeShiftPayperiod::whereActive(true)
            ->select(
                'id',
                'pay_period_id',
                'employee_id',
                'customer_id',
                'payperiod_week',
                DB::raw('TIME_FORMAT(total_overtime_hours, "%H:%i") as total_overtime_hours'),
                DB::raw('TIME_FORMAT(total_statutory_hours, "%H:%i") as total_statutory_hours'),
                DB::raw('TIME_FORMAT(total_regular_hours, "%H:%i") as total_regular_hours'),
                DB::raw('TIME_FORMAT(approved_total_overtime_hours, "%H:%i") as approved_total_overtime_hours'),
                DB::raw('TIME_FORMAT(approved_total_statutory_hours, "%H:%i") as approved_total_statutory_hours'),
                DB::raw('TIME_FORMAT(approved_total_regular_hours, "%H:%i") as approved_total_regular_hours'),
                'approved',
                'approved_by',
                'active',
                'assigned',
                'client_approved_billable_overtime',
                'client_approved_billable_statutory',
                'notes',
                DB::raw('DATE_FORMAT(updated_at, "%Y-%m-%d %H:%i") as updated')
            )
            ->whereSubmitted(true)
            //filter by customer id
            ->where(function ($query) use ($request) {
                $customer = $request->input('customer');
                $employee = $request->input('employee');
                $status = $request->input('status');
                $week = $request->input('week');
                //filter by customer
                if (!empty($customer)) {
                    $query->where('customer_id', '=', $customer);
                }
                //filter by employee
                if (!empty($employee)) {
                    $query->where('employee_id', '=', $employee);
                }
                //filter by status
                if ($status !== null) {
                    $query->where('approved', '=', $status);
                }
                //filter by week
                if ($week !== null) {
                    $query->where('payperiod_week', '=', $week);
                }
            })
            //filter by payperiod
            ->when(request('payperiod') != null, function ($q) use ($payperiod) {
                return $q->where('pay_period_id', $payperiod);
            })
            //check user hierarchy
            ->when(
                !Auth::user()->hasAnyPermission(['admin', 'super_admin']),
                function ($q) use ($projects, $allocatedcustomers, $request) {
                    if ($request->input('customer') == null) {
                        $q->whereIn('customer_id', $allocatedcustomers);
                    }
                }
            )
            //show his own records.
            ->when(Auth::user()->hasAnyPermission(['supervisor'] && !Auth::user()->hasAnyPermission(['admin', 'super_admin'])), function ($q) use ($allocated_employee_ids, $allocatedcustomers) {
                // $q->orWhere('employee_id','=',$supervisor_id);
                array_push($allocated_employee_ids, auth()->user()->id);
                if (\Auth::user()->hasPermissionTo('view_listing_customer_based')) {
                    $q->whereIn('customer_id', $allocatedcustomers);
                } else {
                    $q->whereIn('employee_id', $allocated_employee_ids);
                }
            })
            ->with('user', 'trashed_user', 'trashed_user.roles', 'trashed_user.trashed_employee_profile', 'trashed_payperiod', 'trashed_customer', 'submitted_shifts', 'cpids', 'cpids.cpid_lookup_with_trash', 'cpids.cpid_lookup_with_trash.position')
            ->get();
        // dd(DB::getQueryLog());
        $result_data = $this->prepareDataArray($employee_data, $request->status);

        return datatables()->of($result_data)->toJson();
    }

    /* Get Timesheet Report-End */

    private function getAllPayperiods()
    {
        return PayPeriod::select('id', 'pay_period_name', 'short_name')->whereActive(true)->get();
    }

    private function getCurrentPayperiod()
    {
        $default_object = new \stdClass();
        $default_object->id = 0;
        return (PayPeriod::select('id')->whereActive(true)->where('start_date', '<=', today())->where('end_date', '>=', today())->first()) ?? $default_object;
    }

    /* Building employee data array */

    public function prepareDataArray($employee_data, $status)
    {
        $datatable_rows = array();
        foreach ($employee_data as $key => $each_employee) {
            $each_row["id"] = $each_employee->id;
            $each_row["employee_no"] = (null != $each_employee->trashed_user) ? $each_employee->trashed_user->trashed_employee_profile->employee_no : '--';
            $each_row["full_name"] = $each_employee->trashed_user->first_name . ' ' . $each_employee->trashed_user->last_name;
            $employee_duplicate = (clone ($each_employee));
            $result = data_get($employee_duplicate, 'trashed_user.roles');
            if ($result->isEmpty()) {
                $each_row["role"] = "--";
            } else {
                $each_row["role"] = $result->first()->name;
            }
            $each_row["project_number"] = $each_employee->trashed_customer->project_number;
            $each_row["client_name"] = $each_employee->trashed_customer->client_name;
            $each_row["pay_period_name"] = $each_employee->trashed_payperiod->pay_period_name;
            $each_row["start_date"] = $each_employee->trashed_payperiod->start_date;
            $each_row["end_date"] = $each_employee->trashed_payperiod->end_date;
            if ($status == 1) {
                $each_row["total_regular_hours"] = $each_employee->approved_total_regular_hours;
                $each_row["total_overtime_hours"] = $each_employee->approved_total_overtime_hours;
                $each_row["total_statutory_hours"] = $each_employee->approved_total_statutory_hours;
                $sum = 0;
                foreach ($each_employee->cpids as $item) {
                    $sum += $item->total_amount;
                }
                $each_row["total_earnings"] = '$' . number_format($sum, 2);
            } else {
                $each_row["total_regular_hours"] = $each_employee->total_regular_hours;
                $each_row["total_overtime_hours"] = $each_employee->total_overtime_hours;
                $each_row["total_statutory_hours"] = $each_employee->total_statutory_hours;
                $each_row["total_earnings"] = '--';
            }
            $each_row['payperiod_week'] = is_null($each_employee->payperiod_week)
                ? '--'
                : ($each_employee->payperiod_week);
            $each_row["notes"] = $each_employee->notes;
            $each_row["approved"] = $each_employee->approved;
            $each_row['cpids'] = $each_employee->cpids;
            array_push($datatable_rows, $each_row);
        }
        return $datatable_rows;
    }

    /**
     * Approved Timesheet Export
     * @param  $request
     * @return response
     */
    public function approvedTimesheetExport(Request $request)
    {
        //todo::add filter
        $payperiod = request('payperiod');
        $role_name = auth()->user()->roles->first()->name;
        $supervisor_id = auth()->user()->id;
        $projects = CustomerEmployeeAllocation::where('user_id', $supervisor_id)->pluck('customer_id');
        $guard = EmployeeAllocation::when(!Auth::user()->hasAnyPermission(['admin', 'super_admin']), function ($q) use ($supervisor_id) {
            $q->where('supervisor_id', $supervisor_id);
        })->pluck('user_id');

        $employee_data = EmployeeShiftPayperiod::whereActive(true)
            ->when(request('payperiod') != null, function ($q) use ($payperiod) {
                return $q->where('pay_period_id', $payperiod);
            })
            ->select(
                'id',
                'pay_period_id',
                'employee_id',
                'customer_id',
                DB::raw('TIME_FORMAT(total_overtime_hours, "%H:%i") as total_overtime_hours'),
                DB::raw('TIME_FORMAT(total_statutory_hours, "%H:%i") as total_statutory_hours'),
                DB::raw('TIME_FORMAT(total_regular_hours, "%H:%i") as total_regular_hours'),
                'approved',
                'approved_by',
                'active',
                'assigned',
                'client_approved_billable_overtime',
                'client_approved_billable_statutory',
                'notes',
                DB::raw('DATE_FORMAT(updated_at, "%Y-%m-%d %H:%i") as updated')
            )
            ->whereSubmitted(true)
            ->when(!Auth::user()->hasAnyPermission(['admin', 'super_admin']), function ($q) use ($guard) {
                $q->whereIn('employee_id', $guard);
            })
            ->when(!Auth::user()->hasAnyPermission(['admin', 'super_admin']), function ($q) use ($projects) {
                $q->whereIn('customer_id', $projects);
            }) //filter by customer id
            ->where(function ($query) use ($request) {
                $customer = $request->input('customer');
                $status = $request->input('status');
                $week = $request->input('week');
                //filter by customer
                if (!empty($customer)) {
                    $query->where('customer_id', '=', $customer);
                }
                // //filter by status
                // if($status !== null){
                //     $query->where('approved','=',$status);
                // }
                //filter by week
                if ($week !== null) {
                    $query->where('payperiod_week', '=', $week);
                }
            })->where('approved', '=', 1) //only return approved results
            ->with(
                'user',
                'trashed_user',
                'trashed_user.roles',
                'trashed_user.trashed_employee_profile',
                'trashed_payperiod',
                'trashed_customer',
                'submitted_shifts',
                'cpids',
                'cpids.cpid_lookup_with_trash',
                'cpids.cpid_lookup_with_trash.position'
            )
            ->get();

        $result = $this->employeeShiftRepository->prepareTimesheetExport($employee_data);
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->fromArray($result);
        $writer = new Xlsx($spreadsheet);
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename="TimesheetExport.xlsx"');
        $writer->save("php://output");
        exit;
    }

    public function approvedTimesheetExportVision(Request $request)
    {
        // //todo::add filter
        // $payperiod = request('payperiod');
        // $role_name = auth()->user()->roles->first()->name;
        // $supervisor_id = auth()->user()->id;
        // $projects = CustomerEmployeeAllocation::where('user_id', $supervisor_id)->pluck('customer_id');
        // $guard = EmployeeAllocation::when(!Auth::user()->hasAnyPermission(['admin', 'super_admin']), function ($q) use ($supervisor_id) {
        //     $q->where('supervisor_id', $supervisor_id);
        // })->pluck('user_id');

        // $employee_data = EmployeeShiftPayperiod::whereActive(true)
        //     ->when(request('payperiod') != null, function ($q) use ($payperiod) {
        //         return $q->where('pay_period_id', $payperiod);
        //     })
        //     ->select(
        //         'id',
        //         'pay_period_id',
        //         'employee_id',
        //         'customer_id',
        //         DB::raw('TIME_FORMAT(total_overtime_hours, "%H:%i") as total_overtime_hours'),
        //         DB::raw('TIME_FORMAT(total_statutory_hours, "%H:%i") as total_statutory_hours'),
        //         DB::raw('TIME_FORMAT(total_regular_hours, "%H:%i") as total_regular_hours'),
        //         'approved',
        //         'approved_by',
        //         'active',
        //         'assigned',
        //         'client_approved_billable_overtime',
        //         'client_approved_billable_statutory',
        //         'notes',
        //         DB::raw('DATE_FORMAT(updated_at, "%Y-%m-%d %H:%i") as updated')
        //     )
        //     ->whereSubmitted(true)
        //     ->when(!Auth::user()->hasAnyPermission(['admin', 'super_admin']), function ($q) use ($guard) {
        //         $q->whereIn('employee_id', $guard);
        //     })
        //     ->when(!Auth::user()->hasAnyPermission(['admin', 'super_admin']), function ($q) use ($projects) {
        //         $q->whereIn('customer_id', $projects);
        //     }) //filter by customer id
        //     ->where(function ($query) use ($request) {
        //         $customer = $request->input('customer');
        //         $status = $request->input('status');
        //         $week = $request->input('week');
        //         //filter by customer
        //         if (!empty($customer)) {
        //             $query->where('customer_id', '=', $customer);
        //         }
        //         // //filter by status
        //         // if($status !== null){
        //         //     $query->where('approved','=',$status);
        //         // }
        //         //filter by week
        //         if ($week !== null) {
        //             $query->where('payperiod_week', '=', $week);
        //         }
        //     })->where('approved', '=', 1) //only return approved results
        //     ->with(
        //         'user',
        //         'trashed_user',
        //         'trashed_user.roles',
        //         'trashed_user.trashed_employee_profile',
        //         'trashed_payperiod',
        //         'trashed_customer',
        //         'submitted_shifts',
        //         'cpids',
        //         'cpids.cpid_lookup_with_trash',
        //         'cpids.cpid_lookup_with_trash.position'
        //     )
        //     ->get();


        Config::set('excel.exports.csv.delimiter', '|');
        Config::set('excel.exports.csv.enclosure', '');

        $result = $this->employeeShiftRepository->prepareTimesheetVisionExport($request->all());
        return Excel::download(new CompassExport($result), 'Vision Export ' . date("Y-m-d H:i A") . '.csv');
    }

    public function  employeeTimeSheetApprovalRating()
    {
        try {
            Log::channel('timeSheetApprovalRatingLog')->info("Rating calculation Started");
            DB::beginTransaction();
            $ratingResponse = TimeSheetApprovalRating::dispatch();
            $return = ['success' => true, 'response' => $ratingResponse];
            Log::channel('timeSheetApprovalRatingLog')->info("----employeeTimeSheetApprovalRating Controller---" . collect($ratingResponse));
            DB::commit();
            return response()->json($return);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::channel('timeSheetApprovalRatingLog')->error($e);
            return response()->json($this->helperService->returnFalseResponse($e));
        }
    }

    public function  employeeTimesheetApprovalEmailNotification()
    {
        // $result = $this->employeeShiftAprovalRatingRepository->timesheetApprovalReminder('test',Carbon::now()->format('Y-m-d'));
        // dd($result);
        try {
            Log::channel('timeSheetApprovalRatingLog')->info("Reminder Mail Started");
            DB::beginTransaction();
            TimeSheetApprovalReminder::dispatch();
            DB::commit();
            return response()->json($this->helperService->returnTrueResponse());
        } catch (\Exception $e) {
            DB::rollBack();
            Log::channel('timeSheetApprovalRatingLog')->error($e);
            return response()->json($this->helperService->returnFalseResponse($e));
        }
    }
}
