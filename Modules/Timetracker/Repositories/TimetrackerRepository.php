<?php

namespace Modules\Timetracker\Repositories;

use App\Services\HelperService;
use Auth;
use DB;
use Modules\Admin\Models\EmployeeAllocation;
use Modules\Admin\Models\PayPeriod;
use Modules\Admin\Models\User;
use Modules\Admin\Repositories\CustomerRepository;
use Modules\Admin\Repositories\PayPeriodRepository;
use Modules\Timetracker\Models\EmployeeShift;
use Modules\Timetracker\Models\EmployeeShiftPayperiod;
use Modules\Timetracker\Models\ShiftLiveLocation;

class TimetrackerRepository
{

    /**
     * The Repository instance.
     *
     * @var \Modules\Admin\Models\PayPeriod
     */
    protected $payperiod;
    protected $employeeShiftPayperiod;
    protected $helperService;
    protected $customerRepository;
    protected $EmployeeAllocation;
    protected $payPeriodRepository;

    /**
     * Create Repository instance.
     *
     * @param  \Modules\Admin\Models\PayPeriod $payperiod
     * @return void
     */
    public function __construct(
        PayPeriod $payperiod,
        EmployeeShiftPayperiod $employeeShiftPayperiod,
        CustomerRepository $customerRepository,
        EmployeeAllocation $EmployeeAllocation,
        PayPeriodRepository $payPeriodRepository) {
        $this->payperiod = $payperiod;
        $this->employeeShiftPayperiod = $employeeShiftPayperiod;
        $this->customerRepository = $customerRepository;
        $this->EmployeeAllocation = $EmployeeAllocation;
        $this->helperService = new HelperService();
        $this->payPeriodRepository = $payPeriodRepository;
    }

    /**
     * Get All Payperiods
     * @return object
     */
    public function getAllPayperiods()
    {
        return $this->payperiod->select('id', 'pay_period_name')->whereActive(true)->get();
    }

    /**
     * Get Current Payperiod
     * @return object
     */
    public function getCurrentPayperiod()
    {
        $default_object = new \stdClass();
        $default_object->id = 0;
        return ($this->payperiod->select('id')->whereActive(true)->where('start_date', '<=', today())->where('end_date', '>=', today())->first()) ?? $default_object;
    }

    /**
     * Timesheet Report
     * @param  $payperiod
     * @param  $current_user
     * @return object
     */
    public function timesheetReport($payperiod, $customer, $employee,$current_user, $customer_session = false, $fromdate = null, $todate = null)
    {
        $filterApplied = false;
        $payperiodIds = array();
        $role_name = auth()->user()->roles->first()->name;
        if (!empty($fromdate) || !empty($todate)) {
            if (empty($fromdate)) {
                $fromdate = date("Y-m-d");
            }
            if (empty($todate)) {
                $todate = date("Y-m-d");
            }
            $payperiodIds = $this->payPeriodRepository->getPayperiodIdsInRange($fromdate, $todate);
            $filterApplied = true;
        } elseif ($payperiod != null) {
            $payperiodIds = array($payperiod);
            $filterApplied = true;
        }

        $haveAdminPrivileges = Auth::user()->hasAnyPermission(['admin', 'super_admin']);
        $arr_user = [Auth::User()->id];
        $selectedCustomerIds = [];
        if ($customer_session) {
            $selectedCustomerIds = $this->helperService->getCustomerIds();
        }

        if (empty($selectedCustomerIds)) {
            $selectedCustomerIds = $this->customerRepository->getAllAllocatedCustomerId($arr_user);
        }

        $qry = $this->employeeShiftPayperiod->whereActive(true)
            ->whereSubmitted(true)
            ->whereHas('shifts', function ($query) use ($payperiodIds, $role_name, $filterApplied, $fromdate, $todate) {
                $query->when($filterApplied, function ($q) use ($payperiodIds, $fromdate, $todate) {
                    if ($fromdate != "") {
                        return $q->where('start', '>=', $fromdate)->where('start', '<=', $todate);
                    } else {
                        return $q->whereIn('pay_period_id', $payperiodIds);
                    }
                });
            })
            ->when($employee != null, function ($q) use ($employee) {
                return $q->where('employee_id', $employee);
            })
            ->when($customer != null, function ($q) use ($customer) {
                return $q->where('customer_id', $customer);
            })
            ->with(array(
            'trashed_user',
            'trashed_user.trashed_employee_profile',
            'trashed_user.roles',
            'trashed_customer',
            'user.allocation.supervisor',
            'shifts',
            'user.allocation',
            'total_hours_by_employee',
        ))
            ->when(!$haveAdminPrivileges, function ($query) use ($current_user, $selectedCustomerIds) {
                if (\Auth::user()->hasPermissionTo('view_listing_customer_based')) {
                    return $query->whereIn('customer_id', $selectedCustomerIds);
                } else {
                    $query->whereHas('trashed_user.allocatedSupervisor', function ($query) use ($current_user) {
                        return $query->where('supervisor_id', '=', $current_user->id);
                    });
                }
            }) //apply filter
            ->where(function ($query) use ($selectedCustomerIds, $customer_session, $haveAdminPrivileges) {
                if (!empty($selectedCustomerIds) && ($customer_session || (!$haveAdminPrivileges))) {
                    return $query->whereIn('customer_id', $selectedCustomerIds);
                }
                return $query;
            });
        $result = $qry->get();

        $report = $this->prepareDataFortimesheetReport($result);

        return $report;
    }

    public function prepareDataFortimesheetReport($report)
    {

        $arr_user = [Auth::User()->id];
        $role_name = auth()->user()->roles->first()->name;
        $allocatedcustomers = $this->customerRepository->getAllAllocatedCustomerId($arr_user);

        $datatable_rows = array();
        foreach ($report as $key => $each_employee) {
            $each_row_shift = array();
            foreach ($each_employee->shifts as $key => $shift) {
                $each_row_shift[$key]['employee_shift_payperiod_id'] = $shift->employee_shift_payperiod_id;
                $each_row_shift[$key]['id'] = $shift->id;
                $each_row_shift[$key]['start'] = $shift->start;
                $each_row_shift[$key]['end'] = $shift->end;
                $each_row_shift[$key]['work_hours'] = $shift->work_hours;
                $each_row_shift[$key]['notes'] = $shift->notes;
            }
            $each_row['shifts'] = $each_row_shift;
            $each_row["id"] = $each_employee->id;
            $each_row["project_number"] = $each_employee->trashed_customer->project_number;
            $each_row["client_name"] = $each_employee->trashed_customer->client_name;

            $each_row["employee_no"] = (null != $each_employee->trashed_user) ? $each_employee->trashed_user->trashed_employee_profile->employee_no : '--';
            $each_row["full_name"] = $each_employee->trashed_user->first_name . ' ' . $each_employee->trashed_user->last_name;
            $employee_duplicate = (clone ($each_employee));
            $result = data_get($employee_duplicate, 'trashed_user.roles');
            if ($result->isEmpty()) {
                $each_row["role"] = "--";
            } else {
                $each_row["role"] = $result->first()->name;
            }

            $each_row["pay_period_name"] = $each_employee->trashed_payperiod->pay_period_name;
            $each_row["start_date"] = $each_employee->trashed_payperiod->start_date;
            $each_row["end_date"] = $each_employee->trashed_payperiod->end_date;
            $each_row["total_regular_hours"] = $each_employee->total_regular_hours;
            $each_row["total_overtime_hours"] = $each_employee->total_overtime_hours;
            $each_row["total_statutory_hours"] = $each_employee->total_statutory_hours;
            $each_row["total_hours_employee"] = $each_employee->total_hours_employee;

            $each_row["client_approved_billable_overtime"] = $each_employee->client_approved_billable_overtime == 1 ? 'Yes' : 'No';
            $each_row["client_approved_billable_statutory"] = $each_employee->client_approved_billable_statutory == 1 ? 'Yes' : 'No';
            //$each_row["total_work_hours"] = $each_employee->total_hours_by_employee[0]->total_work_hours;
            $projectid = $each_employee->customer_id;
            //echo "-";
            $each_row["updated_at"] = $each_employee->updated_at->format('Y-m-d H:i:s');

            if (Auth::user()->hasAnyPermission(['admin', 'super_admin'])) {
                array_push($datatable_rows, $each_row);
            } else {

                if (in_array($projectid, $allocatedcustomers)) {
                    array_push($datatable_rows, $each_row);
                }
            }
        }
        return $datatable_rows;
    }

    /**
     * Timesheet Report Detail
     * @param $payperiod,
     * @param $fromDate,
     * @param $toDate
     * @return array
     */
    public function timesheetReportDetail($payperiod, $fromdate = null, $todate = null)
    {
        $arr_user = [\Auth::User()->id];
        $allocatedcustomers = $this->customerRepository->getAllAllocatedCustomerId($arr_user);
        $filterApplied = false;
        $payperiodIds = array();

        $role_name = auth()->user()->roles->first()->name;
        if (!empty($fromdate) || !empty($todate)) {
            if (empty($fromdate)) {
                $fromdate = date("Y-m-d");
            }
            if (empty($todate)) {
                $todate = date("Y-m-d");
            }
            $payperiodIds = $this->payPeriodRepository->getPayperiodIdsInRange($fromdate, $todate);
            //dd($payperiodIds);
            $filterApplied = true;
        } elseif ($payperiod != null) {
            $payperiodIds = array($payperiod);
            $filterApplied = true;
        }

        $qry = EmployeeShift::whereHas('submitted_shift_payperiod',
            function ($query)
             use ($payperiodIds, $allocatedcustomers, $role_name, $filterApplied, $fromdate, $todate) {
                $query->when($filterApplied, function ($q) use ($payperiodIds, $fromdate, $todate) {
                    if ($fromdate != "") {
                        return $q->where('start', '>=', $fromdate)->where('start', '<=', $todate);
                    } else {
                        return $q->whereIn('pay_period_id', $payperiodIds);
                    }
                });
                $query->when(!Auth::user()->hasAnyPermission(['admin', 'super_admin']), function ($query) use ($allocatedcustomers) {
                    if (\Auth::user()->hasPermissionTo('view_listing_customer_based')) {

                        return $query->whereIn('customer_id', $allocatedcustomers);
                    } else {
                        $query->whereHas('trashed_user.allocatedSupervisor', function ($query) {
                            return $query->where('supervisor_id', '=', Auth::user()->id);
                        });
                    }
                });
            })->with(
            'shift_payperiod.trashed_user',
            'shift_payperiod.trashed_user.trashed_employee_profile',
            'shift_payperiod.trashed_user.roles',
            'shift_payperiod.trashed_customer'
        )
            ->has('submitted_shift_payperiod')
            ->whereSubmitted(true);
        // ->orderBy('updated_at')
        $result = $qry->get();
        $data = $this->prepareDataForReport($result);
        return $data;
    }

    /**
     * Timesheet Report Data as Array
     * @param  $result
     * @return object
     */
    public function prepareDataForReport($result)
    {
        $arr_user = [Auth::User()->id];
        $allocatedcustomers = $this->customerRepository->getAllAllocatedCustomerId($arr_user);

        $role_name = auth()->user()->roles->first()->name;
        $supervisingusers = $this->EmployeeAllocation->where('supervisor_id', Auth::user()->id)->get()->pluck('user_id')->toArray();
        $datatable_rows = array();
        foreach ($result as $key => $each_employee) {
            $each_row["updated_at"] = $each_employee->shift_payperiod->updated_at->format('Y-m-d H:i:s');
            $each_row["id"] = $each_employee->id;
            $each_row["employee_no"] = (null != $each_employee->shift_payperiod->trashed_user) ? $each_employee->shift_payperiod->trashed_user->trashed_employee_profile->employee_no : '--';
            $each_row["full_name"] = $each_employee->shift_payperiod->trashed_user->first_name . ' ' . $each_employee->shift_payperiod->trashed_user->last_name;
            $employee_duplicate = (clone ($each_employee));
            $result = data_get($employee_duplicate, 'shift_payperiod.trashed_user.roles');
            if ($result->isEmpty()) {
                $each_row["role"] = "--";
            } else {
                $each_row["role"] = ucfirst($result->first()->name);
            }

            $each_row["project_number"] = $each_employee->shift_payperiod->trashed_customer->project_number;
            // /echo $each_employee->shift_payperiod->trashed_customer->project_number."<br/>";
            $projectid = $each_employee->shift_payperiod->trashed_customer->id;
            $each_row["client_name"] = $each_employee->shift_payperiod->trashed_customer->client_name;
            $each_row["start"] = $each_employee->start;
            $each_row["end"] = $each_employee->end;
            $each_row["work_hours"] = $each_employee->work_hours;

            $each_row["notes"] = $each_employee->notes;
            if (!Auth::user()->hasAnyPermission(['admin', 'super_admin'])) {
                if (in_array($projectid, $allocatedcustomers)) {

                    //echo json_encode($each_employee);
                    if (in_array($each_employee->shift_payperiod->employee_id, $supervisingusers)) {
                        array_push($datatable_rows, $each_row);
                    }
                }
            } else {

                array_push($datatable_rows, $each_row);
            }
        }

        return $datatable_rows;
    }

    /**
     * Employee Summary Report
     * @param  $payperiod
     * @return object
     */
    public function employeeSummaryReport($payperiod, $fromdate = null, $todate = null)
    {
        $filterApplied = false;
        $payperiodIds = array();

        if (!empty($fromdate) || !empty($todate)) {
            if (empty($fromdate)) {
                $fromdate = date("Y-m-d");
            }
            if (empty($todate)) {
                $todate = date("Y-m-d");
            }
            $payperiodIds = $this->payPeriodRepository->getPayperiodIdsInRange($fromdate, $todate);
            //dd($payperiodIds);
            $filterApplied = true;
        } elseif ($payperiod != null) {
            $payperiodIds = array($payperiod);
            $filterApplied = true;
        }

        $result = $this->employeeShiftPayperiod->whereActive(true)
            ->select(
                'id',
                'pay_period_id',
                'employee_id',
                'customer_id',
                DB::raw('TIME_FORMAT(approved_total_overtime_hours, "%H:%i") as total_overtime_hours'),
                DB::raw('TIME_FORMAT(approved_total_statutory_hours, "%H:%i") as total_statutory_hours'),
                DB::raw('TIME_FORMAT(approved_total_regular_hours, "%H:%i") as total_regular_hours'),
                DB::raw('TIME_FORMAT((sec_to_time(time_to_sec(approved_total_overtime_hours)+time_to_sec(approved_total_statutory_hours)+time_to_sec(approved_total_regular_hours))), "%H:%i") as total_hours_employee'),
                'approved',
                'approved_by',
                'active',
                'assigned',
                'client_approved_billable_overtime',
                'client_approved_billable_statutory',
                DB::raw('DATE_FORMAT(updated_at, "%Y-%m-%d %H:%i") as updated')
            )
            ->whereHas('shifts', function ($query) use ($payperiodIds, $filterApplied, $fromdate, $todate) {
                $query->when($filterApplied, function ($q) use ($payperiodIds, $fromdate, $todate) {
                    if ($fromdate != "") {
                        return $q->where('start', '>=', $fromdate)->where('start', '<=', $todate);
                    } else {
                        return $q->where('pay_period_id', $payperiodIds);
                    }
                });
            })
            ->with(array('trashedAllocatedSupervisor' => function ($query) {
                $query->select('id', 'user_id', 'supervisor_id', 'from', 'to', DB::raw('DATE_FORMAT(created_at, "%d/%m/%Y") as created'), DB::raw('DATE_FORMAT(updated_at, "%d/%m/%Y") as updated'));
            }))
            ->with(
                'payperiod',
                'trashed_payperiod',
                'trashed_user',
                'trashed_user.trashed_employee_profile',
                'trashed_user.roles',
                'customer',
                'trashed_customer',
                'trashed_user.allocation.supervisor',
                'total_hours_by_employee',
                'approved_by_trashed_user'
            )
            ->whereApproved(true)
            ->get();

        $employee_summary = $this->prepareDataForEmployeeSummary($result);
        return $employee_summary;
    }

    public function prepareDataForEmployeeSummary($result)
    {
        $arr_user = [Auth::User()->id];
        $role_name = auth()->user()->roles->first()->name;
        $allocatedcustomers = $this->customerRepository->getAllAllocatedCustomerId($arr_user);

        $datatable_rows = array();
        $supervisingusers = $this->EmployeeAllocation->where('supervisor_id', Auth::user()->id)->get()->pluck('user_id')->toArray();

        foreach ($result as $key => $each_employee) {
            $supervisors = $each_employee["trashed_allocated_supervisor"];

            $each_row["id"] = $each_employee->id;
            $each_row["project_number"] = $each_employee->trashed_customer->project_number;
            $each_row["client_name"] = $each_employee->trashed_customer->client_name;
            $projectid = $each_employee->trashed_customer->id;
            $each_row["employee_no"] = (null != $each_employee->trashed_user) ? $each_employee->trashed_user->trashed_employee_profile->employee_no : '--';
            $each_row["full_name"] = $each_employee->trashed_user->first_name
            . ' ' . $each_employee->trashed_user->last_name;
            $employee_duplicate = (clone ($each_employee));
            $result = data_get($employee_duplicate, 'trashed_user.roles');
            if ($result->isEmpty()) {
                $each_row["role"] = "--";
            } else {
                $each_row["role"] = $result->first()->name;
            }

            $each_row["pay_period_name"] = $each_employee->trashed_payperiod->pay_period_name;
            $each_row["start_date"] = $each_employee->trashed_payperiod->start_date;
            $each_row["end_date"] = $each_employee->trashed_payperiod->end_date;

            $each_row["total_regular_hours"] = empty($each_employee->total_regular_hours) ? '00:00' : $each_employee->total_regular_hours;

            $each_row["total_overtime_hours"] = empty($each_employee->total_overtime_hours) ? '00:00' : $each_employee->total_overtime_hours;

            $each_row["total_statutory_hours"] = empty($each_employee->total_statutory_hours) ? '00:00' : $each_employee->total_statutory_hours;

            $each_row["total_hours_employee"] = empty($each_employee->total_hours_employee) ? '00:00' : $each_employee->total_hours_employee;

            $each_row["approved_by_full_name"] = $each_employee->approved_by_trashed_user->first_name
            . ' ' . $each_employee->approved_by_trashed_user->last_name;

            $each_row["client_approved_billable_overtime"] = $each_employee->client_approved_billable_overtime == 1 ? 'Yes' : 'No';

            $each_row["client_approved_billable_statutory"] = $each_employee->client_approved_billable_statutory == 1 ? 'Yes' : 'No';

            $each_row["total_work_hours"] = (!$each_employee->total_hours_by_employee->isEmpty()) ? $each_employee->total_hours_by_employee[0]->total_work_hours : '00:00';

            $each_row["updated"] = $each_employee->updated;
            if (Auth::user()->hasAnyPermission(['admin', 'super_admin'])) {
                array_push($datatable_rows, $each_row);
            } else {
                //echo $projectid."-";
                if (in_array($projectid, $allocatedcustomers)) {

                    if (in_array($each_employee->employee_id, $supervisingusers)) {
                        array_push($datatable_rows, $each_row);
                    }
                }
            }
            //array_push($datatable_rows, $each_row);
        }

        return $datatable_rows;
    }

    /**
     * Employee Performance Report
     * @return object
     */
    public function employeePerformanceReport()
    {
        return $this->employeeShiftPayperiod->whereActive(true)
            ->with(
                'trashed_user',
                'trashed_user.trashed_employee_profile',
                'trashed_user.roles',
                'customer',
                'payperiod',
                'weekly_performance',
                'weekly_performance.lookup'
            )
            ->whereHas('weekly_performance', function ($query) {
                $query->whereNotNull('id');
            })
            ->whereApproved(true)
            ->get();
    }

    /**
     * Allocation Report
     * @return array
     */
    public function allocationReport($payperiod, $fromdate = null, $todate = null)
    {
        $filterApplied = false;
        $payperiodIds = array();

        if (!empty($fromdate) || !empty($todate)) {
            if (empty($fromdate)) {
                $fromdate = date("Y-m-d");
            }
            if (empty($todate)) {
                $todate = date("Y-m-d");
            }
            $payperiodIds = $this->payPeriodRepository->getPayperiodIdsInRange($fromdate, $todate);
            //dd($payperiodIds);
            $filterApplied = true;
        } elseif ($payperiod != null) {
            $payperiodIds = array($payperiod);
            $filterApplied = true;
        }
        $arr_user = [Auth::User()->id];
        $role_name = auth()->user()->roles->first()->name;
        $allocatedcustomers = $this->customerRepository->getAllAllocatedCustomerId($arr_user);

        $user_query = User::withTrashed()->with(array(
            'trashed_employee_profile', 'roles', 'employee_shift_payperiods', 'employee_shift_payperiods.customer',
            'trashedAllocatedSupervisor' => function ($query) {
                $query->select('id', 'user_id', 'supervisor_id', 'from', 'to', DB::raw('DATE_FORMAT(created_at, "%d/%m/%Y") as created'), DB::raw('DATE_FORMAT(updated_at, "%d/%m/%Y") as updated'));
            }, 'trashedAllocatedSupervisor.supervisor',
        ))
            ->whereHas('trashedAllocatedSupervisor', function ($query) {
                $query->where('supervisor_id', '<>', null);
            })
            ->whereHas('employee_shift_payperiods', function ($query) use ($payperiodIds, $filterApplied, $fromdate, $todate) {
                $query->whereSubmitted(true)
                    ->whereHas('shifts', function ($query) use ($payperiodIds, $filterApplied, $fromdate, $todate) {
                        $query->when($filterApplied, function ($q) use ($payperiodIds, $fromdate, $todate) {
                            if ($fromdate != "") {
                                return $q->where('start', '>=', $fromdate)->where('start', '<=', $todate);
                            } else {
                                return $q->where('pay_period_id', $payperiodIds);
                            }
                        });
                    });
            })
            ->get()->toArray();
        $datavalues = array();

        foreach ($user_query as $users) {
            $index = 1;
            $supervisor_arr = array();
            $customer_arr = array();
            $myid = Auth::user()->id;
            $supervisors = $users["trashed_allocated_supervisor"];

            $employee_name = $users['first_name'] . ' ' . $users['last_name'];
            $employee_id = $users['trashed_employee_profile']['employee_no'];
            $employee_role = $users['roles'][0]['name'];
            $employee_from = $users['created_at'];
            $employee_to = $users['created_at'];
            $projectid = $users["employee_shift_payperiods"][0]["customer_id"];
            foreach ($users['employee_shift_payperiods'] as $key => $value) {
                $customer_arr[] = array("project_number" => $value['customer']['project_number'] ?? '', "client_name" => $value['customer']['client_name'] ?? '');
            }

            foreach ($users['trashed_allocated_supervisor'] as $key => $aloc_history) {
                if (!empty($aloc_history['supervisor']['first_name'])) {
                    $eachrow['id'] = $index;
                    $eachrow['name'] = $employee_name;
                    $eachrow['employee_no'] = $employee_id;
                    $eachrow['role'] = $employee_role;
                    $modelsupervisorid = $aloc_history['supervisor']['id'];
                    $eachrow['supervisor'] = $aloc_history['supervisor']['first_name'] . ' ' . $aloc_history['supervisor']['last_name'];
                    $eachrow['employee_shift_payperiods'] = $customer_arr;
                    $eachrow['from'] = $aloc_history['from'];
                    $eachrow['to'] = ($aloc_history['to'] != null) ? $aloc_history['to'] : "--";

                    if (Auth::user()->hasAnyPermission(['admin', 'super_admin'])) {
                        array_push($datavalues, $eachrow);
                    } else {

                        $supervisorid = $aloc_history['supervisor']["id"];

                        if ($modelsupervisorid == $myid) {
                            array_push($datavalues, $eachrow);
                        }
                    }
                    $index += 1;
                }
            }
        }

        return $datavalues;
    }

    public function saveShiftLiveLocation($request)
    {
        $shift_live_location = new ShiftLiveLocation();
        $shift_live_location->shift_id = $request->shift_id;
        $shift_live_location->latitude = $request->latitude;
        $shift_live_location->longitude = $request->longitude;
        $shift_live_location->accuracy = $request->accuracy;
        $shift_live_location->speed = $request->speed;
        $shift_live_location->raw_data = json_encode($request);
        $shift_live_location->shift_start_time = $request->shift_start_time;
        $shift_live_location->user_id = $request->user_id;
        $shift_live_location->customer_id = $request->customer_id;
        $shift_live_location->save();
        return $shift_live_location;
    }

    public function saveQrCodeWithShift($request, $user_id, $shiftid)
    {
        $qrCodeWithShift = new CustomerQrcodeWithShift();

        $qrCodeWithShift->user_id = $user_id;
        $qrCodeWithShift->customer_id = $request->customerId;
        $qrCodeWithShift->shift_id = $shiftid;
        $qrCodeWithShift->qrcode_id = $request->qrcode_id;
        $qrCodeWithShift->time = $request->time;
        $qrCodeWithShift->no_of_attempts = $request->no_of_attempts;
        $qrCodeWithShift->latitude = $request->latitude;
        $qrCodeWithShift->longitude = $request->longitude;
        $qrCodeWithShift->comments = $request->comments;
        //$qrCodeWithShift->image          = $request->image;

        $qrCodeWithShift->save();
    }

}
