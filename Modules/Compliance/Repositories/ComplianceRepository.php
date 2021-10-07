<?php

namespace Modules\Compliance\Repositories;

use Charts;
use Modules\Admin\Models\CompliancePolicy;
use Modules\Admin\Models\TrainingProfileRole;
use Modules\Admin\Repositories\CustomerEmployeeAllocationRepository;
use Modules\Admin\Repositories\UserRepository;
use Modules\Admin\Repositories\CompliancePolicyRoleRepository;
use Modules\Admin\Repositories\CompliancePolicyRepository;
use Modules\Compliance\Models\PolicyAcceptance;
use Modules\LearningAndTraining\Models\RegisterCourse;
use Illuminate\Support\Facades\Auth;
use Modules\Admin\Models\User;

class ComplianceRepository
{

    protected $trainingProfileRole, $customer_employee_allocation_repository,$policyAcceptance;

    /**
     * Create a new TrainingProfileRole instance.
     *
     * @param  Modules\Admin\Models\TrainingProfileRole $trainingProfileRole
     */
    public function __construct(
        TrainingProfileRole $trainingProfileRole,
        CustomerEmployeeAllocationRepository $customer_employee_allocation_repository,
        CompliancePolicy $compliancePolicies,
        PolicyAcceptance $policyAcceptance,
        UserRepository $userRepository,
        CompliancePolicyRoleRepository $compliancePolicyRoleRepository,
        CompliancePolicyRepository $compliancePolicyRepository
    ) {
        $this->trainingProfileRole = $trainingProfileRole;
        $this->customer_employee_allocation_repository = $customer_employee_allocation_repository;
        $this->compliancePolicies = $compliancePolicies;
        $this->policy_acceptance_model = $policyAcceptance;
        $this->userRepository = $userRepository;
        $this->compliancePolicyRoleRepository = $compliancePolicyRoleRepository;
        $this->compliancePolicyRepository = $compliancePolicyRepository;
    }

    /**
     * Return values for datatable
     *
     * @param $type
     */

    public function getIndexPage($id = null)
    {
       // $view_all_complaince=Auth::user()->can('view_compliance_all');
        if (!isset($id)) {
            $current_user = Auth::user()->id;
            $curr_user_role= Auth::user()->getRoleNames();
        } else {
            $current_user =$id;
            $curr_user_role= User::find($id)->getRoleNames();
        }
        $curr_user_role_str = "('".implode("','", data_get($curr_user_role, '*'))."')";
        $sql_total_policy_count =
            "SELECT count(distinct(j.id)) as total
            FROM compliance_policies j
            LEFT JOIN compliance_policy_roles pr
            ON
            j.id = pr.compliance_policy_id
            AND j.deleted_at IS NULL
            AND pr.deleted_at IS NULL
            WHERE j.status=1
            AND j.is_broadcasted=1
            AND pr.role IN $curr_user_role_str";
         //AND if('$view_all_complaince'=0, pr.role IN $curr_user_role_str OR pr.id IS NULL , 1=1)";
        $total_count = $this->elementPreparation($sql_total_policy_count);
        $element_total = $total_count == 0 ? $total_count : [$total_count, 0, $total_count];

        $data['policy_count_chart'] = $this->prepareChart($element_total, "Total Policies");

        $sql_compliant_count = "select count(DISTINCT policy_id) as total
        from policy_acceptances k
        where k.policy_id in(select id from compliance_policies j WHERE j.status=1 and j.is_broadcasted=1 and j.deleted_at IS NULL) and k.employee_id='$current_user'
        ";
        $compliant_count = $this->elementPreparation($sql_compliant_count);
        $element_compliant = $compliant_count == 0 ? $compliant_count : [$compliant_count, 0, $total_count];
        $data['chart_count_compliant'] = $this->prepareChart($element_compliant, "Total Compliant");
        $data['average'] = $this->calculateAverage($sql_compliant_count, $sql_total_policy_count);
        return $data;
    }

    public function getDatatablevalues($id = null)
    {
        $role_policies_ids = $role_policy_ids = [];
        $role_policies_ids = $this->compliancePolicyRoleRepository->getCompliancePoliciesRole($id);
        //$all_role_policies = $this->compliancePolicyRepository->getPolicyAllRoles();
        //$policy_ids = array_merge($role_policies_ids, $all_role_policies);
        $result = $this->compliancePolicyRepository->getPolicyIds($role_policies_ids);

        return $result;
    }

    /**
     * Return datatable values as array
     *
     * @param empty
     */
    public function prepareData($policy, $id = null)
    {

        $datatable_rows = array();
        //dump($policy);
        if (!isset($id)) {
            $each_row["role_name"] = \Auth::user()->roles[0]->name;
        } else {
            $each_row["role_name"]= User::find($id)->getRoleNames()->first();
        }

        foreach ($policy as $key => $each_policy) {
            $each_row["reference_code"] = $each_policy->reference_code;
            $each_row["policy_name"] = $each_policy->policy_name;
            $each_row["policy_description"] = $each_policy->policy_description;
            $each_row["compliance_policy_category"] = $each_policy->category->compliance_policy_category;
            $policies = clone ($each_policy);
            $ids = array();
            $employees = data_get($policies, 'policyAccept');
            $employee_key = 0;
            $userId=isset($id)?$id:\Auth::user()->id;
            foreach ($employees as $key => $value) {
                $ids[] = $value->employee_id;
                if ($userId == $value->employee_id) {
                    $updated_at = $value->updated_at->format('d-m-Y');
                    $employee_key = $key;
                }
            }
            $each_row["status"] = in_array($userId, $ids) ? 'Compliant' : 'Pending';
            $policyAccept = $each_policy->policyAccept;
            data_get($each_policy, 'policyAccept');
            $status = 'df';

            if (in_array($userId, $ids) && $policyAccept->isNotEmpty()) {
                $status = ($policyAccept[$employee_key]->agree == 1) ? 'Compliant' : 'Non-compliant';
            } else {
                $status = 'Pending';
            }
            $each_row["status"] = $status;

            if ($each_row['status'] == 'Pending') {
                $policy_roles = $each_policy->roles->pluck('role')->toArray();
                if (empty($policy_roles)) { //Indicates policies assigned to all roles
                    $each_row['is_compliant'] =  1;
                } else { // Indicates policies assigned to specified roles only
                    $each_row['is_compliant'] =  (in_array($each_row["role_name"], $policy_roles)) ? 1 : 0;
                }
            } else {
                $each_row['is_compliant'] = 0;
            }

            $each_row["policy_status"] = $each_policy->status;
            $each_row["updated_at"] = in_array($userId, $ids) ? $updated_at : "Not Applicable";
            $each_row["id"] = $each_policy->id;
            array_push($datatable_rows, $each_row);
        }
        return $datatable_rows;
    }

    /**
     *
     * @param type $sql
     * @return type
     */
    public function elementPreparation($sql)
    {
        $data = \DB::select(\DB::raw($sql));
        foreach ($data as $each_data) {
            $elements = $each_data->total;
        }
        return $elements;
    }

    /**
     *
     * @param type $element
     * @param type $element_label
     * @return type
     */
    public function prepareChart($element, $element_label)
    {
        $data = array(
            "chart" => array(
                "labels" => $element_label
            ),
            "datasets" => array(
                array(
                    array("name" => "Sample 1", "values" => $element,)
                )
            )
        );

        return (json_encode($data));
        //array('element' => $element, 'element_label' => $element_label);

//        return Charts::create('percentage', 'justgage')
//            ->title(false)
//            ->dimensions(0, 250) // Width x Height
//            ->template("material")
//            ->elementLabel($element_label)
//            ->values($element);
    }

    /**
     * Display details of single training course
     *
     * @param $id
     * @return object
     */
    public function get($id)
    {
        $data = $this->compliancePolicies->with(['category','disagreeReasons','agreeReasons'])->find($id);

        return $data;
    }

    /**
     * Store Details of registercourse
     *
     * @param $id
     * @return object
     */
    public function makeCompliant($request)
    {
        $id = $request->get('policy_id');
        $data = $this->compliancePolicies->where('id', '=', $id)->update(['status' => 0]);
        return $data;
    }

    /**
     * Create Record in policy acceptance
     *
     * @param $request
     * @return object
     */
    public function agreePolicy($request)
    {
        //dd($request->all());
        $policy = new PolicyAcceptance;
        $policy->policy_id = $request->policy_id;
        $policy->employee_id = $request->employee_id;
        $policy->agree = $request->agree;
        $policy->compliance_policy_agree_disagree_reason_id = $request->compliance_policy_agree_reason_id;
        $policy->comment = $request->comment;
        $policy->save();
    }

    /**
     * Calculate average to print in chart
     *
     * @param $sql_compliant_count
     * @param $sql_total_policy_count
     * @return chart
     */
    public function calculateAverage($sql_compliant_count, $sql_total_policy_count)
    {
        $compliant = \DB::select(\DB::raw($sql_compliant_count));
        foreach ($compliant as $each_data) {
            $compliant_count = $each_data->total;
        }
        $total_compliant = \DB::select(\DB::raw($sql_total_policy_count));
        foreach ($total_compliant as $each_data) {
            $total_compliant_count = $each_data->total;
        }
        if ($total_compliant_count != 0) {
            $average[] = (($compliant_count / $total_compliant_count) * 100);
        } else {
            $average[] = 0;
        }
//        return Charts::create('percentage', 'justgage')
//            ->title(false)
//            ->dimensions(0, 250) // Width x Height
//            ->template("material")
//            ->elementLabel('Average')
//            ->values($average);
    }
    /**
     *  To get the policy acceptance along with the agree reason
     *
     *  @param
     *  integer policy_id
     *  boolean compliant/non-compliant
     *
     *  @return array
     */
    public function getPolicyComplianceReasons($policy_id, $agree = null)
    {
        $policy_count = $this->policy_acceptance_model
                            ->select('compliance_policy_agree_disagree_reason_id', \DB::raw('count(*) as total'))
                            ->where('policy_id', $policy_id)
                            ->where('agree', $agree)
                            ->has('agreeDisagreeReason')
                            ->groupBy('compliance_policy_agree_disagree_reason_id')
                            ->with(['agreeDisagreeReason'])
                            ->get();
        return $policy_count;
    }

    /**
     * To get employee details voted for a compliance reason
     *  @param integer
     *  @return array
     *
     */
    public function getEmployeesComplianceReason($data)
    {
        $policy_id = $data['policy_id'];
        $reason_id = $data['reason_id'];
        $policy_count = $this->policy_acceptance_model
                            ->where('policy_id', $policy_id)
                            ->where('compliance_policy_agree_disagree_reason_id', $reason_id)
                            ->with(['employeeWithTrashed.trashedUser'])
                            ->get();
        $policy_count = $policy_count->map(function ($item) {

            $new_item['employee_id'] = $item->employeeWithTrashed->employee_no;
            $new_item['employee_name'] = $item->employeeWithTrashed->trashedUser->full_name;
            $new_item['phone_number'] = $item->employeeWithTrashed->phone;
            $new_item['email_address'] = $item->employeeWithTrashed->trashedUser->email;
            $new_item['date_completed'] = date('Y-m-d', strtotime($item->created_at));
            return $new_item;
        })  ;

        return $policy_count;
    }

    /***
     * To get pending employees for a  compliance policy
     *  @param integer
     *  @return array
     */
    public function getPendingCompliance($data)
    {
        $policy_id = $data['policy_id'];
        $completed = $data['completed'];

        $policy_count = $this->policy_acceptance_model
                            ->where('policy_id', $policy_id)
                            ->with(['employeeWithTrashed.trashedUser'])
                            //->groupBy('employee_id')
                            ->get();
        if ($completed == 1) { /** User has either agreed or disagrees on the policy  */
            $policy_count = $policy_count->map(function ($item) {

                $new_item['employee_id'] = $item->employeeWithTrashed->employee_no;
                $new_item['employee_name'] = $item->employeeWithTrashed->trashedUser->full_name;
                $new_item['phone_number'] = $item->employeeWithTrashed->phone;
                $new_item['email_address'] = $item->employeeWithTrashed->trashedUser->email;
                $new_item['date_completed'] = date('Y-m-d', strtotime($item->created_at));
                return $new_item;
            })  ;
        } else {
            /** Get pending user list for thr selected compliance policy */

            $compliant_user_ids = $policy_count->pluck('employee_id');
            $roles =  $this->compliancePolicyRoleRepository->getComplianceRoles($policy_id);
            $role_user_ids =  $this->userRepository->getUserLookup($roles, null, $active = true, $full_object = true, null, false, false)->pluck('id');

            $pending_users = $this->userRepository->getAllByUserIds($role_user_ids, $active = true, $compliant_user_ids);

            $policy_count = $pending_users->map(function ($item) {

                $new_item['employee_id'] = $item->employee->employee_no;
                $new_item['employee_name'] = $item->full_name;
                $new_item['phone_number'] = $item->employee->phone;
                $new_item['email_address'] = $item->email;
                return $new_item;
            })  ;
        }


        return $policy_count;
    }

    /***
     * To get the number of pending user compliance for a policy
     *
     * @param integer compliance_policy_id
     * @return array
     */
    public function getCountPendingUser($policy_id)
    {
        $compliant_user_count = $this->policy_acceptance_model
            ->select('employee_id')
            ->where('policy_id', $policy_id)
            ->distinct()->get()
            ->count();
        $roles =  $this->compliancePolicyRoleRepository->getComplianceRoles($policy_id);
        $users = $this->userRepository->getUserLookup($roles, null, true, false, null, false, false);
        $full_user_count = count($users);
        $pending_user_count = $full_user_count - $compliant_user_count;
        $result['data'] = [
            0 => [
                'name' => 'Completed Users',
                'color' => '#8fb15a',
                'y' => $compliant_user_count,
                'completed' => 1
            ],
            1 => [
                'name' => 'Pending Users',
                'color' => '#eb5669',
                'y' => $pending_user_count,
                'completed' => 0
            ],
        ];
        $result['labels'] = ['Completed Users','Pending Users'];
        return $result;
    }
}
