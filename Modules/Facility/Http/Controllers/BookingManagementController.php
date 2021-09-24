<?php

namespace Modules\Facility\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use App\Services\HelperService;
use Modules\Facility\Http\Requests\ScheduleFilterRequest;

use Modules\Facility\Repositories\FacilityRepository;
use Modules\Facility\Repositories\FacilityBookingRepository;
use App\Repositories\MailQueueRepository;
use Modules\Admin\Repositories\CustomerEmployeeAllocationRepository;
use Modules\Facility\Repositories\FacilityServiceLockdownRepository;

class BookingManagementController extends Controller
{

    protected $facilityRepository;
    protected $facilityBookingRepository;
    protected $customerEmployeeAllocationRepository;
    private $mailQueueRepository;
    protected $helperService;
    protected $facilityServiceLockdownRepository;

    public function __construct(
        FacilityRepository $facilityRepository,
        FacilityBookingRepository $facilityBookingRepository,
        CustomerEmployeeAllocationRepository $customerEmployeeAllocationRepository,
        MailQueueRepository $mailQueueRepository,
        HelperService $helperService,
        FacilityServiceLockdownRepository $facilityServiceLockdownRepository
    ) {
        $this->facilityBookingRepository = $facilityBookingRepository;
        $this->facilityRepository = $facilityRepository;
        $this->customerEmployeeAllocationRepository = $customerEmployeeAllocationRepository;
        $this->mailQueueRepository = $mailQueueRepository;
        $this->helperService = $helperService;
        $this->facilityServiceLockdownRepository = $facilityServiceLockdownRepository;
    }

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        $inputs = [];
        $inputs['customer_ids'] = $this->customerEmployeeAllocationRepository->getAllocatedCustomers(\Auth::user());
        $allocatedFacilities = $this->facilityBookingRepository->getAllCustomerFacility($inputs);
        return view('facility::schedule.booking-schedule', compact('allocatedFacilities'));
    }

    public function getFacilityServices(Request $request)
    {
        return $this->facilityBookingRepository->getFacilityServices($request->all());
    }

    /**
     * Get booking data.
     * @return Response
     */
    public function bookingData(ScheduleFilterRequest $request)
    {
        $result = [];
        $result['displayFormat'] = [];
        $result['status_code'] = 200;
        $requestInputs = [];
        $facility = $this->facilityRepository->getById($request->facility_id);
        if (!empty($facility)) {
            $requestInputs = $request->all();
            $requestInputs['startDate'] = date('Y-m-d', strtotime($request->input('booking_start_date')));
            $requestInputs['endDate'] = date('Y-m-d', strtotime($request->input('booking_end_date')));
            $requestInputs['is_admin'] = true;
            $result = $this->facilityBookingRepository->setSlotFormat($requestInputs, $facility);
        }
        return $result;
    }

    public function bookingDataRemovel(Request $request)
    {
        $facility = $this->facilityBookingRepository->getBookingById($request->input('facility_booking_id'));
        if ($facility) {
            $this->sentMail($facility);
            $removel = $facility->delete();
            $result['status'] = 200;
            $result['message'] = 'Removed booking';
            $result['success'] = true;
        } else {
            $result['status'] = 204;
            $result['message'] = 'Fail';
            $result['success'] = false;
        }
        return $result;
    }


    public function sentMail($inputs)
    {
        $to = $inputs->facilityUser->email;
        $model_name = 'Modules\IdsSchegetFacilityDetailsduling\Models\FacilityBooking';
        $subject = 'Facility Booking Canceled';
        $message =  "<p> Hi " . $inputs->facilityUser->first_name . ' ' . $inputs->facilityUser->last_name . ' ,</p>';
        $message .=  "<p> Your facility booking is Canceled. </p>";
        if ($inputs->model_type == "Modules\Facility\Models\FacilityService") {
            $serviceInput['model_id'] = $inputs->model_id;
            $facility = $this->facilityBookingRepository->getFacilityDetails($serviceInput);
            $message .= "<p> Booked Details : " . $facility->facility->facility . ' (' . $facility->service . ') </p>';
        } elseif ($inputs->model_type == "Modules\Facility\Models\Facility") {
            $facility = $this->facilityBookingRepository->getServiceDetails($inputs->model_id);
            $message .= "<p> Booked Details : " . $facility->facility . ' </p>';
        } else {
        }
        $message .= "<p> Timing : " . date('l F d, Y', strtotime($inputs->booking_date_start)) . ' ' . date("h:i A", strtotime($inputs->booking_date_start)) . ' to ' .
            date('l F d, Y', strtotime($inputs->booking_date_end)) . ' ' . date("h:i A", strtotime($inputs->booking_date_end)) . ' </p>';

        return $this->mailQueueRepository->storeMail($to, $subject, $message, $model_name, null, null, null, null, null, null);
    }
}
