<?php

namespace Modules\Recruitment\Repositories;

use Modules\Recruitment\Models\RecJobRequisitionReasonLookup;


class RecJobRequisitionReasonLookupRepository
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
     * @param  Modules\Recruitment\Models\RecJobRequisitionReasonLookup $recJobRequisitionReasonLookupModel
     */
    public function __construct(RecJobRequisitionReasonLookup $recJobRequisitionReasonLookupModel)
    {
        $this->model = $recJobRequisitionReasonLookupModel;
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
