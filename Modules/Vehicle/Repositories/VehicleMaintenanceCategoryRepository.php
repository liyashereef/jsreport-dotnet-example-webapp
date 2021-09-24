<?php

namespace Modules\Vehicle\Repositories;

use Modules\Vehicle\Models\VehicleMaintenanceCategory;
use Modules\Vehicle\Models\VehicleMaintenanceType;


class VehicleMaintenanceCategoryRepository
{
    public function __construct(VehicleMaintenanceCategory $maintenanceCategory,VehicleMaintenanceType $vehicleMaintenanceType)
    {
        $this->model = $maintenanceCategory;
        $this->vehicleMaintenanceType=$vehicleMaintenanceType;
    }

 /**
     * Get all Values 
     *
     * @param empty
     * @return array
     */
    public function getAll()
    {
        return $this->model->select(['id', 'category_name','tax'])
            ->latest()
            ->get();
    }
/**
     * Store a newly created Maintenance Parent Category in storage.
     *
     * @param  $data
     * @return object
     */
    public function save($data)
    {
        return $this->model->updateOrCreate(array('id' => $data['id']), $data);
    }

     /**
     * Display details of single Maintenance Parent Category
     *
     * @param $id
     * @return object
     */

    public function get($id)
    {
        return $this->model->find($id);
    }
    
     /**
     * Remove the specified  from storage.
     *
     * @param  $id
     * @return object
     */
    public function delete($id)
    {
        $category_id = $this->vehicleMaintenanceType->pluck('category_id')->toArray();
        if (in_array($id, $category_id)) {
            return false;
        } else {
            return $this->model->destroy($id);
        }
       // return $this->model->destroy($id);
    }
    

}