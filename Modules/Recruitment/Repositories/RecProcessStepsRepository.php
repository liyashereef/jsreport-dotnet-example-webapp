<?php

namespace Modules\Recruitment\Repositories;

use Modules\Recruitment\Models\RecProcessSteps;

class RecProcessStepsRepository
{
    /**
     * The Model instance.
     * ''
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $model;

    /**
     * Create a new TrackingProcessLookupRepository instance.
     *
     * @param  \App\Models\TrackingProcessLookup $trackingProcessLookup
     */
    public function __construct(RecProcessSteps $recProcessSteps)
    {
        $this->model = $recProcessSteps;
    }

    /**
     * Get lookup list
     *
     * @param empty
     * @return array
     */
    public function getAll()
    {
        return $this->model->select(['id','step_order', 'display_name','notes','created_at','updated_at'])->orderBy('step_order', 'asc')->get();
    }

    /**
     * Display details of single resource
     *
     * @param $id
     * @return object
     */
    public function get($id)
    {
        return $this->model->find($id);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  $data
     * @return object
     */
    public function save($data)
    {
        return $this->model->updateOrCreate(array('id' => $data['id']), $data);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  $id
     * @return object
     */
    public function delete($id)
    {
        return $this->model->destroy($id);
    }
}
