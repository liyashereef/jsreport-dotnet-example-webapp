<?php

namespace Modules\Admin\Repositories;

use App\Services\HelperService;
use Mail;
use Modules\Admin\Models\CompliancePolicy;
use Modules\Admin\Models\User;
use Modules\Compliance\Mail\PolicyBroadcast;
use Modules\Compliance\Models\PolicyAcceptance;
use Modules\Admin\Models\CompliancePolicyAgreeDisagreeReason;
use Modules\Admin\Repositories\UserRepository;
use Modules\Admin\Repositories\CompliancePolicyRoleRepository;

class CompliancePolicyRepository
{
    /**
     * The Model instance.
     * ''
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $model, $helperService, $userModel, $policyAcceptance, $compliancePolicyAgreeDisagreeReasonModel;

    /**
     * Create a new CompliancePolicyRepository instance.
     *
     * @param  \App\Models\CompliancePolicy $compliancePolicy
     *
     */
    public function __construct(
        CompliancePolicy $compliancePolicyModel,
        HelperService $helperService,
        User $userModel,
        PolicyAcceptance $policyAcceptance,
        CompliancePolicyAgreeDisagreeReason $compliancePolicyAgreeDisagreeReasonModel,
        UserRepository $userRepository,
        CompliancePolicyRoleRepository $compliancePolicyRoleRepository
    ) {
        $this->model = $compliancePolicyModel;
        $this->helperService = $helperService;
        $this->userModel = $userModel;
        $this->policyAcceptance = $policyAcceptance;
        $this->compliancePolicyAgreeDisagreeReasonModel = $compliancePolicyAgreeDisagreeReasonModel;
        $this->userRepository = $userRepository;
        $this->compliancePolicyRoleRepository = $compliancePolicyRoleRepository;
    }

    /**
     * Get training category list
     *
     * @param empty
     * @return array
     */
    public function getAll()
    {
        return $this->model->orderby('policy_name', 'asc')->with('category')->withCount('policyAccept')->get();
    }

    /**
     * Get training category list
     *
     * @param empty
     * @return array
     */
    public function getList()
    {
        return $this->model->orderby('policy_name', 'asc')->pluck('policy_name', 'id')->toArray();
    }

    /**
     * Display details of single training category
     *
     * @param $id
     * @return object
     */
    public function get($id)
    {
        return $this->model->find($id)->load(['agreeDisagreeReasons','roles']);
    }

    /**
     * Store a newly created training category in storage.
     *
     * @param  $data
     * @return object
     */
    public function save($data)
    {
        $result = $this->model->updateOrCreate(array('id' => $data['id']), $data);
        if ($data['id'] === null) {
            $reference_code = $this->helperService->getUniqueReferenceCode($result->id, [$result->policy_name, $result->category->compliance_policy_category]);
            $this->model->where('id', '=', $result->id)->update(['reference_code' => $reference_code]);
        }
        $created = $result->wasRecentlyCreated;
        $result = $result->fresh();
        $result['created'] = $created;

        return $result;
    }

    /**
     * To upload a file
     *
     * @param [type] $data
     * @param [type] $model
     * @return void
     */
    public function uploadFile($data, $model)
    {
        $this->model = $model;
        $fileName = $this->helperService->sanitiseString($this->model->policy_name) . '-' . time() . '.' . $data['policy_file']->getClientOriginalExtension();
        $path = public_path() . '/policy_files';
        \File::isDirectory($path) or \File::makeDirectory($path, 0777, true, true);
        $data['policy_file']->move(public_path('policy_files'), $fileName);
        $this->model->where('id', '=', $this->model->id)->update(['policy_file' => $fileName]);
        $this->model = $this->model->fresh();
        return $this->model;
    }

    /**
     * Remove the specified training category from storage.
     *
     * @param  $id
     * @return object
     */
    public function delete($id)
    {
        $this->model = $this->model->find($id);
        $delete_accepatance = $this->policyAcceptance->where('policy_id', $id)->delete();
        $path = public_path() . '/policy_files';
        \File::delete($path . '/' . $this->model->policy_file);
        return $this->model->destroy($id);
    }

    /**
     * Broadcast policys to all users
     *
     * @param  $request
     * @return object
     */
    public function policyBroadcasting($request)
    {
        $policy_id = $request->id;
        $roles =  $this->compliancePolicyRoleRepository->getComplianceRoles($policy_id);
        //todo::chagne roles by permission
        if (!$roles) {
            $user_mail_ids = $this->userModel->pluck('email')->toArray();
        } else {
            $user_mail_ids =  $this->userRepository->getUserLookup($roles, null, true, true, null, false, false)
            ->pluck('email')->toArray();
        }
        try {
            foreach ($user_mail_ids as $to) {
                Mail::to($to)->queue(new PolicyBroadcast($request->all(), 'mail.policy.created'));
            }
        } catch (Throwable $th) {
            //throw $th;
            return "false";
        }
      
        $policy_status = CompliancePolicy::where('id', $policy_id)->update(['is_broadcasted' => 1]);
        return $policy_status;
    }

    /**
     * Get all user detailswith respect to Complaint policys and Non Complaint policys
     *
     * @param  $request
     * @return object
     */
    public function getAllPolicies($request)
    {
        $category = $request->get('category');
        $policy_name = $request->get('policy');
        $policy = $this->model->select('id')->where('policy_name', $category)->first();
        if ($policy_name == 'Compliant') {
            $employee_list = $this->policyAcceptance->where('policy_id', $policy->id)->with('employeeWithTrashed', 'employeeWithTrashed.trashedUser')->get();
        } else {
            $employees_id = $this->policyAcceptance->where('policy_id', $policy->id)->pluck('employee_id')->toArray();
            $employee_list = $this->userModel->with('employee')->whereNotIn('id', $employees_id)->get();
        }

        return $employee_list;
    }

    /**
     * Prepare Datatable values as array.
     *
     *@param  $employee_list
     * @return array
     */
    public function makeDatatablevalues($employee_list)
    {
        $base_class = class_basename($employee_list->first());
        $datatable_rows = array();
        if ($base_class == 'PolicyAcceptance') {
            foreach ($employee_list as $key => $each_employee) {
                $each_row["employee_id"] = $each_employee->employeeWithTrashed->employee_no;
                $each_row["employee_name"] = $each_employee->employeeWithTrashed->trashedUser->first_name . ' ' . $each_employee->employeeWithTrashed->trashedUser->last_name;
                $each_row["phone_number"] = $each_employee->employeeWithTrashed->phone;
                $each_row["email_address"] = $each_employee->employeeWithTrashed->trashedUser->email;
                $each_row["date_completed"] = $each_employee->created_at->format('M d,Y');
                array_push($datatable_rows, $each_row);
            }
        } else {
            foreach ($employee_list as $key => $each_employee) {
                $each_row["employee_id"] = $each_employee->employee->employee_no;
                $each_row["employee_name"] = $each_employee->first_name . ' ' . $each_employee->last_name;
                $each_row["phone_number"] = $each_employee->employee->phone;
                $each_row["email_address"] = $each_employee->email;
                $each_row["date_completed"] = '--';
                array_push($datatable_rows, $each_row);
            }
        }
        return $datatable_rows;
    }

    /**
     *  Get policies assigned to all roles
     *
     * @param empty
     * @return array
     */
    public function getPolicyAllRoles()
    {
        return $this->model->where('is_broadcasted', 1)->where('status', 1)->get()->pluck('id')->toArray();
    }

    /**
     *  Get Policies with corresponding ids
     *
     *  @param array policy_ids
     *  @return array
     *
     */
    public function getPolicyIds($policy_ids = null)
    {
        return $this->model->whereIn('id', $policy_ids)
                    ->with(['category','policyAccept.employee','roles'])
                    ->get();
    }
}
