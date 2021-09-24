<?php

namespace Modules\Timetracker\Http\Controllers;

use App\Services\HelperService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\Admin\Models\User;
use Illuminate\Routing\Controller;
use Modules\Admin\Models\Customer;
use Modules\Admin\Models\PayPeriod;
use Illuminate\Support\Facades\Auth;
use Modules\Admin\Models\EmployeeAllocation;
use Modules\Admin\Repositories\CustomerRepository;
use Modules\Admin\Models\CustomerEmployeeAllocation;
use Modules\Timetracker\Models\EmployeeShiftPayperiod;
use Modules\Timetracker\Models\EmployeeShiftReportEntry;
use Modules\Timetracker\Http\Requests\ManualTimesheetReport;
use Modules\Timetracker\Models\WorkHourActivityCodeCustomers;
use Modules\Admin\Repositories\CpidCustomerAllocationRepository;
use Modules\Timetracker\Http\Requests\ManualTimesheetReportRequest;
use Modules\Admin\Repositories\CustomerEmployeeAllocationRepository;
use Modules\Timetracker\Repositories\ManualTimesheetEntryRepository;
use Modules\Timetracker\Http\Requests\ManualTimetrackerReportRequest;
use Modules\Timetracker\Repositories\ManualTimesheetReportRepository;
use Modules\Timetracker\Repositories\EmployeeShiftWorkHourTypeRepository;
use Modules\Admin\Repositories\UserRepository;

class ManualTimesheetReportController extends Controller
{
    protected $customerEmployeeAllocationRepository;
    protected $customerRepository;

    public function __construct(
        CustomerEmployeeAllocationRepository $customerEmployeeAllocationRepository,
        CustomerRepository $customerRepository,
        ManualTimesheetReportRepository $manualTimesheetReportRepository,
        EmployeeShiftWorkHourTypeRepository $employeeShiftWorkHourTypeRepository,
        CpidCustomerAllocationRepository $cpidCustomerAllocationRepository,
        UserRepository $userRepository,
        HelperService $helperService
    ) {
        $this->customerEmployeeAllocationRepository = $customerEmployeeAllocationRepository;
        $this->customerRepository = $customerRepository;
        $this->manualTimesheetReportRepository = $manualTimesheetReportRepository;
        $this->employeeShiftWorkHourTypeRepository = $employeeShiftWorkHourTypeRepository;
        $this->cpidCustomerAllocationRepository = $cpidCustomerAllocationRepository;
        $this->userRepository = $userRepository;
        $this->helperService = $helperService;

    }

    /**
     * Display manual timesheet report page.
     * @return Response
     */
    public function ManualTimesheetReport()
    {
        $employeeLookup = $this->userRepository->getUserLookup(null,['admin','super_admin']);
        $customer = $this->customerRepository->getAllCustomersNameList();
        $employeeList = User::where('active', 1)
            ->orderBy("first_name", "asc")->get()
            ->pluck('name_with_emp_no', 'id')->toArray();

        $customerList = $customer->pluck('client_name_and_number', 'id')->toArray();
        $activityList = $this->employeeShiftWorkHourTypeRepository->getAll()->pluck('name', 'id')->toArray();
        $payperiodList = $this->getAllPayperiods()->pluck('pay_period_name', 'id')->toArray();

        return view('timetracker::manualTimesheetReport', [
            'payperiod_list' => $this->getAllPayperiods(),
            'current_payperiod' => $this->getCurrentPayperiod(),
            'allocated_customers' => $customer,
            'employeeLookupList' => $employeeLookup
        ], compact('employeeList', 'customerList', 'activityList', 'payperiodList'));
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

    /**
     * get list
     *
     * @return datatable
     */
    public function getList(Request $request)
    {
        return datatables()->of($this->manualTimesheetReportRepository->getManualReport($request))->toJson();
    }

    /**
     * Trash Manual data
     *
     * @return data
     */
    public function trashManualData(Request $request)
    {
        return json_encode($this->manualTimesheetReportRepository->trashManualData($request->id), true);
    }
    /**
     * get edit list
     *
     * @return data
     */
    public function getEditData($id)
    {
        return response()->json($this->manualTimesheetReportRepository->getEditData($id));
    }

    /**
     * get customer edit details
     */
    public function getEditCustomer($customer_id)
    {
        $cpidList = $this->cpidCustomerAllocationRepository
            ->getByCustomerIdWithActive($customer_id);

        return $cpidList;
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

    /**
     * get store
     */
    public function updateEntry(ManualTimetrackerReportRequest $request)
    {
        try {
            \DB::beginTransaction();
            $lookup = $this->manualTimesheetReportRepository->updateEmployeeShiftReport($request);
            \DB::commit();
            return response()->json($this->helperService->returnTrueResponse());
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json($this->helperService->returnFalseResponse());
        }
    }
}
