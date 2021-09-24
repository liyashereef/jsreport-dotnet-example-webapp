<?php

namespace Modules\Admin\Repositories;

use Modules\Admin\Models\CompliancePolicyAgreeDisagreeReason;


class CompliancePolicyAgreeDisagreeRepository
{
    /**
     * The Model instance.
     * ''
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $model;

    /**
     * Create a new CompliancePolicyAgreeDisagreeReason instance.
     *
     * @param  \App\Models\CompliancePolicyAgreeDisagreeReason $CompliancePolicyAgreeDisagreeReason
     *
     */
    public function __construct(CompliancePolicyAgreeDisagreeReason $compliancePolicyAgreeDisagreeReasonModel )
    {
        $this->model = $compliancePolicyAgreeDisagreeReasonModel;
       
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
        $user_id = \Auth::id();

        if(!empty($data['agree_reasons']))
        {
            $agree_reasons = explode(',',$data['agree_reasons']);
            foreach($agree_reasons as $agree_reason_data){
                $reason_data['compliance_policy_id'] = $data['compliance_policy_id'];
                $reason_data['agree_or_disagree'] = 1;
                $reason_data['reason'] = $agree_reason_data;
                $reason_data['created_by'] = $user_id;
                $agree_result = $this->save($reason_data);
            }
        }
        if(!empty($data['disagree_reasons']))
        {
            $disagree_reasons = explode(',',$data['disagree_reasons']);
            foreach($disagree_reasons as $disagree_reason_data){
                $reason_data['compliance_policy_id'] = $data['compliance_policy_id'];
                $reason_data['agree_or_disagree'] = 0;
                $reason_data['reason'] = $disagree_reason_data;
                $reason_data['created_by'] = $user_id;
                $disagree_result = $this->save($reason_data);
            }
        }

       
        
    }

    /**
     * Remove all the reasons for a specific compliance
     *
     * @param  $id
     * @return object
     */
    public function deleteComplianceReason($compliance_policy_id)
    {

        $this->model->where('compliance_policy_id',$compliance_policy_id)->delete();
        return true;
    }
}
