<?php

namespace Modules\Timetracker\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\Admin\Models\User;
use App\Services\HelperService;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Modules\Admin\Models\Customer;
use Modules\Admin\Models\WorkType;
use Modules\Admin\Models\CustomerType;
use Modules\Admin\Models\PayrollSettings;
use Modules\Admin\Repositories\CustomerRepository;
use Modules\Admin\Repositories\WorkTypeRepository;
use Modules\Admin\Repositories\PayPeriodRepository;
use Modules\Admin\Repositories\CpidFunctionRepository;
use Modules\Admin\Repositories\CustomerTypeRepository;
use Modules\Timetracker\Models\WorkHourActivityCodeCustomers;
use Modules\Admin\Repositories\CpidCustomerAllocationRepository;
use Modules\Timetracker\Repositories\EmployeeShiftCpidRepository;
use Modules\Admin\Repositories\CustomerEmployeeAllocationRepository;
use Modules\Admin\Rules\ActivityCodeToActivityTypeCustomerType;
use Modules\Admin\Rules\CpidAndCustomer;
use Modules\Admin\Rules\ActivityTypeCustomerType;
use Modules\Timetracker\Repositories\ManualTimesheetEntryRepository;
use Modules\Timetracker\Repositories\EmployeeShiftWorkHourTypeRepository;

class ManualTimesheetEntryController extends Controller
{
    protected $customerRepository;
    protected $payPeriodRepository;

    public function __construct(
        CustomerRepository $customerRepository,
        PayPeriodRepository $payPeriodRepository,
        CustomerEmployeeAllocationRepository $customerEmployeeAllocationRepository,
        CpidCustomerAllocationRepository $cpidCustomerAllocationRepository,
        EmployeeShiftWorkHourTypeRepository $employeeShiftWorkHourTypeRepository,
        EmployeeShiftCpidRepository $employeeShiftCpidRepository,
        ManualTimesheetEntryRepository $manualTimesheetEntryRepository,
        HelperService $helperService
    ) {
        $this->customerRepository = $customerRepository;
        $this->payPeriodRepository = $payPeriodRepository;
        $this->customerEmployeeAllocationRepository = $customerEmployeeAllocationRepository;
        $this->cpidCustomerAllocationRepository = $cpidCustomerAllocationRepository;
        $this->employeeShiftWorkHourTypeRepository = $employeeShiftWorkHourTypeRepository;
        $this->employeeShiftCpidRepository = $employeeShiftCpidRepository;
        $this->manualTimesheetEntryRepository = $manualTimesheetEntryRepository;
        $this->helperService = $helperService;
    }

    /**
     * Display manual timesheet entry page
     * @return Response
     */
    public function manualTimesheetEntry()
    {
        $customerList = $this->customerRepository->getAllCustomersNameList();
        $payperiodList = $this->payPeriodRepository->getLastNPayperiodWithCurrent(6);
        $previousPayperiodDetails = $this->payPeriodRepository->getPreviousWeek();
        $threshold = PayrollSettings::where('setting', 'manualTimesheetThresold')->first()->value('value');
        return view('timetracker::manualTimesheetEntry', compact('customerList', 'payperiodList','previousPayperiodDetails', 'threshold'));
    }

    /**
     * get employee allocation list for particular customer
     * @param customer_id
     * @return array
     */
    public function getCustomerEmployeeAllocationList($customer_id)
    {
        $users = User::where('active', 1)
            ->orderBy("first_name", "asc")
            ->get();

        $employeeList = $users->map(function ($user) {
            return $user->only(['id','name_with_emp_no']);
        });

        $cpidList = $this->cpidCustomerAllocationRepository
            ->getByCustomerIdWithActive($customer_id);

        $activityList = $this->employeeShiftWorkHourTypeRepository->getAll();

        return [
            'employeesList' => $employeeList,
            'cpidList' => $cpidList,
            'activityList' => $activityList
        ];
    }

    /**
     * Get all activity code list
     * @param request
     * @return Array
     */
    public function getActivityCodeList($customer_id, $work_hour_type_id)
    {
        $customerTypeId = Customer::find($customer_id);
        $activityList = [];

        if (null !== $customerTypeId->customer_type_id) {
            $activityList = WorkHourActivityCodeCustomers::where('customer_type_id', $customerTypeId->customer_type_id)
                ->where('work_hour_type_id', $work_hour_type_id)
                ->get();
        }
        return ['activityCode' => $activityList];
    }

    public function getRate($cpid)
    {
        $input = $this->employeeShiftCpidRepository->getEffectiveCpidRates($cpid);
        return $input['rate_p_standard'];
    }

    /**
     * Store a newly created resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        $customerId = $request->input('customer_id');

        $request->validate([
            "employee.*"  => "required|not_in:0|exists:employees,id",
            "payperiod_id" => 'required|exists:pay_periods,id',
            "payperiod_week" => 'required|numeric',
            "customer_id" => 'required|exists:customers,id',
            "cpid.*"  => [
                'required',
                'not_in:0',
                'exists:cpid_lookups,id',
                new CpidAndCustomer($customerId)
            ],
            "work_hour_type.*"  => [
                'required',
                'not_in:0',
                'exists:employee_shift_work_hour_types,id',
                new ActivityTypeCustomerType($customerId)
            ],
            "hours.*"  => "required|string",
            "function_id.*" => 'required|exists:cpid_functions,id',
            "rate_value.*" => 'required|string',
            "activity_code.*" => [
                'required',
                'not_in:0',
                'exists:work_hour_activity_code_customers,id',
                new ActivityCodeToActivityTypeCustomerType(
                    $customerId,
                    $request->input('activity_code')
                )
            ]
        ], [
                'payperiod_id.required' => 'Pay Period is required',
                'payperiod_week.required' => 'Week is required',
                'customer_id.required' => 'Customer is required',
                'employee.*.required' => 'Employee is required',
                'employee.*.not_in' => 'Please choose an employee',
                'cpid.*.required' => 'CPID is required',
                'cpid.*.not_in' => 'Please choose a CPID',
                'function_id.*.required' => 'Function is required',
                'work_hour_type.*.required' => 'Activity Type is required',
                'work_hour_type.*.not_in' => 'Please choose an activity type',
                'activity_code.*.required' => 'Activity Code is required',
                'activity_code.*.not_in' => 'Please choose an activity code',
                'rate_value.*.required' => 'Rate is required',
                'hours.*.required' => 'Hours is required'
            ]);

        try {
            DB::beginTransaction();
            $lookup = $this->manualTimesheetEntryRepository->storeEntry($request);
            DB::commit();
            return response()->json($this->helperService->returnTrueResponse());
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json($this->helperService->returnFalseResponse());
        }
    }

    public function employeecheck($payeriod, $week, $user)
    {
        return $this->manualTimesheetEntryRepository->getEmployeeTimesheetApproval($payeriod, $week, $user);
    }
}
