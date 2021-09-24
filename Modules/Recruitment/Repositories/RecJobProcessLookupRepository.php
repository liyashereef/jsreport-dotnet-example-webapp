<?php

namespace Modules\Recruitment\Repositories;

use Modules\Recruitment\Models\RecJobProcessLookup;

class RecJobProcessLookupRepository
{
    /**
     * The Model instance.
     * ''
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $model;

    /**
     * Create a new JobProcessLookupRepository instance.
     *
     * @param  \App\Models\JobProcessLookup $jobProcessLookupModel
     */
    public function __construct(RecJobProcessLookup $recJobProcessLookup)
    {
        $this->model = $recJobProcessLookup;
    }

    /**
     * Get security clearance lookup list
     *
     * @param empty
     * @return array
     */
    public function getAll()
    {
        return $this->model->select(['id', 'process_name','created_at','updated_at'])->get();
    }
    
    /**
     * Get security clearance lookup list
     *
     * @param empty
     * @return array
     */
    public function getList()
    {
        return $this->model->pluck('process_name', 'id')->toArray();
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
}
