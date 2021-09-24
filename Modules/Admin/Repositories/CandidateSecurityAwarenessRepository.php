<?php

namespace Modules\Admin\Repositories;

use Modules\Admin\Models\CandidateSecurityAwarenes;

class CandidateSecurityAwarenessRepository
{
    /**
     * The Model instance.
     * ''
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $model;

    /**
     * Create a new CandidateSecurityAwarenessRepository instance.
     *
     * @param  \App\Models\CandidateSecurityAwarenes $candidateSecurityAwarenes
     */
    public function __construct(CandidateSecurityAwarenes $candidateSecurityAwarenes)
    {
        $this->model = $candidateSecurityAwarenes;
    }

    /**
     * Get Request Type lookup list
     *
     * @param empty
     * @return array
     */
    public function getAll()
    {
        return $this->model->select(['id', 'answer','order_sequence', 'created_at', 'updated_at'])->get();
    }

    /**
     * Display details of single Request Type
     *
     * @param $id
     * @return object
     */
    public function get($id)
    {
        return $this->model->find($id);
    }

    /**
     * Get Request Type lookup list
     *
     * @param empty
     * @return array
     */
    public function getLookupList()
    {
        return $this->model->orderBy('answer', 'asc')->pluck('answer', 'id')->toArray();
    }

    /**
     * Store a newly created Request Type in storage.
     *
     * @param  $data
     * @return object
     */
    public function save($data)
    {
        $lookup = $this->model->updateOrCreate(array('id' => $data['id']), $data);
        return $lookup;
    }

    /**
     * Remove the specified Request Type from storage.
     *
     * @param  $id
     * @return object
     */
    public function delete($id)
    {
        return $this->model->destroy($id);
    }

}
