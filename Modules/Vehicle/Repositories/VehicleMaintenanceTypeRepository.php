<?php

namespace Modules\Vehicle\Repositories;

use Modules\Vehicle\Models\VehicleMaintenanceType;
use Modules\Vehicle\Models\VehicleMaintenanceDatatype;

class VehicleMaintenanceTypeRepository
{
    public function __construct(VehicleMaintenanceType $maintenanceType,VehicleMaintenanceDatatype $maintanencedatatype)
    {
        $this->model = $maintenanceType;
        $this->maintanencedatatype= $maintanencedatatype;
    }

 /**
     * Get all Values 
     *
     * @param empty
     * @return array
     */
    public function getAll()
    {
        return $this->model->with('category')->orderBy('name','ASC')->get();
    }
/**
     * Store a newly created Maintenance Parent Category in storage.
     *
     * @param  $data
     * @return object
     */
    public function save($data)
    {
       //type ==1 indicating its a kilometer and type==2 denoting date
        if($data['type']==1)
        {
        $data['critical_after_km']=$data['critical_after_km']; 
         $data['critical_after_days']=null;  
        }
        else
        {
            $data['critical_after_days']=$data['critical_after_days'];
            $data['critical_after_km']=null;
        }
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
        return $this->model->with('typeDetails')->find($id);
    }
    
     /**
     * Remove the specified  from storage.
     *
     * @param  $id
     * @return object
     */
    public function delete($id)
    {
        return $this->model->destroy($id);
    }


    /**
     * Get all Values of datatypes 
     *
     * @param empty
     * @return array
     */
    public function getAllMaintenanceDatatypes()
    {
        return $this->maintanencedatatype->orderBy('name')->get();
    }
    

    

}