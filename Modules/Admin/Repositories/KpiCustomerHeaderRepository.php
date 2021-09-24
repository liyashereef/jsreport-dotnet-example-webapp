<?php

namespace Modules\Admin\Repositories;

use Modules\Admin\Models\KpiCustomerHeader;

class KpiCustomerHeaderRepository
{
    /**
     * The Model instance.
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $model;

    /**
     * Create a new Model instance.
     *
     * @param  Modules\Admin\Models\KpiCustomerHeader $kpiCustomerHeader
     */
    public function __construct(KpiCustomerHeader $kpiCustomerHeader)
    {
        $this->model = $kpiCustomerHeader;
    }

    /**
     * Get all headers
     *
     * @param $id
     * @return object
     */
    public function all()
    {
        return $this->model->all();
    }


    public function allActive()
    {
        return $this->model->where('is_active', '=', true)->orderBy('name', 'ASC')->get();
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

    public function getByKpids($ids)
    {
        return $this->model->whereHas('kpiMasterAllocation', function ($query) use ($ids) {
            $query->whereIn('kpi_master_id', $ids);
        })->with(['kpiMasterAllocation' => function ($query) use ($ids) {
            $query->whereIn('kpi_master_id', $ids)->select('id', 'kpi_customer_header_id', 'kpi_master_id','is_active');
        }, 'kpiMasterAllocation.kpiMaster' => function ($query) {
            $query->select('id', 'name', 'threshold_type');
        }, 'kpiMasterAllocation.kpiThresholds' => function ($query) {
            $query->select('id', 'kpi_master_allocation_id', 'kpi_threshold_color_id', 'min', 'max');
        }, 'kpiMasterAllocation.kpiThresholds.kpiThresholdColor' => function ($query) {
            $query->select('id','color','color_code','font_color');
        }])->where('is_active', '=', true)
        ->select('id','name')->get();
    }
}
