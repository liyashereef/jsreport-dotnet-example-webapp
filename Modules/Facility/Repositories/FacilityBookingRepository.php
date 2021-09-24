<?php

namespace Modules\Facility\Repositories;

use App\Services\HelperService;
use Carbon\Carbon;
use Modules\Facility\Models\Facility;
use Modules\Facility\Models\FacilityService;
use Modules\Facility\Models\FacilityServiceSlot;
use Modules\Facility\Models\FacilityServiceTiming;
use Modules\Facility\Models\FacilityServiceData;
use Modules\Facility\Models\FacilityServiceUserAllocation;
use Modules\Facility\Models\FacilityUser;
use Modules\Facility\Models\FacilityBooking;

use Modules\Facility\Repositories\FacilityServiceLockdownRepository;
use Modules\Facility\Repositories\FacilityServiceDataRepository;

class FacilityBookingRepository
{
    protected $helperService;
    protected $facility;
    protected $facilityService;
    protected $facilityServiceSlot;
    protected $facilityServiceUserAllocation;
    protected $facilityServiceTiming;
    protected $facilityBooking;
    protected $facilityServiceDataRepository;
    protected $facilityServiceLockdownRepository;

    public function __construct(
        FacilityServiceLockdownRepository $facilityServiceLockdownRepository,
        FacilityServiceDataRepository $facilityServiceDataRepository
    ) {
        $this->helperService = new HelperService();
        $this->facility = new Facility();
        $this->facilityService = new FacilityService();
        $this->facilityServiceSlot = new FacilityServiceSlot();
        $this->facilityServiceUserAllocation = new FacilityServiceUserAllocation();
        $this->facilityServiceTiming = new FacilityServiceTiming();
        $this->facilityBooking = new FacilityBooking();
        $this->facilityServiceDataRepository = $facilityServiceDataRepository;
        $this->facilityServiceLockdownRepository = $facilityServiceLockdownRepository;
    }

    public function getAllocatedFacility($inputs)
    {
        return $this->facility
            ->whereHas('facilityserviceuserallocation', function ($que) {
                return $que->where('facility_user_id', \Auth::guard('facilityuser')->user()->id);
            })
            ->whereHas('customer', function ($que) {
                return $que->where('facility_booking', 1);
            })
            ->with([
                'facilitydata' => function ($query) {
                    return $query->where('model_type', 'Modules\Facility\Models\Facility')
                        ->whereNull('expiry_date')
                        ->orderBy('id', 'DESC');
                },
                'FacilityPolicy' => function ($query) {
                    return $query->select(
                        'id',
                        'facility_id',
                        'policy',
                        'order'
                    );
                },
                'facilityservices',
                'facilitytiming' => function ($query) {
                    return $query->select(
                        'id',
                        'model_type',
                        'model_id'
                    );
                },
            ])
            ->when(!empty($inputs) && isset($inputs['active']), function ($que) use ($inputs) {
                return $que->where('active', $inputs['active']);
            })
            ->select('id', 'facility', 'description', 'single_service_facility')
            ->orderBy('facility')
            ->get();
    }
    // public function getFacilityServiceData($inputs){
    //     return  $this->facilityServiceData
    //     ->when(!empty($inputs) && isset($inputs['model_id']), function ($que) use($inputs){
    //         return $que->where('model_id',$inputs['model_id']);
    //     }) ->when(!empty($inputs) && isset($inputs['model_type']), function ($que) use($inputs){
    //         return $que->where('model_type',$inputs['model_type']);
    //     })
    //     ->orderBy('id','DESC')
    //     ->first();
    // }

    public function getAllocatedServices($inputs)
    {
        return $this->facilityService
            ->when(!empty($inputs) && isset($inputs['facility_id']), function ($que) use ($inputs) {
                return $que->where('facility_id', $inputs['facility_id']);
            })
            ->whereHas('facilityserviceuserallocation', function ($que) use ($inputs) {
                return $que->where('facility_user_id', \Auth::guard('facilityuser')->user()->id);
            })
            ->when(!empty($inputs) && isset($inputs['active']), function ($que) use ($inputs) {
                return $que->where('active', $inputs['active']);
            })
            ->select('id', 'facility_id', 'service', 'description')
            ->orderBy('service')
            ->get();
    }
    //Day allocation checking.
    public function getAllocationDetails($inputs)
    {
        return $this->facilityServiceUserAllocation
            ->when(!empty($inputs) && isset($inputs['facility_user_id']), function ($que) use ($inputs) {
                return $que->where('facility_user_id', $inputs['facility_user_id']);
            })
            ->when(!empty($inputs) && isset($inputs['model_type']), function ($que) use ($inputs) {
                return $que->where('model_type', $inputs['model_type']);
            })
            ->when(!empty($inputs) && isset($inputs['model_id']), function ($que) use ($inputs) {
                return $que->where('model_id', $inputs['model_id']);
            })
            ->with(['facilityuserweekenddefinition' => function ($query) {
                return $query->select(
                    'id',
                    'facility_service_user_allocation_id',
                    'day_id'
                );
            }])
            ->select('id', 'facility_user_id', 'model_type', 'model_id')
            ->first();
    }


    /**Fetching active start and end time timings
     * param  start_date,end_date,model_id,model_type
     * @return Response
     */
    public function getTimeSlot($inputs)
    {

        return $this->facilityServiceTiming
            ->where(function ($query) use ($inputs) {
                return $query->whereDate('expiry_date', '>=', $inputs['start_date'])
                    ->where('expiry_date', '<=', $inputs['end_date'])
                    ->where('model_type', $inputs['model_type'])
                    ->where('model_id', $inputs['model_id']);
            })
            ->orWhere(function ($query) use ($inputs) {
                return $query->whereNull('expiry_date')
                    ->where('model_type', $inputs['model_type'])
                    ->where('model_id', $inputs['model_id']);
            })->orderBy('expiry_date')
            ->select(
                'id',
                'model_type',
                'model_id',
                'start_time',
                'end_time',
                'start_date',
                'expiry_date',
                'weekend_timing'
            )
            ->orderBy('start_time')
            ->get();
    }


    /**Fetching active time intervels
     * param  start_date,end_date,model_id,model_type
     * @return Response
     */
    public function getSlotIntervels($inputs)
    {

        return $this->facilityServiceSlot
            ->where(function ($query) use ($inputs) {
                return $query->whereDate('expiry_date', '>=', $inputs['start_date'])
                    ->where('expiry_date', '<=', $inputs['end_date'])
                    ->where('model_type', $inputs['model_type'])
                    ->where('model_id', $inputs['model_id']);
            })
            ->orWhere(function ($query) use ($inputs) {
                return $query->whereNull('expiry_date')
                    ->where('model_type', $inputs['model_type'])
                    ->where('model_id', $inputs['model_id']);
            })
            ->orderBy('expiry_date')
            ->select(
                'id',
                'model_type',
                'model_id',
                'slot_interval',
                'start_date',
                'expiry_date'
            )
            ->get();
    }

    public function storeBooking($inputs)
    {
        return $this->facilityBooking->create($inputs);
    }

    public function userBookedDetails($inputs)
    {
        $content = $this->facilityBooking->select("*")
            ->when(!empty($inputs) && isset($inputs['model_type']), function ($que) use ($inputs) {
                return $que->where('model_type', $inputs['model_type']);
            })
            ->when(!empty($inputs) && isset($inputs['model_id']), function ($que) use ($inputs) {
                return $que->where('model_id', $inputs['model_id']);
            })
            ->when(!empty($inputs) && isset($inputs['facility_user_id']), function ($que) use ($inputs) {
                return $que->where('facility_user_id', $inputs['facility_user_id']);
            })
            ->when(!empty($inputs) && isset($inputs['facility_service_ids']), function ($que) use ($inputs) {
                return $que->whereIn('model_id', $inputs['facility_service_ids']);
            })
            ->when(!empty($inputs) && isset($inputs['booking_date_start']), function ($que) use ($inputs) {
                return $que->whereDate('booking_date_start', date('Y-m-d', strtotime($inputs['booking_date_start'])));
            })
            ->orderBy('booking_date_start', 'DESC')
            ->get();

        return $content;
    }

    public function getAllBooked($inputs)
    {
        return $this->facilityBooking
            ->when(!empty($inputs) && isset($inputs['model_type']), function ($que) use ($inputs) {
                return $que->where('model_type', $inputs['model_type']);
            })
            ->when(!empty($inputs) && isset($inputs['model_id']), function ($que) use ($inputs) {
                return $que->where('model_id', $inputs['model_id']);
            })
            ->when(!empty($inputs) && isset($inputs['start_date']), function ($que) use ($inputs) {
                return $que->whereDate('booking_date_start', '>=', $inputs['start_date']);
            })
            ->when(!empty($inputs) && isset($inputs['end_date']), function ($que) use ($inputs) {
                return $que->whereDate('booking_date_end', '<=', $inputs['end_date']);
            })
            ->orderBy('booking_date_start')
            ->with([
                'facilityUser' => function ($query) {
                    return $query->select(
                        'id',
                        'first_name',
                        'last_name',
                        'email',
                        'alternate_email',
                        'phoneno'
                    );
                }
            ])
            ->select('id', 'model_type', 'model_id', 'facility_user_id', 'booking_date_start', 'booking_date_end', 'created_at')
            ->get();
    }



    public function setSlotFormat($inputs, $facility)
    {

        $result['displayFormat'] = [];
        $result['status_code'] = 200;

        if (!empty($facility)) {

            if ($inputs['single_service_facility'] == 0) {
                $data['model_id'] =  (int)$inputs['facility_service_id'];
                $data['model_type'] = 'Modules\Facility\Models\FacilityService';
            } else {
                $data['model_id'] =  (int)$inputs['facility_id'];
                $data['model_type'] = 'Modules\Facility\Models\Facility';
            }

            $facilityServiceData = $this->facilityServiceDataRepository->getActiveData($data);
            $allocationDetails = [];
            $allocationDayAllList = [];

            if (isset($inputs['is_admin']) && $inputs['is_admin'] == false) {
                $data['facility_user_id'] = \Auth::guard('facilityuser')->user()->id;
                //Day allocation checking.
                $allocationDetails = $this->getAllocationDetails($data);
                $allocationDayAllList = collect($allocationDetails->facilityuserweekenddefinition);
                unset($data['facility_user_id']);
            }

            $start_date = $inputs['startDate'];
            $end_date = $inputs['endDate'];
            //Fetching booking data
            $data['start_date'] =  $start_date;
            $data['end_date'] =  $end_date;
            $inputParams['start_date'] =  $start_date;
            $inputParams['end_date'] =   $end_date;
            $inputParams['model_id'] =  (int)$inputs['facility_id'];
            $inputParams['model_type'] = 'Modules\Facility\Models\Facility';
            //Fetching time slots
            $allTimeSlot = collect($this->getTimeSlot($inputParams));

            if (sizeof($allTimeSlot) > 0 && !empty($facilityServiceData) > 0) {
                //Fetching time slot intervels.
                if ($inputs['single_service_facility'] == 0) {
                    $inputParams['model_id'] =  (int)$inputs['facility_service_id'];
                    $inputParams['model_type'] = 'Modules\Facility\Models\FacilityService';
                }
                $slotIntervels = $this->getSlotIntervels($inputParams);

                if (sizeof($slotIntervels) > 0) {

                    //Fetching facility/service booking entries.
                    $bookings = collect($this->getAllBooked($data));
                    //Fetching facility blocking entries.
                    $data['model_id'] =  (int)$inputs['facility_id'];
                    $data['model_type'] = 'Modules\Facility\Models\Facility';
                    $lockDownLists = collect($this->facilityServiceLockdownRepository->getList($data));
                    //Collection query for all day blocked entries
                    $timeBlockLists = $lockDownLists->where('start_date', null);

                    $index = 0;
                    $incrementDate = $start_date;
                    //Looping upto end date for setting slot data.
                    while (strtotime($end_date) >= strtotime($incrementDate)) {
                        $result['displayFormat'][$index]['date'] = $incrementDate;
                        $result['displayFormat'][$index]['is_today'] = \Carbon::parse($incrementDate)->isToday();
                        $result['displayFormat'][$index]['title'] = date('l F d, Y', strtotime($incrementDate));
                        $result['displayFormat'][$index]['booking'] = true;
                        $result['displayFormat'][$index]['weekend'] = false;
                        //Fetching weekday timings.
                        $timeSlot = $allTimeSlot->where('weekend_timing', 0);
                        //User Weekend booking permission allocation.
                        if (isset($inputs['is_admin']) && $inputs['is_admin'] == false) {
                            $allocationDayList = $allocationDayAllList->whereNotIn('day_id', [6, 7]);
                        }

                        if (date('l', strtotime($incrementDate)) == 'Saturday' || date('l', strtotime($incrementDate)) == 'Sunday') {
                            $result['displayFormat'][$index]['weekend'] = true;
                            //Facility Weeekend booking desabled.
                            if ($facilityServiceData->booking_window == 0) {
                                $result['displayFormat'][$index]['booking'] = false;
                            }
                            //User Weekend booking permission allocation.
                            if (isset($inputs['is_admin']) && $inputs['is_admin'] == false) {
                                $allocationDayList = $allocationDayAllList->whereIn('day_id', [6, 7]);
                            }
                            //Fetching weekday timings.
                            $timeSlot = $allTimeSlot->where('weekend_timing', 1);
                            // if(sizeof($timeSlot)<=0){
                            //     $timeSlot = $allTimeSlot->where('weekend_timing',0);
                            // }
                        }

                        //Facility details
                        $result['displayFormat'][$index]['weekend_booking'] = $facilityServiceData->weekend_booking;
                        $result['displayFormat'][$index]['maxbooking_perday'] = $facilityServiceData->maxbooking_perday;
                        $result['displayFormat'][$index]['tolerance_perslot'] = $facilityServiceData->tolerance_perslot;
                        $result['displayFormat'][$index]['booking_window'] = $facility->facilitydata->booking_window;

                        //Slot intervels
                        foreach ($slotIntervels as $intervel) {
                            if ($intervel->expiry_date != null && $incrementDate >= date('Y-m-d', strtotime($intervel->start_date)) && $incrementDate <= date('Y-m-d', strtotime($intervel->expiry_date))) {
                                $result['displayFormat'][$index]['intervel'] = $intervel->slot_interval;
                            } elseif ($intervel->expiry_date == null) {
                                $result['displayFormat'][$index]['intervel'] = $intervel->slot_interval;
                            } else {
                            }
                        }
                        $intervelMinutes = (float)$result['displayFormat'][$index]['intervel'] * 60;
                        if ($result['displayFormat'][$index]['intervel'] < 1) {
                            $result['displayFormat'][$index]['intervelTitle'] = 'Note: ' . $intervelMinutes . ' minutes interval';
                        } elseif ($result['displayFormat'][$index]['intervel'] > 1) {
                            $result['displayFormat'][$index]['intervelTitle'] = "Note: " . $result['displayFormat'][$index]['intervel'] . " hours interval";
                        } elseif ($result['displayFormat'][$index]['intervel'] == 1) {
                            $result['displayFormat'][$index]['intervelTitle'] = "Note: " . $result['displayFormat'][$index]['intervel'] . " hour interval";
                        } else {
                            $result['displayFormat'][$index]['intervelTitle'] = '';
                        }
                        $times = $timeSlot->where('start_date', '<=', date('Y-m-d', strtotime($incrementDate)));

                        if (sizeof($times) > 0) {

                            //Checking Current day start and end time.
                            foreach ($times as $timeKey => $time) {
                                if ($time->expiry_date != null && $incrementDate >= date('Y-m-d', strtotime($time->start_date)) && $incrementDate <= date('Y-m-d', strtotime($time->expiry_date))) {
                                    $result['displayFormat'][$index]['startTime'] = $time->start_time;
                                    $result['displayFormat'][$index]['endTime'] = $time->end_time;
                                    $result['displayFormat'][$index]['timings'][$timeKey] = $time;
                                } elseif ($time->expiry_date == null) {
                                    $result['displayFormat'][$index]['startTime'] = $time->start_time;
                                    $result['displayFormat'][$index]['endTime'] = $time->end_time;
                                    $result['displayFormat'][$index]['timings'][$timeKey] = $time;
                                } else {
                                }
                            }

                            $intervel = '+' . $intervelMinutes . ' minutes';
                            $intervelKey = 0;
                            $result['displayFormat'][$index]['slot'] = [];
                            $slots = [];
                            $slots[$intervelKey]['booking_flag'] = 1;

                            $dateBlockLists = $lockDownLists->where('start_date', '<=', date('Y-m-d', strtotime($incrementDate)))
                                ->where('end_date', '>=', date('Y-m-d', strtotime($incrementDate)));

                            foreach ($result['displayFormat'][$index]['timings'] as $timings) {
                                //Start and end time differance is greater or equal to slot intervel.
                                $diffStartTime = \Carbon\Carbon::parse($timings->start_time);
                                $diffEndTime = \Carbon\Carbon::parse($timings->end_time);
                                $diffInMinutes = $diffStartTime->diffInMinutes($diffEndTime);
                                // dd($diff_in_minutes,$intervelMinutes,$result['displayFormat'][$index]['intervel']);
                                if ($diffInMinutes >= $intervelMinutes) {

                                    $startTime = $timings->start_time;
                                    $endTime = $timings->end_time;
                                    $incrementTime = $startTime;
                                    $nextIncrementTime = $incrementDate . ' ' . $startTime;

                                    while (strtotime($incrementDate . ' ' . $endTime) >= strtotime($nextIncrementTime)) {

                                        if ($incrementDate == date('Y-m-d', strtotime($nextIncrementTime))) {

                                            $slots[$intervelKey]['name'] = date("h:i A", strtotime($incrementTime));
                                            $slots[$intervelKey]['booking_flag'] = 1;
                                            $looptimeStamp = $incrementDate . ' ' . date("H:i:s", strtotime($incrementTime));
                                            //Set slot as unavailable when facility weekend booking is disabled .
                                            if ($result['displayFormat'][$index]['weekend'] == true && $facilityServiceData->weekend_booking == 0) {
                                                $slots[$intervelKey]['booking_flag'] = 0;
                                            }
                                            //Checking facility/service user allocation on weekday and weekend.
                                            if (isset($inputs['is_admin']) && $inputs['is_admin'] == false) {
                                                if (sizeof($allocationDayList) == 0) {
                                                    $slots[$intervelKey]['booking_flag'] = 0;
                                                }
                                            }
                                            $incrementEndTime = date('H:i:s', strtotime($intervel, strtotime($incrementTime)));
                                            $slots[$intervelKey]['display_name'] = date("h:i A", strtotime($incrementTime)).' - '.date("h:i A", strtotime($incrementEndTime));
                                            //Set slot as unavailable when time wise blocking exists.
                                            if (!empty($timeBlockLists)) {
                                                foreach ($timeBlockLists as $timeBlockList) {
                                                    if (date("H:i:s", strtotime($timeBlockList->start_time)) <= $incrementTime &&  date("H:i:s", strtotime($timeBlockList->end_time)) > $incrementTime) {
                                                        $slots[$intervelKey]['booking_flag'] = 0;
                                                    }
                                                    if (date("H:i:s", strtotime($timeBlockList->start_time)) < $incrementEndTime &&  date("H:i:s", strtotime($timeBlockList->end_time)) > $incrementEndTime) {
                                                        $slots[$intervelKey]['booking_flag'] = 0;
                                                    }
                                                }
                                            }
                                            //Set slot as unavailable when date and wise blocking exists.
                                            if (!empty($dateBlockLists)) {
                                                foreach ($dateBlockLists as $dateBlockList) {
                                                    if ($dateBlockList->start_time == null) {
                                                        $slots[$intervelKey]['booking_flag'] = 0;
                                                    } elseif (date("H:i:s", strtotime($dateBlockList->start_time)) <= $incrementTime &&  date("H:i:s", strtotime($dateBlockList->end_time)) > $incrementTime) {
                                                        $slots[$intervelKey]['booking_flag'] = 0;
                                                    } elseif (date("H:i:s", strtotime($dateBlockList->start_time)) < $incrementEndTime &&  date("H:i:s", strtotime($dateBlockList->end_time)) > $incrementEndTime) {
                                                        $slots[$intervelKey]['booking_flag'] = 0;
                                                    } else {
                                                    }
                                                }
                                            }
                                            //Set slot as unavailable when booking avaliable.
                                            if (!empty($bookings)) {
                                                //Get slot booking callection and count.
                                                $slotBooking = $bookings->where('booking_date_start', $looptimeStamp);
                                                $slotBookingCount = $slotBooking->count();
                                                //For admin set booking data
                                                if (isset($inputs['is_admin']) && $inputs['is_admin'] == true) {
                                                    $slots[$intervelKey]['booked_count'] = $slotBookingCount;
                                                    $slots[$intervelKey]['booked'] = $slotBooking;
                                                }
                                                //When Slot booking count is greater or equal to tolerance_perslot.
                                                if ($slotBookingCount >= (int)$facilityServiceData->tolerance_perslot) {
                                                    $slots[$intervelKey]['booking_flag'] = 0;
                                                }
                                                //Preventing the same slot's multiple bookings by a user.
                                                if (isset($inputs['is_admin']) && $inputs['is_admin'] == false) {
                                                    $userBookingCount = $slotBooking->where('facility_user_id', \Auth::guard('facilityuser')->user()->id)->count();
                                                    if ($userBookingCount >= 1) {
                                                        $slots[$intervelKey]['booking_flag'] = 0;
                                                    }
                                                }
                                            }
                                            //Today's slot blocking based current time.
                                            if ($result['displayFormat'][$index]['is_today'] == true) {
                                                if (strtotime(date('H:i:s')) > strtotime($incrementTime)) {
                                                    $slots[$intervelKey]['booking_flag'] = 0;
                                                }
                                            }
                                            $incrementTime = date('H:i:s', strtotime($intervel, strtotime($incrementTime)));
                                            $nextIncrementTime = date('Y-m-d H:i:s', strtotime($intervel, strtotime(date('Y-m-d H:i:s', strtotime($incrementDate . ' ' . $incrementTime)))));

                                            $intervelKey++;
                                        } else {
                                            break;
                                        }
                                        $result['displayFormat'][$index]['slot'] = $slots;
                                    }
                                } else {
                                    // unset($result['displayFormat'][$index]);
                                }
                            }
                        } else {
                            unset($result['displayFormat'][$index]);
                        }
                        $incrementDate = date('Y-m-d', strtotime('+1 day', strtotime($incrementDate)));
                        $index++;
                    }
                } else {
                    $result['message'] = "Slot intervel not found";
                    $result['status_code'] = 404;
                }
            } else {
                $result['message'] = "Facility/Service data not found";
                $result['status_code'] = 404;
            }
        } else {
            $result['message'] = "Facility not found";
            $result['status_code'] = 404;
        }

        return $result;
    }


    /**
     *  Get all customer allocated facility.
     * @param customer_ids
     * @return response
     */
    public function getAllCustomerFacility($inputs)
    {
        return $this->facility
            ->when((isset($inputs) && !empty($inputs['customer_ids'])), function ($que) use ($inputs) {
                return $que->whereIn('customer_id', $inputs['customer_ids']);
            })
            ->orderBy('facility')
            ->select('id', 'facility', 'description', 'single_service_facility')
            ->get();
    }

    /**
     *  Get all facility services.
     * @param facility_id
     * @return response
     */
    public function getFacilityServices($inputs)
    {
        return $this->facilityService
            ->when(!empty($inputs) && isset($inputs['facility_id']), function ($que) use ($inputs) {
                return $que->where('facility_id', $inputs['facility_id']);
            })
            ->select('id', 'facility_id', 'service', 'description')
            ->orderBy('service')
            ->get();
    }

    /**
     *  Delete booking entry by id.
     * @param facility_booking_id
     * @return response
     */

    public function removeBookingEntry($inputs)
    {
        return $this->facilityBooking->where('id', $inputs['facility_booking_id'])->delete();
    }

    /**
     *  Delete booking entry by id.
     * @param facility_booking_id
     * @return response
     */

    public function getBookingById($id)
    {
        return $this->facilityBooking->find($id)->load('facilityUser');
    }

    /**
     * Get user booked a facility/services same slot.
     * User can book one slot single time.
     * @param facility_user_id,model_type,model_id,booking_date_start,booking_date_end
     * @return response
     */

    public function slotAlreadyBookedByUser($inputs)
    {
        return $this->facilityBooking
            ->when(!empty($inputs) && isset($inputs['facility_user_id']) && $inputs['facility_user_id'] != null, function ($que) use ($inputs) {
                return $que->where('facility_user_id', $inputs['facility_user_id']);
            })
            ->where('model_type', $inputs['model_type'])
            ->where('model_id', $inputs['model_id'])
            ->where('booking_date_start', $inputs['booking_date_start'])
            ->where('booking_date_end', $inputs['booking_date_end'])
            ->get();
    }

    public function getFacilityDetails($inputs)
    {
        return $this->facilityService
            ->when(!empty($inputs) && isset($inputs['model_id']), function ($que) use ($inputs) {
                return $que->where('id', $inputs['model_id']);
            })
            ->select('id', 'facility_id', 'service', 'description')
            ->orderBy('service')
            ->with('facility')
            ->first();
    }

    public function getServiceDetails($id)
    {
        return $this->facility->find($id);
    }
}
