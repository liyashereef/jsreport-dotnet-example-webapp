<?php

namespace Modules\Admin\Repositories;

use Modules\Admin\Models\LeaveReason;

class LeaveReasonRepository
{
    /**
     * The Model instance.
     * ''
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $model;

    /**
     * Create a new Model instance.
     *
     * @param  Modules\Admin\Models\Holiday $holidayModel
     */
    public function __construct(LeaveReason $reasonModel)
    {
        $this->model = $reasonModel;

    }

    /**
     * Get  Holiday list
     *
     * @param empty
     * @return array
     */
    public function getAll()
    {
        return $this->model->select(['id', 'reason', 'created_at', 'updated_at'])->orderBy('reason','asc')->get();
    }

    /**
     * Get single Holiday details
     *
     * @param $id
     * @return object
     */
    public function get($id)
    {
        return $this->model->find($id);
    }

    /**
     * Get Reason lookup list
     *
     * @param empty
     * @return array
     */
    public function getLookupList()
    {
        return $this->model->orderBy('reason', 'asc')->pluck('reason', 'id')->toArray();
    }

    /**
     * Store a newly created Holiday in storage.
     *
     * @param  $request
     * @return object
     */
    public function save($data)
    {
        return $this->model->updateOrCreate(['id' => $data['id']], $data);
    }

    /**
     * Remove the specified Holiday from storage.
     *
     * @param  $id
     * @return object
     */
    public function deleteLeaveReason($id)
    {
        return $this->model->destroy($id);
    }

}
