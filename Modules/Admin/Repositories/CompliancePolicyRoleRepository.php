<?php

namespace Modules\Admin\Repositories;

use Modules\Admin\Models\CompliancePolicyRole;
use Illuminate\Support\Facades\Auth;
use Modules\Admin\Models\User;
use Modules\Admin\Repositories\UserRepository;

class CompliancePolicyRoleRepository
{
    /**
     * The Model instance.
     * ''
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $model;

    /**
     * Create a new CompliancePolicyRoleRepository instance.
     *
     * @param  \App\Models\CompliancePolicyAgreeDisagreeReason $compliancePolicyRole
     *
     */
    public function __construct(CompliancePolicyRole $compliancePolicyRoleModel, UserRepository $userRepository)
    {
        $this->model = $compliancePolicyRoleModel;
        $this->userRepository = $userRepository;
    }

    /**
     * Get agree/disgree reason lists
     *
     * @param empty
     * @return array
     */
    public function getAll()
    {
        return $this->model->get();
    }

    /**
     * Display details of agree/disgree reason
     *
     * @param $id
     * @return object
     */
    public function get($id)
    {
        return $this->model->find($id);
    }

    /**
     * Store a newly created agree/disagree reason in storage.
     *
     * @param  $data
     * @return object
     */
    public function save($data)
    {
        //return $this->model->updateOrCreate(array('id' => $data['id']), $data);
        return $this->model->create($data);
    }

    /**
     * Store  agree/disgree reason in storage.
     *
     * @param  $data
     * @return object
     */
    public function saveAll($data)
    {
        $complianceRoles = $this->userRepository->getRoleLookup();
        if (!empty($data['compliance_policy_roles'])) {
            if (!in_array('all_roles', $data['compliance_policy_roles'])) {
                foreach ($data['compliance_policy_roles'] as $compliance_policy_roles) {
                    $compliance_policy_role_data['compliance_policy_id'] = $data['compliance_policy_id'];
                    $compliance_policy_role_data['role'] = $compliance_policy_roles;
                    $compliance_policy_role_result = $this->save($compliance_policy_role_data);
                }
            } else {
                foreach ($complianceRoles as $compliance_policy_roles => $compliance_policy_roles_label) {
                    $compliance_policy_role_data['compliance_policy_id'] = $data['compliance_policy_id'];
                    $compliance_policy_role_data['role'] = $compliance_policy_roles;
                    $compliance_policy_role_result = $this->save($compliance_policy_role_data);
                }
            }
        }
    }

    /**
     * Remove all the reasons for a specific compliance
     *
     * @param  $id
     * @return object
     */
    public function deleteComplianceRole($compliance_policy_id)
    {

        $this->model->where('compliance_policy_id', $compliance_policy_id)->delete();
        return true;
    }

    /**
     *  Get roles of a policy
     *  @param integer policy_id
     *  @return array roles
     *
     */
    public function getComplianceRoles($policy_id)
    {
        $roles  =  $this->model
            ->where('compliance_policy_id', $policy_id)
            ->pluck('role');
            //dd($roles);
        $roles = ($roles->isEmpty()) ? null : $roles;
        return $roles;
    }
    /***
     *  Get Compliance policies based on role
     *
     *  @param empty
     *  @return array policy_ids
     */
    public function getCompliancePoliciesRole($id = null)
    {
        if (!isset($id)) {
            $logged_in_user = Auth::user();
        } else {
            $logged_in_user =User::find($id);
        }
        // $logged_in_user = Auth::user();
        $roles_of_logged_in_user = $logged_in_user->getRoleNames();
        $query = $this->model;
        // List only role based policies if view all permission is not set
        if (!Auth::user()->can('view_compliance_all') || (isset($id))) {
            $query = $query->whereIn('role', $roles_of_logged_in_user);
        }
        $result = $query->whereHas('policy', function ($query) {
                            $query->where('status', 1)->where('is_broadcasted', 1);
        })
                        ->get();
        $policy_ids = $result->pluck('policy.id')->toArray();
        return $policy_ids;
    }
}
