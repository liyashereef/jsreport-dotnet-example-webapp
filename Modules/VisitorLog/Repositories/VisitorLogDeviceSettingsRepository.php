<?php

namespace Modules\VisitorLog\Repositories;

use Modules\VisitorLog\Models\VisitorLogDeviceSettings;

class VisitorLogDeviceSettingsRepository
{

    protected $model;

    public function __construct(VisitorLogDeviceSettings $model){
        $this->model = $model;
    }

    public function store($inputs){
        return $this->model->create($inputs);
    }

    public function updateEntry($inputs){
        return $this->model->updateOrCreate(['id' => $inputs['id']], $inputs);
    }

    public function updateByDeviceId($inputs){
        return $this->model->updateOrCreate(['visitor_log_device_id' => $inputs['visitor_log_device_id']], $inputs);
    }

    public function delete($id){
        return $this->model->where('id', $id)->delete();
    }

    public function getAll(){
        return $this->model->get();
    }

}
