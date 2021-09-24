<?php

namespace Modules\Vehicle\Repositories;

use Modules\Vehicle\Models\VehicleMaintenanceRecord;
use Modules\Vehicle\Repositories\VehiclePendingMaintenanceRepository;
use Modules\Vehicle\Models\VehicleMaintenanceInterval;
use Modules\Vehicle\Models\VehicleMaintenanceRecords;
use Modules\Vehicle\Models\VehicleMaintenanceAttachment;
use App\Repositories\AttachmentRepository;

class VehicleMaintenanceRepository
{
    public function __construct(VehicleMaintenanceRecord $maintenance,
    VehiclePendingMaintenanceRepository $vehiclePendingMaintenanceRepository,
    VehicleMaintenanceInterval $vehicleMaintenanceInterval,VehicleMaintenanceRecord $vehicleMaintenanceRecord,
    VehicleMaintenanceAttachment $vehicleMaintenanceAttachment,
    AttachmentRepository $attachmentRepository)
    {
        $this->model = $maintenance;
        $this->vehicleMaintenanceInterval =$vehicleMaintenanceInterval;
         $this->vehicleMaintenanceRecord =$vehicleMaintenanceRecord;
        $this->vehiclePendingMaintenanceRepository = $vehiclePendingMaintenanceRepository;
        $this->vehicleMaintenanceAttachment = $vehicleMaintenanceAttachment;
        $this->attachmentRepository = $attachmentRepository;
    }

 /**
     * Get all maintenance list
     *
     * @param empty
     * @return array
     */
    public function getAll()
    {
        return $this->model->with('vehicle','vehicle.regionDetails','vendor','maintenanceType','attachments')->orderBy('updated_at','desc')->get();
    }
/**
     * Store a newly created Maintenance Parent Category in storage.
     *
     * @param  $data
     * @return object
     */
    public function store($request)
    {

    //    dd($request->invoice);
         if(isset($request->pending_id) && ($request->pending_id != null) ){
            $this->vehiclePendingMaintenanceRepository->updateStatus($request->pending_id);
         }

         $details = $this->vehicleMaintenanceInterval->where('vehicle_id',$request->vehicle_id)
                                         ->where('service_type_id',$request->type_id)
                                         ->withTrashed()->latest()->first();
         if($details->interval_day != null){
            $interval = $details->interval_day;
         }elseif($details->interval_km != null){
            $interval = $details->interval_km;
         }


        $data['odometer'] = $request->service_kilometre;
        $data['interval'] = $interval;
        $data['total_charges'] = $request->total_amount;
        $data['tax'] = $request->tax;
        $data['tax_amount'] = $request->tax_amount;
        $data['subtotal'] = $request->subtotal;
        $data['notes'] = $request->notes;
        $data['service_kilometre'] = $request->service_kilometre;
        $data['type_id'] = $request->type_id;
        $data['vehicle_id'] = $request->vehicle_id;
        $data['vendor_id'] = (isset($request->vendor_id)) ? $request->vendor_id : null;
        $data['service_date'] = $request->service_date;
        $data['created_by'] = \Auth::id();
        $data['updated_by'] = \Auth::id();
        $service_request = $this->model->create($data);

        if($service_request->id != 0 ){
            if (!empty($request->invoice)) {
             $upload_file = $request->get('upload_file');
              $file = $this->attachmentRepository->saveAttachmentFile("vehicle-maintenance", $request, $upload_file);
              $maintenance_attachment['maintenance_id'] = $service_request->id;
              $maintenance_attachment['attachment_id'] = $file['file_id'];
              VehicleMaintenanceAttachment::create($maintenance_attachment);
            }
            if(!isset($request->pending_id)){
            $respone = $this->vehiclePendingMaintenanceRepository->deleteMaintenance($request->vehicle_id,$request->type_id);
            }
            $this->vehiclePendingMaintenanceRepository->store($data);
        }
        //return true;
    }

     /**
     * Display details of single Maintenance Parent Category
     *
     * @param $id
     * @return object
     */

    public function get($id)
    {
        return $this->model->with('type')->find($id);
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
        return $this->maintanencedatatype->get();
    }

    /**
     * Delete all records based on vehicle id
     *
     * @param empty
     * @return array
     */
    public function drop($vehicle_id)
    {
        return $this->model->where('vehicle_id',$vehicle_id)->delete();
    }

    /**
     * Delete all records based on vehicle id
     *
     * @param empty
     * @return array
     */
    public function maintenaceIntervalDelete($type_id)
    {
        return $this->vehicleMaintenanceInterval->where('service_type_id',$type_id)->delete();
    }

    /**
     * Delete all records based on vehicle id
     *
     * @param empty
     * @return array
     */
    public function maintenaceRecordDelete($type_id)
    {
        return $this->vehicleMaintenanceRecord->where('type_id',$type_id)->delete();
    }


       /**
     * Function to prepare and give attachment path array
     * @param $request
     * @return array
     */
    public static function getAttachmentPathArr($request)
    {
        return array(config('globals.vehicle_maintenance_attachment_folder'), $request->vehicle_id);
    }

    /**
     * Static function to return path as an array when file name is given
     * @param $file_id
     * @return array
     */
    public static function getAttachmentPathArrFromFile($file_id)
    {
        $attachment = VehicleMaintenanceAttachment::with('vehicle_maintenance')->where('attachment_id', $file_id)->first();
        if (isset($attachment)) {
            $maintenance_id = $attachment->vehicle_maintenance->vehicle_id;
        }
        return array(config('globals.vehicle_maintenance_attachment_folder'), $maintenance_id);
    }


}
