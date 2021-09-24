<?php

namespace Modules\Admin\Repositories;

use Modules\Admin\Models\CompetencyMatrixRatingLookup;

class CompetencyMatrixRatingLookupRepository
{
    /**
     * The Model instance.
     * ''
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $model;

    /**
     * Create a new CompetencyMatrixRatingLookup instance.
     *
     * @param  \App\Models\CompetencyMatrixRatingLookup $shiftTiming
     */
    public function __construct(CompetencyMatrixRatingLookup $competencyMatrixRatingLookup)
    {
        $this->model = $competencyMatrixRatingLookup;
    }

    /**
     * Get CompetencyMatrixLookup list
     *
     * @param empty
     * @return array
     */
    public function getAll()
    {
        $result = $this->model->select(['id', 'rating', 'created_at', 'updated_at', 'deleted_at', 'order_sequence'])->orderBy('order_sequence')->get();
        return $result;

    }

    /**
     * Get competency matrix rating lookup list
     *
     * @param empty
     * @return array
     */
    public function getList()
    {
        return $this->model->orderBy('order_sequence')->pluck('rating', 'id')->toArray();
    }

    /**
     * Display details of single resource
     *
     * @param $id
     * @return object
     */
    public function get($competency_rating_id)
    {

        $competency_rating = $this->model->find($competency_rating_id);
        return $competency_rating;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  $data
     * @return object
     */
    public function store($data)
    {
        $shift_save = $this->model->updateOrCreate(array('id' => $data['id']), $data);
        return $shift_save;

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
