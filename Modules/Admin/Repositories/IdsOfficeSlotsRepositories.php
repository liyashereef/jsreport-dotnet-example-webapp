<?php

namespace Modules\Admin\Repositories;

use Modules\Admin\Models\IdsOfficeSlots;
use Modules\Admin\Repositories\IdsOfficeSlotsBlocksRepositories;
use Modules\Admin\Repositories\IdsOfficeTimingsRepository;
use Modules\IdsScheduling\Repositories\IdsEntriesRepositories;
use \Carbon\Carbon; 

class IdsOfficeSlotsRepositories
{

    /**
     * The Model instance.
     * ''
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $model, $idsOfficeSlotsBlocksRepositories, $idsOfficeTimingsRepository, $idsEntriesRepositories;

    /**
     * Create a new Model instance.
     *
     * @param  Modules\Admin\Models\IdsOfficeSlots $idsOfficeSlots
     */
    public function __construct(
        IdsOfficeSlots $idsOfficeSlots,
        IdsOfficeSlotsBlocksRepositories $idsOfficeSlotsBlocksRepositories,
        IdsOfficeTimingsRepository $idsOfficeTimingsRepository,
        IdsEntriesRepositories $idsEntriesRepositories
    ) {
        $this->model = $idsOfficeSlots;
        $this->idsOfficeSlotsBlocksRepositories = $idsOfficeSlotsBlocksRepositories;
        $this->idsOfficeTimingsRepository = $idsOfficeTimingsRepository;
        $this->idsEntriesRepositories = $idsEntriesRepositories;
    }

    /**
     * Get single service details
     *
     * @param $id
     * @return object
     */
    public function getById($id)
    {
        return $this->model->find($id);
    }

    /**
     * Get office slot details
     *
     * @param $id
     * @return object
     */
    public function getByOfficeId($id)
    {
        return $this->model->where('ids_office_id', $id)->get();
    }


    /**
     * Get office slot details
     *
     * @param $id
     * @return object
     */
    public function getAllSlots($inputs)
    {
        return $this->model
            ->where('ids_office_id', $inputs['ids_office_id'])
            ->orderBy('start_time')
            ->with([
                'IdsEntries' => function ($query) use ($inputs) {
                    $query->whereIn('slot_booked_date', $inputs['date']);
                    $query->whereNull('deleted_at');
                    $query->select(
                        'id',
                        'ids_office_slot_id',
                        'slot_booked_date',
                        'first_name',
                        'last_name',
                        'email',
                        'phone_number',
                        'given_rate',
                        'given_interval',
                        'to_be_rescheduled',
                        'ids_office_id',
                        'ids_service_id',
                        'ids_payment_method_id',
                        'is_mask_given',
                        'no_masks_given',
                        'is_payment_received',
                        'payment_reason',
                        'updated_by',
                        'notes'
                    );
                },
                'IdsEntries.IdsServices' => function ($query) use ($inputs) {
                    $query->select('id', 'name');
                },
                'IdsEntries.IdsCustomQuestionAnswers' => function ($query) use ($inputs) {
                    $query->select('id', 'ids_entry_id', 'ids_custom_questions_id', 'ids_custom_option_id', 'ids_custom_questions_str', 'ids_custom_option_str', 'other_value');
                },
                'IdsOfficeSlotBlock' => function ($query) use ($inputs) {
                    $query->whereIn('slot_block_date', $inputs['date']);
                    $query->whereNull('deleted_at');
                    $query->select('id', 'ids_office_slot_id', 'slot_block_date');
                }
            ])->select(
                "id",
                "display_name",
                "ids_office_id",
                "start_time",
                "end_time"
            )
            ->get();
    }

    /**
     * Store a newly created service in storage.
     *
     * @param  $request
     * @return object
     */

    public function store($inputs)
    {
        return $this->model->updateOrCreate(['id' => $inputs['id']], $inputs);
    }

    /**
     * Get single service details
     *
     * @param $id
     * @return object
     */
    public function destroy($id)
    {
        return $this->model->find($id)->delete();
    }

    /**
     * Delete by OfficeId
     * @param $id
     * @return object
     */
    public function destroyByOfficeId($id)
    {
        return $this->model->where('ids_office_id', $id)->delete();
    }


    /**
     * Delete by OfficeTimingId
     * @param $id
     * @return object
     */
    public function destroyByOfficeTimingId($id)
    {
        return $this->model->where('ids_office_timing_id', $id)->delete();
    }

    /**
     * Get office slot details
     *
     * @param $id
     * @return object
     */
    public function getOfficeFreeSlot($inputs)
    {
        $result = [];

        $data['date'] = [];
        $data['ids_office_id'] = $inputs['ids_office_id'];
        $data['date'] = [];
        array_push($data['date'], $inputs['date']);

        $time['start_date'] = $inputs['date'];
        $time['expiry_date'] = $inputs['date'];
        $time['ids_office_id'] = $inputs['ids_office_id'];
        $officeTimings = collect($this->idsOfficeTimingsRepository->getOfficeTimeEntries($time))->first();
        // dd($time,$officeTimings->id);
        if(!empty($officeTimings)){
            $allSlotBlocked = $this->idsOfficeSlotsBlocksRepositories->getAllSlotBlockedByDate($data);
            if (sizeof($allSlotBlocked) == 0) {
                return $this->model
                    ->where('ids_office_id', $inputs['ids_office_id'])
                    ->where('ids_office_timing_id', $officeTimings->id)
                    ->when($inputs['today'] == true, function ($query) use ($inputs) {
                        return $query->where('start_time', '>', date('H:s'));
                    })
                    ->whereDoesntHave('IdsEntries', function ($query) use ($inputs) {
                        return $query->whereDate('slot_booked_date', $inputs['date'])->whereNull('deleted_at');
                    })
                    ->whereDoesntHave('IdsOfficeSlotBlock', function ($query) use ($inputs) {
                        return $query->whereDate('slot_block_date', $inputs['date']);
                    })
                    ->select(
                        "id",
                        "display_name",
                        "ids_office_timing_id",
                        "ids_office_id",
                        "start_time",
                        "end_time"
                    )
                    ->orderBy('start_time')
                    ->get();
            }
        }else{

        }

        return $result;
    }

    public function officeSlotDetails($inputs)
    {

        $slotList = $this->getAllSlots($inputs);

        $allSlotBlocked = $this->idsOfficeSlotsBlocksRepositories->getAllSlotBlockedByDate($inputs);

        $result = [];

        // Preparing data for calender view.
        foreach ($slotList as $key => $slot) {
            $days = [];
            $bookedDate = '';
            //Set slot status (0=Free,1=Booked,2=Blocked) based on date
            foreach ($inputs['date'] as $dateKey => $d) {

                if ($inputs['is_admin'] == true) {
                    $days[$dateKey]['flag'] = 0; //Free
                    $days[$dateKey]['slot'] = [];
                } else {
                    //Public side
                    $days[$dateKey] = 0; //Free
                    //Current day slot booking will available after 2 hours of the current time.
                    if ($d == date('Y-m-d')) {
                        $days[$dateKey] = 2; //Blocked
                        $validTime = Carbon::parse(date('H:i'))->addMinute(120)->format('H:i:s');
                        if ($slot->start_time >= $validTime) {
                            $days[$dateKey] = 0; //Open
                        }
                    }
                }

                //Date wise all office slot blocking.
                foreach ($allSlotBlocked as $allSlot) {
                    if ($d == $allSlot->slot_block_date) {
                        if ($inputs['is_admin'] == true) {
                            $days[$dateKey]['flag'] = 2; //Blocked
                        } else {
                            $days[$dateKey] = 2; //Blocked
                        }
                    }
                }

                //Setting slot blocked status
                foreach ($slot->IdsOfficeSlotBlock as $block) {
                    if ($d == $block->slot_block_date) {
                        if ($inputs['is_admin'] == true) {
                            $days[$dateKey]['flag'] = 2; //Blocked
                        } else {
                            $days[$dateKey] = 2; //Blocked
                        }
                    }
                }
                //Blocking past date slots.
                if ($inputs['is_admin'] == false) {
                    if ($d < date('Y-m-d')) {
                        $days[$dateKey] = 2; //Blocked
                    }
                }

                //Setting slot Booked status
                foreach ($slot->IdsEntries as $entries) {
                    if ($d == $entries->slot_booked_date) {
                        if ($inputs['is_admin'] == true) {
                            $days[$dateKey]['flag'] = 1; //Booked
                            if ($entries->to_be_rescheduled == 1) {
                                $days[$dateKey]['flag'] = 3; //To Be Rescheduled
                            }
                            $days[$dateKey]['slot'] = $entries; //base64_encode(json_encode($entries)); //Booked Details
                            $days[$dateKey]['slot']->booked_display_date = date('l F d, Y', strtotime($entries->slot_booked_date)); //Booked Details
                            $days[$dateKey]['slot']->booked_display_time = $slot->display_name;
                            $days[$dateKey]['slot'] = base64_encode(json_encode($days[$dateKey]['slot']));
                        } else {
                            $days[$dateKey] = 1; //Booked
                        }
                    }
                }
            }

            unset($slot['IdsEntries']);
            unset($slot['IdsOfficeSlotBlock']);

            $result[$key] = $slot;
            $result[$key]['day'] = $days;
        }
        return $result;
    }

    public function getOfficeSlot($inputs){

        //Set slot status (0=Blocked,1=Free,2=Booked,3=To-Be-Rescheduled) based on date
        $allOfficeTimings = collect($this->idsOfficeTimingsRepository->getActiveTimes($inputs));
        $bookedEntries = collect($this->idsEntriesRepositories->getBookedEntries($inputs));
        $result = [];
        $inputs['ids_office_timing_id'] = data_get($allOfficeTimings,'*.id');
        $allDaySlotBlocked = collect($this->idsOfficeSlotsBlocksRepositories->getAllSlotBlockedByDateAndTimeId($inputs));

        foreach($inputs['date'] as $key=>$date){
            $slot = [];
            $result[$key]['date'] = $date;
            $result[$key]['format_date'] = date('l F d, Y', strtotime($date));
            $result[$key]['intervel_text'] = '';
            $result[$key]['intervel'] = '';
            $result[$key]['slots'] = [];
            $pastDay = 1;
            $today = 0;
            if($date < date('Y-m-d')){
                $pastDay = 0;
            }
            if($date == date('Y-m-d')){
                $today = 1;
            }

            //Finding date's ids office timings.
            $officeTimings = $allOfficeTimings->where('start_date', '<=', date('Y-m-d', strtotime($date)))
                ->where('expiry_date', '>=', date('Y-m-d', strtotime($date)))->first();
            if(empty($officeTimings)){
                $officeTimings = $allOfficeTimings->where('start_date', '<=', date('Y-m-d', strtotime($date)))
                    ->where('expiry_date',null)->first();
            }

            if(!empty($officeTimings) && isset($officeTimings->IdsOfficeSlots) && !empty($officeTimings->IdsOfficeSlots)){
                //Intervel title.
                $result[$key]['intervel'] =$officeTimings->intervals;
                if($officeTimings->intervals >= 60){
                    $hour = $officeTimings->intervals/60;
                    $result[$key]['intervel_text'] = 'Note: '. $hour .' hours';
                }else{
                    $result[$key]['intervel_text'] = 'Note: '.$officeTimings->intervals.' minutes';
                }

                foreach($officeTimings->IdsOfficeSlots as $idsSlotKey=>$idsOfficeSlots){

                    $slot[$idsSlotKey]['office_slot_id'] = $idsOfficeSlots->id;
                    $slot[$idsSlotKey]['booking_id'] = null;
                    $slot[$idsSlotKey]['is_candidate'] = 0;
                    $slot[$idsSlotKey]['title'] = Carbon::parse($idsOfficeSlots->start_time)->format('h:i A');
                    $slot[$idsSlotKey]['start_time'] = $idsOfficeSlots->start_time;
                    $slot[$idsSlotKey]['display_name'] = $idsOfficeSlots->display_name;
                    $slot[$idsSlotKey]['status'] = 1;
                    $slot[$idsSlotKey]['bookedBy'] = '';
                    $slot[$idsSlotKey]['serviceName'] = '';

                    if( $inputs['is_admin'] == false){
                        if($pastDay == 1){
                            //Avaliable
                            $slot[$idsSlotKey]['status'] = 1;
                        }else{
                            //Unavaliable
                            $slot[$idsSlotKey]['status'] = 0;
                        }
                        if($today == 1){
                            $validTime = Carbon::parse(date('H:i'))->addMinute(120)->format('H:i:s');
                            if ($idsOfficeSlots->start_time <= $validTime) {
                                $slot[$idsSlotKey]['status'] = 0; //Open
                            }
                        }
                    }
                    //Ids office slot block management.
                    $timeData = collect($idsOfficeSlots->IdsOfficeSlotBlock);
                    $blockEntry = [];
                    if(sizeof($timeData)>=1 || sizeof($allDaySlotBlocked)>=1){
                        $blockEntry = $timeData->where('ids_office_slot_id',$idsOfficeSlots->id)
                            ->where('slot_block_date',$date);

                        if(sizeof($blockEntry) < 1){
                            $blockEntry = $allDaySlotBlocked->where('slot_block_date',$date);
                        }
                    }
                    //Slot Blocking
                    if(sizeof($blockEntry) >= 1){
                        $slot[$idsSlotKey]['status'] = 0;
                    }
                    //Lunch hour blocking
                    if($officeTimings->lunch_start_time < $idsOfficeSlots->end_time &&
                    $officeTimings->lunch_end_time > $idsOfficeSlots->start_time){
                        $slot[$idsSlotKey]['status'] = 0;
                    }

                    $bookingEntry = $bookedEntries->where('ids_office_slot_id',$idsOfficeSlots->id)
                        ->where('slot_booked_date',$date)->first();

                    if(!empty($bookingEntry)){
                        if($inputs['is_admin'] == true){
                            $slot[$idsSlotKey]['is_online_payment'] = $bookingEntry->is_online_payment_received;
                            if($bookingEntry->is_online_payment_received >= 1){
                                 //Slot Booked for admin
                                $slot[$idsSlotKey]['status'] = 2;
                                $slot[$idsSlotKey]['booking_id']=$bookingEntry->id;
                                $slot[$idsSlotKey]['is_candidate'] = $bookingEntry->is_candidate;
                                if($bookingEntry->passport_photo_service_id != null){
                                    $slot[$idsSlotKey]['booing_with_photo'] = 1;
                                }else{
                                    $slot[$idsSlotKey]['booing_with_photo'] = 0;
                                }
                                if($inputs['is_calendar_api'] == true){
                                    $slot[$idsSlotKey]['bookedBy'] = $bookingEntry->first_name.' '.$bookingEntry->last_name;
                                    $slot[$idsSlotKey]['serviceName'] = $bookingEntry->IdsServices->name;
                                }
                            }else{
                                $slot[$idsSlotKey]['status'] = 0;
                                $slot[$idsSlotKey]['booking_id']=$bookingEntry->id;
                            }
                        }else{
                            //Slot Booked for public
                            $slot[$idsSlotKey]['status'] = 2;
                        }
                        if ($bookingEntry->to_be_rescheduled == 1) {
                            $slot[$idsSlotKey]['status'] = 3; //To Be Rescheduled
                        }
                    }

                }
                $result[$key]['slots'] = $slot;
            }else{
                // dd($result);
                unset($result[$key]);
            }
        }
        return $result;
    }

    public function getAllIdByTimingId($idsOfficeTimingId){
        return $this->model
            ->where('ids_office_timing_id', $idsOfficeTimingId)
            ->get()->pluck('id');
    }

}
