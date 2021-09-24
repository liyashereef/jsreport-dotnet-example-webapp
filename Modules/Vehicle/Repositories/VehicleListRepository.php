<?php

namespace Modules\Vehicle\Repositories;

use Modules\Vehicle\Models\Vehicle;

class VehicleListRepository
{
    public function __construct(Vehicle $vehicle)
    {
        $this->model = $vehicle;
    }

 /**
     * Get all Values 
     *
     * @param empty
     * @return array
     */
    public function getAll()
    {
        return $this->model->select(['id', 'make', 'number','model','year','odometer_reading','region','purchasing_date','is_initiated','initiated_date','maintenance_due','maintenance_critical','maintenance_notes','maintenance_critical_date','email_notification_date','active' ,'vin','description' ])
        ->with('regionDetails')
            //->latest()
            ->orderBy('make')
            ->get();
    }

     /**
     * Get selected values for API
     * @param empty
     * @return array
     */

    public function getVehicles()
    {
        $details = $this->model->with('pendingMaintenance','pendingMaintenance.maintenanceType')
        ->select(['id', 'number','model','odometer_reading'])->where('is_initiated',1)->where('active',1)
        ->orderBy('model')->latest()
        ->get()->toArray();
         if(!empty($details)){
          $vehicle_details = array();
          foreach ($details as $key => $each) {
            $vehicle_details[$key]['id'] = $each['id'];
            $vehicle_details[$key]['odometer_reading'] = (int) $each['odometer_reading'];
            $vehicle_details[$key]['number'] = $each['number'];
            $vehicle_details[$key]['model'] = $each['model'];
            if(isset($each['pending_maintenance']) && !empty($each['pending_maintenance'])){
               foreach ($each['pending_maintenance'] as $nkey => $each_pending) {
                if(($each_pending['service_critical'] == 1) && ($each_pending['completion_status'] != 1)){
                    $vehicle_details[$key]['maintenance_critical'] = $each_pending['service_critical'];
                    $vehicle_details[$key]['maintenance_critical_type'][] = $each_pending['maintenance_type']['name'];
                }else if(($each_pending['service_due'] == 1) && ($each_pending['service_critical'] != 1)  && ($each_pending['completion_status'] != 1)){
                    $vehicle_details[$key]['maintenance_due'] = $each_pending['service_due'];
                    $vehicle_details[$key]['maintenance_due_type'][] = $each_pending['maintenance_type']['name'];
                }
               }
            }
          }
            return $vehicle_details;
        }else{
            return false;
        }
    }


     /**
     * Store a newly created Expense Parent Category in storage.
     *
     * @param  $data
     * @return object
     */
    public function save($data)
    {
        $data['active']=(isset($data['active']))?1:0;
        return $this->model->updateOrCreate(array('id' => $data['id']), $data);
    }

     /**
     * Display details of single Expense Parent Category
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
        return $this->model->destroy($id);
    }
    

    public function prepareArray($data)
    {
        $datatable_rows = array();
        foreach ($data as $key => $each_vehicle) {
                $each_row["id"] = $each_vehicle->id;
                $each_row["make"] = $each_vehicle->make;
                $each_row["active"] = ($each_vehicle->active==1)?'Active':'Inactive';
                $each_row["number"] = $each_vehicle->number;
                $each_row["model"] = $each_vehicle->model;
                $each_row["year"] = $each_vehicle->year;
                 $each_row["vin"] = $each_vehicle->vin;
                $each_row["purchasing_date"] = $each_vehicle->purchasing_date;
                $each_row["odometer_reading"] = (int) $each_vehicle->odometer_reading;
                $each_row["region_name"] = ($each_vehicle->region!=null)?($each_vehicle->regionDetails->region_name):'--';
                array_push($datatable_rows, $each_row);
        }

        return $datatable_rows;
    }

    /**
     * Store a newly created Expense Parent Category in storage.
     *
     * @param  $data
     * @return object
     */
    public function updateVehicleOdometre($vehicle_id,$odometre)
    {
        if(($vehicle_id != null) && ($odometre!= null)){
        return $this->model->where('id',$vehicle_id)->update(['odometer_reading' => $odometre]);
        }else{
        return false;
        }
    }

     /**
     * Get vehicle name with their number.
     *
     * @param  $data
     * @return object
     */
      public function getVehicleListwithNameAndModel($vehicle_list)
    {
        $vehicles = array();
        foreach ($vehicle_list as $key => $each_vehicle) {
            $vehicles[$each_vehicle['id']] = $each_vehicle['model'] . ' (' . $each_vehicle['number'] . ')';
        }
        return $vehicles;
        
    }
}
