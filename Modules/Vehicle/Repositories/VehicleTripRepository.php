<?php

namespace Modules\Vehicle\Repositories;

use Modules\Vehicle\Models\VehicleTrip;
use Modules\Vehicle\Repositories\VehiclePendingMaintenanceRepository;
use Modules\Vehicle\Repositories\VehicleListRepository;
use Illuminate\Support\Facades\Artisan;
use Modules\Timetracker\Repositories\EmployeeShiftRepository;
use Illuminate\Support\Facades\Auth;
use Modules\Vehicle\Models\VehicleDamageAttachment;
use Modules\Timetracker\Repositories\ImageRepository;
use App\Repositories\AttachmentRepository;
use Modules\Timetracker\Repositories\TripRepository;
use Modules\Vehicle\Models\Vehicle;
use App\Services\HelperService;
use Illuminate\Support\Facades\Log;

class VehicleTripRepository
{
    public function __construct(
        VehicleTrip $vehicleTrip,
        VehiclePendingMaintenanceRepository $vehiclePendingMaintenanceRepository,
        VehicleListRepository $vehicleListRepository,
        EmployeeShiftRepository $employeeShiftRepository,
        VehicleDamageAttachment $vehicleDamageAttachment,
        AttachmentRepository $attachmentRepository,
        ImageRepository $imageRepository,
        TripRepository $tripRepository,
        HelperService $helperService
    ) {
        $this->model = $vehicleTrip;
        $this->vehicleListRepository = $vehicleListRepository;
        $this->vehiclePendingMaintenanceRepository = $vehiclePendingMaintenanceRepository;
        $this->employeeShiftRepository = $employeeShiftRepository;
        $this->vehicleDamageAttachment = $vehicleDamageAttachment;
        $this->imageRepository = $imageRepository;
        $this->attachmentRepository = $attachmentRepository;
        $this->tripRepository = $tripRepository;
        $this->helperService = $helperService;
    }

 /**
     * Get all maintenance list
     *
     * @param empty
     * @return array
     */
    public function getAll()
    {
        return $this->model->with('vehicle', 'user', 'user.trashedEmployee', 'customer', 'shift')->get();
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
            $each_row["start_datetime_formatted"] = date('F d, Y h:i A', strtotime($each_data->start_datetime));
            $each_row["start_datetime"] = $each_data->start_datetime;
             $each_row["end_datetime_formatted"] = date('F d, Y h:i A', strtotime($each_data->end_datetime));
            $each_row["end_datetime"] = $each_data->end_datetime;
             $each_row["created_by"] = (isset($each_data->user)?(isset($each_data->user->last_name)?($each_data->user->first_name.' '.$each_data->user->last_name):$each_data->user->first_name):'');
           
            $each_row["employee_no"] = (isset($each_data->user->trashedEmployee))?$each_data->user->trashedEmployee->employee_no:'';
            $each_row["client_name"] = $each_data->customer->client_name;
             $each_row["project_number"] = $each_data->customer->project_number;
               $each_row["vehicle_number"] = isset($each_data->vehicle)?$each_data->vehicle->number:'';
               $each_row["vehicle_odometer_reading"] = isset($each_data->vehicle)?(int)$each_data->vehicle->odometer_reading:'';
            $each_row["system_odometer_start"] = $each_data->system_odometer_start;
            $each_row["system_odometer_end"] =($each_data->system_odometer_end==null)?'Shift not submitted':$each_data->system_odometer_end;
            $each_row["system_distance_travelled"] = ($each_data->system_distance_travelled==null && $each_data->system_distance_travelled!=0 )?'--':$each_data->system_distance_travelled;
            $each_row["user_odometer_start"] = $each_data->user_odometer_start;
            $each_row["user_odometer_end"] =($each_data->user_odometer_end==null)?'Shift not submitted':$each_data->user_odometer_end;
            $each_row["user_distance_travelled"] = ($each_data->user_distance_travelled==null && $each_data->user_distance_travelled!=0 )?'--':$each_data->user_distance_travelled;
            
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
    public function saveVehicleTrips($request)
    {
        if (isset($request->shift_id) && !empty($request->shift_id)) {
            $shift_id = $request->shift_id;
        } else {
            $params = null;
            $params->customerId = $request->customer_id;
            $params->startTime = $request->shift_startTime;
            $params->assigned = $request->assigned;
            $shift_det = $this->employeeShiftRepository->startShift(Auth::id(), $params);
            $shift_id = $shift_det->id;
        }
        $data['shift_id'] = $shift_id;
        $data['vehicle_id'] = $request->vehicle_id;
        $data['customer_id'] = $request->customer_id;
        $data['user_odometer_start'] = $request->user_odometer_start;
        $data['user_odometer_end'] = $request->user_odometer_end;
        $data['user_distance_travelled'] = $request->user_distance_travelled;
        $data['system_odometer_start'] = $request->system_odometer_start;
        $data['system_odometer_end'] = null;
        $data['system_distance_travelled'] =null;
        $data['start_visible_damage'] = $request->start_visible_damage;
        $data['end_visible_damage'] = $request->end_visible_damage;
        $data['start_notes'] = $request->start_notes;
        $data['end_notes'] = $request->end_notes;
        $data['start_datetime'] = $request->start_datetime;
        $data['end_datetime'] = $request->end_datetime;
        $data['created_by'] = \Auth::id();
        $data['updated_by'] = \Auth::id();
        $result = $this->model->create($data);


        if ($result) {
            if (isset($request->end_visible_damage_images) && !empty($request->end_visible_damage_images)) {
                foreach ($request->end_visible_damage_images as $imgkey => $eachimage) {
                    $imagefile = $this->imageRepository->imageFromBase64($eachimage);
                    $attachment_id = $this->attachmentRepository->saveBase64ImageFile('vehicle-module', $result, $imagefile);

                    $this->vehicleDamageAttachment->create(
                        ['trip_id' => $result->id,
                        'vehicle_damage_time' => 2,
                        'attachment_id' => $attachment_id
                        ]
                    );
                }
            }
        }

        if ($result) {
            if (isset($request->start_visible_damage_images) && !empty($request->start_visible_damage_images)) {
                foreach ($request->start_visible_damage_images as $imgkey => $eachimage) {
                    $imagefile = $this->imageRepository->imageFromBase64($eachimage);
                    $attachment_id = $this->attachmentRepository->saveBase64ImageFile('vehicle-module', $result, $imagefile);

                    $this->vehicleDamageAttachment->create(
                        ['trip_id' => $result->id,
                        'vehicle_damage_time' => 1,
                        'attachment_id' => $attachment_id
                        ]
                    );
                }
            }
        }
    }

     /**
     * Get vehicle trip details
     *
     * @param $id
     * @return object
     */

    public function getTrip($shift_id)
    {
        return $this->model->where('shift_id', $shift_id)->first();
    }

         /**
     * Display details of single Maintenance Parent Category
     *
     * @param $id
     * @return object
     */

    public function getSingle($id)
    {
        return $this->model->with('vehicle', 'user', 'user.trashedEmployee', 'customer', 'shift', 'attachments')->find($id);
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
     * Function to prepare and give attachment path array
     * @param $request
     * @return array
     */
    public static function getAttachmentPathArr($request)
    {
        return array(config('globals.vehicle_module_attachment_folder'), $request->id);
    }
   
    /**
     * Static function to return path as an array when file name is given
     * @param $file_id
     * @return array
     */
    public static function getAttachmentPathArrFromFile($file_id)
    {
        $attachment = VehicleDamageAttachment::where('attachment_id', $file_id)->first();
        if (isset($attachment)) {
            $trip_id = $attachment->trip_id;
        }
        return array(config('globals.vehicle_module_attachment_folder'), $trip_id);
    }


     /**
     * To update odometer and trip details
     *
     * @param  $data
     * @return object
     */
    public function updateOdometerAndTrip($shift_id)
    {
        try {
            \DB::beginTransaction();
            $shift_details = $this->tripRepository->getTotalKmInSingleShift($shift_id);
            Log::info('calkm'.$shift_details->total_km);
            $vehicle_trip =  $this->getTrip($shift_id);
            $vehicle_model = Vehicle::find($vehicle_trip->vehicle_id);
            $vehicle_model->odometer_reading =$vehicle_model->odometer_reading + $vehicle_trip->user_distance_travelled;
            $vehicle_model->save();
            //$vehicle_odometer = $this->updateVehicleOdometerReading($vehicle_trip->vehicle_id, $shift_details->total_km);
            $this->updateVehicleTrip($vehicle_trip->id, $shift_details->total_km);
            if ($vehicle_model != null) {
                Artisan::call('vehicle:checkvehicleserviceonsubmit', ['vehicle_id' => $vehicle_trip->vehicle_id, 'odometre' => (int) $vehicle_model->odometer_reading]);
            }
            \DB::commit();
            return response()->json($this->helperService->returnTrueResponse());
        } catch (\Exception $e) {
            dd($e);
            \DB::rollBack();
            return response()->json($this->helperService->returnFalseResponse());
        }
    }

     /**
     * Update vehicle trip
     *
     * @return object
     */
    public function updateVehicleTrip($trip_id, $total_km)
    {
        if ($trip_id != null) {
            $trip_model = $this->model->find($trip_id);
            Log::info('tripid'.$trip_id);
            $trip_model->system_odometer_end = $trip_model->system_odometer_start + (int) $total_km;
            $trip_model->system_distance_travelled = (int) $total_km;
            Log::info('end_odometer'.$trip_model->system_odometer_end);
            $trip_model->save();
        } else {
            return false;
        }
    }



    /**
     * To update vehicle odometer reading
     * @param  float $total_km
     * @param  int $vehicle_id
     * @return object
     */
    public function updateVehicleOdometerReading($vehicle_id, $total_km)
    {
        if ($vehicle_id != null) {
            $vehicle_model = Vehicle::find($vehicle_id);
            $end_odometer = $vehicle_model->odometer_reading + $total_km;
            $vehicle_model->odometer_reading = $end_odometer;
            Log::info('end_odometer'.$end_odometer);
            Log::info('vehicle end_odometer'.$vehicle_model->odometer_reading);
            $vehicle_model->save();
            return $end_odometer;
        } else {
            return false;
        }
    }
}
