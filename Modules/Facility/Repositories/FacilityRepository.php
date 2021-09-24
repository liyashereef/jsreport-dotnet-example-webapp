<?php

namespace Modules\Facility\Repositories;

use App\Services\HelperService;
use Carbon\Carbon;
use Auth;
use Modules\Facility\Models\FacilityServiceSlot;
use Modules\Facility\Models\FacilityServiceTiming;
use Modules\Facility\Models\FacilityServiceData;
use Modules\Facility\Models\FacilityServiceLockdown;
use Modules\Facility\Models\FacilityServiceUserAllocation;
use Modules\Facility\Models\FacilityUserWeekendDefinition;
use Modules\Facility\Models\FacilityUser;
use Modules\Facility\Models\FacilityBooking;

use Modules\Facility\Models\Facility;
use Modules\Facility\Models\FacilityService;
use App\Repositories\MailQueueRepository;


class FacilityRepository
{
    protected $helperService, $faciltyuserrelation;
    private $mailQueueRepository;
    public function __construct(MailQueueRepository $mailQueueRepository)
    {
        $this->helperService = new HelperService();
        $this->facility = new Facility();
        $this->mailQueueRepository = $mailQueueRepository;
    }

    public function createFacility($facility, $request)
    {

        if ($request->single_service_facility == "yes") {
            $bookinginterval["model_type"] = "Modules\Facility\Models\Facility";
            $bookinginterval["model_id"] = $facility;

            $bookinginterval["slot_interval"] = $request->booking_interval;
            $bookinginterval["start_date"] = date("Y-m-d");
            $bookinginterval["expiry_date"] = null;
            $bookinginterval["created_by"] = \Auth::user()->id;

            $bookinginterval["created_at"] = date("Y-m-d H:i");
            FacilityServiceSlot::insert($bookinginterval);
        }

        $facilitytiming["model_type"] = "Modules\Facility\Models\Facility";
        $facilitytiming["model_id"] = $facility;
        $facilitytiming["start_time"] = date("H:i:s", strtotime(date("Y-m-d") . " " . $request->start_time));
        $facilitytiming["end_time"] = date("H:i:s", strtotime(date("Y-m-d") . " " . $request->end_time));
        $facilitytiming["start_date"] = date("Y-m-d");
        $facilitytiming["expiry_date"] = null;
        $facilitytiming["created_by"] = \Auth::user()->id;
        $facilitytiming["created_at"] = date("Y-m-d H:i");
        FacilityServiceTiming::insert($facilitytiming);

        if ($request->weekend_booking == 1) {
            $facilitywtiming["model_type"] = "Modules\Facility\Models\Facility";
            $facilitywtiming["model_id"] = $facility;
            $facilitywtiming["start_time"] = date("H:i:s", strtotime(date("Y-m-d") . " " . $request->weekend_start_time));
            $facilitywtiming["end_time"] = date("H:i:s", strtotime(date("Y-m-d") . " " . $request->weekend_end_time));
            $facilitywtiming["start_date"] = date("Y-m-d");
            $facilitywtiming["expiry_date"] = null;
            $facilitywtiming["weekend_timing"] = 1;
            $facilitywtiming["created_by"] = \Auth::user()->id;
            $facilitywtiming["created_at"] = date("Y-m-d H:i");
            FacilityServiceTiming::insert($facilitywtiming);
        }


        $facilitydata["model_type"] = "Modules\Facility\Models\Facility";
        $facilitydata["model_id"] = $facility;
        $facilitydata["weekend_booking"] = $request->weekend_booking;
        $facilitydata["maxbooking_perday"] = $request->maxbooking_perday;
        if ($request->single_service_facility == "no") {
            $facilitydata["tolerance_perslot"] = 0;
        } else {
            $facilitydata["tolerance_perslot"] = $request->tolerance_perslot;
        }

        $facilitydata["booking_window"] = $request->booking_window;
        $facilitydata["start_date"] = date("Y-m-d");
        $facilitydata["expiry_date"] = null;
        $facilitydata["created_by"] = \Auth::user()->id;
        $facilitydata["created_at"] = date("Y-m-d H:i");
        FacilityServiceData::insert($facilitydata);
    }

    public function getById($id)
    {
        return $this->facility
            ->with([
                'facilitydata' => function ($query) use ($id) {
                    return $query->select(
                        'id',
                        'model_type',
                        'model_id',
                        'weekend_booking',
                        'maxbooking_perday',
                        'tolerance_perslot',
                        'booking_window'
                    )
                        ->whereNull('expiry_date')
                        ->orderBy('id', 'DESC')
                        ->where('model_id', $id)
                        ->where('model_type', 'Modules\Facility\Models\Facility');
                }
            ])->find($id);
    }
    public function saveLockdown($request, $model_type)
    {
        $service_id = $request->service_id;
        $st_date = $request->st_date;
        $en_date = $request->en_date;
        $en_date = $request->en_date;
        $editlockdownid = $request->edit_lockdownfacility_id;
        $bookedcount = 0;
        $facilityservice = [];
        $facilityservice = FacilityService::where("facility_id", $service_id)->get()->pluck("id")->toArray();

        if ($st_date != "") {

            $bookedcount = FacilityBooking::whereRaw("(DATE(booking_date_start)>=? and DATE(booking_date_end)<=?)", [$st_date, $en_date])
                ->when(count($facilityservice) > 0, function ($q) use ($facilityservice) {
                    return $q->whereIn("model_id", $facilityservice)->where(["model_type" => "Modules\Facility\Models\FacilityService"]);
                })
                ->when(count($facilityservice) < 1, function ($q) use ($facilityservice, $service_id) {
                    return $q->where("model_id", $service_id)->where(["model_type" => "Modules\Facility\Models\Facility"]);
                })
                ->count();
        }
        if ($request->st_time == "") {
            $st_time = null;
        } else {
            $st_time = date("H:i:s", strtotime(date("Y-m-d") . " " . $request->st_time));
        }
        if ($request->en_time == "") {
            $en_time = null;
        } else {
            $en_time = date("H:i:s", strtotime(date("Y-m-d") . " " . $request->en_time));
        }

        if (($request->st_time == "" && $request->en_time != "") || ($request->st_time != "" && $request->en_time == "")) {
            $content["code"] = 406;
            $content["message"] = "Please fill in Start and End Time";
            $content["success"] = "warning";
        } else if (($st_date == "" && $en_date != "") || ($st_date != "" && $en_date == "")) {
            $content["code"] = 406;
            $content["message"] = "Please fill in Start and End Date";
            $content["success"] = "warning";
        } else if ($st_date == "" && $en_date == "" && $request->st_time == "" && $request->en_time == "") {
            $content["code"] = 406;
            $content["message"] = "Please fill in any of combinations";
            $content["success"] = "warning";
        } else if ($bookedcount > 0) {
            $content["code"] = 406;
            $content["message"] = "Please cancel the bookings";
            $content["success"] = "warning";
        } else {

            if ($st_date != "") {
                $timingexist = FacilityServiceLockdown::where([
                    "model_type" => "Modules\Facility\Models\Facility",
                    "model_id" => $service_id
                ])
                    ->when($request->edit_lockdownfacility_id > 0, function ($q) use ($request) {
                        return $q->where('id', '!=', $request->edit_lockdownfacility_id);
                    })
                    ->when($st_date != "", function ($qry) use ($st_date, $st_time, $en_date, $en_time) {
                        //return $qry->whereDate('start_date','>=',$st_date)->whereDate('end_date','>=',$en_date);
                        return $qry->whereRaw(
                            "((start_date>=? and start_date<=?) or (end_date>=? and end_date<=?))  and isnull(deleted_at)",
                            [$st_date, $en_date, $st_date, $en_date]
                        );
                    })
                    ->when($request->st_time != "", function ($q) use ($st_date, $st_time, $en_date, $en_time) {
                        return $q->whereRaw(
                            "(((? between start_time and end_time) or (? between start_time and end_time) or
                    (?=start_time) or (?=end_time) or (?=start_time) or (?=end_time)) and isnull(deleted_at))",
                            [$st_time, $en_time, $st_time, $st_time, $en_time, $en_time]
                        );
                    })
                    ->whereNotNull('start_date')->count();
            } else {
                $timingexist = FacilityServiceLockdown::where([
                    "model_type" => "Modules\Facility\Models\Facility",
                    "model_id" => $service_id
                ])
                    ->when($request->edit_lockdownfacility_id > 0, function ($q) use ($request) {
                        return $q->where('id', '!=', $request->edit_lockdownfacility_id);
                    })
                    ->when($request->st_time != "", function ($q) use ($st_date, $st_time, $en_date, $en_time) {
                        return $q->whereRaw(
                            "(((? between start_time and end_time) or (? between start_time and end_time) or
                    (?=start_time) or (?=end_time) or (?=start_time) or (?=end_time) )) and isnull(deleted_at)",
                            [$st_time, $en_time, $st_time, $st_time, $en_time, $en_time]
                        );
                    })->whereNull('start_date')->count();
            }



            if ($timingexist > 0) {
                $content["code"] = 406;
                $content["message"] = "Time overlapped";
                $content["success"] = "warning";
            } else {
                $create = FacilityServiceLockdown::create([
                    "model_type" => "Modules\Facility\Models\Facility", "model_id" => $service_id,
                    "start_date" => $st_date, "end_date" => $en_date, "start_time" => $st_time, "end_time" => $en_time
                ]);
                if ($create->id > 0) {
                    $content["code"] = 200;

                    $content["success"] = "success";
                    if ($editlockdownid > 0) {
                        FacilityServiceLockdown::find($editlockdownid)->delete();
                        $content["message"] = "Lockdown period updated successfully";
                    } else {
                        $content["message"] = "Lockdown period added successfully";
                    }
                } else {
                    $content["code"] = 406;
                    $content["message"] = "Data error";
                    $content["success"] = "warning";
                }
            }
        }


        return json_encode($content, true);
    }

    public function saveNewtiming($request, $model_type)
    {
        $service_id = $request->service_id;
        $st_time = date("H:i:s", strtotime(date("Y-m-d") . " " . $request->st_time));
        $en_time = date("H:i:s", strtotime(date("Y-m-d") . " " . $request->en_time));
        $booking_window = $request->booking_window;
        $timing_id = $request->edit_facility_id;
        if ($request->weekend_timing == "true") {
            $weekend_timing = 1;
        } else {
            $weekend_timing = 0;
        }
        $bookingcount = 0;

        if ($request->edit_timing_id > 0) {
            $edit = FacilityServiceTiming::find($request->edit_timing_id);
            $edit_start_time = $edit->start_time;
            $edit_end_time = $edit->end_time;
            $facilitydetails = FacilityService::where("facility_id", $service_id)->pluck('id')->toArray();
            $facilitybooking = FacilityBooking::where("model_id", $service_id)
                ->whereRaw("DATE(booking_date_start)>='" . date("Y-m-d") . "'  and
                        ((TIME(booking_date_start) between TIME('" . $edit_start_time . "')
                        and TIME('" . $edit_end_time . "')) or
                        (TIME(booking_date_end) between TIME('" . $edit_start_time . "')
                        and TIME('" . $edit_end_time . "')) or (TIME(booking_date_start)='" . $edit_start_time . "')
                        or (TIME(booking_date_end)='" . $edit_start_time . "')
                        or (TIME(booking_date_start)='" . $edit_end_time . "')
                        or (TIME(booking_date_end)='" . $edit_end_time . "') )")
                ->where("model_type", "Modules\Facility\Models\Facility")
                ->count();
            $bookingcount = $bookingcount + $facilitybooking;
            if ($facilitydetails) {
                $stringarray = implode(',', $facilitydetails);
                $servicebooking = FacilityBooking::
                    // whereDate('booking_date_start','>=',date("Y-m-d"))
                    whereIn("model_id", $facilitydetails)
                    ->whereRaw("DATE(booking_date_start)>='" . date("Y-m-d") . "'  and
                        ((TIME(booking_date_start) between TIME('" . $edit_start_time . "')
                        and TIME('" . $edit_end_time . "')) or
                        (TIME(booking_date_end) between TIME('" . $edit_start_time . "')
                        and TIME('" . $edit_end_time . "')) or (TIME(booking_date_start)='" . $edit_start_time . "')
                        or (TIME(booking_date_end)='" . $edit_start_time . "')
                        or (TIME(booking_date_start)='" . $edit_end_time . "')
                        or (TIME(booking_date_end)='" . $edit_end_time . "') )")
                    ->where("model_type", "Modules\Facility\Models\FacilityService")
                    ->count();
                $bookingcount = $bookingcount + $servicebooking;
            }
        }







        $timeid = $request->edit_timing_id;
        $timingexist = FacilityServiceTiming::whereRaw(
            "(((? between start_time and end_time)
        or (? between  start_time and end_time)
        or (start_time=?)
        or (end_time=?)
        or (start_time between ? and ?)
        or (end_time between ? and ?)) and  isnull(expiry_date) and  isnull(deleted_at) )",
            [$st_time, $en_time, $st_time, $en_time, $st_time, $en_time, $st_time, $en_time]
        )->when($request->edit_timing_id > 0, function ($q) use ($timeid) {
            return $q->where('id', '!=', $timeid);
        })
            ->where([
                "model_type" => "Modules\Facility\Models\Facility",
                "model_id" => $service_id, "weekend_timing" => $weekend_timing
            ])
            ->count();
        if ($timingexist < 1) {
            if (($timingexist < 1 || $timing_id > 0) && $bookingcount < 1) {
                try {
                    if ($timing_id > 0) {

                        $afterbookingwindowdata = date("Y-m-d", strtotime("-1 day", strtotime(date("Y-m-d"))));
                        $afterbookingwindowstartdate = date("Y-m-d");
                        $facservicetiming = FacilityServiceTiming::find($timing_id);

                        $start_date = $facservicetiming->start_date;
                        if ($facservicetiming && $start_date != date("Y-m-d")) {
                            $facservicetiming->expiry_date = $afterbookingwindowdata;
                            $facservicetiming->save();
                        } else if ($facservicetiming && $start_date == date("Y-m-d")) {
                            $facservicetiming->delete();
                            $afterbookingwindowstartdate = date("Y-m-d");
                        } else {
                            $afterbookingwindowstartdate = date("Y-m-d");
                        }
                    } else {
                        $afterbookingwindowstartdate = date("Y-m-d");
                    }

                    $newservicetime = FacilityServiceTiming::create([
                        "model_type" => $model_type,
                        "model_id" => $service_id,
                        "start_time" => date("H:i:s", strtotime(date("Y-m-d") . " " . $st_time)),
                        "end_time" => date("H:i:s", strtotime(date("Y-m-d") . " " . $en_time)),
                        "weekend_timing" => $weekend_timing,
                        "start_date" => $afterbookingwindowstartdate,
                        "expiry_date" => null,
                        "created_by" => \Auth::user()->id,
                    ]);
                } catch (\Throwable $th) {
                    throw $th;
                    $newservicetime = null;
                }
            } else {
                $newservicetime = null;
            }
        } else {
            $newservicetime = null;
        }


        if ($bookingcount > 0) {
            $content["code"] = 406;
            $content["message"] = "Please remove the future booking prior editing";
            $content["success"] = "warning";
        } else if (($timingexist < 1 || $timing_id > 0) && $newservicetime != null) {
            if ($newservicetime->id > 0) {
                $content["code"] = 200;
                if ($timing_id > 0) {
                    $content["message"] = "Timing updated successfully";
                } else {
                    $content["message"] = "Timing added successfully";
                }

                $content["success"] = "success";
            } else {
                $content["code"] = 406;
                $content["message"] = "Data error";
                $content["success"] = "warning";
            }
        } else {
            $content["code"] = 406;
            $content["message"] = "Time is overlapped";
            $content["success"] = "warning";
        }

        return json_encode($content, true);
    }

    public function addFacilityusers($request)
    {
        $userarray = [];
        $userarray["first_name"] = $request->first_name;
        $userarray["last_name"] = $request->last_name;
        $userarray["username"] = $request->username;
        $userarray["email"] = $request->email;
        $userarray["alternate_email"] = $request->alternate_email;
        $userarray["phoneno"] = $request->phoneno;
        $userarray["password"] = \Hash::make($request->password);
        $userarray["customer_id"] = $request->customer;
        $userarray["unit_no"] = $request->unit_no;
        $userarray["internaluser"] = true;
        $userarray["active"] = true;

        $userid = FacilityUser::create($userarray);
        $user = $userid->id;

        if ($user > 0) {
            $this->associateUserWithExistingFacilities($user, $request->customer);
            $this->sendMail($request);
        }
        return $userid->id;
    }

    public function editFacilityusers($request)
    {
        $userarray = [];
        $userarray["first_name"] = $request->first_name;
        $userarray["last_name"] = $request->last_name;
        $userarray["username"] = $request->username;
        $userarray["email"] = $request->email;
        $userarray["alternate_email"] = $request->alternate_email;
        $userarray["phoneno"] = $request->phoneno;
        $userarray["password"] = \Hash::make($request->password);
        $userarray["customer_id"] = $request->customer;
        $userarray["unit_no"] = $request->unit_no;
        $userarray["internaluser"] = true;
        $userarray["active"] = true;
        if ($request->user_id > 0) {
            $user = FacilityUser::find($request->user_id);
            $user->first_name = $request->first_name;
            $user->last_name = $request->last_name;
            $user->email = $request->email;
            $user->alternate_email = $request->alternate_email;
            $user->phoneno = $request->phoneno;
            $user->unit_no = $request->unit_no;
            $user->customer_id = $request->customer;
            if ($request->active == "no") {
                $user->active = 0;
            } else {
                $user->active = 1;
            }
            $user->save();
            return $request->user_id;
        }
    }

    public function sendMail($request)
    {
        $to = $request->email;
        $model_name = 'Modules\facility\Models\FacilityUser';
        $subject = 'User Registration - CGL360 Booking System';
        $message =  "<p> Hi " . $request->first_name . ' ' . $request->last_name . ' ,</p>';
        $message .=  "<p> Please find your login credentials below. </p>";
        $message .=  "<p> Username - " . $request->username . " <br/>";
        $message .=  "Password - " . $request->password . " </p>";
        $message .=  '<p> <a style="color:#000;" href="' . url('/facility/booking') . '">Please follow the link to login </a> </p>';

        return $this->mailQueueRepository->storeMail($to, $subject, $message, $model_name, null, null, null, null, null, null);
    }

    public function associateUserWithnewFacilities($user, $customer, $facid)
    {

        $facility = Facility::find($facid);
        $array = [];
        $days = [1, 2, 3, 4, 5, 6, 7];

        $id = $facility->id;
        $single_service_facility = $facility->single_service_facility;
        $facilityservices = $facility->facilityservices;
        $array = [
            "facility_user_id" => $user,
            "model_type" => "Modules\Facility\Models\Facility",
            "model_id" => $id,
            "created_by" => \Auth::user()->id,
        ];
        $allocation = FacilityServiceUserAllocation::create($array);
        if ($single_service_facility == "1") {

            $allocationid = $allocation->id;
            foreach ($days as $day) {
                FacilityUserWeekendDefinition::insert([
                    "facility_service_user_allocation_id" => $allocationid,
                    "day_id" => $day,
                    "created_by" => \Auth::user()->id
                ]);
            }
            $array = [];
        } else {
            $i = 0;

            foreach ($facilityservices as $service) {
                $i++;
                $array = [
                    "facility_user_id" => $user,
                    "model_type" => "Modules\Facility\Models\FacilityService",
                    "model_id" => $service->id,
                    "created_by" => \Auth::user()->id,
                ];
                $allocation = FacilityServiceUserAllocation::create($array);
                $allocationid = $allocation->id;

                foreach ($days as $day) {
                    FacilityUserWeekendDefinition::insert([
                        "facility_service_user_allocation_id" => $allocationid,
                        "day_id" => $day,
                        "created_by" => \Auth::user()->id
                    ]);
                }
            }
        }
    }

    public function associateUserWithnewFacilityService($user, $serviceid, $facid)
    {
        $days = [1, 2, 3, 4, 5, 6, 7];
        $array = [
            "facility_user_id" => $user,
            "model_type" => "Modules\Facility\Models\FacilityService",
            "model_id" => $serviceid,
            "created_by" => \Auth::user()->id,
        ];
        $allocation = FacilityServiceUserAllocation::create($array);
        $allocationid = $allocation->id;

        foreach ($days as $day) {
            FacilityUserWeekendDefinition::insert([
                "facility_service_user_allocation_id" => $allocationid,
                "day_id" => $day,
                "created_by" => \Auth::user()->id
            ]);
        }
    }

    public function associateUserWithExistingFacilities($user, $customer)
    {
        $facilities = Facility::where('customer_id', $customer)->get();
        $array = [];
        $days = [1, 2, 3, 4, 5, 6, 7];
        foreach ($facilities as $facility) {
            $id = $facility->id;
            $single_service_facility = $facility->single_service_facility;
            $facilityservices = $facility->facilityservices;
            $array = [
                "facility_user_id" => $user,
                "model_type" => "Modules\Facility\Models\Facility",
                "model_id" => $id,
                "created_by" => \Auth::user()->id,
            ];
            $allocation = FacilityServiceUserAllocation::create($array);
            if ($single_service_facility == "1") {

                $allocationid = $allocation->id;
                foreach ($days as $day) {
                    FacilityUserWeekendDefinition::insert([
                        "facility_service_user_allocation_id" => $allocationid,
                        "day_id" => $day,
                        "created_by" => \Auth::user()->id
                    ]);
                }
                $array = [];
            } else {
                $i = 0;

                foreach ($facilityservices as $service) {
                    $i++;
                    $array = [
                        "facility_user_id" => $user,
                        "model_type" => "Modules\Facility\Models\FacilityService",
                        "model_id" => $service->id,
                        "created_by" => \Auth::user()->id,
                    ];
                    $allocation = FacilityServiceUserAllocation::create($array);
                    $allocationid = $allocation->id;

                    foreach ($days as $day) {
                        FacilityUserWeekendDefinition::insert([
                            "facility_service_user_allocation_id" => $allocationid,
                            "day_id" => $day,
                            "created_by" => \Auth::user()->id
                        ]);
                    }
                }
            }
        }
    }


    public function updateFacility($facility, $request)
    {
        $facservice = Facility::with(['facilityslot' => function ($qry) {
            return $qry
                ->where([["start_date", '<=', date("Y-m-d")]])
                ->orderBy('start_date', 'desc')
                ->first();
        }, 'FacilityServiceDataMany' =>
        function ($q) {
            return $q->where([["start_date", '<=', date("Y-m-d")]])->orderBy('start_date', 'desc')->first();
        }])->find($facility);


        $single_service_facility = $facservice->single_service_facility;
        $facility_data = $facservice->FacilityServiceDataMany;
        $servicedata = $facservice->FacilityServiceDataMany;
        $booking_window = $facility_data->booking_window;
        $weekend_booking = $facility_data->weekend_booking;


        $id = $servicedata->id;
        $weekend_booking = $servicedata->weekend_booking;
        $maxbooking_perday = $servicedata->maxbooking_perday;
        $tolerance_perslot = $servicedata->tolerance_perslot;
        if ($facservice->single_service_facility == 1) {
            if (count($facservice->facilityslot) > 0) {
                $booking_interval = $facservice->facilityslot[0]->slot_interval;
                $slot_id = $facservice->facilityslot[0]->id;
            } else {
                $booking_interval = 0;
                $slot_id = 0;
            }
        } else {
            $booking_interval = 0;
            $slot_id = 0;
        }
        $reqweekend_booking = $request->weekend_booking;
        if ($reqweekend_booking == "yes") {
            $reqweekend_booking = 1;
        } else {
            $reqweekend_booking = 0;
        }

        if ($request->single_service_facility == "yes") {
            $servicetabledata = [];
            if (
                $weekend_booking != $reqweekend_booking || $maxbooking_perday != $request->maxbooking_perday ||
                $tolerance_perslot != $request->tolerance_perslot || $booking_window != $request->booking_window
            ) {
                $weekend_booking = $request->weekend_booking;
                FacilityServiceData::where([
                    "model_id" => $facility,
                    "model_type" => "Modules\Facility\Models\Facility", ["start_date", '>', date("Y-m-d")]
                ])
                    ->delete();

                $servicetabledata = FacilityServiceData::find($id);
                $expstartdate = $servicetabledata->start_date;
                if ($expstartdate == date("Y-m-d")) {
                    $servicetabledata->delete();
                } else {
                    $servicetabledata->expiry_date = date("Y-m-d", strtotime("-1 day", strtotime(date("Y-m-d"))));
                    $servicetabledata->updated_by = \Auth::user()->id;
                    $expireprevious = $servicetabledata->save();
                }



                $servicetabledatamany["model_id"] =  $facility;
                $servicetabledatamany["model_type"] =  "Modules\Facility\Models\Facility";
                $servicetabledatamany["weekend_booking"] =  $reqweekend_booking;
                $servicetabledatamany["maxbooking_perday"] =  $request->maxbooking_perday;
                $servicetabledatamany["tolerance_perslot"] =  $request->tolerance_perslot;
                $servicetabledatamany["booking_window"] =  $request->booking_window;
                $servicetabledatamany["start_date"] =  date("Y-m-d");
                $servicetabledatamany["created_by"] =  \Auth::user()->id;
                $servicetabledatamany["created_at"] =  date("Y-m-d H:i");
                $servicetabledata = FacilityServiceData::create($servicetabledatamany);
            }
        } else {
            $servicetabledata = [];
            if (
                $weekend_booking != $reqweekend_booking || $maxbooking_perday != $request->maxbooking_perday ||
                $tolerance_perslot != $request->tolerance_perslot || $booking_window != $request->booking_window
            ) {

                $servicetabledata = FacilityServiceData::find($id);
                $expstartdate = $servicetabledata->start_date;
                if ($expstartdate == date("Y-m-d")) {
                    $servicetabledata->delete();
                } else {
                    $servicetabledata->expiry_date = date("Y-m-d", strtotime("-1 day", strtotime(date("Y-m-d"))));
                    $servicetabledata->updated_by = \Auth::user()->id;
                    $expireprevious = $servicetabledata->save();
                }


                FacilityServiceData::where([
                    "model_id" => $facility,
                    "model_type" => "Modules\Facility\Models\Facility", ["start_date", '>', date("Y-m-d")]
                ])
                    ->delete();
                $servicetabledatamany["model_id"] =  $facility;
                $servicetabledatamany["model_type"] =  "Modules\Facility\Models\Facility";
                $servicetabledatamany["weekend_booking"] =  $reqweekend_booking;
                $servicetabledatamany["maxbooking_perday"] =  $request->maxbooking_perday;
                $servicetabledatamany["tolerance_perslot"] =  0;
                $servicetabledatamany["booking_window"] =  $request->booking_window;
                $servicetabledatamany["start_date"] =  date("Y-m-d");
                $servicetabledatamany["created_by"] =  \Auth::user()->id;
                $servicetabledatamany["created_at"] =  date("Y-m-d H:i");
                $servicetabledata = FacilityServiceData::create($servicetabledatamany);
            }
        }





        $afterbookingwindowdata = date("Y-m-d", strtotime("+" . $booking_window . " day", strtotime(date("Y-m-d"))));


        if ($request->single_service_facility == "yes") {

            if ($booking_interval != $request->slot_interval) {
                if ($booking_interval > 0) {
                    FacilityServiceSlot::where([
                        "model_type" => 'Modules\Facility\Models\Facility',
                        "model_id" => $facservice->id, ["start_date", '>', date("Y-m-d")]
                    ])->delete();

                    $facilityserviceslot = FacilityServiceSlot::find($slot_id);
                    $expstartdate = $facilityserviceslot->start_date;
                    if ($expstartdate == date("Y-m-d")) {
                        $facilityserviceslot->delete();
                    } else {
                        $facilityserviceslot->expiry_date = date("Y-m-d", strtotime("-1 day", strtotime(date("Y-m-d"))));
                        $facilityserviceslot->save();
                    }
                }


                $bookinginterval["model_type"] = "Modules\Facility\Models\Facility";
                $bookinginterval["model_id"] = $facility;
                $bookinginterval["slot_interval"] = $request->slot_interval;
                $bookinginterval["start_date"] = date("Y-m-d");
                $bookinginterval["expiry_date"] = null;
                $bookinginterval["created_by"] = \Auth::user()->id;
                $bookinginterval["created_at"] = date("Y-m-d H:i");
                FacilityServiceSlot::create($bookinginterval);
            }
        }
    }








    public function updateFacilityservice($facility, $request)
    {
        $facservice = FacilityService::with(['facilityslot' => function ($qry) {
            return $qry
                ->where([["start_date", '<=', date("Y-m-d")]])
                ->orderBy('start_date', 'desc')
                ->first();
        }, 'FacilityServiceDataMany' =>
        function ($q) {
            return $q->where([["start_date", '<=', date("Y-m-d")]])->orderBy('start_date', 'desc')->first();
        }])->find($facility);

        $facility_data = $facservice->getFacility->FacilityServiceDataMany;
        $servicedata = $facservice->FacilityServiceDataMany;
        $booking_window = $facility_data->booking_window;

        $slot_id = ($facservice->facilityslot)[0]->id;

        $id = $servicedata[0]["id"];
        $booking_interval = ($facservice->facilityslot)[0]->slot_interval;

        $tolerance_perslot = $servicedata[0]["tolerance_perslot"];


        $servicetabledata = [];
        if ($tolerance_perslot != $request->tolerance_perslot) {

            FacilityServiceData::where([
                "model_id" => $facility,
                "model_type" => "Modules\Facility\Models\FacilityService", ["start_date", '>', date("Y-m-d")]
            ])
                ->delete();

            $servicetabledata = FacilityServiceData::find($id);
            $expstartdate = $servicetabledata->start_date;
            if ($expstartdate == date("Y-m-d")) {
                $servicetabledata->delete();
            } else {
                $servicetabledata->expiry_date = date("Y-m-d", strtotime("-1 day", strtotime(date("Y-m-d"))));
                $servicetabledata->updated_by = \Auth::user()->id;
                $expireprevious = $servicetabledata->save();
            }


            $servicetabledatamany["model_id"] =  $facility;
            $servicetabledatamany["model_type"] =  "Modules\Facility\Models\FacilityService";
            $servicetabledatamany["weekend_booking"] =  0;
            $servicetabledatamany["maxbooking_perday"] =  0;
            $servicetabledatamany["tolerance_perslot"] =  $request->tolerance_perslot;
            $servicetabledatamany["booking_window"] =  $request->booking_window;
            $servicetabledatamany["start_date"] =  date("Y-m-d");
            $servicetabledatamany["created_by"] =  \Auth::user()->id;
            $servicetabledatamany["created_at"] =  date("Y-m-d H:i");
            $servicetabledata = FacilityServiceData::create($servicetabledatamany);
        }





        $afterbookingwindowdata = date("Y-m-d", strtotime("+" . $booking_window . " day", strtotime(date("Y-m-d"))));

        if ($booking_interval != $request->booking_interval) {
            FacilityServiceSlot::where([
                "model_type" => 'Modules\Facility\Models\FacilityService',
                "model_id" => $facservice->id, ["start_date", '>', date("Y-m-d")]
            ])->delete();

            $facilityserviceslot = FacilityServiceSlot::find($slot_id);
            $expstartdate = $facilityserviceslot->start_date;
            if ($expstartdate == date("Y-m-d")) {
                $facilityserviceslot->delete();
            } else {
                $facilityserviceslot->expiry_date = date("Y-m-d", strtotime("-1 day", strtotime(date("Y-m-d"))));
                $facilityserviceslot->save();
            }

            $bookinginterval["model_type"] = "Modules\Facility\Models\FacilityService";
            $bookinginterval["model_id"] = $facility;
            $bookinginterval["slot_interval"] = $request->booking_interval;
            $bookinginterval["start_date"] = date("Y-m-d");
            $bookinginterval["expiry_date"] = null;
            $bookinginterval["created_by"] = \Auth::user()->id;
            $bookinginterval["created_at"] = date("Y-m-d H:i");
            FacilityServiceSlot::create($bookinginterval);
        }
    }

    public function createFacilityservice($facility, $request)
    {

        $bookinginterval["model_type"] = "Modules\Facility\Models\FacilityService";
        $bookinginterval["model_id"] = $facility;
        $bookinginterval["slot_interval"] = $request->booking_interval;
        $bookinginterval["start_date"] = date("Y-m-d");
        $bookinginterval["expiry_date"] = null;
        $bookinginterval["created_by"] = \Auth::user()->id;

        $bookinginterval["created_at"] = date("Y-m-d H:i");
        FacilityServiceSlot::insert($bookinginterval);


        $facilitydata["model_type"] = "Modules\Facility\Models\FacilityService";
        $facilitydata["model_id"] = $facility;
        $facilitydata["weekend_booking"] = 1;
        $facilitydata["maxbooking_perday"] = 0;
        $facilitydata["tolerance_perslot"] = $request->tolerance_perslot;
        $facilitydata["start_date"] = date("Y-m-d");
        $facilitydata["expiry_date"] = null;
        $facilitydata["booking_window"] = 0;
        $facilitydata["created_by"] = \Auth::user()->id;
        $facilitydata["created_at"] = date("Y-m-d H:i");
        FacilityServiceData::insert($facilitydata);
        $facilitydetail = Facility::find($request->facilityid);
        $customer_users = FacilityUser::where("customer_id", $facilitydetail->customer_id)->get()->pluck("id")->toArray();
        foreach ($customer_users as $key => $value) {
            $this->associateUserWithnewFacilityService($value, $facility, $request->facilityid);
        }
    }

    public function saveorremoveallocation($model_id, $user_id, $model_type, $type)
    {
        $facilityalloc["facility_user_id"] = $user_id;
        $facilityalloc["model_id"] = $model_id;
        $facilityalloc["model_type"] = $model_type;
        $facilityalloc["created_by"] = \Auth::user()->id;
        $facilityalloc["created_at"] = date("Y-m-d H:i");
        $days = [1, 2, 3, 4, 5, 6, 7];
        if ($type == "addfacility" || $type == "addservice") {
            $facserviceuseralloc =  FacilityServiceUserAllocation::create($facilityalloc);
            if ($facserviceuseralloc->id > 0) {
                foreach ($days as $day) {
                    FacilityUserWeekendDefinition::insert([
                        "facility_service_user_allocation_id" => $facserviceuseralloc->id,
                        "day_id" => $day,
                        "created_by" => \Auth::user()->id
                    ]);
                }
            }
            return $facserviceuseralloc;
        } else {
            $facilityremove = FacilityServiceUserAllocation::where(["facility_user_id" => $user_id, "model_id" => $model_id, "model_type" => $model_type])
                ->first();
            $allocid = $facilityremove->id;
            FacilityUserWeekendDefinition::where("facility_service_user_allocation_id", $allocid)->delete();
            return $remove = FacilityServiceUserAllocation::where(["facility_user_id" => $user_id, "model_id" => $model_id, "model_type" => $model_type])->delete();
        }
    }

    public function saveorremovemassallocation($model_id, $user_id, $model_type, $type)
    {
        $facilityalloc["facility_user_id"] = $user_id;
        $facilityalloc["model_id"] = $model_id;
        $facilityalloc["model_type"] = $model_type;
        $facilityalloc["created_by"] = \Auth::user()->id;
        $facilityalloc["created_at"] = date("Y-m-d H:i");

        $days = [1, 2, 3, 4, 5, 6, 7];
        if ($type == "addfacility" || $type == "addservice") {
            \DB::table('facility_service_user_allocations')->insert($facilityalloc);
            $facserviceuseralloc = \DB::getPDO()->lastInsertId();
            //$facserviceuseralloc = FacilityServiceUserAllocation::create($facilityalloc);
            if ($facserviceuseralloc > 0) {
                foreach ($days as $day) {
                    $weekenddef = new FacilityUserWeekendDefinition;

                    $weekenddef->facility_service_user_allocation_id = $facserviceuseralloc;
                    $weekenddef->day_id = $day;
                    $weekenddef->created_by = Auth::user()->id;
                    $weekenddef->save();
                }
            }

            return $facserviceuseralloc;
        }
    }
}
