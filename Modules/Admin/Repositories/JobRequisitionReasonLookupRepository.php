<?php

namespace Modules\Admin\Repositories;

use Modules\Admin\Models\JobRequisitionReasonLookup;

class JobRequisitionReasonLookupRepository
{
    /**
     * The Model instance.
     * ''
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $model;

    /**
     * Create a new SecurityClearanceLookupRepository instance.
     *
     * @param  \App\Models\JobRequisitionReasonLookupLookup $JobRequisitionReasonLookupModel
     */
    public function __construct(JobRequisitionReasonLookup $jobRequisitionReasonLookupModel)
    {
        $this->model = $jobRequisitionReasonLookupModel;
    }

    /**
     * Get security clearance lookup list
     *
     * @param empty
     * @return array
     */
    public function getAll()
    {
        return $this->model->select(['id', 'reason','created_at','updated_at'])->get();
    }

    /**
     * Display details of single Security Clearance
     *
     * @param $id
     * @return object
     */
    public function get($id)
    {
        return $this->model->find($id);
    }

    /**
     * Store a newly created Security Clearance in storage.
     *
     * @param  $data
     * @return object
     */
    public function save($data)
    {
        $lookup = $this->model->updateOrCreate(array('id' => $data['id']), $data);
        return $lookup;
    }

}
