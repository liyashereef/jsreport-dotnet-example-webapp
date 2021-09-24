<?php

namespace Modules\Recruitment\Repositories;

use Modules\Recruitment\Models\RecExperienceLookup;

class RecExperienceLookupRepository
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
     * @param  \App\Models\RecExperienceLookup $recExperienceLookup
     */
    public function __construct(RecExperienceLookup $recExperienceLookup)
    {
        $this->model = $recExperienceLookup;
    }

    /**
     * Get Experience  lookup list
     *
     * @param empty
     * @return array
     */
    public function getAll()
    {
        return $this->model->select(['id', 'experience','created_at','updated_at'])->get();
    }

    /**
     * Get Experience lookup list
     *
     * @param empty
     * @return array
     */
    public function getList()
    {
        return $this->model->orderBy('experience', 'asc')->pluck('experience', 'id')->toArray();
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
