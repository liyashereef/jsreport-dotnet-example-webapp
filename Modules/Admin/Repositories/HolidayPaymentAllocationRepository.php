<?php

namespace Modules\Admin\Repositories;

use Modules\Admin\Models\HolidayPaymentAllocation;

class HolidayPaymentAllocationRepository
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
    public function __construct(HolidayPaymentAllocation $holidaypaymentallocationModel)
    {
        $this->model = $holidaypaymentallocationModel;

    }

    /**
     * Get  Holiday list
     *
     * @param empty
     * @return array
     */
    public function getAll()
    {
        return $this->model->select(['id', 'paymentstatus', 'created_at', 'updated_at'])->where('status',true)->orderBy('paymentstatus','asc')->get();
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
    public function deleteHoliday($id)
    {

        $holiday = $this->model->find($id);
        $holiday->active = 0;
        $holiday->save();
        return $this->model->destroy($id);
    }

}
