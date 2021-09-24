<?php

namespace Modules\Admin\Repositories;

use Modules\Admin\Models\TimeOffRequestTypeLookup;

class TimeOffRequestTypeLookupRepository
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
     * @param  \App\Models\TimeOffRequestTypeLookupLookup $timeOffRequestTypeLookupModel
     */
    public function __construct(TimeOffRequestTypeLookup $timeOffRequestTypeLookupModel)
    {
        $this->model = $timeOffRequestTypeLookupModel;
    }

    /**
     * Get Request Type lookup list
     *
     * @param empty
     * @return array
     */
    public function getAll()
    {
        return $this->model->select(['id', 'request_type','is_deletable', 'created_at', 'updated_at'])->get();
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
        return $this->model->orderBy('request_type', 'asc')->pluck('request_type', 'id')->toArray();
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
