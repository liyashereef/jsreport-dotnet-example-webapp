<?php

namespace Modules\Admin\Repositories;

use Modules\Admin\Models\KpiMasterCustomerAllocation;

class KpiMasterCustomerAllocationRepository
{

    /**
     * The Model instance.
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $model;

    /**
     * Create a new Model instance.
     *
     * @param  Modules\Admin\Models\KpiMasterCustomerAllocation $kpiMasterAllocation
     */
    public function __construct(KpiMasterCustomerAllocation $kpiMasterAllocation)
    {
        $this->model = $kpiMasterAllocation;
    }

    /**
     * Get  service list
     *
     * @param empty
     * @return array
     */

    public function getAll()
    {
        return $this->model->with('customer', 'kpiMaster')->get();
    }

    public function checkAlreadyAllocated($kpid, $customerId)
    {
        $res =  $this->model->where([
            ['customer_id', '=', $customerId],
            ['kpi_master_id', '=', $kpid]
        ])->get();

        return $res->count() == 0 ? false : true;
    }


    public function removeAllocationByHeader($headerId)
    {
        return $this->model->where('kpi_master_id', '=', $headerId)->delete();
    }

    public function getByCustomerId($customerId){
        return $this->model->where('customer_id',$customerId)->with('kpiMaster')->get();
    }

    /**
     * Get single service details
     *
     * @param $id
     * @return object
     */
    public function getById($id)
    {
        return $this->model->with('customer', 'kpiMaster')->findOrFail($id);
    }

    /**
     * Store a newly created service in storage.
     *
     * @param  $request
     * @return object
     */

    public function store($inputs)
    {
        return $this->model->updateOrCreate(['id' => $inputs['id']], $inputs);
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
}
