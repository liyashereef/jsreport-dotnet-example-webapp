<?php

namespace Modules\UniformScheduling\Repositories;

use Modules\UniformScheduling\Models\UniformSchedulingEntries;
use Modules\Admin\Repositories\UniformSchedulingOfficesRepository;
use Modules\Admin\Repositories\UniformSchedulingOfficeTimingsRepository;
use Modules\Admin\Repositories\UniformSchedulingOfficesBlockRepository;
use \Carbon\Carbon;

class UniformSchedulingEntriesRepository
{
    /**
     * The Model instance.
     * ''
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $model;

    /**
     * Create a new Model instance.
     *
     * @param  Modules\Admin\Models\UniformSchedulingEntries $uniformSchedulingEntries
     */
    public function __construct(
        UniformSchedulingEntries $uniformSchedulingEntries,
        UniformSchedulingOfficesRepository $uniformSchedulingOfficesRepository,
        UniformSchedulingOfficeTimingsRepository $uniformSchedulingOfficeTimingsRepository,
        UniformSchedulingOfficesBlockRepository $uniformSchedulingOfficesBlockRepository
        )
    {
        $this->model = $uniformSchedulingEntries;
        $this->officesRepository = $uniformSchedulingOfficesRepository;
        $this->timingsRepository = $uniformSchedulingOfficeTimingsRepository;
        $this->blockRepository = $uniformSchedulingOfficesBlockRepository;
    }


    /**
     * Get count of today and future dates booking counts for Delete office or service
     * @param ids_office_timing_id,
     */

    public function getTimingBookedCountOrLast($inputs)
    {
        $que = $this->model
        ->when(isset($inputs) && !empty($inputs['uniform_scheduling_office_timing_id']), function ($query) use ($inputs) {
                return $query->where('uniform_scheduling_office_timing_id', $inputs['uniform_scheduling_office_timing_id']);
        });
        if (isset($inputs['count']) && $inputs['count'] == true) {
                return $que ->when(isset($inputs) && !empty($inputs['booked_date']), function ($query) use ($inputs) {
                    return $query->where('booked_date', '>=', $inputs['booked_date']);
                })
                ->count();
        } else {
            return $que ->when(isset($inputs) && !empty($inputs['booked_date']), function ($query) use ($inputs) {
                return $query->where('booked_date', '>', $inputs['booked_date']);
            })
            ->orderBy('booked_date', 'DESC')->first();
        }
    }

    public function store($inputs)
    {
        return $this->model->create($inputs);
    }

    public function getById($id)
    {
        return $this->model->find($id);
    }

    public function updateEntry($inputs)
    {
        return $this->model->updateOrCreate(['id' => $inputs['id']], $inputs);
    }

    public function delete($id)
    {
        return $this->model->where('id', $id)->delete();
    }

    /**
     * Get day slot's with status.
     * @param ids_office_timing_id,
     */

    public function setSlotFormat($inputs)
    {
        $result['displayFormat'] = [];
        $result['status_code'] = 200;
        $office = $this->officesRepository->first();


        if (!empty($office)) {

            $start_date = $inputs['startDate'];
            $end_date = $inputs['endDate'];
            //Fetching booking data
            $inputParams['start_date'] =  $start_date;
            $inputParams['end_date'] =   $end_date;
            $inputParams['uniform_scheduling_office_id'] =   $office->id;

            //Fetching time slots
            $allTimeSlot = collect($this->timingsRepository->getActiveTimes($inputParams));
            $allAvaliableTimings = $allTimeSlot;
            // $inputParams['allTimeSlot'] =   $allTimeSlot;
            if (sizeof($allTimeSlot) > 0) {

                    //Fetching facility/service booking entries.
                    $bookings = collect($this->getAllBooked($inputParams));

                    //Fetching facility blocking entries.
                    $lockDownLists = collect($this->blockRepository->getBlockEntries($inputParams));
                    // dd($allTimeSlot,$inputParams,$bookings,$lockDownLists);

                    //Collection query for all day blocked entries
                    // $timeBlockLists = $lockDownLists->where('end_date', null);

                    $index = 0;
                    $result= $inputParams;

                    $incrementDate = $start_date;
                    //Looping upto end date for setting slot data.
                    while (strtotime($end_date) >= strtotime($incrementDate)) {
                        $result['displayFormat'][$index]['date'] = $incrementDate;
                        $result['displayFormat'][$index]['is_today'] = Carbon::parse($incrementDate)->isToday();
                        $result['displayFormat'][$index]['title'] = date('l F d, Y', strtotime($incrementDate));
                        $result['displayFormat'][$index]['booking'] = true;
                        $result['displayFormat'][$index]['startTime'] = '';
                        $result['displayFormat'][$index]['endTime'] = '';
                        $result['displayFormat'][$index]['uniform_scheduling_office_timing_id'] = '';
                        $result['displayFormat'][$index]['intervel'] = '';

                        $dayId = (int)date('N', strtotime($incrementDate));
                        $result['displayFormat'][$index]['day'] = $dayId;
                        $result['displayFormat'][$index]['weekend'] = false;
                        if (date('l', strtotime($incrementDate)) == 'Saturday' || date('l', strtotime($incrementDate)) == 'Sunday') {
                            $result['displayFormat'][$index]['weekend'] = true;
                        }
                        $result['displayFormat'][$index]['slot'] = [];
                      //Fetching weekday timings.
                      $allTimeSlot = collect($allAvaliableTimings);
                      $timings = $allTimeSlot->where('start_date', '<=', date('Y-m-d', strtotime($incrementDate)))
                      ->where('expiry_date', '>=', date('Y-m-d', strtotime($incrementDate)));
                        if(sizeof($timings) == 0){
                            $timings = $allTimeSlot->where('start_date', '<=', date('Y-m-d', strtotime($incrementDate)))
                            ->where('expiry_date', '=',null);

                        }
                        if (!empty($timings)) {

                            foreach($timings as $times){

                               //Checking Current day start and end time.
                               $result['displayFormat'][$index]['startTime'] = $times->start_time;
                               $result['displayFormat'][$index]['endTime'] = $times->end_time;
                            //    $result['displayFormat'][$index]['timings'] = $times;
                               $result['displayFormat'][$index]['uniform_scheduling_office_timing_id'] = $times->id;
                               $result['displayFormat'][$index]['intervel'] = $times->intervals;

                               //Slot intervels

                               if ($result['displayFormat'][$index]['intervel'] < 60) {
                                   $result['displayFormat'][$index]['intervelTitle'] = 'Note: ' . $result['displayFormat'][$index]['intervel'] . ' minutes interval';
                               } elseif ($result['displayFormat'][$index]['intervel'] > 60) {
                                   $hour = (float)$result['displayFormat'][$index]['intervel'] / 60;
                                   $result['displayFormat'][$index]['intervelTitle'] = "Note: " . $hour . " hours interval";
                               } elseif ($result['displayFormat'][$index]['intervel'] == 60) {
                                   $result['displayFormat'][$index]['intervelTitle'] = "Note: 1 hour interval";
                               } else {
                                   $result['displayFormat'][$index]['intervelTitle'] = '';
                               }

                               $intervelMinutes = (float)$result['displayFormat'][$index]['intervel'];
                               $intervel = '+' . $intervelMinutes . ' minutes';
                               $intervelKey = 0;
                               $result['displayFormat'][$index]['slot'] = [];
                               $slots = [];

                               $timeBlockLists = $lockDownLists->where('start_date', '<=', date('Y-m-d', strtotime($incrementDate)))
                               ->where('end_date', null);//->where('day_id', null);

                               $dateBlockLists = $lockDownLists->where('start_date', '<=', date('Y-m-d', strtotime($incrementDate)))
                                   ->where('end_date', '>=', date('Y-m-d', strtotime($incrementDate)));//->where('day_id', null);

                               // foreach ($result['displayFormat'][$index]['timings'] as $timings) {
                               //Start and end time differance is greater or equal to slot intervel.
                               // $timings = $timesAA;
                               $diffStartTime = \Carbon\Carbon::parse($times->start_time);
                               $diffEndTime = \Carbon\Carbon::parse($times->end_time);
                               $diffInMinutes = $diffStartTime->diffInMinutes($diffEndTime);
                               // dd($diff_in_minutes,$intervelMinutes,$result['displayFormat'][$index]['intervel']);
                               if ($diffInMinutes >= $intervelMinutes) {

                                   $startTime = $times->start_time;
                                   $endTime = $times->end_time;
                                   $incrementTime = $startTime;
                                   $nextIncrementTime = $incrementDate . ' ' . $startTime;

                                   while (strtotime($incrementDate . ' ' . $endTime) >= strtotime($nextIncrementTime)) {

                                    if ($incrementDate == date('Y-m-d', strtotime($nextIncrementTime))) {

                                        $nextIncrementTime = date('Y-m-d H:i:s', strtotime($intervel, strtotime(date('Y-m-d H:i:s', strtotime($incrementDate . ' ' . $incrementTime)))));

                                        if(date('H:i:s', strtotime($nextIncrementTime))<=$endTime){


                                            $slots[$intervelKey]['name'] = date("h:i A", strtotime($incrementTime));
                                            $slots[$intervelKey]['booking_flag'] = 1;
                                            $slots[$intervelKey]['status'] = 1;
                                            if (isset($inputs['is_admin']) && $inputs['is_admin'] == true) {
                                                $slots[$intervelKey]['booking_flag'] = 0;
                                            }

                                            $looptimeStamp = $incrementDate . ' ' . date("H:i:s", strtotime($incrementTime));

                                            $incrementEndTime = date('H:i:s', strtotime($intervel, strtotime($incrementTime)));
                                            $slots[$intervelKey]['display_name'] = date("h:i A", strtotime($incrementTime)).' - '.date("h:i A", strtotime($incrementEndTime));
                                            $slots[$intervelKey]['start_time'] = date("H:i:s", strtotime($incrementTime));
                                            $slots[$intervelKey]['end_time'] = date("H:i:s", strtotime($incrementEndTime));

                                            //Set slot as unavailable when time wise blocking exists.
                                            if (sizeof($timeBlockLists)>=1) {
                                                foreach ($timeBlockLists as $timeBlockList) {
                                                    //    dd($timeBlockList->day_id,$dayId);
                                                    if($timeBlockList->day_id== null || $timeBlockList->day_id ==$dayId){
                                                            if (date("H:i:s", strtotime($timeBlockList->start_time)) <= $incrementTime &&  date("H:i:s", strtotime($timeBlockList->end_time)) > $incrementTime) {
                                                                $slots[$intervelKey]['booking_flag'] = 0; //dd($timeBlockLists,$slots[$intervelKey],$incrementTime);
                                                                $slots[$intervelKey]['status'] = 0;
                                                            }
                                                            if (date("H:i:s", strtotime($timeBlockList->start_time)) < $incrementEndTime &&  date("H:i:s", strtotime($timeBlockList->end_time)) > $incrementEndTime) {
                                                                $slots[$intervelKey]['booking_flag'] = 0;
                                                                $slots[$intervelKey]['status'] = 0;
                                                            }
                                                    }
                                                }
                                            }
                                            //Set slot as unavailable when date and wise blocking exists.
                                            if (!empty($dateBlockLists)) {
                                                foreach ($dateBlockLists as $dateBlockList) {
                                                        if($dateBlockList->day_id== null || $dateBlockList->day_id ==$dayId){
                                                            if ($dateBlockList->start_time == null) {
                                                                $slots[$intervelKey]['booking_flag'] = 0;
                                                                $slots[$intervelKey]['status'] = 0;
                                                            } elseif (date("H:i:s", strtotime($dateBlockList->start_time)) <= $incrementTime &&  date("H:i:s", strtotime($dateBlockList->end_time)) > $incrementTime) {
                                                                $slots[$intervelKey]['booking_flag'] = 0;
                                                                $slots[$intervelKey]['status'] = 0;
                                                            } elseif (date("H:i:s", strtotime($dateBlockList->start_time)) < $incrementEndTime &&  date("H:i:s", strtotime($dateBlockList->end_time)) > $incrementEndTime) {
                                                                $slots[$intervelKey]['booking_flag'] = 0;
                                                                $slots[$intervelKey]['status'] = 0;
                                                            } else {
                                                            }
                                                        }
                                                }
                                            }
                                                //Today's slot blocking based current time.
                                                if ($result['displayFormat'][$index]['is_today'] == true) {
                                                    if (strtotime(date('H:i:s')) > strtotime($incrementTime)) {
                                                        $slots[$intervelKey]['booking_flag'] = 0;
                                                    }
                                                }

                                            //Set slot as unavailable when booking avaliable.
                                            if (!empty($bookings)) {
                                                //Get slot booking callection and count.
                                                $slotBooking = $bookings->where('booked_date', $incrementDate)
                                                ->where('start_time',$incrementTime)
                                                ->where('end_time',date('H:i:s',strtotime($nextIncrementTime)));
                                                $slotBookingCount = $slotBooking->count();

                                                //When Slot booking count is greater or equal to tolerance_perslot.
                                                if ($slotBookingCount >= 1) {
                                                        $slots[$intervelKey]['booking_flag'] = 0;
                                                        $slots[$intervelKey]['status'] = 2;
                                                    }

                                                //For admin set booking data
                                                if (isset($inputs['is_admin']) && $inputs['is_admin'] == true) {
                                                    //    $slots[$intervelKey]['booked_count'] = $slotBookingCount;
                                                    //    $slots[$intervelKey]['booked'] = $slotBooking;
                                                    $slots[$intervelKey]['booked_entry_id'] = '';
                                                    if($slotBookingCount >= 1){
                                                        $slots[$intervelKey]['booked_entry_id'] = $slotBooking->first()->id;
                                                        $slots[$intervelKey]['booking_flag'] = 1;
                                                    }

                                                }

                                            }

                                            $incrementTime = date('H:i:s', strtotime($intervel, strtotime($incrementTime)));

                                            $intervelKey++;
                                        } else {
                                            break;
                                        }
                                    } else {
                                    break;
                                    }
                                       $result['displayFormat'][$index]['slot'] = $slots;
                                   }
                               } else {
                                   // unset($result['displayFormat'][$index]);
                               }

                            }


                            // }
                        } else {
                            // unset($result['displayFormat'][$index]);
                        }
                        $incrementDate = date('Y-m-d', strtotime('+1 day', strtotime($incrementDate)));
                        $index++;
                    }
                    $result['length'] = sizeof($result['displayFormat']);
            } else {
                // $incrementDate = date('Y-m-d', strtotime('+1 day', strtotime($incrementDate)));
                // $index++;
                $result['message'] = "Office timing data not found";
                $result['status_code'] = 404;
            }
        } else {
            $result['message'] = "Office not found";
            $result['status_code'] = 404;
        }

        return $result;
    }



    /**Fetching active start and end time timings
     * param  start_date,end_date,model_id,model_type
     * @return Response
     */
    public function getAllBooked($inputs)
    {
        return $this->model
            ->when(!empty($inputs) && isset($inputs['start_date']), function ($que) use ($inputs) {
                return $que->whereDate('booked_date', '>=', $inputs['start_date']);
            })
            ->when(!empty($inputs) && isset($inputs['end_date']), function ($que) use ($inputs) {
                return $que->whereDate('booked_date', '<=', $inputs['end_date']);
            })
            ->when(!empty($inputs) && isset($inputs['uniform_scheduling_office_id']), function ($que) use ($inputs) {
                return $que->where('uniform_scheduling_office_id',$inputs['uniform_scheduling_office_id']);
            })
            ->orderBy('booked_date')
            ->select('id','user_id','booked_date','start_time','end_time','uniform_scheduling_office_timing_id',
            'uniform_scheduling_office_id', 'created_at')
            ->get();
    }

    public function getEntryById($id)
    {
        return $this->model
            ->where('id', $id)
            ->select('*')
            ->with(['user',
            'UniformSchedulingCustomQuestionAnswer' => function ($query) {
                $query->select(
                    'id',
                    'uniform_scheduling_entry_id',
                    'uniform_scheduling_custom_question_id',
                    'uniform_scheduling_custom_option_id',
                    'custom_questions_str',
                    'custom_option_str',
                    'other_value'
                );
            },
            'uniformMeasurements'=> function ($query) {
                $query->select(
                    'id',
                    'user_id',
                    'candidate_id',
                    'uniform_scheduling_entry_id',
                    'uniform_scheduling_measurement_point_id',
                    'measurement_values'
                );
            },'uniformMeasurements.uniformSchedulingMeasurementPoints'=> function ($query) {
                $query->select('id','name');
            }
            ])
            ->first();
    }

    public function checkAlreadyBooked($inputs){
        return $this->model
        ->where('uniform_scheduling_office_id',$inputs['uniform_scheduling_office_id'])
        ->whereDate('booked_date', $inputs['booked_date'])
        ->where('start_time', $inputs['start_time'])
        ->where('end_time', $inputs['end_time'])
        ->count();

    }

      /**Fetching active start and end time timings
     * param  start_date,end_date,model_id,model_type
     * @return Response
     */
    public function getAllLists($inputs)
    {
        return $this->model
            ->when(!empty($inputs) && isset($inputs['start_date']), function ($que) use ($inputs) {
                return $que->whereDate('booked_date', '>=', $inputs['start_date']);
            })
            ->when(!empty($inputs) && isset($inputs['end_date']), function ($que) use ($inputs) {
                return $que->whereDate('booked_date', '<=', $inputs['end_date']);
            })
            ->when(!empty($inputs) && isset($inputs['uniform_scheduling_office_id']), function ($que) use ($inputs) {
                return $que->where('uniform_scheduling_office_id',$inputs['uniform_scheduling_office_id']);
            })
            ->orderBy('booked_date')
            ->with([
                'user' => function ($query) {
                    return $query->select(
                        'id',
                        'first_name',
                        'last_name',
                        'email'
                    );
                },
                'uniformMeasurements'=> function ($query) {
                    return $query->select(
                        'id',
                        'candidate_id',
                        'uniform_scheduling_entry_id',
                        'uniform_scheduling_measurement_point_id',
                        'measurement_values'
                    );
                },
                'uniformMeasurements.uniformSchedulingMeasurementPoints'=> function ($query) {
                    return $query->select(
                        'id',
                        'name'
                    );
                },
                'UniformSchedulingCustomQuestionAnswer',
                'uniformSchedulingOffice'=> function ($query) {
                    return $query->select(
                        'id',
                        'name'
                    );
                },
            ])
            ->select('id','user_id','booked_date','start_time','end_time','email','phone_number','is_client_show_up',
            'gender','uniform_scheduling_office_timing_id','uniform_scheduling_office_id', 'created_at')
            ->get();
    }

}



