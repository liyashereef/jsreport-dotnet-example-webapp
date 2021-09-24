<?php

namespace Modules\Admin\Repositories;

use Modules\Admin\Models\KpiMaster;

class KpiMasterRepository
{
    /**
     * The Model instance.
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $model;

    /**
     * Create a new Model instance.
     *
     * @param  Modules\Admin\Models\KpiMaster $kpiMaster
     */
    public function __construct(KpiMaster $kpiMaster)
    {
        $this->model = $kpiMaster;
    }

    /**
     * Get  service list
     *
     * @param empty
     * @return array
     */

    public function getAll(){
       return $this->model->all();
    }

     /**
     * Get single service details
     *
     * @param $id
     * @return object
     */
    public function getById($id){
        return $this->model->find($id);
    }

     /**
     * Store a newly created service in storage.
     *
     * @param  $request
     * @return object
     */

    public function store($inputs){
        return $this->model->updateOrCreate(['id' => $inputs['id']], $inputs);
    }

    /**
     * Get single service details
     *
     * @param $id
     * @return object
     */
    public function destroy($id){
        return $this->model->find($id)->delete();
    }

    public function getUnallocatedKpis($headerId){
        return $this->model
        // ->doesntHave('KpiMasterAllocation')
        ->whereDoesntHave('KpiMasterAllocation', function($q) use($headerId){
            return $q->where('kpi_master_id','!=',$headerId);
        })
        ->get();
    }

}
