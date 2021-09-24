<?php

namespace Modules\Recruitment\Repositories;

use Modules\Recruitment\Models\RecUniformSizes;

class RecUniformSizeRepository
{
    /**
     * The Model instance.
     * ''
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $model;

    /**
     * Create a new RecExperienceLookupRepository instance.
     *
     * @param  Modules\Recruitment\Models\RecUniformSizes $recUniformSizes
     */
    public function __construct(RecUniformSizes $recUniformSizes)
    {
        $this->model = $recUniformSizes;
    }

    /**
     * Get Experience  lookup list
     *
     * @param empty
     * @return array
     */
    public function getAll()
    {
        return $this->model->select(['id', 'size_name'])->get();
    }

    /**
     * Get Experience lookup list
     *
     * @param empty
     * @return array
     */
    public function getList()
    {
        return $this->model->orderBy('size_name')->pluck('size_name', 'id')->toArray();
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
