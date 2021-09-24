<?php

namespace Modules\Hranalytics\Repositories;

use App\Repositories\MailQueueRepository;
use App\Services\HelperService;
use Auth;
use Carbon\Carbon;
use DB;
use Modules\Admin\Models\Customer;
use Modules\Admin\Models\Employee;
use Modules\Admin\Models\ScheduleAssignmentTypeLookup;
use Modules\Admin\Models\ShiftTiming;
use Modules\Admin\Models\User;
use Modules\Admin\Repositories\StatusLogLookupRepository;
use Modules\Hranalytics\Models\Candidate;
use Modules\Hranalytics\Models\EventLogEntry;
use Modules\Hranalytics\Models\ScheduleCustomerMultipleFillShifts;
use Modules\Hranalytics\Models\ScheduleCustomerRequirement;
use Modules\Hranalytics\Repositories\ScheduleCustomerMultipleFillShiftsRepository;
use Modules\Timetracker\Models\CandidateOpenshiftApplication;
use Modules\Timetracker\Models\EmployeeAvailability;
use Modules\Timetracker\Models\EmployeeUnavailability;

class ScheduleCustomerRequirementRepository
{
    /**
     * The Model instance.
     * ''
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $requirementModel, $eventLogModel, $helperService, $scheduleCustomerMultipleFillShiftsRepository, $mailQueueRepository;

    /**
     * Create a new CandidateScheduleRepository instance.
     *
     * @param  \App\Models\ScheduleCustomerRequirements $requirementsModel
     */
    public function __construct(ScheduleCustomerRequirement $requirementModel, MailQueueRepository $mailQueueRepository, HelperService $helperService, EventLogEntry $eventLogModel, StatusLogLookupRepository $statusLogLookupRepository, ScheduleCustomerMultipleFillShiftsRepository $scheduleCustomerMultipleFillShiftsRepository)
    {
        $this->requirementModel = $requirementModel;
        $this->eventLogModel = $eventLogModel;
        $this->statusLogLookupRepository = $statusLogLookupRepository;
        $this->helperService = $helperService;
        $this->scheduleCustomerMultipleFillShiftsRepository = $scheduleCustomerMultipleFillShiftsRepository;
        $this->mailQueueRepository = $mailQueueRepository;
    }

    public function GetDrivingDistance($lat1, $long1, $lat2, $long2)
    {
        $apiKey = config("globals.google_api_curl_key");
        // try {
        //     \App\Services\HelperService::googleAPILog('distancematrix', 'Modules\Hranalytics\Repositories\ScheduleCustomerRequirementRepository\GetDrivingDistance');
        //     $url = "https://maps.googleapis.com/maps/api/distancematrix/json?origins=" . $lat1 . "," . $long1 . "&destinations=" . $lat2 . "," . $long2 . "&mode=driving&language=pl-PL&key=" . $apiKey;
        //     $ch = curl_init();
        //     curl_setopt($ch, CURLOPT_URL, $url);
        //     curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        //     curl_setopt($ch, CURLOPT_PROXYPORT, 3128);
        //     curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        //     curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        //     $response = curl_exec($ch);
        //     curl_close($ch);
        //     $response_a = json_decode($response, true);
        //     try {
        //         $dist = $response_a['rows'][0]['elements'][0]['distance']['value'];
        //         $time = $response_a['rows'][0]['elements'][0]['duration']['value'];
        //         if ($dist < 1) {
        //             $theta = $long1 - $long2;
        //             $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
        //             $dist = acos($dist);
        //             $dist = rad2deg($dist);
        //             $miles = $dist * 60 * 1.1515;
        //             $dist = $miles * 1.609344;
        //             $time = 10;
        //         }
        //     } catch (\Throwable $th) {
        //         $theta = $long1 - $long2;
        //         $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
        //         $dist = acos($dist);
        //         $dist = rad2deg($dist);
        //         $miles = $dist * 60 * 1.1515;
        //         $dist = $miles * 1.609344;
        //         $time = 10;
        //     }
        // } catch (\Throwable $th) {
        //     $theta = $long1 - $long2;
        //     $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
        //     $dist = acos($dist);
        //     $dist = rad2deg($dist);
        //     $miles = $dist * 60 * 1.1515;
        //     $dist = $miles * 1.609344;
        // }

        try {
            $theta = $long1 - $long2;
            $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
            $dist = acos($dist);
            $dist = rad2deg($dist);
            $miles = $dist * 60 * 1.1515;
            $dist = $miles * 1.609344;
        } catch (\Throwable $th) {
            $dist = 0;
        }

        //return array('distance' => $dist, 'time' => $time);
        return $dist;
    }

    public function distance($lat1, $lon1, $lat2, $lon2, $unit)
    {
        if (($lat1 == $lat2) && ($lon1 == $lon2)) {
            return 0;
        } else {
            $theta = $lon1 - $lon2;
            $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
            $dist = acos($dist);
            $dist = rad2deg($dist);
            $miles = $dist * 60 * 1.1515;
            $unit = strtoupper($unit);

            if ($unit == "K") {
                return ($miles * 1.609344);
            } elseif ($unit == "N") {
                return ($miles * 0.8684);
            } else {
                return $miles;
            }
        }
    }
    /**
     * Submit Open shift
     *
     * @param array $details
     * @return json response
     */
    public function submitOpenShiftApplication($valuearray, $userId, $shiftid)
    {
        try {
            $saveOpenShift = CandidateOpenshiftApplication::updateOrCreate(["shiftid" => $shiftid, "userid" => $userId, "multifillid" => $valuearray["multifillid"]], $valuearray);
            if ($saveOpenShift) {
                $multipleShifts = ScheduleCustomerMultipleFillShifts::where('parent_id', $valuearray["multifillid"])->get();
                foreach ($multipleShifts as $multipleShift) {
                    $reqId = $multipleShift->id;
                    $valuearray['multifillid'] = $reqId;
                    $saveOPenShiftChild = CandidateOpenshiftApplication::updateOrCreate(["shiftid" => $shiftid, "userid" => $userId, "multifillid" => $valuearray["multifillid"]], $valuearray);
                }

                if ($saveOpenShift->wasRecentlyCreated) {
                    $shiftTiming = ShiftTiming::pluck('shift_name', 'id');
                    $assignmentTypeLookups = ScheduleAssignmentTypeLookup::pluck('type', 'id');
                    $multipleFillObject = $this->scheduleCustomerMultipleFillShiftsRepository->get($valuearray["multifillid"]);

                    $helper_variable = array(
                        '{receiverFullName}' => ucfirst($saveOpenShift->user->first_name) . ' ' . ucfirst($saveOpenShift->user->last_name),
                        '{loggedInUserEmployeeNumber}' => \Auth::user()->employee->employee_no,
                        '{loggedInUser}' => \Auth::user()->first_name . ' ' . \Auth::user()->last_name,
                        '{client}' => $saveOpenShift->customer->client_name,
                        '{projectNumber}' => $saveOpenShift->customer->project_number,
                        '{candidateScheduleShiftStartDate}' => Carbon::parse($saveOpenShift->startdate)->format('M d,Y'),
                        '{candidateScheduleShiftEndDate}' => Carbon::parse($saveOpenShift->enddate)->format('M d,Y'),
                        '{candidateScheduleShiftStartTime}' => Carbon::parse($saveOpenShift->starttime)->format('h:i A'),
                        '{candidateScheduleShiftEndTime}' => Carbon::parse($saveOpenShift->endtime)->format('h:i A'),
                        '{candidateScheduleSiteRate}' => (($multipleFillObject) ? $multipleFillObject->scheduleCustomerRequirement->site_rate : 0),
                        '{candidateScheduleNoOfShifts}' => ($multipleFillObject) ? $multipleFillObject->no_of_position : 0,
                        '{candidateScheduleShiftTiming}' => ($multipleFillObject && $shiftTiming) ? ucfirst($shiftTiming[$multipleFillObject->shift_timing_id]) : '',
                        '{candidateScheduleSiteAddress}' => ($multipleFillObject) ? $multipleFillObject->scheduleCustomerRequirement->customer->address : '',
                        '{candidateScheduleCity}' => ($multipleFillObject) ? $multipleFillObject->scheduleCustomerRequirement->customer->city : '',
                        '{candidateSchedulePostalCode}' => ($multipleFillObject) ? $multipleFillObject->scheduleCustomerRequirement->customer->postal_code : '',
                        '{candidateScheduleAssignmentType}' => ($multipleFillObject && isset($assignmentTypeLookups[$multipleFillObject->scheduleCustomerRequirement->fill_type])) ? $assignmentTypeLookups[$multipleFillObject->scheduleCustomerRequirement->fill_type] : '',
                        '{candidateScheduleAssigneeName}' => ($saveOpenShift->user) ? ucfirst($saveOpenShift->user->first_name) . ' ' . ucfirst($saveOpenShift->user->last_name) . ' (' . $saveOpenShift->user->employee->employee_no . ')' : '',
                    );
                    $this->mailQueueRepository->prepareMailTemplate("candidate_open_shift_notification", $saveOpenShift->customer->id, $helper_variable, "Modules\Timetracker\Models\CandidateOpenshiftApplication", 0, $userId);
                }

                $content['success'] = true;
                $content['message'] = 'Saved';
                $content['code'] = 200;
            } else {
                $content['success'] = false;
                $content['message'] = 'Something went wrong';
                $content['code'] = 406;
            }
        } catch (\Throwable $th) {
            throw $th;
            $content['success'] = false;
            $content['message'] = $th;
            $content['code'] = 406;
        }
        return response()->json(['content' => $content], $content['code']);
    }

    /**
     * get schedule summary list in Datatable
     *
     * @param [type] $type
     * @return void
     */
    public function scheduleRequirementOpenshiftList($type, $mylat, $mylong, $maxdistance, $minrate, $pageno, $orderby)
    {

        $limit = 6;
        $offset = ($limit * $pageno) - 6;
        $user = \Auth::user();
        $customerarray = [];
        $geolocarray = [];
        $totalpages = 1;
        $totalpage = 1;

        $customersinglequery = DB::table('schedule_customer_requirements')
            ->select(
                'schedule_customer_requirements.*',
                'customers.project_number',
                'customers.geo_location_lat',
                'customers.geo_location_long',
                'customers.client_name',
                'customers.address',
                'customers.city',
                'customers.province',
                'customers.postal_code',
                'customers.billing_address',
                DB::raw('(select 6371 * acos(cos(radians("' . $mylat . '"))
                * cos(radians(geo_location_lat))
                * cos(radians(geo_location_long) - radians("' . $mylong . '"))
                + sin(radians("' . $mylat . '"))
                * sin(radians(geo_location_lat)))  from customers where id = schedule_customer_requirements.customer_id) as mydis')
            )
            ->addselect(DB::raw('(select status from `event_log_entries` where schedule_customer_requirement_id=schedule_customer_requirements.id order by created_at desc limit 0,1 ) as singlefill'))
            ->addselect(DB::raw('(select security_clearance from security_clearance_lookups where id=schedule_customer_requirements.security_clearance_level) as seclevel'))
            ->addselect(DB::raw('(select count(id) from `schedule_customer_multiple_fill_shifts` where schedule_customer_requirement_id=schedule_customer_requirements.id and parent_id=0 and `assigned` = 0 ) as unassignedshifts'))
            ->addselect(DB::raw('(select count(id) from `schedule_customer_multiple_fill_shifts` where schedule_customer_requirement_id=schedule_customer_requirements.id and `assigned_employee_id` > 0 ) as multifill'))
            ->addselect(DB::raw('(select count(id) from `schedule_customer_multiple_fill_shifts` where schedule_customer_requirement_id=schedule_customer_requirements.id and `deleted_at` IS NULL) as multipleShiftCount'))
            ->addselect(DB::raw('(select concat_ws(" to ",`shift_from`,`shift_to`) from `schedule_customer_multiple_fill_shifts` where schedule_customer_requirement_id=schedule_customer_requirements.id and `deleted_at` IS NULL order by shift_from limit 0,1) as first_shift'))
            ->addselect(DB::raw('(select concat_ws(" to ",`shift_from`,`shift_to`) from `schedule_customer_multiple_fill_shifts` where schedule_customer_requirement_id=schedule_customer_requirements.id and `deleted_at` IS NULL order by shift_from desc limit 0,1) as last_shift'))
            ->addSelect(DB::raw($mylat . " as mylat"))
            ->addSelect(DB::raw($mylong . " as mylong"))
            ->leftJoin('customers', 'schedule_customer_requirements.customer_id', '=', 'customers.id')
            ->where('site_rate', '>=', $minrate)
            ->where('end_date', '>=', date("Y-m-d"))
            // ->where('status', '<', 2)
            ->whereRaw("expiry_date IS NULL or expiry_date>=?", [date("Y-m-d h:i A")])
            // ->orwhere("expiry_date", '<=', date("Y-m-d"))
            ->whereRaw('(select 6371 * acos(cos(radians("' . $mylat . '"))
            * cos(radians(geo_location_lat))
            * cos(radians(geo_location_long) - radians("' . $mylong . '"))
            + sin(radians("' . $mylat . '"))
            * sin(radians(geo_location_lat)))  from customers where id = schedule_customer_requirements.customer_id) <=' . $maxdistance);

        if ($orderby == SITERATETOPFIRST) {
            $customersinglequery = $customersinglequery->orderBy('site_rate', 'desc');
        } elseif ($orderby == SITERATEBOTTOMFIRST) {
            $customersinglequery = $customersinglequery->orderBy('site_rate', 'asc');
        } elseif ($orderby == SITERATESHORTDISTANCEFIRST) {
            $customersinglequery = $customersinglequery->orderBy('mydis', 'asc');
        } elseif ($orderby == SITERATEDATEFIRST) {
            $customersinglequery = $customersinglequery->orderBy('start_date', 'asc');
        }
        //$customersinglequery->skip($offset)->take($limit);
        $customersinglequery = $customersinglequery->get();

        $detailedarray = [];
        $detailedarray[0] = [];
        $detailedarray[1] = $customersinglequery;
        $detailedarray[2] = 1;
        return $detailedarray;
    }

    public function getOpenShiftdetailview($shiftid)
    {
        return $this->requirementModel
            ->select('*')
            ->addselect(DB::raw('(select security_clearance from security_clearance_lookups where id=schedule_customer_requirements.security_clearance_level) as seclevel'))
            ->where('id', $shiftid)->with('customer')->first();
    }
    /**
     * get schedule summary list in Datatable
     *
     * @param [type] $type
     * @return void
     */
    public function scheduleRequirementList($type, $client_id = null)
    {
        $user = \Auth::user();
        $role = $user->roles[0]->name;
        $scheduleRequirementList = $this->requirementModel->with('multifill')
            ->whereHas('customer', function ($query) use ($type) {
                $query->where('stc', '=', $type);
            })
            ->with([
                'customer' => function ($query) {
                    $query->select();
                },
                'event_log_entry_latest_accepted',
                'event_log_entry_latest',
                'scheduleCustomerAllShifts',
                'eventLogs',
                'trashed_fill_type',
            ])
            ->when(($role == 'duty_officer' || $role == 'operator'), function ($query, $role) use ($user) {
                return $query->where('user_id', $user->id);
            })
            ->when($client_id != null && $client_id != 0, function ($q) use ($client_id) {
                return $q->whereHas('customer', function ($query) use ($client_id) {
                    $query->where('id', '=', $client_id);
                });
            })
            ->select([
                'id', 'customer_id', 'type', 'fill_type', 'site_rate', \DB::raw('DATE_FORMAT(start_date,\'%Y-%m-%d\') as start_date, DATE_FORMAT(end_date,\'%Y-%m-%d\') as end_date'), 'time_scheduled', 'notes', 'security_clearance_level', 'length_of_shift', 'created_at', 'updated_at',
            ])
            ->get();

        return $scheduleRequirementList;
    }
    public function getApplieddates($shiftid)
    {
        $applieddates = [];
        $startdate = date("y-m-d", strtotime("-3 Months"));
        $applieddatesarray = CandidateOpenshiftApplication::select('startdate', 'enddate')->where(["userid" => \Auth::user()->id])->where('startdate', '>', $startdate)->get();
        foreach ($applieddatesarray as $approws) {
            $startDate = new Carbon($approws->startdate);
            $endDate = new Carbon($approws->enddate);
            $all_dates = array();
            if (!in_array($startDate->toDateString(), $applieddates)) {
                array_push($applieddates, $startDate->toDateString());
            }
            // while ($startDate->lte($endDate)) {

            //     array_push($applieddates, $startDate->toDateString());
            //     $startDate->addDay();
            // }
        }
        return $applieddates;
    }

    /**
     * Function to get the schedule requirement details
     * @param  $id
     * @return object
     */
    public function getScheduleRequirementDetails($requirement_id)
    {
        $scheduleRequirement = $this->requirementModel->with(
            'scheduleCustomerAllShifts',
            'trashed_security_clearance',
            'assignment_type',
            'trashed_assignment_type',
            'customer',
            'user',
            'eventLogs'
        )
            ->where('id', $requirement_id)
            ->select(
                'id',
                'user_id',
                'customer_id',
                'type',
                'site_rate',
                'start_date',
                'end_date',
                'time_scheduled',
                'notes',
                'overtime_notes',
                'no_of_shifts',
                'length_of_shift',
                'require_security_clearance',
                'security_clearance_level',
                DB::raw('(select count(id) from `schedule_customer_multiple_fill_shifts` where
            `schedule_customer_requirement_id`=schedule_customer_requirements.id and `assigned` > 0) as closedshifts '),
                DB::raw('DATE_FORMAT(created_at,\'%Y-%m-%d\') as inquiry_date, DATE_FORMAT(created_at,\'%l:%i %p\') as inquiry_time')
            )
            ->addselect(DB::raw('(select security_clearance from security_clearance_lookups where id=schedule_customer_requirements.security_clearance_level) as seclevel'))
            ->first();

        $applieddates = ScheduleCustomerMultipleFillShifts::where(['schedule_customer_requirement_id' => $requirement_id, 'assigned_employee_id' => Auth::user()->id])->get()->pluck('shift_from');
        $results = [$scheduleRequirement, $applieddates];
        return $results;
    }

    /**
     * Function to get the schedule details from schedule summary
     * @param  $id
     * @return object
     */
    public function prepareScheduleRecords($project_id, $requirement_id)
    {
        return $this->requirementModel->with('trashed_user', 'customer')
            ->where('customer_id', '=', $project_id)
            ->where('id', '=', $requirement_id)
            ->select('id', 'customer_id', 'user_id', 'type', 'site_rate', 'start_date', 'end_date', 'time_scheduled', 'notes', 'length_of_shift', \DB::raw('DATE_FORMAT(created_at,\'%d %M,%Y\') as inquiry_date, DATE_FORMAT(created_at,\'%l:%i %p\') as inquiry_time'))
            ->first();
    }

    /**
     * Function to store Schedule Requirements
     * @param  $request
     * @return object
     */
    public function store($request)
    {

        $expiry_date = null;
        if ($request->get('expiry_date')) {
            if ($request->get('expiry_time')) {
                $expiry_date = \Carbon::parse($request->get('expiry_date') . " " . $request->get('expiry_time'));
            } else {
                $expiry_date = $request->get('expiry_date');
            }
        }
        $requirement = new ScheduleCustomerRequirement([
            'user_id' => \Auth::user()->id,
            'customer_id' => $request->get('customer_id'),
            'type' => $request->get('type'),
            'site_rate' => $request->get('site_rate'),
            'expiry_date' => $expiry_date,
            'start_date' => $request->get('start_date'),
            'end_date' => $request->get('end_date'),
            'time_scheduled' => $request->get('time_scheduled'),
            'notes' => $request->get('notes'),
            'length_of_shift' => $request->get('length_of_shift'),
            'require_security_clearance' => $request->get('require_security_clearance'),
            'security_clearance_level' => $request->get('security_clearance_level'),
            'no_of_shifts' => $request->get('no_of_shifts'),
            'overtime_notes' => $request->get('overtime_hours_notes'),
            'fill_type' => ($request->get('type') == config('globals.multiple_fill_id')) ? $request->get('fill_type') : $request->get('type'),
        ]);
        $requirement->save();
        $requirement->load('customer');
        return $requirement;
    }

    /**
     * Get single Schedule Requirement
     *
     * @param $id
     * @return object
     */
    public function get($id)
    {
        return $this->requirementModel->with('eventLogs', 'trashed_assignment_type', 'multifill', 'multifill.shiftTiming')->find($id);
    }

    /**
     * Get  EventLog
     *
     * @param $id
     * @return object
     */
    public function getEventLog($requirement_id)
    {
        $query = $this->eventLogModel->with(['user' => function ($q) {
            $q->select('id', 'email', \DB::raw("CONCAT(first_name,' ',COALESCE(last_name,'')) as name"));
        }, 'user.candidate_transition.candidate.latestJobApplied', 'status_log'])->select(['candidate_id', 'user_id', 'status', \DB::raw('DATE_FORMAT(created_at,\'%d %M,%Y\') as call_date'), \DB::raw('DATE_FORMAT(created_at,\'%l:%i %p\') as call_time'), 'created_at', 'schedule_customer_requirement_id'])
            ->where('schedule_customer_requirement_id', '=', $requirement_id)
            ->orderBy('created_at', 'asc');

        return $query->get();
    }

    /**
     * Save EventLog
     *
     * @param $id
     * @return object
     */
    public function saveEventLog($request)
    {

        try {
            \DB::beginTransaction();

            // if ($request->get('shift_id') == 0) {
            //     EventLogEntry::where('user_id', $request->get('user_id'))->where('schedule_customer_requirement_id', $request->get('requirement_id'))->delete();
            // } else if ($request->get('assignment_type_id') == config('globals.multiple_fill_id')) {
            //     EventLogEntry::where('user_id', $request->get('user_id'))->where('multiple_shift_id', $request->get('shift_id'))->update(['status' => 3, 'score' => 1]);
            //     $deleteAllocatedUser = ScheduleCustomerMultipleFillShifts::where('id', $request->get('shift_id'))->update(['assigned_employee_id' => null, 'assigned' => 0, 'assigned_by' => null]);
            // } else {
            //     $destroy_assigned = $this->scheduleCustomerMultipleFillShiftsRepository->deleteAllocated($request->get('shift_id'));
            // }
            $shift_id = $request->get('shift_id');

            if ($request->get('assignment_type_id') == config('globals.multiple_fill_id')) {
                if ($request->get('status') == config('globals.called_accepted_shift_id')) {
                    $childrenObject = ScheduleCustomerMultipleFillShifts::where('parent_id', $shift_id)->where('assigned_employee_id', null)->first();
                    if (!empty($childrenObject)) {
                        $shift_id = $childrenObject->id;
                    }
                }

                if ($request->get('status') == config('globals.called_accepted_shift_id')) {
                    $data['assigned_employee_id'] = $request->get('user_id');
                    $data['assigned_by'] = \Auth::user()->id;
                    $data['assigned'] = 1;
                } else {
                    $data['assigned_employee_id'] = null;
                    $data['assigned_by'] = null;
                    $data['assigned'] = 0;
                }
                $multipleFillObject = ScheduleCustomerMultipleFillShifts::find($shift_id);
                $updateStatus = $multipleFillObject->update($data);
                $startDateTime = $multipleFillObject->shift_from;
                $endDateTime = $multipleFillObject->shift_to;
            }

            $eventlog = new EventLogEntry;
            $eventlog->user_id = $request->get('user_id');
            $eventlog->duty_officer_id = \Auth::user()->id;
            $eventlog->status = $request->get('status');
            $eventlog->accepted_rate = $request->get('accepted_rate');
            $eventlog->status_notes = $request->get('status_notes');
            $eventlog->multiple_shift_id = (null !== $shift_id) ? $shift_id : null;
            $eventlog->schedule_customer_requirement_id = $request->get('requirement_id');
            $eventlog->score = $this->statusLogLookupRepository->getScore($request->get('status'));
            $eventlog->save();

            $singlefillcount = EventLogEntry::where('schedule_customer_requirement_id', $request->get('requirement_id'))->where('status', 1)->count();
            $multifillcount = ScheduleCustomerMultipleFillShifts::where('schedule_customer_requirement_id', $request->get('requirement_id'))->count();
            if ($shift_id == 0) {
                if ($singlefillcount != 0) {
                    $this->requirementModel->where('id', $request->get('requirement_id'))->update(['status' => 2]);
                } else {
                    $this->requirementModel->where('id', $request->get('requirement_id'))->update(['status' => 1]);
                }
            } else {
                if ($singlefillcount == $multifillcount) {
                    $this->requirementModel->where('id', $request->get('requirement_id'))->update(['status' => 2]);
                } else {
                    $this->requirementModel->where('id', $request->get('requirement_id'))->update(['status' => 1]);
                }
            }
            if (($shift_id == 0) && $request->get('status') == config('globals.called_accepted_shift_id')) {
                CandidateOpenshiftApplication::where('shiftid', $request->get('requirement_id'))->where('multifillid', $shift_id)->where('userid', $request->get('user_id'))->update(['status' => 1, 'approved_by' => \Auth::user()->id, 'multifillid' => $shift_id]);
            } elseif ($shift_id == 0) {
                CandidateOpenshiftApplication::where('shiftid', $request->get('requirement_id'))->where('multifillid', $shift_id)->where('userid', $request->get('user_id'))->update(['status' => 0, 'approved_by' => null]);
            }

            //email trigger - start
            if (($shift_id != 0) && $request->get('assignment_type_id') == config('globals.multiple_fill_id')) {
                $shiftTiming = ShiftTiming::pluck('shift_name', 'id');
                $assignmentTypeLookups = ScheduleAssignmentTypeLookup::pluck('type', 'id');
                $helper_variable = array(
                    '{receiverFullName}' => $request->get('user_name'),
                    '{loggedInUserEmployeeNumber}' => \Auth::user()->employee->employee_no,
                    '{loggedInUser}' => \Auth::user()->first_name . ' ' . \Auth::user()->last_name,
                    '{client}' => $request->get("client_name"),
                    '{projectNumber}' => $request->get("project_number"),
                    '{candidateScheduleShiftStartDate}' => $startDateTime ? Carbon::parse($startDateTime)->format('M d,Y') : '',
                    '{candidateScheduleShiftEndDate}' => $endDateTime ? Carbon::parse($endDateTime)->format('M d,Y') : '',
                    '{candidateScheduleShiftStartTime}' => $startDateTime ? Carbon::parse($startDateTime)->format('h:i A') : '',
                    '{candidateScheduleShiftEndTime}' => $endDateTime ? Carbon::parse($endDateTime)->format('h:i A') : '',
                    '{candidateScheduleAssigneeName}' => ($multipleFillObject && $multipleFillObject->user) ? $multipleFillObject->user->first_name . ' ' . $multipleFillObject->user->last_name . ' (' . $multipleFillObject->user->employee->employee_no . ')' : '',
                    '{candidateScheduleShiftStatus}' => $eventlog ? ucfirst($eventlog->status_log->status) : '',
                    '{candidateScheduleShiftStatusNote}' => $request->get('status_notes') ? ucfirst($request->get('status_notes')) : '',
                    '{candidateScheduleSiteRate}' => ((((!empty($request->get('status'))) && ($request->get('status') == config('globals.called_accepted_shift_id')) && !empty($request->get('accepted_rate')))) ? $request->get('accepted_rate') : (($multipleFillObject) ? $multipleFillObject->scheduleCustomerRequirement->site_rate : 0)),
                    '{candidateScheduleNoOfShifts}' => ($multipleFillObject) ? $multipleFillObject->no_of_position : 0,
                    '{candidateScheduleShiftTiming}' => ($multipleFillObject && $shiftTiming) ? ucfirst($shiftTiming[$multipleFillObject->shift_timing_id]) : '',
                    '{candidateScheduleSiteAddress}' => ($multipleFillObject) ? $multipleFillObject->scheduleCustomerRequirement->customer->address : '',
                    '{candidateScheduleCity}' => ($multipleFillObject) ? $multipleFillObject->scheduleCustomerRequirement->customer->city : '',
                    '{candidateSchedulePostalCode}' => ($multipleFillObject) ? $multipleFillObject->scheduleCustomerRequirement->customer->postal_code : '',
                    '{candidateScheduleAssignmentType}' => ($assignmentTypeLookups) ? $assignmentTypeLookups[$multipleFillObject->scheduleCustomerRequirement->fill_type] : '',
                    '{candidateScheduleAssigneeName}' => $request->get('user_name'),
                );
                $this->mailQueueRepository->prepareMailTemplate("candidate_schedule_employee_assigned", $multipleFillObject->scheduleCustomerRequirement->customer->id, $helper_variable, "Modules\Timetracker\Models\ScheduleCustomerMultipleFillShifts", 0, $request->get('user_id'));
            }
            //email trigger - end

            \DB::commit();
            return $eventlog;
        } catch (\Exception $e) {
            \DB::rollBack();
            return $e->getMessage();
        }
    }

    /**
     * Get Candidates details listed in Candidate schedule grid
     *
     * @param $id
     * @return object
     */
    public function getScheduleCandidates($request)
    {
        $id = $request->get('array_candidate');
        $ids = json_decode($id);
        $requirement_id = $request->get('requirement_id');
        $candidate_query = Candidate::with([
            'wageexpectation' => function ($query) {
                $query->select('candidate_id', 'wage_expectations_from', 'wage_expectations_to');
            },
            'securityclearance' => function ($query) {
                $query->select('candidate_id', 'years_lived_in_canada');
            },
            'guardingexperience' => function ($query) {
                $query->select('candidate_id', 'years_security_experience');
            },
            'eventlog' => function ($query) use ($requirement_id) {
                $query->select('candidate_id', 'status', 'updated_at')->where('schedule_customer_requirement_id', $requirement_id)->orderBy('updated_at', 'desc');
            },
            'eventlog_score',
            'latestJobApplied',
        ])
            ->whereHas('latestJobApplied')
            ->select('id', 'name', 'address', 'postal_code', 'phone_cellular', 'email')
            ->whereIn('id', $ids)
            ->orderby('name');
        return $candidate_query->get();
    }

    /**
     * Get Employees details
     * @return object
     */
    public function getScheduleEmployees($request)
    {
        //dd($request->all());
        $day = $request->get('day');
        $shift = $request->get('shift');
        $requirement_id = $request->get('requirement_id');
        $security_clearence_id = $request->get('level');
        $date = $request->get('dateval');
        $selectedDate = '';
        if ($date != 0) {
            $selectedDate = Carbon::parse($date);
        }

        $checkedvalue = $request->get('checkedvalue');
        $security_clearance_check = $request->get('security_clearance_check');
        if (!empty($checkedvalue)) {
            $current_availability = $checkedvalue;
        } else {
            $current_availability = array(1, 2);
        }
        //dd($security_clearence_id, $security_clearance_check);
        $unavailable_employees = array();
        if ($requirement_id != 0) {
            $requirement = $this->getScheduleRequirement($requirement_id);
            $unavailable_employees = EmployeeUnavailability::select('employee_id')
                ->where('from', '<=', $requirement->start_date)
                ->where('to', '>=', $requirement->end_date)->get()->toArray();
        }

        $employee_query = EmployeeAvailability::where('shift_timing_id', '=', $shift)
            ->where('week_day', '=', $day)
            ->whereNotIn('employee_id', $unavailable_employees)
            ->with('employee.employee_unavailability')
            ->whereHas('employee', function ($q) use ($current_availability) {
                $q->whereIn('work_type_id', $current_availability)->select('id', 'user_id', 'phone', 'employee_address', 'employee_postal_code', 'wage_expectations_from', 'wage_expectations_to', 'years_of_security', \DB::raw("floor(datediff(curdate(),being_canada_since) / 365) as being_canada_since"));
            })
            ->whereHas('employee.user', function ($q) {
                $q->where('active', 1);
            });
        $employee_query->when($date != 0, function ($q) use ($selectedDate) {
            $q->whereDoesntHave('employee.employee_unavailability', function ($q) use ($selectedDate) {
                $q->where('from', '<=', $selectedDate)->where('to', '>=', $selectedDate);
            });
            // }
        });
        $employee_query->when($security_clearance_check != 0, function ($q) use ($security_clearence_id) {
            $q->whereHas('employee.user.securityClearanceUser', function ($q) use ($security_clearence_id) {
                $q->where('security_clearance_lookup_id', $security_clearence_id);
            });
            // }
        });
        $employee_query->when($security_clearance_check == 0, function ($q) use ($security_clearence_id) {
            $q->whereDoesntHave('employee.user.securityClearanceUser', function ($q) use ($security_clearence_id) {
                $q->where('security_clearance_lookup_id', $security_clearence_id);
            });
        });

        $employee_query->with([
            'employee.user' => function ($q) {
                $q->select('id', 'email', \DB::raw("CONCAT(first_name,' ',COALESCE(last_name,'')) as name"));
            }, 'employee.user.eventlog' => function ($q) use ($requirement_id) {
                $q->select('id', 'user_id', 'candidate_id', 'status', 'updated_at')->where('schedule_customer_requirement_id', $requirement_id)->orderBy('updated_at', 'desc');
            },
            'employee.user.eventlog_score',
            'employee.user.candidate_transition.candidate.latestJobApplied',
            'employee.user.securityClearanceUser.securityClearanceLookups',
            'employee.user.employee_shift_payperiods.availableShift',
        ])->get();
        //  return $employee_query;
        if ($requirement_id != 0) {
            $result = $this->getArray($employee_query->get(), $requirement);
        } else {
            $result = $this->getArray($employee_query->get());
        }
        return $result;
    }
    public function getArray($employee_query, $requirement = null)
    {
        $list_data = array();
        foreach ($employee_query as $key => $data) {
            $value['name'] = $data->employee->user->name;
            $value['id'] = $data->id;
            $value['employee_address'] = $data->employee->employee_address;
            $value['employee_city'] = $data->employee->employee_city;
            $value['user_id'] = $data->employee->user_id;
            $value['employee_postal_code'] = $data->employee->employee_postal_code;
            $value['years_of_security'] = $data->employee->years_of_security;
            $value['being_canada_since'] = $data->employee->being_canada_since;
            $value['wage_expectations_from'] = $data->employee->wage_expectations_from;
            $value['wage_expectations_to'] = $data->employee->wage_expectations_to;
            $securityClearanceUsersArray = [];
            $securityClearanceUsers = $data->employee->user->securityClearanceUser;
            if ($securityClearanceUsers != null) {
                foreach ($securityClearanceUsers as $ky => $security_clearance_user) {
                    $securityClearanceUsersArray[] = ($security_clearance_user->securityClearanceLookups->security_clearance);
                }
            }
            $value['security_clearance_user'] = implode(', ', $securityClearanceUsersArray);
            $value['unavailability'] = !empty(data_get($data, 'employee.employee_unavailability.*')) ? data_get($data, 'employee.employee_unavailability.*') : null;
            if ($value['unavailability'] != null && $requirement != null) {
                foreach ($value['unavailability'] as $key => $date) {
                    if (($date->from >= $requirement->start_date && $date->from <= $requirement->end_date) || ($date->to >= $requirement->start_date && $date->to <= $requirement->end_date)) {
                        $unavailability_set = true;
                        break;
                    } else {
                        $unavailability_set = false;
                    }
                }
            } else {
                $unavailability_set = false;
            }

            $statusColor = 3;
            if ($data->employee->user != null && $data->employee->user->employee_shift_payperiods != null) {
                foreach ($data->employee->user->employee_shift_payperiods as $shiftPayPeriod) {
                    if ($shiftPayPeriod->availableShift != null) {
                        if ($shiftPayPeriod->availableShift->live_status_id == 1) {
                            $statusColor = 1;
                        } elseif ($shiftPayPeriod->availableShift->live_status_id == 2) {
                            $statusColor = 2;
                        }
                    }
                }
            }
            $value['live_status_color'] = $statusColor;
            $value['unavailability_set'] = $unavailability_set;
            $value['eventlog_status'] = ((null !== ($data->employee->user->eventlog)) && ($data->employee->user->eventlog->first())) ? $data->employee->user->eventlog->first()->status : 0;
            $value['prev_attempt'] = (null !== ($data->employee->user->eventlog_score->first())) ? $data->employee->user->eventlog_score->first()->prev_attempt : '';
            $value['phone'] = $data->employee->phone;
            $value['email'] = $data->employee->user->email;
            $value['candidate_transition'] = isset($data->employee->user->candidate_transition) ? $data->employee->user->candidate_transition : null;
            $value['candidate_id'] = isset($data->employee->user->candidate_transition) ? $data->employee->user->candidate_transition->candidate->id : '--';
            $value['job_id'] = (isset($data->employee->user->candidate_transition) && isset($data->employee->user->candidate_transition->candidate) && isset($data->employee->user->candidate_transition->candidate->latestJobApplied)) ? $data->employee->user->candidate_transition->candidate->latestJobApplied->job_id : '';
            $value['avg_score'] = (null !== ($data->employee->user->eventlog_score->first())) ? $data->employee->user->eventlog_score->first()->avg_score : '';
            array_push($list_data, $value);
        }
        return $list_data;
    }
    public function getStcSummaryCandidates($allocated_employees = null, $flag)
    {
        $candidate_query = Candidate::with([
            'wageexpectation' => function ($query) {
                $query->select('candidate_id', 'wage_expectations_from', 'wage_expectations_to');
            },
            'securityclearance' => function ($query) {
                $query->select('candidate_id', 'years_lived_in_canada');
            },
            'guardingexperience' => function ($query) {
                $query->select('candidate_id', 'years_security_experience');
            },
            'eventlog' => function ($query) {
                $query->select('candidate_id', 'status', 'updated_at')->orderBy('updated_at', 'desc');
            },
            'eventlog_score',
            'latestJobApplied',
        ])
            ->when($allocated_employees != null || $flag == true, function ($query) use ($allocated_employees) {
                $query->whereHas('candidateEmployees', function ($query) use ($allocated_employees) {
                    $query->whereIn('user_id', $allocated_employees);
                });
            }, function ($query) {
                $query->whereHas('candidateEmployees');
            })
            ->whereHas('latestJobApplied')

            ->select('id', 'name', 'address', 'postal_code', 'phone_cellular', 'email')
            ->orderby('name');

        return $candidate_query->get();
    }
    /**
     * Get Candidates details listed in Candidate schedule grid
     *
     * @param $id
     * @return object
     */
    public function getStcSummaryEmployees($allocated_employees = null, $flag, $userId = 0, $spare = 1)
    {
        $candidate_query = Employee::with([
            'user' => function ($query) {
                $query->select('id', 'email', 'active', \DB::raw("CONCAT(first_name,' ',COALESCE(last_name,'')) as name"));
            }, 'user.eventlog' => function ($query) {
                $query->select('candidate_id', 'status', 'updated_at')->orderBy('updated_at', 'desc');
            },
            'user.eventlog_score', 'user.candidate_transition.candidate.latestJobApplied', 'user.employee', 'user.roles',
        ])->whereHas('user', function ($query) {
            $query->where('active', 1);
        })->whereHas('user.roles', function ($q) {
            $q->whereNotIn('name', ['admin', 'super_admin']);
        })->when($spare == 1, function ($query) {
            $query->whereHas('user', function ($query) {
                $usersWithSparePoolPermission = User::whereHas('roles.permissions', function ($query) {
                    $query->where('name', "Spares Pool");
                })->pluck('id');
                $query->whereIn('id', $usersWithSparePoolPermission);
            });
        })->when($allocated_employees != null || $flag == true, function ($query) use ($allocated_employees) {
            $query->whereHas('candidateEmployees', function ($query) use ($allocated_employees) {
                $query->whereIn('user_id', $allocated_employees);
            });
        })->when($userId != 0, function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })->select(
            'id',
            'user_id',
            'phone',
            'employee_city',
            'employee_rating',
            'employee_address',
            'employee_postal_code',
            'current_project_wage',
            'years_of_security',
            \DB::raw("floor(datediff(curdate(),being_canada_since) / 365) as being_canada_since")
        );
        return $this->pepareData($candidate_query->get());
    }

    public function pepareData($objects)
    {
        if (empty($objects)) {
            return [];
        }

        $resultArr = [];
        foreach ($objects as $key => $object) {
            $result['id'] = $object->id;
            $result['user_id'] = $object->user_id;
            $result['phone'] = $object->phone;
            $result['employee_city'] = $object->employee_city;
            $result['employee_rating'] = $object->employee_rating;
            $result['employee_address'] = $object->employee_address;
            $result['employee_postal_code'] = $object->employee_postal_code;
            $result['years_of_security'] = $object->years_of_security;
            $result['current_project_wage'] = $object->current_project_wage;
            $result['being_canada_since'] = $object->being_canada_since;
            $result['email'] = ($object->user) ? $object->user->email : '';
            $result['user_name'] = ($object->user) ? $object->user->name : '';
            $result['prev_attempt'] = ($object->user && isset($object->user->eventlog_score[0])) ? $object->user->eventlog_score[0]->prev_attempt : '';
            $result['avg_score'] = ($object->user && isset($object->user->eventlog_score[0])) ? $object->user->eventlog_score[0]->avg_score : '';
            $result['role'] = ($object->user && isset($object->user->roles[0])) ? ucfirst($object->user->roles[0]->name) : '';
            $result['employee_no'] = ($object->user && $object->user->employee) ? $object->user->employee->employee_no : '';
            $result['candidate_id'] = ($object->user && $object->user->candidate_transition) ? $object->user->candidate_transition->candidate_id : '';
            $result['job_id'] = ($object->user && $object->user->candidate_transition && $object->user->candidate_transition->candidate && $object->user->candidate_transition->candidate->latestJobApplied) ? $object->user->candidate_transition->candidate->latestJobApplied->job_id : '';

            $resultArr[$key] = $result;
        }

        return $resultArr;
    }

    /**
     * Get Schedule Requirement
     *
     * @param $id
     * @return object
     */
    public function getScheduleRequirement($id)
    {
        return $this->requirementModel->find($id);
    }

    public function getScheduleOverViewByParams($userId, $type = 0, $keyDate = '')
    {
        $records = [];
        $keyDate = ($keyDate == null) ? now() : $keyDate;
        if ($type == 1) {
            $startDate = Carbon::parse($keyDate)->addDay(1);
            $endDate = Carbon::parse($startDate)->addDay(6);
        } elseif ($type == 2) {
            $endDate = Carbon::parse($keyDate)->subDay(1);
            $startDate = Carbon::parse($endDate)->subDay(6);
        } else {
            $startDate = now();
            $endDate = Carbon::parse($startDate)->addDay(6);
        }

        $requirementIdWithStartDateComp = $this->requirementModel
            ->where('start_date', '>=', $startDate)
            ->where('start_date', '<=', $endDate)
            ->orderBy('start_date', 'ASC')
            ->pluck('id')->toArray();

        $requirementIdWithEndDateComp = $this->requirementModel
            ->where('end_date', '>=', $startDate)
            ->where('end_date', '<=', $endDate)
            ->orderBy('start_date', 'ASC')
            ->pluck('id')->toArray();

        $requirementIds = array_unique(array_merge($requirementIdWithStartDateComp, $requirementIdWithEndDateComp));

        $eventLogData = EventLogEntry::whereIn('schedule_customer_requirement_id', $requirementIds)
            ->where('status', 1)
            ->where('user_id', $userId)
            ->get();

        if (!empty($eventLogData)) {
            foreach ($eventLogData as $k => $eventLog) {
                $shiftId = $eventLog->multiple_shift_id;
                $requirement = $eventLog->requirement;

                if ($shiftId != 0) {
                    $shift = ScheduleCustomerMultipleFillShifts::find($shiftId);

                    $key = strtotime(Carbon::parse($shift->shift_from)->format('Y-m-d'));
                    $records[$key][] = [
                        'date_key' => Carbon::parse($shift->shift_from)->format('d-m-Y'),
                        'date' => Carbon::parse($shift->shift_from)->format("M d, Y"),
                        'site' => $requirement->customer->project_number . ' - ' . $requirement->customer->client_name,
                        'timing' => Carbon::parse($shift->shift_from)->format('h:i A') . ' - ' . Carbon::parse($shift->shift_to)->format('h:i A'),
                        'type' => $requirement->trashed_fill_type ? $requirement->trashed_fill_type->type : '--',
                    ];
                } else {
                    $key = strtotime(Carbon::parse($requirement->start_date)->format('Y-m-d'));
                    $startTiming = "";
                    $endTiming = "";
                    if ($requirement->time_scheduled) {
                        $startTiming = Carbon::createFromFormat('Y-m-d h:i A', $requirement->start_date . ' ' . $requirement->time_scheduled)->format('h:i A');
                        $endTiming = Carbon::createFromFormat('Y-m-d h:i A', $requirement->start_date . ' ' . $requirement->time_scheduled)->addHours($requirement->length_of_shift)->format('h:i A');
                    }

                    $records[$key][] = [
                        'date_key' => Carbon::parse($requirement->start_date)->format('d-m-Y'),
                        'date' => Carbon::parse($requirement->start_date)->format("M d, Y"),
                        'site' => $requirement->customer ? $requirement->customer->project_number . ' - ' . $requirement->customer->client_name : '--',
                        'timing' => $startTiming . ' - ' . $endTiming,
                        'type' => $requirement->trashed_fill_type ? $requirement->trashed_fill_type->type : '--',
                    ];
                }
            }
        }

        $startDateStr = strtotime($startDate);
        $endDateStr = strtotime($endDate);
        while ($startDateStr <= $endDateStr) {
            $key = strtotime(date('Y-m-d', $endDateStr));
            if (!array_key_exists($key, $records)) {
                $dateObj = Carbon::parse(date('d-m-Y', $endDateStr));
                $records[$key][] = [
                    'date_key' => $dateObj->format('d-m-Y'),
                    'date' => Carbon::parse($dateObj)->format("M d, Y"),
                    'site' => '--',
                    'timing' => '--',
                    'type' => '--',
                ];
            }
            $endDateStr = strtotime('-1 day', $endDateStr);
        }

        if (!empty($records)) {
            ksort($records);
        }

        return $records;
    }
}
