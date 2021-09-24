<?php

namespace Modules\Admin\Repositories;

use Modules\Admin\Models\KpiMasterAllocation;

class KpiMasterAllocationRepository
{

    /**
     * The Model instance.
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $model;

    /**
     * Create a new Model instance.
     *
     * @param  Modules\Admin\Models\KpiMasterAllocation $kpiMasterAllocation
     */
    public function __construct(KpiMasterAllocation $kpiMasterAllocation)
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
        return $this->model->with('kpiCustomerHeader', 'kpiMaster', 'kpiThresholds','kpiThresholds', 'kpiThresholds.kpiThresholdColor')
            ->get();
    }

    public function checkAlreadyAllocated($kpid, $headerId, $id)
    {
        $res =  $this->model->where('kpi_customer_header_id', '=', $headerId)
            ->where('kpi_master_id', '=', $kpid)
            ->when(!empty($id), function ($q) use ($id) {
                $q->where('id', '!=', $id);
            })->get();
        return $res->count() == 0 ? false : true;
    }


    public function removeAllocationByHeader($headerId)
    {
        $this->model->where('kpi_customer_header_id', '=', $headerId)->delete();
    }


    /**
     * Get single service details
     *
     * @param $id
     * @return object
     */
    public function getById($id)
    {
        return $this->model->with('kpiCustomerHeader', 'kpiMaster', 'kpiThresholds', 'kpiThresholds.kpiThresholdColor')->findOrFail($id);
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
