<?php

namespace Modules\Timetracker\Repositories;

use App\Services\HelperService;
use DB;
use function GuzzleHttp\json_decode;
use Illuminate\Support\Facades\Auth;
use Log;
use Matrix\Exception;
use Modules\Admin\Models\MobileAppSetting;
use Modules\Admin\Models\User;
use Modules\Admin\Repositories\CustomerRepository;
use Modules\Admin\Repositories\UserRepository;
use Modules\Timetracker\Models\EmployeeShift;
use Modules\Timetracker\Models\MobileSecurityPatrolTrip;
use Modules\Timetracker\Models\MobileSecurityPatrolTripCoordinate;

class TripRepository
{

    /**
     * The Model instance.
     * ''
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $model;

    /**
     * Create a new TripRepository instance.
     *
     * @param  Modules\Timetracker\Models\MobileSecurityPatrolTripCoordinate
     */
    public function __construct(MobileSecurityPatrolTripCoordinate $trip_coordinate,
        MobileSecurityPatrolTrip $mobileSecurityPatrolTrip,
        EmployeeShift $employeeShift,
        UserRepository $userRepository,
        CustomerRepository $customerRepository,
        MobileAppSetting $mobileAppSetting
    ) {
        $this->trip_coordinate_model = $trip_coordinate;
        $this->trip_model = $mobileSecurityPatrolTrip;
        $this->employee_shift_model = $employeeShift;
        $this->user_repository = $userRepository;
        $this->customer_repository = $customerRepository;
        $this->mobileAppSetting = $mobileAppSetting;
        $this->helper_service = new HelperService();
    }

    /**
     *  To store trip coordinates
     *
     *  @param array
     *  @return array
     *
     */
    public function storeCoordinates($request)
    {
        $trip_location = new MobileSecurityPatrolTripCoordinate();
        $trip_location->latitude = $request->latitude;
        $trip_location->longitude = $request->longitude;
        $trip_location->time = $this->datetimeFromUnixEpoch($request->time);
        $trip_location->accuracy = $request->accuracy;
        $trip_location->speed = $request->speed;
        $trip_location->raw_data = json_encode($request);
        $trip_location->mobile_security_patrol_trips_id = $request->mobile_security_patrol_trips_id;
        $trip_location->save();
        return $trip_location;
    }

    /**
     * To store date
     *
     * @param unix epoch in milleseconds
     * @return date time
     *
     */
    public function datetimeFromUnixEpoch($unix_epoch)
    {
        $unix_epoch_in_seconds = $unix_epoch / 1000;
        return date("Y-m-d H:i:s", $unix_epoch_in_seconds);
    }

    /**
     *  To store trip coordinates
     *
     *  @param array
     *  @return integer
     *
     */
    public function storeTrip($request)
    {
        $trip = new MobileSecurityPatrolTrip();
        $trip->shift_id = $request->shift_id;
        $trip->start_time = $this->datetimeFromUnixEpoch($request->startTime);
        $trip->end_time = $this->datetimeFromUnixEpoch($request->endTime);
        $trip->starting_location = $this->getAddressFromLatLng($request->source_latlng);
        $trip->destination = $this->getAddressFromLatLng($request->destination_latlng);
        $difference = $request->endTime - $request->startTime;
        $trip->travel_time = round($difference / 60 / 1000);
        $trip->save();
        return $trip;
    }

    /**
     *  Identify trip coordinates
     *
     *  @param array
     *  @return array
     *
     */
    public function identifyTrip($request, $shift)
    {
        try {
            \DB::beginTransaction();
            $json_trip_identification_array = $request->trips;
            $json_trip_coordinates_array = $request->coordinates;
            $trip_count = count($json_trip_identification_array);
            $json_trip_coordinates_collection = collect($json_trip_coordinates_array);
            foreach ($json_trip_identification_array as $trip) {
                $start_time = $trip->startTime;
                $end_time = $trip->endTime;
                /** Filter the set of cooridnates captured between two timestamps and sorting the same  --Begin */
                $filtered_coordinates = $json_trip_coordinates_collection->filter(function ($item) use ($trip) {
                    if ($item->time >= $trip->startTime && $item->time <= $trip->endTime) {
                        return $item;
                    }
                });
                $sorted_coordinates = $filtered_coordinates->sortBy('time');
                $sorted_coordinates = $sorted_coordinates->values()->all();
                /** Filter the set of cooridnates captured between two timestamps and sorting the same  --End */
                if (count($sorted_coordinates)) {
                    $coordinate_count = (count($sorted_coordinates) - 1);
                    $trip->source_latlng = $sorted_coordinates[0]->latitude . ',' . $sorted_coordinates[0]->longitude;
                    $trip->destination_latlng = $sorted_coordinates[$coordinate_count]->latitude . ',' . $sorted_coordinates[$coordinate_count]->longitude;
                    $trip->shift_id = $shift->id;
                    $saved_trip = $this->storeTrip($trip);
                    $total_km = 0;
                    $path = '';
                    $snapped_coordinates = [];
                    for ($j = 1; $j <= $coordinate_count; $j = $j + 100) {
                        $path = '';
                        for ($i = $j; ($i < ($j + 100) && $i <= $coordinate_count); $i++) {

                            $sorted_coordinates[$i]->mobile_security_patrol_trips_id = $saved_trip['id'];
                            $trip_location = $this->storeCoordinates($sorted_coordinates[$i]);
                            $path .= $sorted_coordinates[$i]->latitude . ',' . $sorted_coordinates[$i]->longitude . '|';

                        }
                        $path = rtrim($path, '|');
                        $snapped_coordinates = $this->getSnappedCoordinates($path);
                        if(isset($snapped_coordinates->snappedPoints)) {
                            for ($k = 1; $k < count($snapped_coordinates->snappedPoints); $k++) {
                                $current = $snapped_coordinates->snappedPoints[$k]->location;
                                $previous = $snapped_coordinates->snappedPoints[$k-1]->location;
                                $distance =  $this->helper_service->haversineGreatCircleDistance($previous->latitude,$previous->longitude,$current->latitude,$current->longitude);
                                $total_km = $total_km + $distance;
                            }
                        }
                    }
                    /**Calculate total distance covered  for a trip --Begin */
                    $saved_trip->total_km = ($total_km / 1000);
                    $saved_trip->save();
                    /**Calculate total distance covered  for a trip --End */
                }
            }
            \DB::commit();
            return response()->json(['message' => 'Success']);
        } catch (\Exception $e) {
            \DB::rollBack();
            Log::error('triperr ' . $e->getMessage() . ' ' . $e->getLine());
            return response()->json(['message' => $e->getMessage()]);
        }
    }

    /**
     *  To List Mobile Security Patrol Trips
     *
     */
    public function index($limit = null,$fromdate = null,$todate = null)
    {
        $trip_id_arr = [];
        $logged_in_user_id = \Auth::id();
        $logged_in_user = User::find($logged_in_user_id);
        $mobile_settings = MobileAppSetting::first();
        $speed_limit = $mobile_settings->trip_show_speed;
        $distance_limit = $mobile_settings->trip_show_distance;
        /** Get all ids for shift with trips**/
        $employee_shifts_with_trips = DB::table('employee_shifts AS es')
            ->join('mobile_security_patrol_trips AS mspt', 'es.id', '=', 'mspt.shift_id')
            ->select('es.id')->distinct()
            ->get();
        $employee_shifts_with_trips = data_get($employee_shifts_with_trips,'*.id');
        $mobile_patrol_trips = $this->employee_shift_model->whereIn('id',$employee_shifts_with_trips);
        if($fromdate != null && $todate != null){
            $mobile_patrol_trips = $mobile_patrol_trips ->whereBetween('start',[$fromdate,$todate]);
        }
        if ($limit == null && $logged_in_user->hasAnyPermission(['view_all_mobile_security_patrol_trips','admin', 'super_admin'])) {
            $mobile_patrol_trips = $mobile_patrol_trips->orderBy('created_at', 'desc')
                ->with(['vehicle_details',
                    'shift_payperiod.customer',
                    'shift_payperiod.trashed_user.trashedEmployee',
                    'shift_payperiod.trashed_payperiod']);
        } elseif ($limit == null && $logged_in_user->hasPermissionTo('view_allocated_mobile_security_patrol_trips')) {

            /**********Get the customer list of the logged in employee  - Start************** */
            // $customer_list = array_unique($this->customer_repository->getOnlyAllocatedCustomers());
            /**********Get the customer list of the logged in employee  - End**************** */
            $arr_user = [Auth::User()->id];
            $allocatedcustomers = $this->customer_repository->getAllAllocatedCustomerId($arr_user);

            $mobile_patrol_trips = $mobile_patrol_trips->orderBy('created_at', 'desc')
                ->whereHas('shift_payperiod.customer', function ($query) use ($allocatedcustomers) {
                    $query->whereIn('customer_id', $allocatedcustomers);
                })
                ->with(['vehicle_details',
                    'shift_payperiod.customer',
                    'shift_payperiod.trashed_user.trashedEmployee',
                    'shift_payperiod.trashed_payperiod',
                    'trips']);

        } else {
            $mobile_patrol_trips = $mobile_patrol_trips->orderBy('created_at', 'desc')
                ->whereHas('shift_payperiod', function ($query) use ($logged_in_user_id) {
                    $query->where('employee_id', $logged_in_user_id);
                })
                ->with(['vehicle_details',
                    'shift_payperiod.customer',
                    'shift_payperiod.trashed_user.trashedEmployee',
                    'shift_payperiod.trashed_payperiod',
                    'trips']);
        }
        if ($limit != null) {
            $mobile_patrol_trips = $mobile_patrol_trips->take($limit)->get();
            $trip_id_arr = array();
            $trip = data_get($mobile_patrol_trips, '*.trips');
            foreach ($trip as $trip_collection) {
                foreach ($trip_collection as $each_trip) {
                    array_push($trip_id_arr, $each_trip['id']);
                }
            }
        } else {
            $mobile_patrol_trips = $mobile_patrol_trips->get();
            $avg_speed_query = $this->trip_coordinate_model
                ->selectRaw('mobile_security_patrol_trips_id, AVG(speed)')
                ->groupBy('mobile_security_patrol_trips_id')->havingRaw('AVG(speed) > ?', [$speed_limit])
                ->get();
            $trip_id_arr = data_get($avg_speed_query, '*.mobile_security_patrol_trips_id');
        }
        $formatted_mobile_patrols = [];
        foreach ($mobile_patrol_trips as $key => $mobile_patrol) {
            $formatted_mobile_patrols[$key]['shift_id'] = $mobile_patrol['id'];
            $formatted_mobile_patrols[$key]['vehicle'] = $mobile_patrol['vehicle_details']['vehicle']['number'];
            $formatted_mobile_patrols[$key]['start'] = date("g:i A", strtotime($mobile_patrol['start']));
            $formatted_mobile_patrols[$key]['end'] = date("g:i A", strtotime($mobile_patrol['end']));
            $formatted_mobile_patrols[$key]['created_at'] = $mobile_patrol['created_at']->toFormattedDateString(); //->format('g:i A');
            $formatted_mobile_patrols[$key]['employee_no'] = $mobile_patrol['shift_payperiod']['trashed_user']['trashedEmployee']['employee_no'];
            $formatted_mobile_patrols[$key]['first_name'] = $mobile_patrol['shift_payperiod']['trashed_user']['full_name'];
            $formatted_mobile_patrols[$key]['employee_name'] = $mobile_patrol['shift_payperiod']['trashed_user']['full_name'];
            $formatted_mobile_patrols[$key]['pay_period'] = $mobile_patrol['shift_payperiod']['trashed_payperiod']['pay_period_name'];
            $formatted_mobile_patrols[$key]['project_number'] = $mobile_patrol['shift_payperiod']['customer']['project_number'];
            $formatted_mobile_patrols[$key]['client_name'] = $mobile_patrol['shift_payperiod']['customer']['client_name'];
            $formatted_mobile_patrols[$key]['incident_reported'] = $mobile_patrol['mobile_security_patrol_incident_reported'] == 1 ? "Yes" : "No";

            $total_km = 0;
            $total_average_speed = 0;
            $total_average_count = 0;
            $i = 1;
            $formatted_mobile_patrols[$key]['trip_details'] = [];

            foreach ($mobile_patrol['trips'] as $inner_key => $mobile_trips) {
                //$avg_speed = $this->trip_coordinate_model->where('mobile_security_patrol_trips_id',$mobile_trips['id'])->avg('speed');

                if ($mobile_trips['total_km'] < $distance_limit || (!in_array($mobile_trips['id'], $trip_id_arr))) {
                    continue;
                }

                //dump($avg_speed);
                //                if(($mobile_trips['total_km'] < $distance_limit)  || ($avg_speed < $speed_limit))
                //                    continue;
                $formatted_mobile_patrols[$key]['trip_id'] = $mobile_trips['id'];
                $trip_detail_array['trip_id'] = $mobile_trips['id'];
                $trip_detail_array['source_count'] = $i;
                $trip_detail_array['destination_count'] = $i + 1;
                $trip_detail_array['start_time'] = date("g:i A", strtotime($mobile_trips['start_time']));
                $trip_detail_array['end_time'] = date("g:i A", strtotime($mobile_trips['end_time']));
                $trip_detail_array['source'] = $mobile_trips['starting_location'];
                $trip_detail_array['destination'] = $mobile_trips['destination'];
                $trip_detail_array['travel_time'] = date('H:i', mktime(0, $mobile_trips['travel_time']));
                $trip_detail_array['incident_reported'] = '';
                $trip_detail_array['total_km'] = $mobile_trips['total_km'];
                $splitTimeStamp = explode(":",$trip_detail_array['travel_time']);
                $totalHours = ($splitTimeStamp[0])+($splitTimeStamp[1]/60);
                if($totalHours > 0) {
                    $calculatedAverageSpeed = round(($trip_detail_array['total_km'])/$totalHours);
                    $total_average_count = $total_average_count+1;
                }else{
                    $calculatedAverageSpeed = "--";
                }

                $averageSpeed = $calculatedAverageSpeed;
                $trip_detail_array['average_speed'] =  (is_numeric($averageSpeed)) ? $averageSpeed." km/h" : '--';
                $trip_detail_array['created_at'] = $mobile_trips['created_at']->format('d-m-Y H:i:s');
                $trip_detail_array['day'] = date("l", strtotime($mobile_trips['created_at']));
                array_push($formatted_mobile_patrols[$key]['trip_details'], $trip_detail_array);
                $total_km = $total_km + $mobile_trips['total_km'];
                 if($totalHours > 0) {
                $total_average_speed = $total_average_speed + $averageSpeed;
                 }
                $i = $trip_detail_array['destination_count'] + 1;
            }

            if (empty($formatted_mobile_patrols[$key]['trip_details'])) {
                unset($formatted_mobile_patrols[$key]);

                continue;
            }
            $formatted_mobile_patrols[$key]['total_km'] = $total_km;
            if($total_average_count > 0){
                $total_speed  = round($total_average_speed/$total_average_count);
            }else{
                $total_speed  = "--";
            }
            $formatted_mobile_patrols[$key]['average_speed'] = (is_numeric($total_speed)) ? $total_speed." km/h" : '--';
            $formatted_mobile_patrols[$key]['average_speed_limit'] = (!empty($this->mobileAppSetting->first())) ? $this->mobileAppSetting->first()->average_speed_limit : '';

        }
        return array_values($formatted_mobile_patrols); //= collect($formatted_mobile_patrols);
    }
    /**
     *  Get trip coordinates from trip id
     *
     *  @param trip_id integer
     *  @return array (latitude and longitude)
     *
     */
    public function getCoordinates($trip_id)
    {
        $original_coordinates = $this->trip_coordinate_model->where('mobile_security_patrol_trips_id', $trip_id)->orderby('time', 'asc')->get();
        $lat_lng_coordinates = [];
        $formatted_coordinates = ''; //[];
        foreach ($original_coordinates as $key => $coordinate) {
            $formatted_coordinates .= $coordinate['latitude'] . ',' . $coordinate['longitude'] . '|';
            $lat_lng_coordinates[$key]['latitude'] = $coordinate['latitude'];
            $lat_lng_coordinates[$key]['longitude'] = $coordinate['longitude'];
        }

        //return $formatted_coordinates;
        $formatted_coordinates = rtrim($formatted_coordinates, '|');
        $coordinates['formatted_coordinates'] = $formatted_coordinates;

        $coordinates['original_coordinates'] = $lat_lng_coordinates;
        //dd($coordinates);
        return $coordinates;
    }
    /**
     *  Get trip coordinates from shift id
     *
     *  @param shift_id integer
     *  @return array (location,latitude and longitude)
     *
     */
    public function getTripPatrol($shift_id)
    {

        $mobile_patrol_trips = $this->employee_shift_model->with('trips')->where('id', $shift_id)->first();
        $mobile_settings = MobileAppSetting::first();
        $speed_limit = $mobile_settings->trip_show_speed;
        $distance_limit = $mobile_settings->trip_show_distance;
        $total_km = 0;
        $trip_detail_array = $sam_arr = [];
        $i = 1;
        $formatted_coordinates = ''; //[];
        foreach ($mobile_patrol_trips['trips'] as $inner_key => $mobile_trips) {
            $avg_speed = $this->trip_coordinate_model->where('mobile_security_patrol_trips_id', $mobile_trips['id'])->avg('speed');
            if (($avg_speed < $speed_limit) || ($mobile_trips['total_km'] < $distance_limit)) {
                continue;
            }

            $source_lat_lng = $this->trip_coordinate_model->select('time', 'longitude', 'latitude')->where('mobile_security_patrol_trips_id', $mobile_trips['id'])->orderby('time', 'asc')->first();
            $desc_lat_lng = $this->trip_coordinate_model->select('latitude', 'longitude', 'time')->where('mobile_security_patrol_trips_id', $mobile_trips['id'])->orderby('time', 'desc')->first();

            $trip_detail_array[] = array($mobile_trips['starting_location'], floatval($source_lat_lng['latitude']), floatval($source_lat_lng['longitude']), "$i");
            $desc_count = $i + 1;
            $trip_detail_array[] = array($mobile_trips['destination'], floatval($desc_lat_lng['latitude']), floatval($desc_lat_lng['longitude']), "$desc_count");
            $i = $desc_count + 1;

            //////////////

            $original_coordinates = $this->trip_coordinate_model->where('mobile_security_patrol_trips_id', $mobile_trips['id'])->orderby('time', 'asc')->get();
            $lat_lng_coordinates = [];

            foreach ($original_coordinates as $key => $coordinate) {
                $formatted_coordinates .= $coordinate['latitude'] . ',' . $coordinate['longitude'] . '|';
                $arr = array('latitude' => $coordinate['latitude'], 'longitude' => $coordinate['longitude']);
                array_push($sam_arr, $arr);

            }

            $coordinates['original_coordinates'] = $lat_lng_coordinates;
        }

        $formatted_coordinates = rtrim($formatted_coordinates, '|');
        $coordinates['formatted_coordinates'] = $formatted_coordinates;
        $coordinates['original_coordinates'] = $sam_arr;
        $coordinates['result'] = $trip_detail_array;
        return $coordinates;

    }

    /**
     * Get address from latitude and longitude
     *
     * @param string latitude and longitude values concatenated with comma
     * @return string
     */
    public function getAddressFromLatLng($latlng)
    {
        try {
            $google_api_key = config('globals.google_api_curl_key');
            $address = file_get_contents("https://maps.google.com/maps/api/geocode/json?latlng=" . $latlng . "&sensor=false&key=" . $google_api_key);
            $address = json_decode($address);
            $google_address = '';
            if ($address->status == "OK") {
                $google_address = $address->results[0]->formatted_address;
            }
        } catch (\Exception $e) {
            $google_address = '';
            Log::error('Geocode Error ' . $e->getMessage() . ' ' . $e->getLine());
        }
        return $google_address;

    }

    /**
     * Get snapped coordinates
     *
     * @param string  path - latitude and longitude values concatenated with '|'
     * @return integer distance
     */

    public function getSnappedCoordinates($path)
    {
        try {
            $google_api_key = config('globals.google_api_curl_key');
            $snapped_coordinates = file_get_contents("https://roads.googleapis.com/v1/snapToRoads?path=" . $path . "&interpolate=true&key=" . $google_api_key);
            $snapped_coordinates = json_decode($snapped_coordinates);
        } catch (\Exception $e) {
            $snapped_coordinates = '{}';
            Log::error('Snapped Coordinates Error ' . $e->getMessage() . ' ' . $e->getLine());
        }
        return $snapped_coordinates;
    }

    /**
     * Calculates the great-circle distance between two points, with
     * the Haversine formula.
     * @param float $latitudeFrom Latitude of start point in [deg decimal]
     * @param float $longitudeFrom Longitude of start point in [deg decimal]
     * @param float $latitudeTo Latitude of target point in [deg decimal]
     * @param float $longitudeTo Longitude of target point in [deg decimal]
     * @param float $earthRadius Mean earth radius in [m]
     * @return float Distance between points in [m] (same as earthRadius)
     */
    // public function haversineGreatCircleDistance(
    //     $latitudeFrom, $longitudeFrom, $latitudeTo, $longitudeTo, $earthRadius = 6371000)
    // {
    //     // convert from degrees to radians
    //     $latFrom = deg2rad($latitudeFrom);
    //     $lonFrom = deg2rad($longitudeFrom);
    //     $latTo = deg2rad($latitudeTo);
    //     $lonTo = deg2rad($longitudeTo);

    //     $latDelta = $latTo - $latFrom;
    //     $lonDelta = $lonTo - $lonFrom;

    //     $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
    //     cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));
    //     return $angle * $earthRadius;
    // }

    // trip details latest

    public function tripDetailsLatest()
    {
        $logged_in_user_id = \Auth::id();
        $trips = MobileSecurityPatrolTrip::orderBy('created_at', 'desc')
            ->whereHas('shift.shift_payperiod.user', function ($query) use ($logged_in_user_id) {
                $query->where('employee_id', $logged_in_user_id);
            });

        $trips = $trips->take(10)->get();

        return $trips;
    }

     /**
     * Get total kilometer in a shift
     * @param int  shift_id
     */
    public function getTotalKmInSingleShift($shift_id){
       return $this->trip_model->select( \DB::raw('SUM(total_km) as total_km'))
              ->where('shift_id',$shift_id)->first();
    }

}
