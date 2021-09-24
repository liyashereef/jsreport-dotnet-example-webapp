<?php

namespace Modules\Admin\Repositories;

use Modules\Admin\Models\RateExperienceLookups;

class RateExperienceLookupRepository
{
    /**
     * The Model instance.
     * ''
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $model;

    /**
     * Create a new RateExperienceLookups instance.
     *
     * @param  \App\Models\RateExperienceLookups $rateExperienceLookups
     */
    public function __construct(RateExperienceLookups $rateExperienceLookups)
    {
        $this->model = $rateExperienceLookups;
    }

    /**
     * Get Rate Experience lookup list
     *
     * @param empty
     * @return array
     */
    public function getAll()
    {
        return $this->model->select(['id', 'experience_ratings', 'created_at', 'updated_at', 'score'])->orderBy('score', 'desc')->get();
    }

    /**
     * Display details of single Rate Experience
     *
     * @param $id
     * @return object
     */
    public function get($id)
    {
        return $this->model->find($id);
    }

    /**
     * Store a newly created Rate Experience in storage.
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
     * Remove the specified employee rating from storage.
     *
     * @param  $id
     * @return object
     */
    public function delete($id)
    {
        return $this->model->destroy($id);
    }
}
