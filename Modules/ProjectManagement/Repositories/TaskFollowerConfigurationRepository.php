<?php

namespace Modules\ProjectManagement\Repositories;

use Modules\ProjectManagement\Models\PmTaskFollowerConfiguration;

class TaskFollowerConfigurationRepository
{
    /**
     * The Model instance.
     *
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $model;
    /**
     * Create a new Model instance.
     *
     * @param  \App\Models\PmTaskFollowerConfiguration $followerConfiguration
     */
    public function __construct(PmTaskFollowerConfiguration $followerConfiguration)
    {
        $this->model = $followerConfiguration;
    }

    public function getFirstData()
    {
        return $this->model->first();
    }

    public function saveOrCreate($data)
    {
        return $this->model->updateOrCreate(array('id' => $data['id']), $data);
    }
}
