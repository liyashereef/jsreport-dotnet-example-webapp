<?php

namespace Modules\Hranalytics\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\HelperService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\Admin\Models\Employee;
use Modules\Admin\Models\EmployeeRatingLookup;
use Modules\Admin\Models\PositionLookup;
use Modules\Admin\Models\SecurityClearanceLookup;
use Modules\Admin\Repositories\CustomerEmployeeAllocationRepository;
use Modules\Admin\Repositories\CustomerRepository;
use Modules\Admin\Repositories\EmployeeAllocationRepository;
use Modules\Admin\Repositories\PermissionMappingRepository;
use Modules\Admin\Repositories\RolesAndPermissionRepository;
use Modules\Timetracker\Repositories\EmployeeShiftAprovalRatingRepository;
use Modules\Admin\Repositories\UserRepository;
use Modules\Client\Models\ClientEmployeeFeedback;
use Modules\Compliance\Repositories\ComplianceRepository;
use Modules\Hranalytics\Http\Requests\UserRatingRequest;
use Modules\Hranalytics\Models\UserRating;
use Modules\Hranalytics\Repositories\EmployeeMapRepository;
use Modules\LearningAndTraining\Repositories\TrainingUserCourseAllocationRepository;
use Modules\LearningAndTraining\Repositories\TrainingUserTeamRepositories;
use Modules\Timetracker\Models\TimeSheetApprovalRating;
use Spatie\Permission\Models\Permission;
use Modules\Admin\Models\EmployeeRatingPolicies;

class EmployeeController extends Controller
{

    protected $helperService;
    protected $employeeModel;
    protected $user_repository;
    protected $employee_allocation;
    protected $employeeShiftAprovalRatingRepository;

    public function __construct(
        CustomerEmployeeAllocationRepository $customerEmployeeAllocationRepository,
        CustomerRepository $customer_repository,
        EmployeeMapRepository $employeeMapRepository,
        PermissionMappingRepository $permissionMappingRepository,
        ComplianceRepository $complianceRepository,
        RolesAndPermissionRepository $rolesAndPermissionRepository,
        EmployeeShiftAprovalRatingRepository $employeeShiftAprovalRatingRepository,
        Employee $employeeModel
    ) {
        $this->employeeModel = $employeeModel;
        $this->user_repository = new UserRepository();
        $this->helperService = new HelperService();
        $this->employee_allocation = new EmployeeAllocationRepository();
        $this->employee_rating_lookups = new EmployeeRatingLookup();
        $this->employee_team = new TrainingUserTeamRepositories();
        $this->employee_course_allocation = new TrainingUserCourseAllocationRepository();
        $this->customerEmployee_allocation_repository = $customerEmployeeAllocationRepository;
        $this->customer_repository = $customer_repository;
        $this->employee_map = $employeeMapRepository;
        $this->permissionMappingRepository = $permissionMappingRepository;
        $this->complianceRepository = $complianceRepository;
        $this->rolesAndPermissionRepository = $rolesAndPermissionRepository;
        $this->employeeShiftAprovalRatingRepository = $employeeShiftAprovalRatingRepository;
    }
    /**
     * Get Employees in Map.
     *
     * @param  nil
     * @return \Illuminate\Http\Response
     */
    public function getEmployeesMap(Request $request)
    {
        $user = \Auth::user();
        $current_role = \Auth::user()->roles[0]->name;
        $position = PositionLookup::orderBy('position', 'asc')->pluck('position', 'id')->toArray();
        $security_clearance = SecurityClearanceLookup::pluck('security_clearance', 'id')->toArray();
        if ($user->hasAnyPermission(['admin', 'super_admin'])) {
            $fundamental_roles = $this->rolesAndPermissionRepository->getBasePermissionAsRoleArray();
        } else {
            $permission_arr = $this->permissionMappingRepository->getPermissionBasedOnRole(\Auth::user()->roles[0]->id)->pluck('permission_id')->toArray();
            $mappedRolePermissions = Permission::whereIn('id', $permission_arr)->pluck('name', 'name')->toArray();
            $fundamental_roles = array_map(function ($mappedRolePermissions) {
                return ucwords(str_replace("_", " ", $mappedRolePermissions));
            }, $mappedRolePermissions);
        }
        $role = isset($request->fundamental_role) ? $request->fundamental_role : array_keys($fundamental_roles);
        $user_id = \Auth::user()->id;
        if ($user->hasAnyPermission(['admin', 'super_admin'])) {
            $allocated_employees = $this->user_repository->getUserLookup($role, ['super_admin', 'admin'], true, true, null, true);
        } else {
            $allocated_employees = $this->employee_allocation->getEmployeeAssigned($user_id, $role, true);
        }
        $allocated_employees = $this->employee_map->getFilter($request, $allocated_employees);
        $list_data = $this->employee_map->prepareDataForEmployeeMap($allocated_employees);
        $form_route = route('employee.mapping');
        return view('hranalytics::employee-map.employee-maping', compact('list_data', 'form_route', 'position', 'security_clearance', 'request', 'role', 'fundamental_roles'));
    }

    /**
     * Get Employees Performance Log.
     *
     * @param $id
     * @return \Illuminate\Http\Response
     */
    public function getPerfomanceLog($id)
    {
        $ratingLookups = $this->employee_rating_lookups
            ->orderBy('score', 'ASC')
            ->pluck('rating', 'id')
            ->toArray();
        $user = $this->user_repository->getUserDetails($id);
        $employee_rating = UserRating::with('user', 'userRating', 'policyDetails')->where('employee_id', $id)->orderBy('created_at', 'desc')->get();
        $client_rating = ClientEmployeeFeedback::where('user_id', $id)->with(['createdUser', 'userRating'])->get();
        $total_hours = \DB::select("select sum(hours_off) as hours from customer_report_adhocs where deleted_at IS NULL AND employee_id='$id'");
        $hours = isset($total_hours[0]->hours) ? $total_hours[0]->hours : 0;
        $team_lists = $this->employee_team->getAllByUserId($id);
        $project_list = $this->customerEmployee_allocation_repository->getAllocatedCustomersList(\Auth::user());
        $data = $this->complianceRepository->getIndexPage($id);
        $policy_count_chart = $data['policy_count_chart'];
        $compliant_count_chart = $data['chart_count_compliant'];
        $average = $data['average'];
        $employeeDeatils = $this->employeeModel->where('user_id', $id)->first();
        $employeeTimesheetApprovalScore = $this->employee_rating_lookups->where('score', round($employeeDeatils->time_sheet_approval_rating))->first();
        $timeSheetApprovalRating = TimeSheetApprovalRating::where('user_id', $id)->with(['shiftPayperiod', 'payperiod', 'timesheetApprovalPayPeriodRating'])->orderBy('created_at', 'desc')->first();
        $policyDeatils = EmployeeRatingPolicies::where('id', 41)->withTrashed()->first();
        return view('hranalytics::employee-map.performance-log', compact('employeeTimesheetApprovalScore', 'timeSheetApprovalRating', 'policyDeatils', 'ratingLookups', 'user', 'employee_rating', 'client_rating', 'hours', 'team_lists', 'project_list', 'policy_count_chart', 'compliant_count_chart', 'average'));
    }

    /**
     * Get Employees course List.
     *
     * @param $id
     * @return \Illuminate\Http\Response
     */

    public function getCourseAllocation($user_id)
    {
        return datatables()->of($this->employee_course_allocation->getAllUserAllocation($user_id))->toJson();
    }

    /**
     * Store Employees Performance Log.
     *
     * @param \Illuminate\Http\Request
     * @return \Illuminate\Http\Response
     */
    public function storePerfomance(UserRatingRequest $request)
    {
        $rate_employee = $this->employee_map->storeEmployeeRating($request);
        return response()->json($rate_employee);
    }

    /**
     *List Employees Leave Log.
     *
     * @param \Illuminate\Http\Request
     * @return \Illuminate\Http\Response
     */
    public function employeeTimeOffList($id)
    {
        return datatables()->of($this->employee_map->listEmployeeTimeoff($id))->addIndexColumn()->toJson();
    }

    public function exitInterview()
    {
        $mutable = Carbon::now();
        echo $mutable;
    }

    public function employeeTimesheetApprovalRatingList($employeeId)
    {
        return datatables()->of($this->employeeShiftAprovalRatingRepository->employeeTimesheetApprovalRatingList($employeeId))->addIndexColumn()->toJson();
    }

    public function getTimesheetapprovalbypayperiod($payperiodId, $userId)
    {
        $data['cpids'] = $this->employeeShiftAprovalRatingRepository->timesheetApprovalByPayperiod($payperiodId, $userId);
        return $data;
    }

    public function timeSheetApprovalRatingDestroy($id)
    {
        try {
            \DB::beginTransaction();
            $this->employeeShiftAprovalRatingRepository->timeSheetApprovalRatingDelete($id);
            \DB::commit();
            return response()->json($this->helperService->returnTrueResponse());
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json($this->helperService->returnFalseResponse());
        }
    }

    public function deleteManagerRating($id)
    {
        return response()->json($this->helperService->returnTrueResponse());
        try {
            $r = UserRating::find($id);
            if ($r) {
                DB::beginTransaction();
                $r->delete();
                DB::commit();
                $rating = $this->employee_map->averageRating($r->employee_id);
                Employee::where('user_id', $r->employee_id)
                    ->update(['employee_rating' => $rating]);
                return response()->json($this->helperService->returnTrueResponse());
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json($this->helperService->returnFalseResponse());
        }
    }

    public function deleteClientRating($id)
    {
        return response()->json($this->helperService->returnTrueResponse());

        try {
            DB::beginTransaction();
            ClientEmployeeFeedback::destroy($id);
            DB::commit();
            return response()->json($this->helperService->returnTrueResponse());
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json($this->helperService->returnFalseResponse());
        }
    }
}
