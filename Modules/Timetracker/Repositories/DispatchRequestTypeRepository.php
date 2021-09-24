<?php

namespace Modules\Timetracker\Repositories;


use Modules\Timetracker\Models\DispatchRequestType;

class DispatchRequestTypeRepository
{
    protected $model;

    public function __construct(DispatchRequestType $dispatchRequestType)
    {
        $this->model = $dispatchRequestType;
    }

    public function getById($id){
        return $this->model->find($id);
    }

    public function save($data){
        $result = $this->model->updateOrCreate(array('id' => $data['id']), $data);

        $created = $result->wasRecentlyCreated;
        $result = $result->fresh();
        $result['created'] = $created;
        return $result;
    }

    public function getAll()
    {
        return $this->model->select(['id','name','description','rate','created_at','updated_at','deleted_at'])->orderby('id', 'DESC')->get();
    }


    public function delete($id)
    {
        return $this->model->destroy($id);
    }


}