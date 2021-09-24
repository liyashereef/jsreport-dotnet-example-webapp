<?php

namespace Modules\Recruitment\Repositories;

use Modules\Recruitment\Models\RecUniformMeasurementPoint;

class RecUniformMeasurementPointRepository
{
    /**
     * The Model instance.
     * ''
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $model;

    /**
     * Create a new RecUniformMeasurementPointRepository instance.
     *
     * @param  Modules\Recruitment\Models\RecUniformMeasurementPoint $recUniformMeasurementPoint
     */
    public function __construct(RecUniformMeasurementPoint $recUniformMeasurementPoint)
    {
        $this->model = $recUniformMeasurementPoint;
    }

    /**
     * Get uniform measurement point list
     *
     * @param empty
     * @return array
     */
    public function getAll()
    {
        return $this->model->select(['id', 'name'])->orderBy('name')->get();
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
