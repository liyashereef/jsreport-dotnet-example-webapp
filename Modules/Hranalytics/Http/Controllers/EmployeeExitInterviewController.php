<?php

namespace Modules\Hranalytics\Http\Controllers;

use App\Services\HelperService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Admin\Models\ExitResignationReasonLookup;
use Modules\Admin\Models\Employee;
use Modules\Admin\Models\ExitTerminationReasonLookup;
use Modules\Admin\Models\User;
use Modules\Admin\Repositories\CustomerEmployeeAllocationRepository;
use Modules\Admin\Repositories\CustomerRepository;
use Modules\Admin\Repositories\EmployeeAllocationRepository;
use Modules\Client\Repositories\ClientRepository;
use Modules\Hranalytics\Http\Requests\EmployeeExitRequest;
use Modules\Hranalytics\Repositories\EmployeeExitInterviewRepository;
use Modules\Reports\Repositories\TerminationReportRepository;

class EmployeeExitInterviewController extends Controller
{
    public function __construct(
        ClientRepository $clientRepository,
        EmployeeAllocationRepository $employeeAllocationRepository,
        EmployeeExitInterviewRepository $employeeExitInterviewRepository,
        CustomerEmployeeAllocationRepository $customerEmployeeallocationRepository,
        TerminationReportRepository $terminationReportRepository,
        CustomerRepository $customerRepository,
        Employee $EmployeesModel,
        User $UsersModel,
        HelperService $helperService
    ) {
        $this->clientRepository = $clientRepository;
        $this->employeeAllocationRepository = $employeeAllocationRepository;
        $this->employeeExitInterviewRepository = $employeeExitInterviewRepository;
        $this->customerEmployeeallocationRepository = $customerEmployeeallocationRepository;
        $this->terminationReportRepository = $terminationReportRepository;
        $this->customerRepository = $customerRepository;
        $this->EmployeesModel = $EmployeesModel;
        $this->helperService = $helperService;

    }

    public function save(EmployeeExitRequest $request)
    {
        try {
            \DB::beginTransaction();
            $exitInterview = $this->employeeExitInterviewRepository->save($request);
                if ($exitInterview) {
                    $saveToTerminationReport = $this->terminationReportRepository->save($request, $exitInterview->id);
                }
            \DB::commit();
            return response()->json($this->helperService->returnTrueResponse());
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json($this->helperService->returnFalseResponse($e));
        }
    }

    public function getEmployeeExitInterview()
    {
        $current_date = Carbon::now()->toDateString();
        $current_time = Carbon::now()->toTimeString();
        $default = [null => 'Please Select'];
        $resignation_details = $default + ExitResignationReasonLookup::pluck('reason', 'id')->toArray();
        $termination_details = $default + ExitTerminationReasonLookup::pluck('reason', 'id')->toArray();
        $emp_list = [];
        $user_id = \Auth::user()->id;
        $user = \Auth::user();
        if ($user->can('create_all_exit_interview')) {
            $project_list = $this->customerRepository->getProjectsDropdownList('all');
            asort($project_list);
        } else if ($user->can('create_exit_interview')) {
            $project_list = $this->customerRepository->getProjectsDropdownList('allocated');
            asort($project_list);
        }
        return view('hranalytics::exit-interviews.employee-exit-interview', compact('current_time', 'current_date', 'project_list', 'emp_list', 'resignation_details', 'termination_details'));
    }

    public function getEmployeeExitInterviewSummary()
    {

        return view('hranalytics::exit-interviews.employee-exit-interview-summary');
    }

    public function getEmployeeExitInterviewSummaryList(Request $request)
    {
        return datatables()->of($this->employeeExitInterviewRepository->getEmployeeSummaryList($request))->addIndexColumn()->toJson();

    }

    public function editsummary($id)
    {

        return response()->json($this->employeeExitInterviewRepository->get($id));
    }
     /**
     * Get projectallocatedemployee list
     * @param customerid
     * @return array
     */
    public function getAllocationList($customer_id = null)
    {
        return $this->customerEmployeeallocationRepository->allocationList($customer_id)->toArray();
    }


}
