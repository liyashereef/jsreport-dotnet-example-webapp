<?php

namespace Modules\Admin\Repositories;

use Modules\Admin\Models\ScheduleAssignmentTypeLookup;

class ScheduleAssignmentTypeLookupRepository
{
    /**
     * The Model instance.
     * ''
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $model;

    /**
     * Create a new ScheduleAssignmentTypeLookupRepository instance.
     *
     * @param  \App\Models\ScheduleAssignmentTypeLookup $scheduleAssignmentTypeLookup
     */
    public function __construct(ScheduleAssignmentTypeLookup $scheduleAssignmentTypeLookup)
    {
        $this->model = $scheduleAssignmentTypeLookup;
    }

    /**
     * Get security clearance lookup list
     *
     * @param empty
     * @return array
     */
    public function getAll()
    {
        return $this->model->select(['id', 'type', 'created_at', 'updated_at', 'is_deletable'])->get();
    }

    /**
     * Get security clearance lookup list
     *
     * @param empty
     * @return array
     */
    public function getList()
    {
        return $this->model->orderBy('type')->pluck('type', 'id')->toArray();
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
