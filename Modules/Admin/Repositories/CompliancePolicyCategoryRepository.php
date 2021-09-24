<?php

namespace Modules\Admin\Repositories;

use Modules\Admin\Models\CompliancePolicy;
use Modules\Admin\Models\CompliancePolicyCategory;

class CompliancePolicyCategoryRepository
{
    /**
     * The Model instance.
     * ''
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $model;

    /**
     * Create a new CompliancePolicyCategoryRepository instance.
     *
     * @param  \App\Models\CompliancePolicyCategory $compliancePolicyCategory
     *
     */
    public function __construct(CompliancePolicyCategory $compliancePolicyCategoryModel, CompliancePolicy $compliancePolicyModel)
    {
        $this->model = $compliancePolicyCategoryModel;
        $this->policyModel = $compliancePolicyModel;
    }

    /**
     * Get training category list
     *
     * @param empty
     * @return array
     */
    public function getAll()
    {
        return $this->model->orderby('compliance_policy_category', 'asc')->get();
    }

    /**
     * Get training category list
     *
     * @param empty
     * @return array
     */
    public function getList()
    {
        return $this->model->orderby('compliance_policy_category', 'asc')->pluck('compliance_policy_category', 'id')->toArray();
    }

    /**
     * Display details of single training category
     *
     * @param $id
     * @return object
     */
    public function get($id)
    {
        return $this->model->find($id);
    }

    /**
     * Store a newly created training category in storage.
     *
     * @param  $data
     * @return object
     */
    public function save($data)
    {
        return $this->model->updateOrCreate(array('id' => $data['id']), $data);
    }

    /**
     * Remove the specified training category from storage.
     *
     * @param  $id
     * @return object
     */
    public function delete($id)
    {

        $category_id = $this->policyModel->pluck('compliance_policy_category_id')->toArray();
        if (in_array($id, $category_id)) {
            return false;
        } else {
            return $this->model->destroy($id);
        }
    }
}
