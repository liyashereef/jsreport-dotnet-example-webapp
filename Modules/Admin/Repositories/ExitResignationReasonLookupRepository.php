<?php

namespace Modules\Admin\Repositories;

use Modules\Admin\Models\ExitResignationReasonLookup;

class ExitResignationReasonLookupRepository{

    protected $model;

    public function __construct(ExitResignationReasonLookup $model)
    {
       $this->model = $model; 
    }

    public function getAll(){

        return $this->model->select(['id','reason','shortname','created_at','updated_at'])->get();
    }

    public function getList()
    {
        return $this->model->orderBy('reason', 'asc')->pluck('type', 'id')->toArray();
    }

    /**
     * Store a newly created Security Clearance in storage.
     *
     * @param  $data
     * @return object
     */

    public function save($data)
    {
       $result =  $this->model->updateOrCreate(array('id' => $data['id']), $data);
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
