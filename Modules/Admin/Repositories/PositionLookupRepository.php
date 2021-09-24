<?php

namespace Modules\Admin\Repositories;

use Modules\Admin\Models\PositionLookup;

class PositionLookupRepository
{
    /**
     * The Model instance.
     * ''
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $model;

    /**
     * Create a new PositionLookupLookupRepository instance.
     *
     * @param  \App\Models\PositionLookup $positionLookup
     */
    public function __construct(PositionLookup $positionLookupModel)
    {
        $this->model = $positionLookupModel;
    }

    /**
     * Get security clearance lookup list
     *
     * @param empty
     * @return array
     */
    public function getAll()
    {
        return $this->model->select(['id', 'position', 'created_at', 'updated_at'])->orderBy('position','asc')->get();
    }

    /**
     * Get Position lookup list
     *
     * @param empty
     * @return array
     */
    public function getList()
    {
        return $this->model->orderBy('position', 'asc')->pluck('position', 'id')->toArray();
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
        return $this->model->updateOrCreate(array('id' => $data['id']), $data);
    }

    /**
     * Remove the specified Security Clearance from storage.
     *
     * @param  $id
     * @return object
     */
    public function delete($id)
    {
        return $this->model->destroy($id);
    }

    public function getPositionBasedOnCPID(){
        return $this->model->whereHas('CpidLookUpWithTrashed')->get();
    }
}
