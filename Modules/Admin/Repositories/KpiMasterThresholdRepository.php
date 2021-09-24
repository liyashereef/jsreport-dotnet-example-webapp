<?php

namespace Modules\Admin\Repositories;

use Modules\Admin\Models\KpiMasterThreshold;

class KpiMasterThresholdRepository
{

     /**
     * The Model instance.
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $model;

    /**
     * Create a new Model instance.
     *
     * @param  Modules\Admin\Models\KpiMasterThreshold $kpiMasterThreshold
     */
    public function __construct(KpiMasterThreshold $kpiMasterThreshold)
    {
        $this->model = $kpiMasterThreshold;
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
     public function getByCustomerId($customerId){
         return $this->model
         ->where('customer_id',$customerId)
         ->get();
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


}
