<?php

namespace Modules\Admin\Repositories;

use Modules\Admin\Models\EmployeeRatingLookup;

class EmployeeRatingLookupRepository
{
    /**
     * The Model instance.
     * ''
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $model;

    /**
     * Create a new EmployeeRatingLookup instance.
     *
     * @param  \App\Models\EmployeeRatingLookup $employeeRatingLookup
     */
    public function __construct(EmployeeRatingLookup $employeeRatingLookup)
    {
        $this->model = $employeeRatingLookup;
    }

    /**
     * Get employee rating lookup list
     *
     * @param empty
     * @return array
     */
    public function getAll()
    {
        return $this->model->select(['id', 'rating', 'score', 'created_at', 'updated_at'])->get();
    }

    /**
     * Get employee rating lookup list
     *
     * @param empty
     * @return array
     */
    public function getList()
    {
        return $this->model->orderBy('score', 'asc')->pluck('rating', 'id')->toArray();
    }

    /**
     * Display details of single employee rating
     *
     * @param $id
     * @return object
     */
    public function get($id)
    {
        return $this->model->find($id);
    }

    /**
     * Store a newly created employee rating lookup in storage.
     *
     * @param  $data
     * @return object
     */
    public function save($data)
    {
        return $this->model->updateOrCreate(array('id' => $data['id']), $data);
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

    public function getScore()
    {
        return $this->model->pluck('score', 'id')->toArray();
    }
}
