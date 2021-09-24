<?php

namespace Modules\Uniform\Repositories;

use Modules\Uniform\Models\UraOperationType;

class UraOperationTypeRepository
{
    protected $model;
    public function __construct(UraOperationType $uraOperationType)
    {
        return $this->model = $uraOperationType;
    }

    public function getAll()
    {
        return $this->model->all();
    }

    public function getUnrestricted()
    {
        return $this->model->where('restricted', false)->get();
    }

    public function getUnrestrictedList()
    {
        return $this->model->where('restricted', false)->pluck('display_name', 'id')->toArray();
    }
}
