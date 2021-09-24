<?php

namespace Modules\UniformScheduling\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;

use App\Services\HelperService;
use Modules\UniformScheduling\Http\Requests\UpdateBookingEntriesRequest;
use Modules\Admin\Repositories\UniformSchedulingOfficesRepository;
use Modules\UniformScheduling\Repositories\UniformSchedulingEntriesRepository;
use Modules\Admin\Repositories\UniformSchedulingOfficesBlockRepository;
use App\Repositories\MailQueueRepository;
use Modules\UniformScheduling\Repositories\UniformSchedulingCustomQuestionRepository;
use Modules\Admin\Models\UniformSchedulingMeasurementPoints;
use Modules\UniformScheduling\Repositories\UniformMeasurementsRepository;

class UniformSchedulingEntriesController extends Controller
{

    private $officeRepository;
    private $entriesRepository;
    private $officesBlockRepository;
    private $customQuestionRepository;
    private $mailQueueRepository;
    private $helperService;
    protected $uniformMeasurementsRepository;
     /**
     * Create a new Model instance.
     *
     * @param UniformSchedulingOfficesRepository $officeRepository
     * @param UniformSchedulingEntriesRepository $entriesRepository
     * @param UniformSchedulingOfficesBlockRepository $officesBlockRepository
     * @param UniformSchedulingCustomQuestionRepository $customQuestionRepository
     * @param MailQueueRepository $mailQueueRepository
     * @param HelperService $helperService
     * @param UniformMeasurementsRepository $uniformMeasurementsRepository
     */
    public function __construct(
        UniformSchedulingOfficesRepository $officeRepository,
        UniformSchedulingEntriesRepository $entriesRepository,
        UniformSchedulingOfficesBlockRepository $officesBlockRepository,
        UniformSchedulingCustomQuestionRepository $customQuestionRepository,
        MailQueueRepository $mailQueueRepository,
        HelperService $helperService,
        UniformMeasurementsRepository $uniformMeasurementsRepository
    )
    {
        $this->officeRepository = $officeRepository;
        $this->entriesRepository = $entriesRepository;
        $this->officesBlockRepository = $officesBlockRepository;
        $this->mailQueueRepository = $mailQueueRepository;
        $this->customQuestionRepository = $customQuestionRepository;
        $this->helperService = $helperService;
        $this->uniformMeasurementsRepository = $uniformMeasurementsRepository;
    }

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index(){
        $measurementPoints = UniformSchedulingMeasurementPoints::get();
        return view('uniformscheduling::admin.index',compact('measurementPoints'));
    }

    public function getOfficeSlotTimings(Request $request){
        $result = $request->all();
        //By default, Current month data will be displayed
        if(!$request->filled('start_date')){
            $result['start_date'] = date('Y-m-01');
        }
        if(!$request->filled('end_date')){
            $result['end_date'] = date("Y-m-t", strtotime(date('Y-m-d')));
        }

        //we provide all available times 10 day prior, and 1 day after, to provide a 9 day
        $result = $this->getDateArray($result);

        //Fetching slot details along with booked and blocked details
        $result['is_admin'] = true;
        $result['is_calendar_api'] = false;
        $result['startDate'] = $result['start_date'] ;
        $result['endDate']= $result['end_date'];
        $result['daySlotDetails'] = $this->entriesRepository->setSlotFormat($result);

        unset($result['date']);
        unset($result['display_date']);
        unset($result['is_admin']);

        return $result;
     }

     /**
     * We will show 20 day window
     */

     public function getDateArray($inputs){

        $result['start_date'] = date('Y-m-d', strtotime($inputs['start_date']));
        $result['end_date'] = date('Y-m-d', strtotime($inputs['end_date']));

        $index = 0;
        $incrementDate =  $result['start_date'];

        while(strtotime($inputs['end_date']) >= strtotime($incrementDate)){

            $result['date'][$index] = $incrementDate;
            $result['end_date'] = $incrementDate;
            //Formated date for Displaying
            $result['display_date'][$index]['name'] = date('l F d, Y', strtotime($incrementDate));
            $result['display_date'][$index]['weekdys'] = false;

            if(date('l', strtotime($incrementDate)) == 'Saturday' || date('l', strtotime($incrementDate)) == 'Sunday'){
                $result['display_date'][$index]['weekdys'] = true;
            }
            $incrementDate = date('Y-m-d', strtotime('+1 day', strtotime($incrementDate)));
            $index++;
        }

        return $result;
    }

    public function getBookingEntryById($bookingId){
        return $this->entriesRepository->getEntryById($bookingId);
    }

    /**
     * Admin/Employees : Remove/Cancel slot booking.
     * @param ids_entries_id.
     * @return message.
     */
    public function deleteBooking(Request $request){
        if($request->has(['id','is_canceled'])){
            try {
                \DB::beginTransaction();
                $inputs['id'] = $request->input('id');
                $inputs['is_canceled'] = $request->input('is_canceled');
                $inputs['deleted_by'] = \Auth::user()->id;
                $inputs['deleted_at'] = \Carbon::now()->format('Y-m-d H:i:s');
                $this->entriesRepository->updateEntry($inputs);
                \DB::commit();
                return response()->json($this->helperService->returnTrueResponse());
            } catch (\Exception $e) {
                \DB::rollBack();
                return response()->json($this->helperService->returnFalseResponse($e));
            }
        }else{
            return response()->json($this->helperService->returnFalseResponse());
        }
    }

    /**
     * Admin/Employees : Slot Booking.
    * Update
    * Reschedule
    * Remove old entry after reschedule.
    * After Reschedule send mail.
     * @param  \Modules\UniformScheduling\Http\Requests\UpdateBookingEntriesRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function updateBooking(UpdateBookingEntriesRequest $request){
        try {
            \DB::beginTransaction();
            $inputs = $request->all();
            // dd($inputs);
            $booking = $this->entriesRepository->getById($inputs['id']);
            $reload = false;
            $entry = null;
            if($booking){

                unset($inputs['id']);
                //For reschedule
                if( $request->has(['start_time','end_time','uniform_scheduling_office_timing_id','booked_date']) &&
                    $request->filled('start_time') && $request->filled('end_time') && $request->filled('booked_date')
                    && $request->filled('uniform_scheduling_office_timing_id')
                ){
                    $inputs['user_id'] = $booking->user_id;
                    $inputs['uniform_scheduling_office_id'] = $booking->uniform_scheduling_office_id;

                    $entry = $this->entriesRepository->store($inputs);
                    if($entry){
                        $reload = true;
                        $reshedule['old_entry_id'] = $request->input('id');
                        $reshedule['new_entry_id'] = $entry->id;
                        $this->customQuestionRepository->resheduleEntry($reshedule);

                        $inputs['is_rescheduled'] = true;
                        $inputs['rescheduled_id'] = $entry->id;
                        $inputs['rescheduled_at'] = date('Y-m-d');
                        $inputs['rescheduled_by'] = \Auth::id();
                        $inputs['deleted_by'] = \Auth::id();
                        $inputs['deleted_at'] = \Carbon::now();
                    }
                }

                //For reschedule
                    $inputs['id'] = $request->input('id');
                    $inputs['updated_by'] = \Auth::id();
                    unset($inputs['start_time']);
                    unset($inputs['end_time']);
                    unset($inputs['booked_date']);
                    unset($inputs['uniform_scheduling_office_timing_id']);
                    $store = $this->entriesRepository->updateEntry($inputs);
                    //*Start*Uniform section
                    if($store){
                        $uniform = [];
                        $uniform['user_id'] = $booking->user_id;
                        $uniform["uniform_scheduling_entry_id"] = $request->input('id');
                        $uniform["candidate_id"] = null;
                        $uniform["input"] = [];
                        $measurements = [];
                        foreach($request->input('point_ids') as $pointId){
                            $measurements[$pointId] = (int)$request->input('point_value_'.$pointId) + $request->input('point_decimal_value_'.$pointId);
                        }
                        $uniform["input"] = $measurements;
                        $this->uniformMeasurementsRepository->store($uniform);

                        if($request->input('gender') != $booking->gender && $request->input('gender') == 1){
                            $hipInput['uniform_scheduling_entry_id'] = $booking->id;
                            $this->uniformMeasurementsRepository->deleteHipData($hipInput);
                        }
                    }

                    //*End*Uniform section
                if($request->has(['start_time','end_time','uniform_scheduling_office_timing_id','booked_date']) &&
                $request->filled('start_time') && $request->filled('end_time') && $request->filled('booked_date')
                && $request->filled('uniform_scheduling_office_timing_id')){

                    //Updating entry_id when appoinmanet get rescheduled.
                    if(!empty($entry)){
                        $updateEntry['uniform_scheduling_entry_id'] = $entry->id;
                        $updateEntry['old_uniform_scheduling_entry_id'] = $booking->id;
                        $this->uniformMeasurementsRepository->updateEntryIds($updateEntry);
                    }

                    $office = $this->officeRepository->getById($booking->uniform_scheduling_office_id);
                    $number = $office->phone_number;
                    if ($office->phone_number_ext) {
                        $number .= " ext." . $office->phone_number_ext;
                    }
                    $helper_variables = array(
                        '{bookedDate}' =>date('l F d, Y', strtotime($request->input('booked_date'))). ' at '
                        . date("h:i A", strtotime($request->input('start_time'))),
                        '{officeNameAndAddress}' => $office->name .', '.$office->adress,
                        '{officePhoneNumber}' => $number,
                    );

                $this->mailQueueRepository->prepareMailTemplate("uniform_reschedule_confirmation", '', $helper_variables,
                 "Modules\UniformScheduling\Models\UniformSchedulingEntries", $booking->user_id);
                }
                $msg = array('success' => true, 'message' => 'Updated','reload'=>$reload);
            }else{
                $msg = array('success' => false, 'message' => 'Data not avaliable','reload'=>$reload);
            }
            \DB::commit();
            return response()->json($msg);
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json($this->helperService->returnFalseResponse($e));
        }
    }

    /**
     * Free office slot by
     * @param slot_booked_date and office
     */
    public function getOfficeFreeSlot(Request $request){
        $input = $request->all();
        $input['date'] = $request->input('bookedDate');
        $input['is_today'] = false;
        if(strtotime($input['date']) == strtotime(date('Y-m-d'))){
            $input['is_today'] = true;
        }
        return $this->officeRepository->getFreeSlots($input);
    }

    /**
     * Get Calender page with office list
     */
    public function getBookingListPage(){
        $measurementPoints = UniformSchedulingMeasurementPoints::get();
        return view('uniformscheduling::admin.booking-list',compact('measurementPoints'));
    }

    public function getBookingLists(Request $request){
        $result['measurementDecimalPoints'] = array_flip(config('globals.uniform_measurement_decimal_points'));
        $result['result'] = $this->entriesRepository->getAllLists($request->all());
         return $result;
    }

}
