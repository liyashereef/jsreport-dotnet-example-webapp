<?php

namespace Modules\Admin\Repositories;

use App\Services\HelperService;
use Illuminate\Support\Arr;
use Modules\Admin\Models\Geofence;
use Carbon\Carbon;
use Modules\Timetracker\Models\EmployeeShift;
use Modules\Timetracker\Models\MobileSecurityPatrolTripCoordinate;
use Modules\Timetracker\Repositories\MobileSecurityPatrolFenceDataRepository;

class GeofenceRepository
{
    /**
     * The Model instance.
     * ''
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $model, $trainingCourseModel;
    private $fence_time_limit;

    /**
     * Create a new TrainingCategoryLookupRepository instance.
     *
     * @param \App\Models\TrainingCategory $trainingCategory
     * @param \App\Models\TrainingCourse $trainingCourse
     */
    public function __construct(
        Geofence $geofence,
        HelperService $helper_service,
        MobileSecurityPatrolFenceDataRepository $fence_data,
        CustomerRepository $customer
    )
    {
        $this->model = $geofence;
        $this->helper_service = $helper_service;
        $this->fence_data = $fence_data;
        $this->customer = $customer;
        $this->fenceExitTimeArr = Array();
        $this->fence_time_limit = 1;

    }

    public function getSingleFence($id)
    {
        return $this->model->find($id)->first();
    }

    public function getFenceByCustomer($customer_id)
    {
        return $this->model->where('customer_id', $customer_id)->where('active', 1)->get();
    }

    /**
     * @param $coordinates_obj
     *  Coordinates
     * @param $fence_obj
     *  Customer Fence
     * @param $shift_id
     * @return array
     */
    public function prepareFenceCount($coordinates_obj, $fence_obj, $shift_id, $customer_id)
    {
        \Config::set('logging.channels.travelpath.path', storage_path('logs/fencelog/'.$customer_id.'_'.$shift_id.'.log'));
        // for each coordinate, check if the point is within any fence,
        //// if the point is under any do
        ////// get the timestamp
        ////// get the coordinate id
        ////// for remaining coordinates, check if the point is still within current fence,
        //////// if yes, continue
        //////// else no,
        ////////// get the timestamp
        ////////// get the coordinate id
        ////////// get the points and save it
        ////////// get aggregates for all total enters, total misses etc. store in new table.
        //// else the point is not under any, continue to next point

        if($customer_id != null) {
            $this->fence_time_limit = $this->customer->getFenceInterval($customer_id)*60;
        }

        $current_fence_id = null;

        $fence_arr_count = 0;

        $fence_start_coordinate_id = 0;
        $fence_end_coordinate_id = 0;
        $fence_start_time = "";
        $fence_end_time = "";
        $duration = 0;
        $fence_start_timestamp = 0;
        $fence_end_timestamp = 0;
        $fence_arr = Array();

        for ($coordinate_index = 0; $coordinate_index < count($coordinates_obj);) {
                if (!isset($coordinates_obj[$coordinate_index]->mobile_security_patrol_trips_id)) {
                $coordinate_index++;
                continue;
            }
            // for each coordinate, check if the point is within any fence,
            while ($fence_arr_count < count($fence_obj) && $coordinate_index < count($coordinates_obj)) {
                // if point does not belong any trip, skip
                if (!isset($coordinates_obj[$coordinate_index]->mobile_security_patrol_trips_id)) {
                    $coordinate_index++;
                    continue;
                }
                $fence_id = $fence_obj[$fence_arr_count]->id;
                $fence_lat = $fence_obj[$fence_arr_count]->geo_lat;
                $fence_lon = $fence_obj[$fence_arr_count]->geo_lon;
                $fence_rad = $fence_obj[$fence_arr_count]->geo_rad;
                \Log::channel('travelpath')->info("coordinate_index: ". $coordinate_index.", fence_arr_count: ".$fence_arr_count.", current_fence_id: ".$current_fence_id.", fence_id: ".$fence_id);
                //dump("coordinate_index: ". $coordinate_index.", fence_arr_count: ".$fence_arr_count.", current_fence_id: ".$current_fence_id.", fence_id: ".$fence_id);
                $isInside = $this->helper_service->isInsideFence(
                    $fence_lat,
                    $fence_lon,
                    $fence_rad,
                    $coordinates_obj[$coordinate_index]->latitude,
                    $coordinates_obj[$coordinate_index]->longitude
                );
                \Log::channel('travelpath')->info([$isInside, "latitude: " .$coordinates_obj[$coordinate_index]->latitude.", longitude: " .$coordinates_obj[$coordinate_index]->longitude]);
                //dump($isInside, "latitude: " .$coordinates_obj[$coordinate_index]->latitude.", longitude: " .$coordinates_obj[$coordinate_index]->longitude);
                $curr_timestamp = (integer) round(($coordinates_obj[$coordinate_index]->time / 1000));

                if ($isInside && $current_fence_id != $fence_id) {

                    // if the time took

                    $fence_time_exceeded = $this->verifyFenceExitTime($fence_id, $curr_timestamp);
                    if(!$fence_time_exceeded) {
                        \Log::channel('travelpath')->info("skiped ". $coordinate_index . " " . $fence_arr_count);
                        //dump("skiped ". $coordinate_index . " " . $fence_arr_count);
                        $coordinate_index++;
                        $fence_arr_count++;
                        continue;
                    }
//                    dump("start");
//                    dump($coordinates_obj[$coordinate_index]);
                    \Log::channel('travelpath')->info("start");
                    \Log::channel('travelpath')->info(json_encode($coordinates_obj[$coordinate_index]));
                    // if the point is under any do

                    // get the timestamp
                    // get the coordinate id
                    $this->storeFenceExitTime($fence_id, $curr_timestamp);
                    $fence_start_timestamp = $curr_timestamp;
                    $fence_start_time = (Carbon::createFromTimestamp($fence_start_timestamp))->toDateTimeString();
                    $fence_start_coordinate_id = (MobileSecurityPatrolTripCoordinate::where("time", $fence_start_time)
                        ->where("mobile_security_patrol_trips_id", $coordinates_obj[$coordinate_index]->mobile_security_patrol_trips_id)
                        ->pluck('id')->first());
                    $current_fence_id = $fence_id;

                    $coordinate_index++;
                    continue;
                } elseif ($isInside && $current_fence_id == $fence_id ) {
                    // for remaining coordinates, check if the point is still within current fence,
                    // if yes, continue
//                    dump('current fence '.$fence_id);
                    $coordinate_index++;
                    continue;
                } elseif (!$isInside && $current_fence_id == $fence_id) {
                    //else no, - The point is just outside fence after being within fence

                    // get the timestamp
                    // get the coordinate id
                    $fence_end_timestamp = $curr_timestamp;
                    $fence_end_time = (Carbon::createFromTimestamp($fence_end_timestamp))->toDateTimeString();
                    $fence_end_coordinate_id = (MobileSecurityPatrolTripCoordinate::where("time", $fence_end_time)
                        ->where("mobile_security_patrol_trips_id", $coordinates_obj[$coordinate_index]->mobile_security_patrol_trips_id)
                        ->pluck('id')->first());
                    $duration = ($fence_end_timestamp - $fence_start_timestamp);
                    // get the points and save it
                    $current_fence_arr['shift_id'] = $shift_id;
                    $current_fence_arr['fence_id'] = $fence_id;
                    $current_fence_arr['start_coordinate_id'] = $fence_start_coordinate_id;
                    $current_fence_arr['end_coordinate_id'] = $fence_end_coordinate_id;
                    $current_fence_arr['time_entry'] = $fence_start_time;
                    $current_fence_arr['time_exit'] = $fence_end_time;
                    $current_fence_arr['duration'] = $duration;
                    $current_fence_arr['visited'] = 1;
                    array_push($fence_arr,$current_fence_arr);
                    \Log::channel('travelpath')->info(json_encode($coordinates_obj[$coordinate_index]));
                    \Log::channel('travelpath')->info("end");
//                    dump($coordinates_obj[$coordinate_index]);
//                    dump("end");
                    $current_fence_id = null;
                    $coordinate_index++;
                } else {
                    //current point not in current fence and was not already in any fence
                    $fence_arr_count++;
                }
            }
            $fence_arr_count = 0;
            $coordinate_index++;
        }
        $employee_shift = EmployeeShift::with('shift_payperiod','shift_payperiod.trashed_customer.geoFenceDetails')
            ->where('id', $shift_id)
            ->first();
        //$expected_visit_obj = data_get($employee_shift,'shift_payperiod.trashed_customer.geoFenceDetails');
        $expected_visit_arr = data_get($employee_shift,'shift_payperiod.trashed_customer.geoFenceDetails.*.id');
        $actual_visit_arr = data_get($fence_arr,'*.fence_id');

        $skipped_fence_arr = array_diff($expected_visit_arr, $actual_visit_arr);
        foreach($skipped_fence_arr as $each_skipped_fence_id) {
            $each_skipped_fence['shift_id'] = $shift_id;
            $each_skipped_fence['fence_id'] = $each_skipped_fence_id;
            $each_skipped_fence['fence_start_coordinate_id'] = "";
            $each_skipped_fence['fence_end_coordinate_id'] = "";
            $each_skipped_fence['fence_start_time'] = "";
            $each_skipped_fence['fence_end_time'] = "";
            $each_skipped_fence['duration'] = 0;
            $each_skipped_fence['visited'] = 0;
            array_push($fence_arr,$each_skipped_fence);
        }
        return $fence_arr;
    }

    public function storeFenceExitTime($fence_id, $timestamp){
        $this->fenceExitTimeArr['fence_'.$fence_id] = $timestamp;
    }

    public function verifyFenceExitTime($fence_id, $timestamp){
        if(!isset($this->fenceExitTimeArr['fence_'.$fence_id])){
            //dump("exit time ok first fence ".$fence_id);
            return true;
        }
        //dump($timestamp - $this->fenceExitTimeArr['fence_'.$fence_id]." >= ". $this->fence_time_limit);
        return ($timestamp - $this->fenceExitTimeArr['fence_'.$fence_id]) >= $this->fence_time_limit;
    }
}
