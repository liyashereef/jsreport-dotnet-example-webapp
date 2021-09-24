<?php

namespace Modules\UniformScheduling\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use App\Services\HelperService;
use App\Repositories\MailQueueRepository;
use Modules\UniformScheduling\Http\Requests\BookingFilterRequest;
use Modules\UniformScheduling\Http\Requests\UniformSchedulingEntriesRequest;
use Modules\UniformScheduling\Repositories\UniformSchedulingEntriesRepository;
use Modules\Admin\Repositories\UniformSchedulingOfficesRepository;
use Modules\Admin\Repositories\UniformSchedulingOfficesBlockRepository;
use Modules\UniformScheduling\Repositories\UniformSchedulingCustomQuestionRepository;
use Modules\Admin\Models\UniformSchedulingMeasurementPoints;
use Modules\UniformScheduling\Repositories\UniformMeasurementsRepository;

class UniformSchedulingBookingController extends Controller
{

    private $mailQueueRepository;
    protected $helperService;
    protected $uniformSchedulingEntriesRepository;
    protected $uniformSchedulingOfficesRepository;
    protected $uniformSchedulingCustomQuestionRepository;
    protected $uniformSchedulingOfficesBlockRepository;
    protected $uniformMeasurementsRepository;


    public function __construct(
        UniformSchedulingEntriesRepository $uniformSchedulingEntriesRepository,
        MailQueueRepository $mailQueueRepository,
        HelperService $helperService,
        UniformSchedulingOfficesRepository $uniformSchedulingOfficesRepository,
        UniformSchedulingCustomQuestionRepository $uniformSchedulingCustomQuestionRepository,
        UniformSchedulingOfficesBlockRepository $uniformSchedulingOfficesBlockRepository,
        UniformMeasurementsRepository $uniformMeasurementsRepository
    ) {
        $this->uniformSchedulingEntriesRepository = $uniformSchedulingEntriesRepository;
        $this->mailQueueRepository = $mailQueueRepository;
        $this->helperService = $helperService;
        $this->officesRepository = $uniformSchedulingOfficesRepository;
        $this->customQuestionRepository = $uniformSchedulingCustomQuestionRepository;
        $this->officesBlockRepository = $uniformSchedulingOfficesBlockRepository;
        $this->uniformMeasurementsRepository = $uniformMeasurementsRepository;
    }

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        if (\Auth::check()) {
            $office = $this->officesRepository->first();
            $questions = $this->customQuestionRepository->getCustomQuestionsWithOptions();
            $measurementPoints = UniformSchedulingMeasurementPoints::get();
            return view('uniformscheduling::public.booking',compact('office','questions','measurementPoints'));
        }else{
            return redirect()->route('uniform.login');
        }
    }

    /**
     * Get booking data.
     * @return Response
     */
    public function bookingData(BookingFilterRequest $request)
    {


        $result = [];
        $result['displayFormat'] = [];
        $result['status_code'] = 200;
        $requestInputs = $request->all();
        $requestInputs['startDate'] = date('Y-m-d', strtotime($request->input('booked_date')));
        // $requestInputs['endDate'] =  date('Y-m-d', strtotime('+'.
        //                     config("globals.uniform_scheduling_future_days").' day',
        //                     strtotime($request->input('booked_date'))
                        // ));
        $requestInputs['endDate'] = date('Y-m-d', strtotime($request->input('end_date')));
        $requestInputs['is_admin'] = false;
        $result = $this->uniformSchedulingEntriesRepository->setSlotFormat($requestInputs);

        return $result;
    }


     /**
     * Store a Slot booking.
     *
     * @param  \Modules\UniformScheduling\Http\Requests\UniformSchedulingEntriesRequest  $request
     * @return \Illuminate\Http\Response
     */

    public function slotBooking(UniformSchedulingEntriesRequest $request)
    {
// dd($request->all());
        try {

            \DB::beginTransaction();
            $office = $this->officesRepository->getById($request->input('uniform_scheduling_office_id'));
            $input = $request->all();
            $number = $office->phone_number;
            if ($office->phone_number_ext) {
                $number .= " ext." . $office->phone_number_ext;
            }

            //Check slot already booked/Blocked or not.
            $day_id = (int)date('N', strtotime($request->input('booked_date')));
            $chechBlocked = $this->officesBlockRepository->checkAlreadyBlocked($input);
            $dayBlocked = $chechBlocked->where('day_id',$day_id)->count();
            $otherBlocked = $chechBlocked->where('day_id','')->count();
            $isBlocked = (int)$dayBlocked+(int)$otherBlocked;
            $checkedBooked = $this->uniformSchedulingEntriesRepository->checkAlreadyBooked($input);

            if ($isBlocked == 0 && $checkedBooked == 0 && !empty($office)) {

                $input['user_id'] = \Auth::id();
                $result = $this->uniformSchedulingEntriesRepository->store($input);
                if ($result) {

                    $request->request->add(['uniform_scheduling_entry_id' => $result->id]);
                    $storeAnswers = $this->customQuestionRepository->saveAnswers($request->all());

                    $uniform = [];
                    $uniform['user_id'] = \Auth::id();
                    $uniform["uniform_scheduling_entry_id"] = $result->id;
                    $uniform["candidate_id"] = null;
                    $uniform["input"] = [];
                    $measurements = [];
                    foreach($request->input('point_ids') as $pointId){
                        $measurements[$pointId] = (int)$request->input('point_value_'.$pointId) + $request->input('point_decimal_value_'.$pointId);
                    }
                    if($request->input('gender') == 1){
                        unset($measurements[6]);
                    }

                    $uniform["input"] = $measurements;
                    $this->uniformMeasurementsRepository->store($uniform);

                    if ($storeAnswers['showMessage'] == true) {
                        return response()->json($storeAnswers);
                    }
                    if ($storeAnswers['success'] == true) {

                        $helper_variables = array(
                            '{bookedDate}' =>date('l F d, Y', strtotime($input['booked_date'])). ' at ' . date("h:i A", strtotime($input['start_time'])),
                            '{officeNameAndAddress}' => $office->name .', '.$office->adress,
                            '{officePhoneNumber}' => $number,
                        );

                        $this->mailQueueRepository->prepareMailTemplate("uniform_schedule_confirmation", '', $helper_variables,
                         "Modules\UniformScheduling\Models\UniformSchedulingEntries", \Auth::id());

                        $helper_variables_for_scheduling_info = array(
                            '{scheduledUserName}' => \Auth::user()->first_name . ' ' . \Auth::user()->last_name,
                            '{phoneNumber}' => $input['phone_number'],
                            '{bookedDate}' =>date('l F d, Y', strtotime($input['booked_date'])). ' at ' . date("h:i A", strtotime($input['start_time'])),
                        );

                        $this->mailQueueRepository->prepareMailTemplate("uniform_schedule_info", 0 , $helper_variables_for_scheduling_info,
                         "Modules\UniformScheduling\Models\UniformSchedulingEntries");


                    }

                }
                $return = ['success' => true, 'modalHide' => true, 'reload' => false, 'message' => 'Slot successfully booked.'];

            } else {
                if ($isBlocked >= 1) {
                    $messge = 'Slot blocked.Try with another slot.';
                    $reload = false;
                    $modalHide = true;
                }
                if ($checkedBooked >= 1) {
                    $messge = 'Slot already booked.Try with another slot.';
                    $reload = false;
                    $modalHide = true;
                }
                if(empty($office)){
                    $messge = 'Office data missing.';
                    $reload = false;
                    $modalHide = true;
                }
                $return = ['success' => false, 'modalHide' => $modalHide, 'reload' => $reload, 'message' => $messge];
            }

            \DB::commit();
            return response()->json($return);
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json($this->helperService->returnFalseResponse($e));
        }

    }

    /**
     * Show the specified resource.
     * @return Response
     */
    public function show()
    {
        return view('uniformscheduling::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @return Response
     */
    public function edit()
    {
        return view('uniformscheduling::edit');
    }

    /**
     * Update the specified resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function update(Request $request)
    {
    }

    /**
     * Remove the specified resource from storage.
     * @return Response
     */
    public function destroy()
    {
    }
}
