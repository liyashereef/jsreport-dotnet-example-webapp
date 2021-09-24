<?php

namespace Modules\Admin\Repositories;

use Modules\Admin\Models\KpiGroup;
use Modules\Admin\Models\KpiGroupCustomerAllocation;

class KpiGroupCustomerAllocationRepository
{
    /**
     * The Model instance.
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $model;


    public function __construct(KpiGroupCustomerAllocation $kpiGroupCustomerAllocation)
    {
        $this->model = $kpiGroupCustomerAllocation;
    }

    /**
     * Get  service list
     *
     * @param empty
     * @return array
     */

    public function getAll()
    {
        return $this->model->all();
    }

    public function getAllActiveByCustomerId($customerId)
    {
        return $this->model->where('is_active', 1)
            ->where('customer_id', $customerId)
            ->with('group')
            ->get();
    }

    /**
     * Get single service details
     *
     * @param $id
     * @return object
     */
    public function getByCustomerId($customerId)
    {
        return $this->model->where('customer_id', $customerId)
            ->with('group')
            ->get();
    }

    /**
     * Get single service details
     *
     * @param $id
     * @return object
     */
    public function getById($id)
    {
        return $this->model->find($id);
    }

    /**
     * Store a newly created service in storage.
     *
     * @param  $request
     * @return object
     */

    public function store($inputs)
    {
        return $this->model->create($inputs);
    }

    /**
     * Get single service details
     *
     * @param $id
     * @return object
     */
    public function destroy($id)
    {
        return $this->model->find($id)->delete();
    }

    /**
     * Get all groups of a customer
     */
    public function getGroupsOfaCustomer($customerId)
    {
        $gids =  KpiGroupCustomerAllocation::where('customer_id', '=', $customerId)->pluck('kpi_group_id')->toArray();;
        return KpiGroup::find($gids);
    }

    public function checkAlreadyAllocated($customerId, $groupId)
    {
        $res =  KpiGroupCustomerAllocation::where([
            ['customer_id', '=', $customerId],
            ['kpi_group_id', '=', $groupId]
        ])->get();

        return $res->count() == 0 ? false : true;
    }
}
