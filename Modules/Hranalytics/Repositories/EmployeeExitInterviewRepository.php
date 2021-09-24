<?php

namespace Modules\Hranalytics\Repositories;

use App\Services\HelperService;
use Auth;
use Carbon\Carbon;
use Modules\Admin\Models\Employee;
use Modules\Admin\Models\User;
use Modules\Admin\Repositories\CustomerEmployeeAllocationRepository;
use Modules\Admin\Repositories\CustomerRepository;
use Modules\Admin\Repositories\EmployeeAllocationRepository;
use Modules\Admin\Repositories\ExitResignationReasonLookupRepository;
use Modules\Hranalytics\Models\EmployeeExitInterview;

class EmployeeExitInterviewRepository
{
    protected $EmployeesModel, $employeeexitinterviewModel, $userModel, $exitResignationReasonLookupRepository, $employeeAllocationRepository;

    /**
     * The Model instance.
     * ''
     * @var \Illuminate\Database\Eloquent\Model
     */


    public function __construct(
        EmployeeExitInterview $employeeexitinterviewModel,
        CustomerEmployeeAllocationRepository $customerEmployeeallocationRepository,
        CustomerRepository $customerRepository,
        Employee $EmployeesModel,
        User $userModel,
        ExitResignationReasonLookupRepository $exitResignationReasonLookupRepository,
        EmployeeAllocationRepository $employeeAllocationRepository
    ) {
        $this->EmployeesModel = $EmployeesModel;
        $this->employeeexitinterviewModel = $employeeexitinterviewModel;
        $this->customerEmployeeallocationRepository = $customerEmployeeallocationRepository;
        $this->customerRepository = $customerRepository;
        $this->userModel = $userModel;
        $this->exitResignationReasonLookupRepository = $exitResignationReasonLookupRepository;
        $this->employeeAllocationRepository = $employeeAllocationRepository;
    }

    public function save($request)
    {

        $exit_interview = new EmployeeExitInterview;
        $data = $request->all();
        $empid = $data['employee_name_id'];
        $exit_interview->unique_id = $this->genrateID($empid);
        $exit_interview->project_id = $data['project_name_id'];
        $exit_interview->user_id = $data['employee_name_id'];
        $exit_interview->exit_interview_reason_id = $data['reason_id'];
        $reasonID = $exit_interview->exit_interview_reason_id;
        if ($reasonID == RESIGNATION) {
            $exit_interview->exit_interview_reason_details = $data['resignation_reason_id'];
        } else {
            $exit_interview->exit_interview_reason_details = $data['termination_reason_id'];
        }
        $exit_interview->exit_interview_explanation = $data['exit_interview_explantion'];
        $exit_interview->created_by = Auth::user()->id;
        if ($exit_interview->save()) {
            return $exit_interview;
        } else {
            return false;
        }
    }

    public function genrateID($empid)
    {
        $employeeno = $this->EmployeesModel->where('user_id', $empid)->pluck('employee_no')->first();
        $string = strtoupper(substr(Auth::user()->first_name, 0, 2)) . $employeeno;
        return $string;
    }
    /**
     * Get projectallocatedemployee list
     * @param customerid
     * @return array
     */
    // public function getAllocationList($customer_id = null)
    // {
    //     return datatables()->of( $this->customerEmployeeallocationRepositoryallocationList($customer_id))->toJson();
    // }
    /**
     * Get resignationemployee lookup list
     *
     * @param empty
     * @return array
     */

    public function getResignationList()
    {
        $id_array = array();
        if (\Auth::user()->can('view_exit_interview')) {
            $id_array = $this->employeeAllocationRepository->getEmployeeIdAssigned(\Auth::user()->id);
        }
        if (\Auth::user()->can('view_all_exit_interview')) {
            $id_array = $this->getResignedUsers();
            array_push($id_array, \Auth::user()->id);
        }
        $resigned_list = $this->employeeexitinterviewModel->select('unique_id', 'id', 'user_id', 'project_id')->with('customer', 'user', 'user.trashedEmployee')->whereIn('user_id', $id_array)->where('exit_interview_reason_id', RESIGNATION)->get();
        $resignation_arr = array();
        foreach ($resigned_list as $key => $each_list) {
            $resignation_arr[$each_list->id] = $each_list->user->full_name . ' (' . $each_list->user->trashedEmployee->employee_no . ')' . '-' . $each_list->unique_id . '-' . '(' . $each_list->customer->project_number . ')';
        }
        return $resignation_arr;
    }

    /**
     * Get Terminationemployee lookup list
     *
     * @param empty
     * @return array
     */

    public function getTerminationList()
    {
        $id_array = array();
        if (\Auth::user()->can('view_exit_interview')) {
            $id_array = $this->employeeAllocationRepository->getEmployeeIdAssigned(\Auth::user()->id);
        }
        if (\Auth::user()->can('view_all_exit_interview')) {
            $id_array = $this->getTerminatedUsers();
            array_push($id_array, \Auth::user()->id);
        }
        $terminated_list = $this->employeeexitinterviewModel->select('unique_id', 'id', 'user_id', 'project_id')->with('customer', 'user', 'user.trashedEmployee')->whereIn('user_id', $id_array)->where('exit_interview_reason_id', TERMINATION)->get();
        $termination_arr = array();
        foreach ($terminated_list as $key => $each_list) {
            $termination_arr[$each_list->id] = $each_list->user->full_name . ' (' . $each_list->user->trashedEmployee->employee_no . ')' . '-' . $each_list->unique_id . '-' . '(' . $each_list->customer->project_number . ')';
        }
        return $termination_arr;
    }

    public function getEmployeeSummaryList($request)
    {
        $customer_ids = array();
        $user = \Auth::user();
        if (\Auth::user()->can('view_all_exit_interview') || $user->hasAnyPermission(['admin', 'super_admin'])) {
            $customer_ids = $this->customerRepository->getAllCustomers(\Auth::user());
        } elseif (\Auth::user()->can('view_exit_interview')) {
            $arr_user = [Auth::User()->id];
            $customer_ids = $this->customerRepository->getAllAllocatedCustomerId($arr_user);
        }

        $from = $request->input('from');
        $to = $request->input('to');
        $cids = $request->input('cids');


        $query = $this->employeeexitinterviewModel
            ->select([
                'id', 'unique_id',
                'project_id',
                'user_id',
                'exit_interview_reason_id',
                'exit_interview_reason_details',
                'exit_interview_explanation',
                'created_by',
                'created_at'
            ]);
        // if user has view all permission, then no need for condition
        if (!\Auth::user()->can('view_all_exit_interview')) {
            // this user doesnt have view all permission
            if (!\Auth::user()->can('view_exit_interview')) {
                // if user doesnt have allocated permission
                $query->where('created_by', \Auth::user()->id);
            } else {
                // if user has only allocated permission
                $query->where(function ($q) use ($customer_ids) {
                    $q->whereIn('project_id', $customer_ids)
                        ->orWhere('created_by', \Auth::user()->id);
                });
            }
        }

        //Filter by from and to date
        if (!empty($from) && !empty($to)) {
            $query = $query->whereDate('created_at', '<=', Carbon::parse($to));
            $query = $query->whereDate('created_at', '>=', Carbon::parse($from));
        }
        //filter customer ids
        if (is_array($cids) && !empty($cids)) {
            $ids =  array_intersect($customer_ids, $cids);
            $query->whereIn('project_id', $ids);
        }

        $regional_manager_list = $query->with(['user', 'regional_manager', 'reason_detail_termination', 'reason_detail_resignation', 'customer'])->get();
        return $this->prepareDataForEmployeeExitSummary($regional_manager_list, $customer_ids);
    }

    public function prepareDataForEmployeeExitSummary($regional_manager_list, $customer_ids)
    {
        $datatable_rows = array();
        foreach ($regional_manager_list as $key => $each_list) {
            $each_row["id"] = $each_list->id;
            $each_row["unique_id"] = $each_list->unique_id;
            $each_row["regional_manager"] = $each_list->regional_manager->full_name;
            $each_row["date"] = HelperService::getFormattedDate($each_list->created_at);
            $each_row["date_raw"] = $each_list->created_at;
            $user  = $this->userModel->withTrashed()->with(['trashedEmployee'])->find($each_list->user_id);
            $each_row["employee_details"] = $user->trashedEmployee->employee_no ? data_get($user, 'FullName') . ' ' . '(' . $user->trashedEmployee->employee_no . ')' : data_get($user, 'FullName');
            $reason_detail = $each_list->exit_interview_reason_id;
            if ($reason_detail == 1) {
                $each_row["reason"] = "Resigned";
                $each_row["reason_details"] = $each_list->reason_detail_resignation->reason;
            } else {
                $each_row["reason"] = "Terminated or Removed from Site";
                $each_row["reason_details"] = $each_list->reason_detail_termination->reason;
            }
            $each_row["exit_interview_explanation"] = $each_list->exit_interview_explanation;
            $each_row["site_details"] = $each_list->customer->client_name . ' ' . '(' . $each_list->customer->project_number . ')';

            array_push($datatable_rows, $each_row);
        }
        return $datatable_rows;
    }
    /**
     * Get all Resigned Users list.
     *
     * @param  $id
     * @return object
     */
    public function getResignedUsers()
    {
        return $this->employeeexitinterviewModel->where('exit_interview_reason_id', 1)->pluck('user_id')->toArray();
    }

    /**
     * Get all Terminated Users list.
     *
     * @param  $id
     * @return object
     */
    public function getTerminatedUsers()
    {
        return $this->employeeexitinterviewModel->where('exit_interview_reason_id', 2)->pluck('user_id')->toArray();
    }

    /**
     * Get exit interview reason and details
     *
     * @param $id exit interview id
     * @return array
     */
    public function getReason($id)
    {
        $exitInterivewQuery = $this->employeeexitinterviewModel->find($id)->toArray();
        $exitReason = array();

        if ($exitInterivewQuery->exit_interview_reason_id == 1) {
            $exitReason["reason"] = "Resigned";
            $exitReason["reasonDetails"] = $exitInterivewQuery->reason_detail_resignation->reason;
        } else {
            $exitReason["reason"] = "Terminated or Removed from Site";
            $exitReason["reasonDetails"] = $exitInterivewQuery->reason_detail_termination->reason;
        }
        return $exitReason;
    }
}
