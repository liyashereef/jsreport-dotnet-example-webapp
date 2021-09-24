<?php

namespace Modules\Facility\Http\Controllers\FacilityUser;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use App\Services\HelperService;
use Illuminate\Support\Facades\DB;

use Modules\Facility\Http\Requests\FacilityBookingFilterRequest;
use Modules\Facility\Http\Requests\FacilityBookingRequest;

use Modules\Facility\Repositories\FacilityBookingRepository;
use Modules\Facility\Repositories\FacilityRepository;
use App\Repositories\MailQueueRepository;
use Modules\Facility\Repositories\FacilityServiceDataRepository;

class FacilityBookingController extends Controller
{
    protected $facilityBookingRepository;
    protected $facilityRepository;
    private $mailQueueRepository;
    protected $facilityServiceDataRepository;

    public function __construct(
        FacilityBookingRepository $facilityBookingRepository,
        FacilityRepository $facilityRepository,
        MailQueueRepository $mailQueueRepository,
        FacilityServiceDataRepository $facilityServiceDataRepository
    ) {
        $this->helperService = new HelperService();
        $this->facilityBookingRepository = $facilityBookingRepository;
        $this->facilityRepository = $facilityRepository;
        $this->mailQueueRepository = $mailQueueRepository;
        $this->facilityServiceDataRepository = $facilityServiceDataRepository;
    }
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        $input['active'] = true;
        $allocatedFacilities = $this->facilityBookingRepository->getAllocatedFacility($input);
        return view('facility::FacilityUser.booking', compact('allocatedFacilities'));
    }

    public function getAllocatedServices(Request $request)
    {
        $input = $request->all();
        $input['active'] = true;
        return $this->facilityBookingRepository->getAllocatedServices($input);
    }

    /**
     * Get booking data.
     * @return Response
     */
    public function bookingData(FacilityBookingFilterRequest $request)
    {
        $result = [];
        $result['displayFormat'] = [];
        $result['status_code'] = 200;
        $requestInputs = [];
        $facility = $this->facilityRepository->getById($request->facility_id);
        if (!empty($facility) && !empty($facility->facilitydata)) {
            $requestInputs = $request->all();
            $requestInputs['startDate'] = date('Y-m-d', strtotime($request->input('booking_date')));
            $endDateCount = $facility->facilitydata->booking_window - 1;
            $requestInputs['endDate'] = date('Y-m-d', strtotime('+' . $endDateCount . ' day', strtotime(date('Y-m-d'))));
            $requestInputs['is_admin'] = false;

            $result = $this->facilityBookingRepository->setSlotFormat($requestInputs, $facility);
        } else {
            $result['message'] = "Facility/Service not found.";
            $result['status_code'] = 404;
        }
        return $result;
    }

    /**
     * Store a newly created resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function bookFacility(FacilityBookingRequest $request)
    {
        try {
            DB::beginTransaction();

            $intervelMinutes =  (float)$request->input('intervel') * 60;
            $intervel = '+' . $intervelMinutes . ' minutes';
            if ($request->input('intervel') >= 24) {
                $inputs['booking_date_start'] =  $request->input('booking_date') . ' ' . date("H:i:s", strtotime('00:00:00'));
                $inputs['booking_date_end'] = $request->input('booking_date') . ' ' . date('H:i:s', strtotime('23:45:00'));
            } else {
                $inputs['booking_date_start'] =  $request->input('booking_date') . ' ' . date("H:i:s", strtotime($request->input('slotName')));
                $inputs['booking_date_end'] = $request->input('booking_date') . ' ' . date('H:i:s', strtotime($intervel, strtotime(date("H:i:s", strtotime($request->input('slotName'))))));
            }
            $inputs['model_type'] = 'Modules\Facility\Models\Facility';
            $inputs['model_id'] =  $request->input('facility_id');
            $inputs['single_service_facility'] =  $request->input('single_service_facility');
            // $facilityData = $this->facilityBookingRepository->getFacilityServiceData($inputs);
            $facilityData = $this->facilityServiceDataRepository->getActiveData($inputs);
            /**
             *If facility have service.
             *Fetching all facility service ids for finding total hours booked agenist that facility.
             */
            if ($request->has('single_service_facility') && $request->input('single_service_facility') == 0) {
                $inputs['model_type'] = 'Modules\Facility\Models\FacilityService';
                $inputs['model_id'] = $request->input('facility_service_id');
                $inputs['facility_id'] =  $request->input('facility_id');
                $inputs['facility_service_ids'] = data_get($this->facilityBookingRepository->getFacilityServices($inputs), '*.id');
            }
            //Day allocation checking.
            $allocationInputs = [];
            $allocationInputs = $inputs;
            $allocationInputs['facility_user_id'] = \Auth::guard('facilityuser')->user()->id;
            $allocationDetails = $this->facilityBookingRepository->getAllocationDetails($allocationInputs);
            $allocationDayAllList = collect($allocationDetails->facilityuserweekenddefinition);
            if (date('l', strtotime($request->input('booking_date'))) == 'Saturday' || date('l', strtotime($request->input('booking_date'))) == 'Sunday') {
                $allocationDayList = $allocationDayAllList->whereIn('day_id', [6, 7]);
            } else {
                $allocationDayList = $allocationDayAllList->whereNotIn('day_id', [6, 7]);
            }
            if (!empty($allocationDetails) && sizeof($allocationDayList) >= 1) {

                $slotAlreadyBooked = collect($this->facilityBookingRepository->slotAlreadyBookedByUser($inputs));
                $slotAlreadyBookedCount = $slotAlreadyBooked->count();
                $slotAlreadyBookedByUser = $slotAlreadyBooked->where('facility_user_id', \Auth::guard('facilityuser')->user()->id)->count();
                // $facilityServiceData = $this->facilityBookingRepository->getFacilityServiceData($inputs);
                $facilityServiceData = $this->facilityServiceDataRepository->getActiveData($inputs);
                $inputs['facility_user_id'] = \Auth::guard('facilityuser')->user()->id;
                $totalBookedHours = 0;
                $totalBookedHours = $this->userTotalBookedHours($inputs);
                $totalBookedHours = $totalBookedHours + $request->input('intervel');

                if ($facilityData->maxbooking_perday >= $totalBookedHours &&  $slotAlreadyBookedByUser == 0 && $facilityServiceData->tolerance_perslot > $slotAlreadyBookedCount) {
                    unset($inputs['facility_id']);
                    unset($inputs['servicesIds']);
                    $result = $this->facilityBookingRepository->storeBooking($inputs);
                    $this->sentMail($inputs);
                    $result['success'] = true;
                    $result['reload'] = true;
                    $result['message'] = "Successfully booked.";
                } elseif ($totalBookedHours > $facilityData->maxbooking_perday) {
                    $result['success'] = false;
                    $result['reload'] = false;
                    $result['message'] = "Facility's maximum booking per day exceeded. Try with another day.";
                } elseif ($slotAlreadyBookedByUser > 0) {
                    $result['success'] = false;
                    $result['reload'] = false;
                    $result['message'] = "You already booked this slot. Try with another slot.";
                } elseif ($slotAlreadyBookedCount >= $facilityServiceData->tolerance_perslot) {
                    $result['success'] = false;
                    $result['reload'] = false;
                    $result['message'] = "Slot already booked. Try with another slot.";
                } else {
                    $result['success'] = false;
                    $result['reload'] = false;
                    $result['message'] = "Something went wrong. Reload and try again.";
                }
            } else {
                $result['success'] = false;
                $result['reload'] = false;
                $result['message'] = "Facility/Service not allocated. Please contact administrator.";
            }



            DB::commit();
            return response()->json($result);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json($this->helperService->returnFalseResponse($e));
        }
    }

    public function userTotalBookedHours($inputs)
    {
        if (isset($inputs['single_service_facility']) && $inputs['single_service_facility'] == 0) {
            unset($inputs['model_type']);
            unset($inputs['model_id']);
        }
        $bookedEntries = $this->facilityBookingRepository->userBookedDetails($inputs);
        $totalBookedMinuts = 0;
        $bookedStartTime = 0;
        foreach ($bookedEntries as $booked) {
            $bookedStartTime = \Carbon::parse($booked->booking_date_start);
            $totalBookedMinuts = $totalBookedMinuts + $bookedStartTime->diffInMinutes(\Carbon::parse($booked->booking_date_end));
        }
        return ($totalBookedMinuts / 60);
    }

    public function sentMail($inputs)
    {
        $to = \Auth::guard('facilityuser')->user()->email;
        $model_name = 'Modules\IdsScheduling\Models\FacilityBooking';
        $subject = 'Facility Booking';
        $message =  "<p> Hi " . \Auth::guard('facilityuser')->user()->first_name . ' ' . \Auth::guard('facilityuser')->user()->last_name . ',</p>';
        $message .=  "<p> Thank you for using our booking service. Please find your booking details below. </p>";
        if ($inputs['model_type'] == "Modules\Facility\Models\FacilityService") {
            $facility = $this->facilityBookingRepository->getFacilityDetails($inputs);
            $message .= "<p> Booked Details : " . $facility->facility->facility . ' (' . $facility->service . ') </p>';
        } elseif ($inputs['model_type'] == "Modules\Facility\Models\Facility") {
            $facility = $this->facilityBookingRepository->getServiceDetails($inputs['model_id']);
            $message .= "<p> Booked Details : " . $facility->facility . ' </p>';
        } else {
        }
        $message .= "<p> Timing : " . date('l F d, Y', strtotime($inputs['booking_date_start'])) . ' ' . date("h:i A", strtotime($inputs['booking_date_start'])) . ' to ' .
            date('l F d, Y', strtotime($inputs['booking_date_end'])) . ' ' . date("h:i A", strtotime($inputs['booking_date_end'])) . ' </p>';
        return $this->mailQueueRepository->storeMail($to, $subject, $message, $model_name, null, null, null, null, null, null);
    }
}
