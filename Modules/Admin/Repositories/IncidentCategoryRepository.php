<?php

namespace Modules\Admin\Repositories;

use Modules\Admin\Models\IncidentCategory;

class IncidentCategoryRepository
{
    protected $model;


    public function __construct(IncidentCategory $incidentCategory)
    {
        $this->model = $incidentCategory;
    }

    /**
     * Get all lookup list
     *
     * @param empty
     * @return array
     */
    public function getAll()
    {
        return $this->model->all();
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

    public function getList()
    {
        return $this->model->orderBy('name', 'asc')->pluck('name', 'id')->toArray();
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
