<?php

namespace Modules\Vehicle\Repositories;

use Modules\Vehicle\Models\VehicleMaintenanceInterval;
use Modules\Vehicle\Models\VehicleMaintenanceRecord;
use Modules\Vehicle\Models\Vehicle;
use Auth;
use Modules\Vehicle\Repositories\VehicleMaintenanceRepository;

class VehicleRepository
{
    public function __construct(VehicleMaintenanceInterval $vehicleMaintenanceInterval,Vehicle $vehicleModel,VehicleMaintenanceRepository $vehicleMaintenanceRepository)
    {
        $this->vehicleMaintenanceInterval = $vehicleMaintenanceInterval;
        $this->vehicleModel=$vehicleModel;
        $this->vehicleMaintenanceRepository=$vehicleMaintenanceRepository;
    }

 /**
     * Get all Values 
     *
     * @param empty
     * @return array
     */
    public function getAll()
    {
        return $this->model->with('category')->get();
    }
/**
     * Store a newly created Maintenance Parent Category in storage.
     *
     * @param  $data
     * @return object
     */
    public function save($data)
    {
        $deleteMaintenanceInterval=$this->vehicleMaintenanceInterval->where('vehicle_id',$data['vehicle_id'])->delete();
        $deleteMaintenanceRecord=$this->vehicleMaintenanceRepository->drop($data['vehicle_id']);
       for($i = 0; $i < count($data['type']); $i++){
       $maintenanceRecords = new \stdClass();    
       $maintenanceInterval['vehicle_id']=$maintenanceRecords->vehicle_id=$data['vehicle_id'];
       $maintenanceInterval['service_type_id']= $maintenanceRecords->type_id= $data['type'][$i];
       $maintenanceRecords->notes =$data['notes'];
       $maintenanceRecords->created_by=\Auth::id();
       $maintenanceRecords->updated_by=\Auth::id();
       //data type checking whether kilometer or date
       if($data['data_type'][$i]=='km')
         {
        $maintenanceInterval['service_km']= $maintenanceRecords->service_kilometre =$data['service'][$i];
        $maintenanceInterval['interval_km']=$maintenanceRecords->interval = $data['interval'][$i];
          $maintenanceInterval['service_date']= $maintenanceRecords->service_date =null;
          $maintenanceInterval['interval_day']= null;
          $maintenanceRecords->odometer =$data['service'][$i];
         }
        else
         {
         $maintenanceInterval['service_date']=  $maintenanceRecords->service_date =$data['service'][$i];
         $maintenanceInterval['interval_day']=$maintenanceRecords->interval = $data['interval'][$i];
         $maintenanceInterval['service_km']=$maintenanceRecords->service_kilometre = null;
         $maintenanceInterval['interval_km']= null;
         $maintenanceRecords->odometer =$data['service'][$i];
          }     
          $maintenanceRecords->total_amount = $maintenanceRecords->tax =  $maintenanceRecords->tax_amount = $maintenanceRecords->subtotal = null;
         $storeMaintenanceIntervals=$this->vehicleMaintenanceInterval->create($maintenanceInterval);
         $storeMaintenanceRecords=$this->vehicleMaintenanceRepository->store($maintenanceRecords);
        
       }
         $this->vehicleModel->where('id',$data['vehicle_id'])->update(['is_initiated'=>1,'initiated_date'=>date('Y-m-d H:i:s'),'odometer_reading'=>$data['odometer_reading'],'notes'=>$data['notes']]);
            return true;
     
    }

     /**
     * Display details of single Maintenance Parent Category
     *
     * @param $id
     * @return object
     */

    public function get($id)
    {
        return $this->vehicleModel->with('vehicles')->find($id);
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
    public function getInitiatedType($id)
    {
        $data= $this->vehicleMaintenanceInterval->with('serviceType')->where('vehicle_id',$id)->get();
        $result=(array_combine(data_get($data, '*.serviceType.id'),data_get($data, '*.serviceType.name')));
       return $result;
    }
    

    

}