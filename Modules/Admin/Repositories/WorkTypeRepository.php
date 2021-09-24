<?php

namespace Modules\Admin\Repositories;

use Modules\Admin\Models\WorkType;

class WorkTypeRepository
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
     * @param  \App\Models\WorkType $workTypeModel
     */
    public function __construct(WorkType $workTypeModel)
    {
        $this->model = $workTypeModel;

    }

    /**
     * Get  WorkType list
     *
     * @param empty
     * @return array
     */
    public function getAll()
    {
        return $this->model->select(['id', 'type'])->whereActive(true)->get();
    }

    /**
     * Get single WorkType details
     *
     * @param $id
     * @return object
     */
    public function get($id)
    {
        return $this->model->find($id);

    }

    /**
     * Store a newly created WorkType in storage.
     *
     * @param  $request
     * @return object
     */
    public function save($data)
    {
        return $this->model->updateOrCreate(array('id' => $data['id']), $data);
    }

    /**
     * Remove the specified WorkType from storage.
     *
     * @param  $id
     * @return object
     */
    public function deleteWorkType($id)
    {
        $worktype = $this->model->find($id);
        $worktype->active = 0;
        $worktype->save();
        return $this->model->destroy($id);
    }

}
