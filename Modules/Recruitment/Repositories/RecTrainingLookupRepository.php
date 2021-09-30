<?php

namespace Modules\Recruitment\Repositories;

use Modules\Recruitment\Models\RecTrainingTimingLookup;


class RecTrainingLookupRepository
{
    /**
     * The Model instance.
     * ''
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $model;

    /**
     * Create a new TrainingLookupRepository instance.
     *
     * @param  \App\Models\TrainingLookup $trainingLookup
     */
    public function __construct(RecTrainingTimingLookup $trainingLookup)
    {
        $this->model = $trainingLookup;
    }

    /**
     * Get security clearance lookup list
     *
     * @param empty
     * @return array
     */
    public function getAll()
    {
        return $this->model->select(['id', 'training','created_at','updated_at'])->get();
    }

    /**
     * Get Position lookup list
     *
     * @param empty
     * @return array
     */
    public function getList()
    {
        return $this->model->orderBy('training', 'asc')->pluck('training', 'id')->toArray();
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