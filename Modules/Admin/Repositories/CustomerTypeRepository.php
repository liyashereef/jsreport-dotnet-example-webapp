<?php

namespace Modules\Admin\Repositories;

use Modules\Admin\Models\CpidFunction;
use Modules\Admin\Models\CustomerType;

class CustomerTypeRepository
{
    /**
     * The Model instance.
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $model;

    /**
     * Create a new Model instance.
     *
     * @param  Modules\Admin\Models\CustomerType $model
     */
    public function __construct(CustomerType $model)
    {
        $this->model = $model;
    }

    /**
     * Get  service list
     *
     * @param empty
     * @return array
     */

    public function getAll()
    {
        return $this->model->all();
    }

    /**
     * Get single service details
     *
     * @param $id
     * @return object
     */
    public function getById($id)
    {
        return $this->model->find($id);
    }

    /**
     * Store a newly created service in storage.
     *
     * @param  $request
     * @return object
     */

    public function store($inputs)
    {
        return $this->model->updateOrCreate(['id' => $inputs['id']], $inputs);
    }

    /**
     * Get single service details
     *
     * @param $id
     * @return object
     */
    public function destroy($id)
    {
        return $this->model->find($id)->delete();
    }

    public function getList()
    {
        return $this->model->orderBy('name', 'asc')->pluck('name', 'id')->toArray();
    }
}
