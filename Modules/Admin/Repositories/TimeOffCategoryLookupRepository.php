<?php

namespace Modules\Admin\Repositories;

use Modules\Admin\Models\TimeOffCategoryLookup;

class TimeOffCategoryLookupRepository
{
    /**
     * The Model instance.
     * ''
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $model;

    /**
     * Create a new TimeOffRequestTypeLookupRepository instance.
     *
     * @param  \App\Models\TimeOffCategoryLookup $timeOffCategoryLookupModel
     */
    public function __construct(TimeOffCategoryLookup $timeOffCategoryLookupModel)
    {
        $this->model = $timeOffCategoryLookupModel;
    }

    /**
     * Get Request Type lookup list
     *
     * @param empty
     * @return array
     */
    public function getAll()
    {
        return $this->model->select(['id', 'type', 'description', 'reference', 'allowed_days', 'allowed_weeks', 'allowed_hours', 'created_at', 'updated_at'])->get();
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
        return $this->model->orderBy('type', 'asc')->pluck('type', 'id')->toArray();
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
