<?php

namespace Modules\Admin\Repositories;

use Modules\Admin\Models\EnglishRatingLookup;
use Modules\Admin\Repositories\EnglishRatingLookupRepository;

class EnglishRatingLookupRepository
{
   
    protected $repository;

    /**
     * Create Repository instance.
     * @param  \App\Repositories\EmployeeRatingLookupRepository $employeeRatingLookupRepository
     * @return void
     */
    public function __construct(EnglishRatingLookup $englishRatingLookup)
    {
        $this->model = $englishRatingLookup;
    }

    /**
     * Get english rating lookup list
     *
     * @param empty
     * @return array
     */
    public function getAll()
    {
        return $this->model->select(['id', 'english_ratings','order_sequence','score', 'created_at', 'updated_at'])->get();
    }

    /**
     * Get english rating lookup list
     *
     * @param empty
     * @return array
     */
    public function getList()
    {
        return $this->model->orderBy('score', 'asc')->pluck('english_ratings', 'id')->toArray();
    }

    /**
     * Display details of single english rating
     *
     * @param $id
     * @return object
     */
    public function get($id)
    {
        return $this->model->find($id);
    }

    /**
     * Store a newly created english rating lookup in storage.
     *
     * @param  $data
     * @return object
     */
    public function save($data)
    {
        return $this->model->updateOrCreate(array('id' => $data['id']), $data);
    }

    /**
     * Remove the specified english rating from storage.
     *
     * @param  $id
     * @return object
     */
    public function delete($id)
    {
        return $this->model->destroy($id);
    }
}
