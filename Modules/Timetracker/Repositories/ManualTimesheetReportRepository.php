<?php

namespace Modules\Timetracker\Repositories;

use App\Services\HelperService;
use Modules\Admin\Models\Customer;
use Modules\Admin\Models\CpidRates;
use Illuminate\Support\Facades\Auth;
use Modules\Admin\Repositories\CpidLookupRepository;
use Modules\Timetracker\Models\EmployeeShiftReportEntry;
use Modules\Timetracker\Models\WorkHourActivityCodeCustomers;
use Modules\Admin\Repositories\CpidCustomerAllocationRepository;
use Modules\Timetracker\Repositories\EmployeeShiftCpidRepository;
use Modules\Uniform\Repositories\UraTransactionRepository;

class ManualTimesheetReportRepository
{

    protected $helperService;
    protected $uraTransactionRepository;

    public function __construct(
        CpidLookupRepository $cpidLookupRepository,
        EmployeeShiftCpidRepository $employeeShiftCpidRepository,
        CpidCustomerAllocationRepository $cpidCustomerAllocationRepository,
        UraTransactionRepository $uraTransactionRepository,
        HelperService $helperService
    ) {
        $this->cpidLookupRepository = $cpidLookupRepository;
        $this->employeeShiftCpidRepository = $employeeShiftCpidRepository;
        $this->cpidCustomerAllocationRepository = $cpidCustomerAllocationRepository;
        $this->uraTransactionRepository = $uraTransactionRepository;
        $this->helperService = $helperService;
    }

    public function getManualReport($request)
    {
        $payperiod = request('payperiod');

        $manualEntry = EmployeeShiftReportEntry::where(function ($query) use ($request) {
            $customer = $request->input('customer');
            $employee = $request->input('employee');
            $week = $request->input('week');
            //filter by customer
            if (!empty($customer)) {
                $query->where('customer_id', '=', $customer);
            }
            //filter by employee
            if (!empty($employee)) {
                $query->where('user_id', '=', $employee);
            }
            //filter by week
            if ($week !== null) {
                $query->where('payperiod_week', '=', $week);
            }
            $query->where('is_manual', '=', 1);
        })
            ->when(request('payperiod') != null, function ($q) use ($payperiod) {
                return $q->where('payperiod_id', $payperiod);
            })
            ->with(
                'user',
                'user.roles',
                'user.trashedEmployee',
                'customer',
                'cpidRate.cpidLookup',
                'cpidFunction',
                'activityType',
                'workHourActivityCodeCustomer',
                'payPeriod'
            )
            ->get();

        return $this->prepareDataArray($manualEntry);
    }

    public function prepareDataArray($employee_data)
    {
        $datatable_rows = array();
        foreach ($employee_data as $key => $each_employee) {
            $each_row["id"] = $each_employee->id;

            $each_row["employee_no"] = ($each_employee->user != null && $each_employee->user->trashedEmployee != null) ? $each_employee->user->trashedEmployee->employee_no : '--';
            $each_row["full_name"] = $each_employee->user != null ? ($each_employee->user->first_name . ' ' . $each_employee->user->last_name) : '--';
            $employee_duplicate = (clone ($each_employee));
            $result = data_get($employee_duplicate, 'user.roles');
            if ($result == null || $result->isEmpty()) {
                $each_row["role"] = "--";
            } else {
                $each_row["role"] = $result->first()->name;
            }
            $each_row["project_number"] = $each_employee->customer->project_number;
            $each_row["client_name"] = $each_employee->customer->client_name;
            $each_row['payperiod_week'] = is_null($each_employee->payperiod_week)
                ? '--'
                : ($each_employee->payperiod_week);
            $each_row["pay_period_name"] = ($each_employee->payPeriod != null) ? $each_employee->payPeriod->pay_period_name : '--';
            $each_row["cpid"] = $each_employee->cpidRate->cpidLookup->cpid;
            $each_row["function"] = $each_employee->cpidFunction == null ? '--' : $each_employee->cpidFunction->name;
            $each_row["activity_type"] = ($each_employee->activityType == null) ? '--' : $each_employee->activityType->name;
            $each_row["activity_code"] = ($each_employee->workHourActivityCodeCustomer == null) ? '--' : $each_employee->workHourActivityCodeCustomer->code;
            $each_row["total_hours"] = $this->helperService->convertToHoursMins($each_employee->hours);
            $each_row["total_earnings"] = '$' . number_format($each_employee->total_amount, 2);
            array_push($datatable_rows, $each_row);
        }
        return $datatable_rows;
    }
    public function trashManualData($id)
    {
        $employeeShiftEntry = EmployeeShiftReportEntry::find($id);
        $return = [
            "code" => 403,
            "message" => "System Error"
        ];
        if ($employeeShiftEntry->delete()) {
            $return = [
                "code" => 200,
                "message" => "Removed successfully"
            ];
            //URA transaction
            $this->uraTransactionRepository->processTimesheetApproval($employeeShiftEntry);
        }

        return $return;
    }

    public function getEditData($id)
    {
        $employeeShiftEntry = EmployeeShiftReportEntry::find($id);
        $cpidList = $this->cpidCustomerAllocationRepository
            ->getByCustomerIdWithActive($employeeShiftEntry->customer_id);

        $cpidRates = CpidRates::find($employeeShiftEntry->cpid_rate_id);
        $cpid = isset($cpidRates->cp_id) ? $cpidRates->cp_id : null;
        $rate = isset($cpidRates->p_standard) ? $cpidRates->p_standard : null;

        $customerTypeId = Customer::withTrashed()->find($employeeShiftEntry->customer_id);
        $activityList = WorkHourActivityCodeCustomers::where('customer_type_id', $customerTypeId->customer_type_id)
            ->where('work_hour_type_id', $employeeShiftEntry->work_hour_type_id)
            ->get();

        return [
            'entry' => $employeeShiftEntry,
            'cpidList' => $cpidList,
            'activityCode' => $activityList,
            'cpid' => $cpid,
            'rate' => $rate
        ];
    }

    /**
     * update employee shift report entry
     * @param Request
     * @return Bool
     */
    public function updateEmployeeShiftReport($data)
    {
        $each['payperiod_id'] = $data->payperiod;
        $each['payperiod_week'] = $data->week;
        $each['user_id'] = $data->employee;
        $each['customer_id'] = $data->customer;

        // verify rate
        $cpidRate = $this->employeeShiftCpidRepository->getEffectiveCpidRates($data->cpid);
        $each['cpid_rate_id'] = $cpidRate['cpid_rate_id'];

        // verify function id
        $cpidFunction = $this->cpidLookupRepository->get($data->cpid);
        $each['cpid_function_id'] = $cpidFunction->cpid_function_id;

        $each['work_hour_type_id'] = $data->activityType;
        $each['work_hour_activity_code_customer_id'] = $data->activityCode;

        // total amount calculation
        $hoursAndTotalRate = $this->totalAmountCalculation($data->hour, $cpidRate['rate_p_standard']);
        $each['hours'] = $hoursAndTotalRate['totalMin'];
        $each['total_amount'] = $hoursAndTotalRate['totalAmount'];
        $each['updated_by'] = Auth::user()->id;

        //URA transaction
        $entry =  EmployeeShiftReportEntry::updateOrCreate(array('id' => $data->id), $each);
        $this->uraTransactionRepository->processTimesheetApproval($entry);

        return $entry;
    }

    /**
     * calculate rate
     */
    public function totalAmountCalculation($hour, $rate)
    {
        $totalMin = $this->helperService->h2m($hour);
        $totalHour = $totalMin / 60;
        $totalAmount = $totalHour * $rate;
        return ['totalMin' => $totalMin, 'totalAmount' => $totalAmount];
    }
}
