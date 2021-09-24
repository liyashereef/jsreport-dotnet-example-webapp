<?php

namespace Modules\Vehicle\Repositories;

use Modules\Vehicle\Models\VehiclePendingMaintenance;
use Modules\Vehicle\Models\VehicleMaintenanceInterval;
use Modules\Vehicle\Models\Vehicle;
use Carbon\Carbon;
use DB;
use Illuminate\Support\Facades\Log;
use App\Repositories\MailQueueRepository;
use Modules\Vehicle\Models\VehicleMaintenanceType;

class VehiclePendingMaintenanceRepository
{
    public function __construct(VehiclePendingMaintenance $pendingmaintenance,
    VehicleMaintenanceInterval $vehicleMaintenanceInterval,
    MailQueueRepository $mailQueueRepository,
    VehicleMaintenanceType $vehicleMaintenanceType)
    {
        $this->model = $pendingmaintenance;
        $this->mailQueueRepository = $mailQueueRepository;
        $this->vehicleMaintenanceInterval = $vehicleMaintenanceInterval;
        $this->vehicleMaintenanceType = $vehicleMaintenanceType;
    }

     /**
     * Get all maintenance list
     *
     * @param empty
     * @return array
     */
    public function getAll($all)
    {
       \DB::EnableQueryLog();
        $query = $this->model->with('maintenanceType')->has('maintenanceType');
        $query1=$query->when($all, function ($query) use ($all) {
          return $query;
      }, function ($query)   {
          $query->where(function($query){
            $query->where('service_due','!=', 0)->where('service_critical', '!=',1);
            $query->orWhere('service_critical', '!=',0);
        });

                });
        $result = $query1->whereHas('vehicle',function($query){
            $query->where('active',1);
        })
        ->where('completion_status',0)->orderBy('updated_at','desc')->get();
        return $result;
    }

    /**
     * Get all maintenance list
     *
     * @param empty
     * @return array
     */
    public function getDataArray($data)
    {
        $datatable_rows = array();
        foreach ($data as $key => $each_data) {
            $each_row["id"] = $each_data->id;
            $each_row["vehicle_number"] = isset($each_data->vehicle)?$each_data->vehicle->number:'';
            $each_row["vehicle_make"] = $each_data->vehicle->make;
            $each_row["vehicle_odometer_reading"] = (int)$each_data->vehicle->odometer_reading;
            $each_row["vehicle_model"] = $each_data->vehicle->model;
            $each_row["maintenance_type_name"] = $each_data->maintenanceType->name;
            $each_row["service_date"] = $each_data->service_date;
            $each_row["service_kilometre"] = $each_data->service_kilometre;
            $each_row["service_due"] = ($each_data->service_due==1)?'Yes':'No';
            $each_row["service_critical"] = ($each_data->service_critical==1)?'Yes':'No';
            array_push($datatable_rows, $each_row);
        }
        return $datatable_rows;
    }


     /**
     * Store a newly created Maintenance Parent Category in storage.
     *
     * @param  $data
     * @return object
     */
    public function store($request)
    {

        $interval = $this->vehicleMaintenanceInterval->where('vehicle_id',$request['vehicle_id'])
                                         ->where('service_type_id',$request['type_id'])
                                         ->whereNull('deleted_at')->first();
        if(!isset($interval)) {
            return false;
        }
        $service_type = $this->vehicleMaintenanceType->where('id',$request['type_id'])->first();

        $vehicle_details = Vehicle::select('odometer_reading')->where('id',$request['vehicle_id'])->first();

        $service_due = 0;
        $service_critical = 0;
        if($interval->interval_day != null){
          $next_service_date =  Carbon::parse($request['service_date'])->addDays($interval->interval_day);
          if((Carbon::now() > Carbon::parse($next_service_date))){
            $service_due = 1;
            if((Carbon::now() > Carbon::parse($next_service_date)->addDays($service_type->critical_after_days))){
            $service_critical = 1;
            }
          }
        }elseif($interval->interval_km != null){
          $next_service_km = (int)$request['service_kilometre'] + (int)$interval->interval_km;
          if($vehicle_details->odometer_reading > $next_service_km){
            $service_due = 1;
            if(($vehicle_details->odometer_reading) > ($next_service_km + $service_type->critical_after_km)){
            $service_critical = 1;
            }
          }
        }else{
          return false; // TODO : Log this result
        }

        $data['vehicle_id'] = $request['vehicle_id'];
        $data['type_id'] = $request['type_id'];
        $data['service_kilometre'] = (isset($next_service_km)) ? $next_service_km : null;
        $data['service_date'] = (isset($next_service_date)) ? $next_service_date : null;
        $data['service_due'] = $service_due;
        $data['service_critical'] = $service_critical;
        return $this->model->create($data);
    }

     /**
     * Display details of single Maintenance Parent Category
     *
     * @param $id
     * @return object
     */

    public function get($id)
    {
        return $this->model->with('maintenanceType','maintenanceType.category','vehicle')->find($id);
    }

     /**
     * Update completion status.
     *
     * @param  $id
     * @return object
     */
    public function updateStatus($id)
    {
        return $this->model->where('id',$id)->update(['completion_status' => 1]);
    }

    /**
     * Delete pending maintenance
     *
     * @param empty
     * @return array
     */
    public function deleteMaintenance($vehicle_id,$service_id)
    {
        return $this->model->where('vehicle_id',$vehicle_id)
                           ->where('type_id',$service_id)
                           ->where('completion_status',0)->delete();
    }

    /**
     * Service to check and update pending service on daily basis
     * @return Response
     */
    public function updatePendingServiceByDate(){
        try {
            \DB::beginTransaction();
              $this->model->where('completion_status',0)->whereDate('service_date', '<=', date("Y-m-d"))->update(['service_due' => 1]);

              $pending_service =  $this->model->with('maintenanceType')
                          ->where('completion_status',0)
                           ->whereDate('service_date', '<', date("Y-m-d"))
                           ->get();

              foreach ($pending_service as $each_service) {
               $current = Carbon::now();
               $expdate = $current->subDays($each_service->maintenanceType->critical_after_days)->toDateString();
               $this->model->whereDate('service_date', '<=',$expdate )->update(['service_critical' => 1]);
              }
            \DB::commit();
            return true;
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json(array('success' => false, 'message' => $e->getMessage()));
        }
    }



    /**
     * Service to check and update pending service on submit trip
     * @return Response
     */
    public function updatePendingServiceByOdometre($vehicle_id,$odometer){
        try {
            \DB::beginTransaction();

                      $this->model->where('vehicle_id',$vehicle_id)
                                 ->where('service_kilometre','!=',null)
                                 ->where('completion_status',0)
                                 ->where('service_kilometre','<',$odometer)
                                 ->update(['service_due' => 1]);

          $pending_service =  $this->model->with('maintenanceType')
                                 ->where('service_kilometre','!=',null)
                                 ->where('vehicle_id',$vehicle_id)
                                 ->where('completion_status',0)
                                 ->where('service_kilometre','<',$odometer)
                                  ->get();

             foreach ($pending_service as $each_service) {
              $expkm = (int)$odometer - (int)$each_service->maintenanceType->critical_after_km;

              $this->model->where('vehicle_id',$each_service->vehicle_id )
              ->where('type_id',$each_service->type_id )
              ->where('service_kilometre', '<',$expkm )
              ->update(['service_critical' => 1]);
             }
            \DB::commit();
            return true;
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json(array('success' => false, 'message' => $e->getMessage()));
        }
    }


    /**
     * Service to add pending service mail to mail queue
     * @return Response
     */
    public function addPendingServiceMailToMailQueue(){
        try {
            \DB::beginTransaction();

            $details = Vehicle::with('pendingMaintenance','pendingMaintenance.maintenanceType','regionDetails')
            ->select(['id','make', 'number','region'])->where('is_initiated',1)->where('active',1)
            ->latest()
            ->get()->toArray();

              foreach ($details as $key => $each) {
               $send_due_mail = false;
               $send_critical_mail = false;
               $pending_due = array();
               $pending_critical = array();
                if(isset($each['pending_maintenance']) && !empty($each['pending_maintenance'])){
                   foreach ($each['pending_maintenance'] as $nkey => $each_pending) {

                    if(($each_pending['service_critical'] == 0) && ($each_pending['service_due'] == 1) && ($each_pending['completion_status'] != 1)){
                        $send_due_mail = true;
                        $pending_due[] = '- '.$each_pending['maintenance_type']['name'];
                    }else if(($each_pending['service_due'] == 1) && ($each_pending['service_critical'] == 1)  && ($each_pending['completion_status'] != 1)){
                        $send_critical_mail = true;
                        $pending_critical[] = '- '.$each_pending['maintenance_type']['name'];
                    }
                   }

                }
                   if($send_due_mail){
                    $helper_variable = array('{maintenanceDetails}' =>  implode("<br>",$pending_due), '{vehicleNumber}' =>  $each['number'],'{vehicleName}' =>  $each['make'],'{region}' =>  $each['region_details']['region_name']);
                    $mail_queue = $this->mailQueueRepository->prepareMailTemplate('vehicle_maintenance_due', 0,$helper_variable,'vehicle_maintenance_due');
                   }
                   if($send_critical_mail){
                    $helper_variable = array('{maintenanceDetails}' =>  implode("<br>",$pending_critical), '{vehicleNumber}' =>  $each['number'],'{vehicleName}' =>  $each['make'],'{region}' =>  $each['region_details']['region_name']);
                    $mail_queue = $this->mailQueueRepository->prepareMailTemplate('vehicle_maintenance_critical', 0,$helper_variable,'vehicle_maintenance_due');
                   }
              }

            \DB::commit();
            return true;
        } catch (\Exception $e) {
            \DB::rollBack();
            Log::error('errr ' . $e->getMessage() . ' ' . $e->getLine());
            return response()->json(array('success' => false, 'message' => $e->getMessage()));
        }
    }

    public function deletePendingMaintenanceOfDeletedType($type_id){
        $this->model->where('type_id',$type_id)->delete();
    }

    /**
     * Delete all pending maintenance
     *
     * @param empty
     * @return array
     */
    public function deleteAllPendingMaintenance($vehicle_id)
    {
        return $this->model->where('vehicle_id',$vehicle_id)
                           ->where('completion_status',0)->delete();
    }

}
