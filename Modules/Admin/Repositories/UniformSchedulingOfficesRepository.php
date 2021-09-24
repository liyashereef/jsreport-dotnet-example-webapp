<?php

namespace Modules\Admin\Repositories;

use Modules\Admin\Models\UniformSchedulingOffices;
use Modules\Admin\Repositories\UniformSchedulingOfficesBlockRepository;
class UniformSchedulingOfficesRepository
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
     * @param  Modules\Admin\Models\UniformSchedulingOffices $uniformSchedulingOffices
     */
    public function __construct(UniformSchedulingOffices $uniformSchedulingOffices,
    UniformSchedulingOfficesBlockRepository $uniformSchedulingOfficesBlockRepository)
    {
        $this->model = $uniformSchedulingOffices;
        $this->officesBlockRepository = $uniformSchedulingOfficesBlockRepository;
    }

    /**
     * Get offfice list
     *
     * @param empty
     * @return array
     */

    public function getAll(){
       return $this->model->orderBy('name')
       ->with('UniformSchedulingOfficeTimings','UniformSchedulingOfficeSlotBlocks','UniformSchedulingOfficeSlotBlocks.day')
       ->get();
    }

    public function getById($id){
        return $this->model->find($id);
     }

     public function first(){
        return $this->model->first();
     }
    /**
     * Store a newly created offfice in storage.
     *
     * @param  $request
     * @return object
     */

    public function store($inputs){
        return $this->model->updateOrCreate(['id' => $inputs['id']], $inputs);
    }

    public function getFreeSlots($inputs){
        $result = [];
        $officeDetails = $this->model
        ->with(['UniformSchedulingOfficeTimings'=> function($query) use($inputs){
            $timings = $query->where('start_date','<=',$inputs['date']);
            $timings = $query->where('expiry_date','>=',$inputs['date'])->orwhereNull('expiry_date');
            return $timings->first();
        },'UniformSchedulingOfficeSlotBlocks'=> function($query) use($inputs){
            $blocks = $query->where('start_date','<=',$inputs['date'])
            ->where('end_date','>=',$inputs['date'])->orwhereNull('end_date');
            return $blocks;
        },
        'UniformSchedulingOfficeSlotBlocks.day',
        'UniformSchedulingEntries'=> function($query) use($inputs){
            return $query->where('booked_date',$inputs['date']);
        }
        ])->first();

        $timings = $officeDetails->UniformSchedulingOfficeTimings;
        $blockEntryes = $officeDetails->UniformSchedulingOfficeSlotBlocks;
        $bookingEntries = $officeDetails->UniformSchedulingEntries;

        if($timings){
            foreach($timings as $timing){
                if($timing->start_date <= $inputs['date']){
                    $nextIncrementTime = $inputs['date'] . ' ' . $timing->start_time;
                    $intervel = '+' . $timing->intervals . ' minutes';
                    $intervelKey = 0;
                    $incrementTime = $timing->start_time;
                    // $result['times'] = $timing;

                    while (strtotime($inputs['date'] . ' ' . $timing->end_time) >= strtotime($nextIncrementTime)) {

                        if ($inputs['date'] == date('Y-m-d', strtotime($nextIncrementTime))) {

                            $incrementEndTime = date('H:i:s', strtotime($intervel, strtotime($incrementTime)));
                            $slots[$intervelKey]['name'] = date("h:i A", strtotime($incrementTime));
                            $slots[$intervelKey]['display_name'] = date("h:i A", strtotime($incrementTime)).' - '.date("h:i A", strtotime($incrementEndTime));
                            $slots[$intervelKey]['booking_flag'] = 1;
                            $slots[$intervelKey]['uniform_scheduling_office_timing_id'] = $timing->id;
                            $slots[$intervelKey]['start_time'] = $incrementTime;
                            $slots[$intervelKey]['end_time'] = $incrementEndTime;
                            $dayId = (int)date('N', strtotime($inputs['date']));
                            $slots[$intervelKey]['day'] = $dayId;



                             //Set slot as unavailable when date and wise blocking exists.
                             if (!empty($blockEntryes)) {
                                foreach ($blockEntryes as $block) {
                                    if($block->day_id== null || $block->day_id ==$dayId){
                                        if ($block->start_time == null) {
                                            $slots[$intervelKey]['booking_flag'] = 0;
                                        } elseif (date("H:i:s", strtotime($block->start_time)) <= $incrementTime &&  date("H:i:s", strtotime($block->end_time)) > $incrementTime) {
                                            $slots[$intervelKey]['booking_flag'] = 0;
                                        } elseif (date("H:i:s", strtotime($block->start_time)) < $incrementEndTime &&  date("H:i:s", strtotime($block->end_time)) > $incrementEndTime) {
                                            $slots[$intervelKey]['booking_flag'] = 0;
                                        } else {
                                        }
                                    }
                                }
                            }

                             //Set slot as unavailable when date and wise booking exists.
                             if (!empty($bookingEntries)) {
                                foreach ($bookingEntries as $booking) {
                                    if (date("H:i:s", strtotime($booking->start_time)) <= $incrementTime &&  date("H:i:s", strtotime($booking->end_time)) > $incrementTime) {
                                        $slots[$intervelKey]['booking_flag'] = 0;
                                    } elseif (date("H:i:s", strtotime($booking->start_time)) < $incrementEndTime &&  date("H:i:s", strtotime($booking->end_time)) > $incrementEndTime) {
                                        $slots[$intervelKey]['booking_flag'] = 0;
                                    } else {
                                    }
                                }
                            }

                            //Today's slot blocking based current time.
                            if ($inputs['is_today'] == true) {
                                if (strtotime(date('H:i:s')) > strtotime($incrementTime)) {
                                    $slots[$intervelKey]['booking_flag'] = 0;
                                }
                            }

                            $incrementTime = date('H:i:s', strtotime($intervel, strtotime($incrementTime)));
                            $nextIncrementTime = date('Y-m-d H:i:s', strtotime($intervel, strtotime(date('Y-m-d H:i:s', strtotime($inputs['date'] . ' ' . $incrementTime)))));

                            $intervelKey++;

                        } else {
                            break;
                        }
                    }
                    $result['slots']= $slots;
                }else{
                    $result['message']= 'Timing not avaliable.';
                    $result['success']= false;
                }
            }
        }else{
            $result['message']= 'Data not avaliable.';
            $result['success']= false;
        }
       return $result;
    }

}
