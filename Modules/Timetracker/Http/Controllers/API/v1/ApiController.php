<?php

namespace Modules\Timetracker\Http\Controllers\API\v1;

use App\Helpers\S3HelperService;
use App\Http\Controllers\Controller;
use App\Repositories\AttachmentRepository;
use App\Repositories\PushNotificationRepository;
use App\Services\HelperService;
use App\Repositories\MailQueueRepository;
use Carbon\Carbon;
use Config;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\Admin\Models\Customer;
use Modules\Admin\Models\CustomerEmployeeAllocation;
use Modules\Admin\Models\CustomerQrcodeLocation;
use Modules\Admin\Models\Employee;
use Modules\Admin\Models\LeaveReason;
use Modules\Admin\Models\MobileAppSetting;
use Modules\Admin\Models\VisitorLogCustomerTemplateAllocation;
use Modules\Admin\Repositories\CpidLookupRepository;
use Modules\Admin\Repositories\CustomerEmployeeAllocationRepository;
use Modules\Admin\Repositories\CustomerIncidentSubjectAllocationRepository;
use Modules\Admin\Repositories\CustomerRepository;
use Modules\Admin\Repositories\EmployeeAllocationRepository;
use Modules\Admin\Repositories\EmployeeRatingLookupRepository;
use Modules\Admin\Repositories\EmployeeRatingPolicyRepository;
use Modules\Admin\Repositories\EmployeeWhistleblowerCategoryRepository;
use Modules\Admin\Repositories\EmployeeWhistleblowerPriorityRepository;
use Modules\Admin\Repositories\HolidayRepository;
use Modules\Admin\Repositories\IncidentPriorityLookupRepository;
use Modules\Admin\Repositories\MobileSecurityPatrolSubjectRepository;
use Modules\Admin\Repositories\PayPeriodRepository;
use Modules\Admin\Repositories\ShiftModuleRepository;
use Modules\Admin\Repositories\UserCertificateExpirySettingsRepository;
use Modules\Admin\Repositories\UserRepository;
use Modules\Admin\Repositories\VisitorLogTemplateRepository;
use Modules\Employeescheduling\Repositories\SchedulingRepository;
use Modules\Expense\Repositories\ExpenseClaimRepository;
use Modules\Expense\Repositories\MileageClaimRepository;
use Modules\Hranalytics\Models\ScheduleCustomerMultipleFillShifts;
use Modules\Hranalytics\Repositories\EmployeeDashboardRepository;
use Modules\Hranalytics\Repositories\EmployeeMapRepository;
use Modules\Hranalytics\Repositories\EmployeeWhistleblowerRepository;
use Modules\Hranalytics\Repositories\ScheduleCustomerRequirementRepository;
use Modules\Supervisorpanel\Models\IncidentReport;
use Modules\Supervisorpanel\Repositories\IncidentReportRepository;
use Modules\Supervisorpanel\Repositories\ShiftJournalRepository;
use Modules\Timetracker\Models\CandidateOpenshiftApplication;
use Modules\Timetracker\Models\LiveLocation;
use Modules\Timetracker\Models\ShiftType;
use Modules\Timetracker\Repositories\DispatchCoordinatesIdleSettingRepository;
use Modules\Timetracker\Repositories\DispatchRequestStatusRepository;
use Modules\Timetracker\Repositories\EmailRepository;
use Modules\Timetracker\Repositories\EmployeeAvailabilityRepository;
use Modules\Timetracker\Repositories\EmployeeShiftAprovalRatingRepository;
//use Modules\Vehicle\Jobs\VehicleEndOdometerUpdate;
use Modules\Timetracker\Repositories\EmployeeShiftRepository;
use Modules\Timetracker\Repositories\EmployeeTimeoffRepository;
use Modules\Timetracker\Repositories\ImageRepository;
use Modules\Timetracker\Repositories\MobileSecurityPatrolRepository;
use Modules\Timetracker\Repositories\NotificationRepository;
use Modules\Timetracker\Repositories\TimetrackerRepository;
use Modules\Timetracker\Repositories\TripRepository;
use Modules\Vehicle\Repositories\VehicleTripRepository;
use Modules\VideoPost\Models\VideoPost;
use Modules\Admin\Models\WhistleblowerStatusLookup;
use Modules\Hranalytics\Models\EmployeeWhistleblowerLogs;
use Modules\Uniform\Repositories\UniformOrderRepository;
use Modules\Uniform\Repositories\UniformProductRepository;
use Modules\Uniform\Repositories\UraTransactionRepository;
use Modules\VideoPost\Repositories\VideoPostRepository;
use Modules\Admin\Models\ComplianceExpiryAcknowledgementLogs;
use Modules\Admin\Models\TimeOffRequestTypeLookup;

class ApiController extends Controller
{

    public $successStatus = 200;
    protected $imageUploadPath;
    protected $mailQueueRepository;

    /**
     * The UserRepository instance.
     *
     * @var \App\Repositories\UserRepository
     */
    protected $employeeTimeoffRepository, $userRepository, $customerRepository,
        $employeeAllocationRepository, $employeeShiftRepository,
        $payPeriodRepository, $holidayRepository, $notificationRepository, $employeeAvailabilityRepository,
        $dispatchRequestStatusRepository, $emailRepository, $imageRepository, $incidentReportRepository,
        $customerIncidentSubjectAllocationRepository, $mobileSecurityPatrolRepository, $mobileSecurityPatrolSubjectRepository,
        $shiftModuleRepository, $attachmentRepository, $incidentPriorityLookupRepository,
        $timeTrackerRepository,
        $expenseClaimRepository, $mileageClaimRepository, $ScheduleCustomerRequirementRepository, $vehicleTripRepository,
        $visitor_log_repository, $customerEmployeeAllocationRepository, $cpidLookupRepository,
        $employeeRatingLookupRepository, $employeeRatingPolicyRepository, $employeeMapRepository, $pushNotificationRepository, $userCertificateExpirySettingsRepository,
        $employeeWhistleblowerCategoryRepository, $employeeWhistleblowerPriorityRepository, $employeeWhistleblowerRepository,
        $employeeShiftAprovalRatingRepository, $videoPostRepository, $schedulingRepository, $whistleblowerStatusLookup,
        $uniformProductRepository, $uniformOrderRepository;

    protected $employeeDashboardRepository;
    protected $uraTransactionRepository;

    /**
     * Create a new JobController instance.
     *
     * @param  \App\Repositories\JobRepository $jobRepository
     * @return void
     */
    public function __construct(
        UserRepository $userRepository,
        CustomerRepository $customerRepository,
        EmployeeAllocationRepository $employeeAllocationRepository,
        EmployeeShiftRepository $employeeShiftRepository,
        HolidayRepository $holidayRepository,
        PayPeriodRepository $payPeriodRepository,
        NotificationRepository $notificationRepository,
        EmailRepository $emailRepository,
        ImageRepository $imageRepository,
        IncidentReportRepository $incidentReportRepository,
        CustomerIncidentSubjectAllocationRepository $customerIncidentSubjectAllocationRepository,
        MobileSecurityPatrolRepository $mobileSecurityPatrolRepository,
        MobileSecurityPatrolSubjectRepository $mobileSecurityPatrolSubjectRepository,
        TripRepository $tripRepository,
        ShiftJournalRepository $shiftJournalRepository,
        EmployeeAvailabilityRepository $employeeAvailabilityRepository,
        ShiftModuleRepository $shiftModuleRepository,
        AttachmentRepository $attachmentRepository,
        IncidentPriorityLookupRepository $incidentPriorityLookupRepository,
        TimetrackerRepository $timeTrackerRepository,
        DispatchRequestStatusRepository $dispatchRequestStatusRepository,
        DispatchCoordinatesIdleSettingRepository $dispatchCoordinatesIdleSettingsRepository,
        ExpenseClaimRepository $expenseClaimRepository,
        MileageClaimRepository $mileageClaimRepository,
        ScheduleCustomerRequirementRepository $ScheduleCustomerRequirementRepository,
        VisitorLogTemplateRepository $visitor_log_repository,
        CustomerEmployeeAllocationRepository $customerEmployeeAllocationRepository,
        EmployeeTimeoffRepository $employeeTimeoffRepository,
        CpidLookupRepository $cpidLookupRepository,
        EmployeeRatingLookupRepository $employeeRatingLookupRepository,
        EmployeeRatingPolicyRepository $employeeRatingPolicyRepository,
        EmployeeMapRepository $employeeMapRepository,
        PushNotificationRepository $pushNotificationRepository,
        VehicleTripRepository $vehicleTripRepository,
        UserCertificateExpirySettingsRepository $userCertificateExpirySettingsRepository,
        EmployeeWhistleblowerCategoryRepository $employeeWhistleblowerCategoryRepository,
        EmployeeWhistleblowerPriorityRepository $employeeWhistleblowerPriorityRepository,
        EmployeeWhistleblowerRepository $employeeWhistleblowerRepository,
        EmployeeDashboardRepository $employeeDashboardRepository,
        EmployeeShiftAprovalRatingRepository $employeeShiftAprovalRatingRepository,
        VideoPost $videoPost,
        VideoPostRepository $videoPostRepository,
        S3HelperService $s3HelperService,
        SchedulingRepository $schedulingRepository,
        MailQueueRepository $mailQueueRepository,
        WhistleblowerStatusLookup $whistleblowerStatusLookup,
        UniformProductRepository $uniformProductRepository,
        UniformOrderRepository $uniformOrderRepository,
        UraTransactionRepository $uraTransactionRepository
    ) {
        $this->uraTransactionRepository = $uraTransactionRepository;
        $this->userRepository = $userRepository;
        $this->customerRepository = $customerRepository;
        $this->employeeAllocationRepository = $employeeAllocationRepository;
        $this->employeeShiftRepository = $employeeShiftRepository;
        $this->payPeriodRepository = $payPeriodRepository;
        $this->holidayRepository = $holidayRepository;
        $this->notificationRepository = $notificationRepository;
        $this->emailRepository = $emailRepository;
        $this->imageRepository = $imageRepository;
        $this->incidentReportRepository = $incidentReportRepository;
        $this->customerIncidentSubjectAllocationRepository = $customerIncidentSubjectAllocationRepository;
        $this->mobileSecurityPatrolRepository = $mobileSecurityPatrolRepository;
        $this->mobileSecurityPatrolSubjectRepository = $mobileSecurityPatrolSubjectRepository;
        $this->tripRepository = $tripRepository;
        $this->shiftJournalRepository = $shiftJournalRepository;
        $this->employeeAvailabilityRepository = $employeeAvailabilityRepository;
        $this->shiftModuleRepository = $shiftModuleRepository;
        $this->attachmentRepository = $attachmentRepository;
        $this->incidentPriorityLookupRepository = $incidentPriorityLookupRepository;
        $this->timeTrackerRepository = $timeTrackerRepository;
        $this->dispatchRequestStatusRepository = $dispatchRequestStatusRepository;
        $this->dispatchCoordinatesIdleSettingsRepository = $dispatchCoordinatesIdleSettingsRepository;
        $this->helper_service = new HelperService();
        $this->expenseClaimRepository = $expenseClaimRepository;
        $this->mileageClaimRepository = $mileageClaimRepository;
        $this->ScheduleCustomerRequirementRepository = $ScheduleCustomerRequirementRepository;
        $this->visitor_log_repository = $visitor_log_repository;

        $this->customerEmployeeAllocationRepository = $customerEmployeeAllocationRepository;
        $this->employeeTimeoffRepository = $employeeTimeoffRepository;
        $this->cpidLookupRepository = $cpidLookupRepository;
        $this->employeeRatingLookupRepository = $employeeRatingLookupRepository;
        $this->employeeRatingPolicyRepository = $employeeRatingPolicyRepository;
        $this->employeeMapRepository = $employeeMapRepository;
        $this->pushNotificationRepository = $pushNotificationRepository;
        $this->vehicleTripRepository = $vehicleTripRepository;
        $this->userCertificateExpirySettingsRepository = $userCertificateExpirySettingsRepository;
        $this->employeeWhistleblowerCategoryRepository = $employeeWhistleblowerCategoryRepository;
        $this->employeeWhistleblowerPriorityRepository = $employeeWhistleblowerPriorityRepository;
        $this->employeeWhistleblowerRepository = $employeeWhistleblowerRepository;
        $this->employeeDashboardRepository = $employeeDashboardRepository;
        $this->employeeShiftAprovalRatingRepository = $employeeShiftAprovalRatingRepository;
        $this->s3HelperService = $s3HelperService;
        $this->videopost = $videoPost;
        $this->videoPostRepository = $videoPostRepository;
        $this->schedulingRepository = $schedulingRepository;
        $this->mailQueueRepository = $mailQueueRepository;
        $this->whistleblowerStatusLookup = $whistleblowerStatusLookup;
        $this->uniformProductRepository = $uniformProductRepository;
        $this->uniformOrderRepository = $uniformOrderRepository;
    }

    /**
     * login api
     *
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        $mobile_app_settings = MobileAppSetting::first();
        $loggedInUsername = $request->get('username');
        $user = $this->userRepository->loginForApp($request);
        if ($user) {
            $content['user']['user_id'] = $user->id;
            $content['user']['full_name'] = $user->first_name . ' ' . $user->last_name;
            $content['user']['first_name'] = $user->first_name;
            $content['user']['last_name'] = $user->last_name;
            $content['user']['email'] = $user->email;
            $content['user']['phone'] = $user->employee_profile->phone;
            $content['user']['address'] = $user->employee_profile->employee_address;
            $content['user']['city'] = $user->employee_profile->employee_city;
            $content['user']['postal_code'] = $user->employee_profile->employee_postal_code;
            $content['user']['image'] = (!empty($user->employee_profile->image)) ? (url('/') . Config::get('globals.profilePicPath') . $user->employee_profile->image . "?ts=" . strtotime("now")) : "";
            $content['user']['employee_no'] = $user->employee_profile->employee_no;
            $content['token'] = $user->createToken('MyApp')->accessToken;
            $content['user']['user_role'] = $user->roles[0]->name;
            $content['user']['mobile_security_patrol_enabled'] = Auth::user()->hasPermissionTo('enable_mobile_security_patrol') ? 1 : 0;
            $content['user']['app_shift_status_enabled'] = Auth::user()->hasPermissionTo('app_enable_shift_status') ? 1 : 0;
            $content['user']['add_my_availability'] = Auth::user()->hasPermissionTo('add_my_availability') ? 1 : 0;
            $content['user']['view_employee_rating'] = Auth::user()->hasPermissionTo('employee-mapping-rating') ? 1 : 0;
            $content['user']['view_expense'] = (Auth::user()->hasPermissionTo('view_expense_in_app')) ? 1 : 0;
            $content['user']['view_employee_whistleblower'] = (Auth::user()->hasPermissionTo('show_employee_whistleblower_in_app')) ? 1 : 0;
            $content['user']['show_employee_feedback_inapp'] = (Auth::user()->hasPermissionTo('show_employee_feedback_inapp')) ? 1 : 0;
            $content['user']['view_video_post'] = (Auth::user()->hasPermissionTo('view_video_post_in_app')) ? 1 : 0;
            $content['user']['view_uniform'] = (Auth::user()->hasPermissionTo('view_uniform_in_app')) ? 1 : 0;
            $content['user']['view_chat'] = (Auth::user()->hasPermissionTo('view_chat_in_api')) ? 1 : 0;
            if ($request->get('version') != null && $request->get('version') >= Config::get('globals.mobile_app_version')) {
                $content['success'] = true;
                $content['message'] = 'ok';
                $content['loggedInUsername'] = $loggedInUsername;
                $content['code'] = $this->successStatus;
            } else {
                $content['loggedInUsername'] = $loggedInUsername;
                $content['success'] = false;
                $content['message'] = 'A new version of CGL 360 is available. Please update the app to continue using CGL 360';
                $content['code'] = 400;
            }
            return response()->json(['content' => $content], $content['code']);
        } else {
            $content['loggedInUsername'] = $loggedInUsername;
            $content['success'] = false;
            $content['message'] = 'Invalid Username or Password or Check whether the user has permissions';
            $content['code'] = 401;
            return response()->json(['content' => $content], 401);
        }
    }

    /**
     * edit employee_profile
     *
     * @return \Illuminate\Http\Response
     */
    public function editprofile(Request $request)
    {
        $content = $this->userRepository->update($request);
        return response()->json(['content' => $content], $content['code']);
    }

    /**
     *  forgot password api
     */
    public function forgotPassword(Request $request)
    {
        $content = $this->userRepository->resetPassword($request);
        return response()->json($content, $content['code']);
    }

    /**
     *  Get employee details
     */
    public function getEmployeedetails(Request $request)
    {
        $user = Auth::user()->id;
        $apiKey = config("globals.google_api_curl_key");

        $geolocarray = [];

        $employeedetails = Employee::where('user_id', $user)->first();
        $postalcode = $employeedetails->employee_postal_code;
        $address = $employeedetails->employee_address . " " . $employeedetails->employee_city;

        $url = 'https://maps.googleapis.com/maps/api/geocode/json?components=postal_code:' . $postalcode . '&key=' . $apiKey;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $responseJson = curl_exec($ch);
        curl_close($ch);
        $response = json_decode($responseJson);

        if ($response->status == 'OK') {
            $latitude = $response->results[0]->geometry->location->lat;
            $longitude = $response->results[0]->geometry->location->lng;
            $geolocarray = [$latitude, $longitude];
        } else {
            $postalurl = 'https://maps.googleapis.com/maps/api/geocode/json?address=' . urlencode($address) . '&key=' . $apiKey;
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $postalurl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            $responseJson = curl_exec($ch);
            curl_close($ch);
            $response = json_decode($responseJson);

            if ($response->status == 'OK') {
                $latitude = $response->results[0]->geometry->location->lat;
                $longitude = $response->results[0]->geometry->location->lng;
                $geolocarray = [$latitude, $longitude];
            }
        }
        return json_encode($geolocarray, true);
    }

    /**
     *  forgot password api
     */
    public function logout()
    {
        $user = Auth::user();
        $user->token()->revoke();
        Auth::guard('web')->logout();
        return response()->json(['success' => true], $this->successStatus);
    }

    /**
     * sync api
     *
     * @return \Illuminate\Http\Response
     */
    public function syncdata(Request $request)
    {
        $optimizationVersion = $request->get('optimizationVersion');
        $appSyncOptimizationVersion = 1;
        if (!($optimizationVersion === $appSyncOptimizationVersion)) {
            $content['trip_details'] = $this->tripRepository->index(10);
        }
        $content['projects'] = $this->customerRepository->getAllActiveCustomers($request);
        $content['holidays'] = $this->holidayRepository->getAll($request);
        // $content['pay_periods'] = $this->payPeriodRepository->getAllActivePayPeriods($request);
        $content['pay_periods'] = $this->payPeriodRepository->getRecentPeriods();
        // $content['incident_subjects'] = $this->incidentReportSubjectRepository->getAll();
        $content['incident_priority'] = $this->incidentPriorityLookupRepository->getAll();

        $content['mobile_security_patrol_subjects'] = $this->mobileSecurityPatrolSubjectRepository->getAll();
        $content['mobile_security_patrol_time_interval'] = MobileAppSetting::first()->time_interval;
        $content['mobile_security_patrol_speed_limit'] = MobileAppSetting::first()->speed_limit;
        $content['keymanagement_mobile_app_image_limit'] = MobileAppSetting::first()->key_management_module_image_limit;
        $content['dispatch_request_status'] = $this->dispatchRequestStatusRepository->getAll();

        //$content['totalRecords'] = $this->customerRepository->getAllActiveCustomersCount();

        $content['shift_types'] = ShiftType::select('id', 'name')->get();
        $content['success'] = true;
        $content['message'] = 'ok';
        $content['code'] = $this->successStatus;

        return response()->json(['content' => $content], $content['code']);
    }

    /**
     * Get Open Shift
     *
     */
    public function setOpenShift(Request $request)
    {
        $userid = Auth::user()->id;
        $index = $request->index;
        $result = $request->results;
        $company = $request->companyresults;
        $location = $request->mylocation;
        $latitude = $location[0];
        $longitude = $location[1];
        //dd($result[$index]["timingid"]);
        //$shiftid = $result[$index]["shiftid"];
        $shiftid = $result["shiftid"];

        $customerid = $company["clientid"];
        $startdate = date("Y-m-d", strtotime($result["startdate"]));

        $enddate = date("Y-m-d", strtotime($result["enddate"]));
        $starttime = date("h:i A", strtotime($result["startdate"] . " " . $result["timingfrom"]));
        $endtime = date("h:i A", strtotime($result["startdate"] . " " . $result["timingtill"]));

        $openshifts = $company["openshift"];
        $address = $company["address"];
        $siterate = $company["site_rate"];
        $lineardistance = $company["distance"];
        $actualdistance = $company["distance"];
        if (isset($result["multifillid"])) {
            $timingid = $result["multifillid"];
        } else {
            $timingid = null;
        }

        $sitenotes = $company["notes"];
        $status = false;
        $valuearray = [
            "shiftid" => $shiftid, "customerid" => $customerid, "startdate" => $startdate, "enddate" => $enddate,
            "starttime" => $starttime, "endtime" => $endtime, "openshifts" => $openshifts, "address" => $address, "siterate" => $siterate,
            "lineardistance" => $lineardistance, "multifillid" => $timingid, "actualdistance" => $actualdistance, "sitenotes" => $sitenotes, "status" => $status, "latitude" => $latitude, "longitude" => $longitude,
        ];

        return $this->ScheduleCustomerRequirementRepository->submitOpenShiftApplication($valuearray, $userid, $shiftid);
    }

    public function getOpenShiftdetail(Request $request)
    {
        $shiftdataarray = null;
        $returndata = $this->ScheduleCustomerRequirementRepository->getScheduleRequirementDetails($request->id);
        $data = $returndata[0];

        $applieddates = collect($returndata[1]);
        $datapplied = $this->ScheduleCustomerRequirementRepository->getApplieddates($request->id);

        $shiftdataarray["appliedShifts"] = CandidateOpenshiftApplication::where('shiftid', $request->id)
            ->where('userid', \Auth::user()->id)
            ->pluck('multifillid')->toArray();

        $shiftschedules = $data->scheduleCustomerAllShifts;

        $Customer = Customer::find($data->customer_id);
        $openshifts = 1;
        if ($data->no_of_shifts > 0) {
            $openshifts = $data->no_of_shifts - $data->closedshifts;
        }
        $shiftdataarray["applieddates"] = $datapplied;
        $shiftdataarray["client"] = [
            "clientid" => $data->customer_id,
            "project_number" => $Customer->project_number,
            "clientname" => $Customer->client_name,
            "address" => $Customer->address . " " . $Customer->city . " " . $Customer->postal_code,
            "site_rate" => $data->site_rate,
            "distance" => $request->distance,
            "openshift" => $openshifts,
            "geo_lat" => $Customer->geo_location_lat,
            "geo_long" => $Customer->geo_location_long,
            "defaultstarttime" => "08:00",
            "defaultendtime" => "18:00",
            "notes" => $data->notes,
            "seclevel" => $data->seclevel,
        ];
        $i = 0;
        if ($data->no_of_shifts == null) {
            $datetimeposted = Carbon::parse($data->created_at);

            $shiftdataarray["shifts"][0] = [
                "shiftid" => $request->id,
                "dateposted" => $datetimeposted->format('d M Y'),
                "timeposted" => $datetimeposted->format('h:i A'),
                "startdate" => date("M d Y", strtotime($data->start_date)),
                "enddate" => date("M d Y", strtotime($data->end_date)),
                "timingid" => 0,
                "timingname" => $Customer->client_name,
                "applicationcount" => $data->applicationcount,
                "timingfrom" => "09:00",
                "timingtill" => "18:00",
                "displayable" => true,
            ];
        }

        foreach ($shiftschedules as $shifts) {
            $shifttimings = $shifts->shiftTiming;
            $datetimeposted = Carbon::parse($shifts->created_at);
            $timingdet = $shifts->shiftTiming;
            $assigned = $shifts->assigned;
            $parentId = $shifts->parent_id;
            if ($assigned < 1 && $parentId < 1) {
                $collect = $applieddates->filter(function ($user) use ($shifts) {
                    return starts_with($user, date("Y-m-d", strtotime($shifts->shift_from)));
                });

                $applicationcount = $collect->count();

                $openPositionsCount = ScheduleCustomerMultipleFillShifts::where('id', $shifts->id)
                    ->orWhere('parent_id', $shifts->id)
                    ->where('assigned_employee_id', null)
                    ->count();

                $totalNoOfShifts = ScheduleCustomerMultipleFillShifts::where('id', $shifts->id)
                    ->orWhere('parent_id', $shifts->id)
                    ->count();

                $shiftdataarray["shifts"][$i] = [
                    "shiftid" => $request->id,
                    "multifillid" => $shifts->id,
                    "dateposted" => $datetimeposted->format('d M Y'),
                    "timeposted" => $datetimeposted->format('h:i A'),
                    "startdate" => date("M d Y", strtotime($shifts->shift_from)),
                    "enddate" => date("M d Y", strtotime($shifts->shift_to)),
                    "timingid" => $timingdet->id,
                    "timingname" => $timingdet->shift_name,
                    "applicationcount" => $applicationcount,
                    "openpositions" => $openPositionsCount,
                    'totalpositions' => $totalNoOfShifts,
                    "timingfrom" => date("h:i A", strtotime($shifts->shift_from)),
                    "timingtill" => date("h:i A", strtotime($shifts->shift_to)),
                    "displayable" => $timingdet->displayable,
                ];

                $i++;
            }
        }
        return response()->json($shiftdataarray);
    }

    /**
     * Get Openshift detail view
     * @param id
     */
    public function getOpenShift(Request $request)
    {
        define('SITERATETOPFIRST', 1);
        define('SITERATEBOTTOMFIRST', 2);
        define('SITERATESHORTDISTANCEFIRST', 3);
        define('SITERATEDATEFIRST', 4);

        $params = json_decode($request, true);

        $user = Auth::user()->id;
        // $apiKey = config("globals.google_api_key");
        $apiKey = config("globals.google_api_curl_key");

        $geolocarray = [];

        $employeedetails = Employee::where('user_id', $user)->first();
        $postalcode = $employeedetails->employee_postal_code;
        $address = $employeedetails->employee_address . " " . $employeedetails->employee_city;

        $url = 'https://maps.googleapis.com/maps/api/geocode/json?components=postal_code:' . $postalcode . '&key=' . $apiKey;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $responseJson = curl_exec($ch);
        curl_close($ch);
        $response = json_decode($responseJson);

        $mylat = $mylong = null;
        if ($response->status == 'OK') {
            $mylat = $response->results[0]->geometry->location->lat;
            $mylong = $response->results[0]->geometry->location->lng;
        } else {
            $postalurl = 'https://maps.googleapis.com/maps/api/geocode/json?address=' . urlencode($address) . '&key=' . $apiKey;
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $postalurl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            $responseJson = curl_exec($ch);
            curl_close($ch);
            $response = json_decode($responseJson);

            if ($response->status == 'OK') {
                $mylat = $response->results[0]->geometry->location->lat;
                $mylong = $response->results[0]->geometry->location->lng;
            }
        }

        if ($mylat == null || $mylong == null) {
            return [];
        }

        $maxdistance = $request->get('maxDistance');
        $pageno = $request->get('pageno');
        $timestamp = time();
        $contracttype = 1;
        $minrate = $request->get('minrate');
        $orderby = $request->input('orderBy');

        $limit = 6;
        $cacheLimit = 10;
        $offset = ($limit * $pageno) - $limit;
        if ($pageno == 1) {
            //\Log::info("Processing page 1-db");

            $openshiftdata = $this->ScheduleCustomerRequirementRepository->scheduleRequirementOpenshiftList($contracttype, $mylat, $mylong, $maxdistance, $minrate, $pageno, $orderby);
            \Cache::put('openshiftdata' . Auth::user()->id, $openshiftdata, $cacheLimit);
        } else {

            if (\Cache::has('openshiftdata' . Auth::user()->id)) {
                //\Log::info("Processing page not 1 cache");
                $openshiftdata = \Cache::get('openshiftdata' . Auth::user()->id);
            } else {
                //\Log::info("Processing page not 1 - db");

                $openshiftdata = $this->ScheduleCustomerRequirementRepository->scheduleRequirementOpenshiftList($contracttype, $mylat, $mylong, $maxdistance, $minrate, $pageno, $orderby);
                \Cache::put('openshiftdata' . Auth::user()->id, $openshiftdata,  $cacheLimit);
            }
        }

        $customerdata = $openshiftdata[0];
        $totalcount = $openshiftdata[2];
        $returndatarray = [];
        $i = 0;
        if ($pageno == 1 || !\Cache::has('openshiftdatadataarray' . Auth::user()->id)) {
            //\Log::info("no cache page 1");
            foreach ($openshiftdata[1] as $openshifts) {
                $realdistance = $this->ScheduleCustomerRequirementRepository->GetDrivingDistance($mylat, $mylong, $openshifts->geo_location_lat, $openshifts->geo_location_long);
                $realdistance = $realdistance / 1000;
                $distance = $openshifts->mydis;
                if ($realdistance < 1) {
                    $realdistance = $distance;
                }
                $clientname = $openshifts->client_name;
                $address = $openshifts->address;
                if ($openshifts->city != null || $openshifts->city != "") {
                    $address .= ", " . $openshifts->city;
                }
                if ($openshifts->province != null || $openshifts->province != "") {
                    // $address .= " " . $openshifts->province;
                }
                if ($openshifts->postal_code != null || $openshifts->postal_code != "") {
                    // $address .= " " . $openshifts->postal_code;
                }
                $singlefill = $openshifts->singlefill;
                $multifill = $openshifts->multifill;
                $multipleShiftCount = $openshifts->multipleShiftCount;
                $noofshifts = $openshifts->no_of_shifts;

                if ($maxdistance > 0 && $realdistance <= $maxdistance && $realdistance >= 0 && $openshifts->site_rate >= $minrate) {
                    if ($noofshifts > 0 && $multifill == $noofshifts) {
                    } elseif ($noofshifts == null && $singlefill == 1) {
                    } elseif (($multipleShiftCount > 0) && ($multifill != $noofshifts)) {
                        $firstShiftDetail = explode("to", $openshifts->first_shift);
                        $lastShiftDetail = explode("to", $openshifts->last_shift);
                        $returndatarray[$i] = [
                            "openshiftid" => $openshifts->id,
                            "project_number" => $openshifts->project_number,
                            "cusid" => $openshifts->customer_id,
                            "cusname" => $clientname,
                            "address" => $address,
                            "province" => $openshifts->province,
                            "postal_code" => $openshifts->postal_code,
                            "city" => $openshifts->city,
                            "address_only" => $openshifts->address,
                            "siterate" => $openshifts->site_rate,
                            "shiftlength" => $openshifts->length_of_shift,
                            "lineardistance" => ceil($distance),
                            "actualdistance" => ceil($realdistance),
                            "securityclearence" => $openshifts->require_security_clearance,
                            "openshifts" => $openshifts->no_of_shifts,
                            "unAssignedShifts" => $openshifts->unassignedshifts,
                            "singlefill" => $openshifts->singlefill,
                            "first_shift" => $openshifts->first_shift,
                            "first_shift_start" => $firstShiftDetail[0],
                            "first_shift_end" => $firstShiftDetail[1],
                            "last_shift" => $openshifts->last_shift,
                            "last_shift_start" => $lastShiftDetail[0],
                            "last_shift_end" => $lastShiftDetail[1],
                            "multifill" => $openshifts->multifill,
                            "totalcount" => $totalcount,
                            "clientname" => $openshifts->client_name,
                            "distance" => $realdistance,
                            "seclevel" => $openshifts->seclevel,
                            "notes" => $openshifts->notes,
                            "multipleShiftCount" => $multipleShiftCount,
                        ];
                        $i++;
                    }
                }
            }

            if ($orderby == SITERATETOPFIRST) {
                //$returndatarray = collect($returndatarray)->sortBy('siterate')->reverse()->toArray();
            } elseif ($orderby == SITERATEBOTTOMFIRST) {
                //$returndatarray = collect($returndatarray)->sortBy('siterate')->toArray();
            } elseif ($orderby == SITERATESHORTDISTANCEFIRST) {
                //$returndatarray = collect($returndatarray)->sortBy('actualdistance')->toArray();
            } elseif ($orderby == SITERATEDATEFIRST) {
                //$returndatarray = collect($returndatarray)->sortBy('actualdistance')->toArray();
            }

            \Cache::put('openshiftdatadataarray' . Auth::user()->id, $returndatarray, $cacheLimit);
        } else {
            //\Log::info("cache page not 1");

            $returndatarray = \Cache::get('openshiftdatadataarray' . Auth::user()->id);
        }
        return array_values(array_slice($returndatarray, $offset, $limit));
    }

    /**
     * Get Openshift detail view
     * @param id
     */
    public function getOpenShiftdetailview(Request $request)
    {
        $openshiftid = $request->get('shiftid');
        $shiftdetails = $this->ScheduleCustomerRequirementRepository->getOpenShiftdetailview($openshiftid);
        $shiftdetail = [];
        $shiftdetail["client_id"] = $shiftdetails->customer_id;
        $shiftdetail["client_name"] = $shiftdetails->customer->client_name;
        $shiftdetail["start_date"] = $shiftdetails->start_date;
        $shiftdetail["end_date"] = $shiftdetails->end_date;

        $shiftdetail["start_time"] = null;
        $shiftdetail["end_time"] = null;
        $shiftdetail["no_of_shifts"] = $shiftdetails->no_of_shifts;
        $shiftdetail["client_address"] = $shiftdetails->customer->address;
        $shiftdetail["site_rate"] = $shiftdetails->site_rate;
        $shiftdetail["distance"] = $shiftdetails->site_rate;
        return response()->json($shiftdetail);
    }

    /**
     * save Employee availability : Employee availability
     *
     */
    public function setEmployeeavailability(Request $request)
    {

        $employeeid = Auth::user()->id;
        $empavailabilityarray = (array) $request->input('employeeavailability');

        $Workdays = config("globals.array_shift_day");
        $useravailability = $this->employeeAvailabilityRepository->getUseravailability();

        //$shift_timing = $this->;
        $userid = Auth::user()->id;

        try {
            DB::beginTransaction();
            $this->employeeAvailabilityRepository->setEmployeeavailable($Workdays, $empavailabilityarray);
            DB::commit();
            $content['success'] = true;
            $content['message'] = 'ok';
            $content['code'] = 200;
        } catch (\Throwable $th) {
            DB::rollback();
            throw $th;
            $content['success'] = false;
            $content['message'] = 'Something went wrong';
            $content['code'] = 406;
        }

        return response()->json(['content' => $content], $content['code']);

        return response()->json(['content' => $content], $content['code']);
    }

    /**
     * get Shift Timing : Employee availability
     *
     */
    public function getWorkdays(Request $request)
    {
        $Workdays = config("globals.array_shift_day");
        return response()->json($Workdays);
    }

    /**
     * get Shift Timing : Employee availability
     *
     */
    public function getShiftTimings(Request $request)
    {
        $shifttimingarray = $this->employeeAvailabilityRepository->getShiftTimings();
        return response()->json($shifttimingarray);
    }

    /**
     * submitreport : Guard submits his timesheet
     *
     */
    public function submitShift(Request $request)
    {
        $user = Auth::user();
        try {
            DB::beginTransaction();
            $response = $this->employeeShiftRepository->saveShiftDetails($user, $request);
            if (($response['mobileSecurityPatrol'] == true) && ($response['shift_id'] != null) && ($response['vehicle_id'] != null)) {
                Artisan::call('vehicle:vehicleodometerupdate', ['shift_id' => $response['shift_id']]);
            }
            $content['success'] = true;
            $content['message'] = 'ok';
            $content['code'] = 200;

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            $content['success'] = false;
            if ($e->getMessage() == 'No Payperiod Found') {
                $content['message'] = $e->getMessage();
            } else {
                $content['message'] = $e->getMessage() . " at " . $e->getLine() . " in " . $e->getFile();
            }
            \Log::channel('apiError')->info("API Error : " . $e->getMessage() . " at " . $e->getLine() . " in " . $e->getFile() . "\n Trace: \n" . $e->getTraceAsString());
            $content['code'] = 406;
        }
        return response()->json(['content' => $content], $content['code']);
    }

    /**
     * Submit shift journal
     *
     * @param array
     * @return json
     *
     */
    public function submitShiftJournal(Request $request)
    {
        $user = Auth::user();
        try {
            DB::beginTransaction();
            $details = $this->shiftJournalRepository->saveShiftJournalWeb($request);

            $content['success'] = true;
            $content['message'] = 'ok';
            $content['code'] = $this->successStatus;
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            $content['success'] = false;
            $content['message'] = $e->getMessage();
            $content['code'] = 406;
        }
        return response()->json(['content' => $content], $content['code']);
    }

    /**
     * Submit Shift Journals
     *
     * @param Request Json
     * @return response Json
     */
    // public function submitShiftJournal(Request $request)
    // {
    //     try {
    //         DB::beginTransaction();
    //         $shift_journal_details = json_decode($request->get('shift_journal_details'));
    //         foreach ($shift_journal_details as $key => $shift_journal) {
    //             $shiftJournal['shift_id'] = $shift_journal->shift_id;
    //             $shiftJournal['submitted_time'] = $shift_journal->time;
    //             $shiftJournal['notes'] = $shift_journal->notes;
    //             $shiftJournal['image'] = isset($shift_journal->image) ? $this->imageRepository->saveImage($shift_journal->image, $shift_journal->shift_id) : null;
    //             ShiftJournal::create($shiftJournal);
    //         }
    //         $content['success'] = true;
    //         $content['message'] = 'ok';
    //         $content['code'] = $this->successStatus;
    //         DB::commit();
    //     } catch (\Exception $e) {
    //         DB::rollBack();
    //         $content['success'] = false;
    //         $content['message'] = $e->getMessage();
    //         $content['code'] = 406;
    //     }
    //     return response()->json(['content' => $content], $content['code']);
    // }

    /**
     * accept time sheets by employee for a particuar time period
     *
     * @param type $name Description
     * @return type Description     *
     */
    public function submitTimeSheet(Request $request)
    {
        $user = Auth::user();
        $pay_period_id = $request->get('payperiodId');
        try {
            DB::beginTransaction();
            $this->employeeShiftRepository->submitShiftDetails($user, $pay_period_id);
            $content['success'] = true;
            $content['message'] = 'ok';
            $content['code'] = $this->successStatus;
            $employeeShiftPayperiods = $this->employeeShiftRepository->getEmployeeShiftPayperiod($pay_period_id, $user->id)->first();
            $this->notificationRepository->createNotification($employeeShiftPayperiods, "TIME_SHEET_SUBMITTED");
            if ($user->hasAnyPermission(['supervisor'])) {
                $request->request->add(['payPeriodId' => $pay_period_id]);
                $request->request->add(['customerId' => $employeeShiftPayperiods->customer_id]);
                $request->request->add(['employeeId' => $user->id]);
                //$this->approveTimeSheet($request);
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            $content['success'] = false;
            $content['message'] = $e->getMessage();
            $content['code'] = 406;
        }
        try {
            if (!$user->hasAnyPermission(['supervisor'])) {
                $this->emailRepository->emailSupervisor($user);
            }
        } catch (\Exception $e) {
            $content['success'] = true;
            $content['message'] = 'SMTP issues';
            $content['code'] = $this->successStatus;
        }
        return response()->json(['content' => $content], $content['code']);
    }

    /**
     * Approve a timesheet of a guard by supervisor
     *
     * @param type $name Description
     * @return type Description
     *
     */
    public function approveTimeSheet(Request $request)
    {
        try {
            DB::beginTransaction();
            $employeeShiftPayperiod = $this->employeeShiftRepository->approveShiftDetailsAndReview($request);
            foreach ($employeeShiftPayperiod->approved as $eachEmployeeShiftPayperiod) {
                $this->notificationRepository->createNotification($eachEmployeeShiftPayperiod, "TIME_SHEET_APPROVED");
                $this->emailRepository->emailAdmin($eachEmployeeShiftPayperiod);
            }
            $content['success'] = true;
            $content['message'] = 'ok';
            $content['code'] = $this->successStatus;
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            $content['success'] = false;
            $content['message'] = $e->getMessage();
            $content['code'] = 406;
        }
        return response()->json(['content' => $content], $content['code']);
    }

    /**
     * The pending request list
     *
     * @param Request $request
     */
    public function getApprovedRequests(Request $request)
    {
        try {
            $content = $employeeShiftPayperiod = $this->employeeShiftRepository->getApprovedRequests();
            $content['success'] = true;
            $content['message'] = 'ok';
            $content['code'] = $this->successStatus;
        } catch (\Exception $e) {
            $content['success'] = false;
            $content['message'] = $e->getMessage();
            $content['code'] = 406;
        }
        return response()->json(['content' => $content], $content['code']);
    }

    /**
     * Get all guards under the user
     *
     * @return \Illuminate\Http\Response
     */
    public function getGuards(Request $request)
    {
        $user = Auth::user();

        if (auth()->user()->hasAnyPermission(['supervisor'])) {
            $search_data = $this->employeeShiftRepository->getAllActiveGuards($user, $request);
            $content['guards'] = $search_data; //isset($search_data->employee_id) ? $search_data : 'No records found';
            $content['success'] = true;
            $content['message'] = 'ok';
            $content['code'] = $this->successStatus;
        } else {
            $content['success'] = false;
            $content['message'] = 'You are not a supervisor';
            $content['code'] = 401;
        }
        return response()->json(['content' => $content], $content['code']);
    }

    /**
     * Get all guards under the user
     *
     * @return \Illuminate\Http\Response
     */
    public function getGuardDetails(Request $request)
    {
        $user = Auth::user();
        $employee_id = (int) $request->get('employeeId');
        $approved = $request->get('approved');
        if ($this->employeeAllocationRepository->checkIfValid($user, $employee_id)) {
            $content['guard'] = $this->employeeShiftRepository->getAllShiftDetails($employee_id, $approved, $request);
            $content['success'] = true;
            $content['message'] = 'ok';
            $content['code'] = $this->successStatus;
        } else {
            $content['success'] = false;
            $content['message'] = 'Either the input is an Invalid employee ID or you dont have privilages to access this.';
            $content['code'] = 401;
        }
        return response()->json(['content' => $content], $content['code']);
    }

    /**
     * submit IncidentReport
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function submitIncidentReport(Request $request)
    {
        try {
            DB::beginTransaction();
            $user = Auth::user();
            $reportDetails = json_decode($request->get('reportDetails'));
            $customer_details = $this->customerRepository->getSingleCustomer((int) ($reportDetails[0]->customer_id));
            if ($customer_details == null) {
                $content['success'] = false;
                $content['message'] = 'No Customer found';
                $content['code'] = 406;
                return $content;
            }
            $current_payperiod = $this->payPeriodRepository->getCurrentPayperiod();
            if ($current_payperiod == null) {
                $content['success'] = false;
                $content['message'] = 'No current Payperiod';
                $content['code'] = 406;
                return $content;
            }
            $incident_report = $this->incidentReportRepository->storeReport($reportDetails, $user, $current_payperiod, $customer_details);
            $content['success'] = true;
            $content['message'] = 'ok';
            $content['code'] = $this->successStatus;
            DB::commit();
        } catch (\Exception $e) {
            $content['success'] = false;
            $content['message'] = $e->getMessage();
            $content['code'] = 406;
        }
        return response()->json(['content' => $content], $content['code']);
    }

    /**
     * submit Mobile security patrol
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function submitSecurityPatrol(Request $request)
    {
        try {
            DB::beginTransaction();
            $user = Auth::user();
            $security_patrol = $this->mobileSecurityPatrolRepository->storeNotes($request, $user);
            $content['success'] = true;
            $content['message'] = 'ok';
            $content['code'] = $this->successStatus;
            DB::commit();
        } catch (\Exception $e) {
            $content['success'] = false;
            $content['message'] = $e->getMessage();
            $content['code'] = 406;
        }
        return response()->json(['content' => $content], $content['code']);
    }

    public function getAddressFromLatLng()
    {
        return $this->tripRepository->getAddressFromLatLng();
    }

    /**
     * Get Customer module Details
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function getCustomerModuleDetails(Request $request)
    {

        $customer_id = (int) $request->get('customerId');
        if ($customer_id != null) {
            $content['data'] = $this->shiftModuleRepository->getAllModuleDetails($customer_id);
            $content['image_limit'] = MobileAppSetting::first()->shift_module_image_limit;
            $content['success'] = true;
            $content['message'] = 'ok';
            $content['code'] = $this->successStatus;
        } else {
            $content['success'] = false;
            $content['message'] = 'Either the input is an Invalid Customer ID or you dont have privilages to access this.';
            $content['code'] = 401;
        }
        return response()->json(['content' => $content], $content['code']);
    }

    /**
     * Get Customer user relation
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function getCustomerUserAllocation(Request $request)
    {
        $customerdata = [];
        $customer_employee_allocations = CustomerEmployeeAllocation::where('user_id', Auth::user()->id)->get();
        $i = 0;
        foreach ($customer_employee_allocations as $customeremprelations) {
            if ($customeremprelations->customer->id > 0) {
                $customerdata[$i]["id"] = $customeremprelations->customer->id;
                $customerdata[$i]["name"] = $customeremprelations->customer->client_name;
                $i++;
            }
        };
        $returndata = ["data" => $customerdata];
        echo json_encode($returndata, true);
    }

    public function getCustomerVisitorlogTemplates(Request $request)
    {
        $customer_id = $request->customer_id;
        $customertemplateallocation = VisitorLogCustomerTemplateAllocation::where('customer_id', $customer_id)->get();
        $i = 0;
        $templatelistarray = [];
        foreach ($customertemplateallocation as $templates) {
            if ($templates->id > 0) {
                $templatelistarray[$i]["id"] = $templates->template->id;
                $templatelistarray[$i]["name"] = $templates->template->template_name;
                foreach ($templates->template_feature as $temp_feature) {
                    if ($temp_feature->is_required == 0) {
                        $reqval = false;
                    } else {
                        $reqval = true;
                    }

                    if ($temp_feature->feature_name == "picture") {
                        $templatelistarray[$i]["enImageCapture"] = $reqval;
                    }
                    if ($temp_feature->feature_name == "signature") {
                        $templatelistarray[$i]["enSignature"] = $reqval;
                    }
                }
                $fieldarray = [];
                $j = 0;
                foreach ($templates->template_fields as $temp_fields) {
                    if ($temp_fields->is_required == 0) {
                        $reqval = false;
                    } else {
                        $reqval = true;
                    }
                    $templatelistarray[$i]["fields"][$j]["name"] = $temp_fields->fieldname;
                    if ($temp_fields->fieldname == "first_name") {
                        $field_type = "text";
                    } elseif ($temp_fields->fieldname == "email") {
                        $field_type = "email";
                    } elseif ($temp_fields->fieldname == "phone") {
                        $field_type = "phone";
                    } elseif ($temp_fields->fieldname == "visitor_type_id") {
                        $field_type = "radio";
                    } elseif ($temp_fields->fieldname == "checkin") {
                        $field_type = "time";
                    } else {
                        $field_type = "text";
                    }
                    $templatelistarray[$i]["fields"][$j]["type"] = $field_type;
                    $templatelistarray[$i]["fields"][$j]["label"] = $temp_fields->field_displayname;
                    $templatelistarray[$i]["fields"][$j]["mandatory"] = $reqval;
                    $templatelistarray[$i]["fields"][$j]["pattern"] = "";
                    $j++;
                }
                $i++;
            }
        }
        $templatelistarray = ["data" => $templatelistarray];
        return json_encode($templatelistarray, true);
    }

    /**
     * Get Employee time off reason
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function getEmployeetimeoffreasons(Request $request)
    {
        $user = Auth::user();
        $leaveperiod = [
            "0" => "Full Day",
            "1" => "First Half",
            "2" => "Second Half",
            "3" => "1st Quarter",
            "4" => "2nd Quarter",
            "5" => "3rd Quarter",
            "6" => "4th Quarter",
        ];
        $employeeDoj = $user->employee->employee_doj;
        $diffInMonth = \Carbon::parse($employeeDoj)->diffInMonths(\Carbon::now());
        $returnArray = [];
        $leaveReasons = TimeOffRequestTypeLookup::with(
            ["timeOffSettings" =>
            function ($q) use ($employeeDoj, $diffInMonth) {
                return $q->where("min_experience", "<=", $diffInMonth)->orderBy("min_experience", "desc")->first();
                //     ->orderBy("greater_than", "desc")->first();
            }]
        )->whereHas(
            "timeOffSettings"
        )->get();
        foreach ($leaveReasons as $leaveReason) {

            $returnArray[] = [
                'id' => ($leaveReason->timeOffSettings)[0]->id,
                'reason' => $leaveReason->request_type
            ];
        }
        // $timeoffreasons = LeaveReason::select('id', 'reason')->get();
        return json_encode([
            "reasons" => $returnArray,
            "leave_period" => $leaveperiod
        ], true);
    }

    public function storeVideo(Request $request)
    {

        try {
            \DB::beginTransaction();
            $request['user_id'] = Auth::id();
            $result = $this->attachmentRepository->saveAttachmentFile('shift-module', $request, 'video');
            \DB::commit();
            $content['data'] = $result['file_id'];
            $content['success'] = true;
            $content['message'] = 'ok';
            $content['code'] = $this->successStatus;
        } catch (\Exception $e) {
            $content['success'] = false;
            $content['message'] = 'Video not uploaded';
            $content['code'] = 401;
        }
        return response()->json(['content' => $content], $content['code']);
    }

    /**
     * store Image
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function saveFile(Request $request)
    {
        try {
            \DB::beginTransaction();
            $request['user_id'] = Auth::id();
            $result = $this->attachmentRepository->saveAttachmentFile($request->module, $request, 'file');
            \DB::commit();
            $content['data'] = $result['file_id'];
            $content['success'] = true;
            $content['message'] = 'ok';
            $content['code'] = $this->successStatus;
        } catch (\Exception $e) {
            $content['success'] = false;
            $content['message'] = 'Image not uploaded';
            $content['code'] = 401;
        }
        return response()->json(['content' => $content], $content['code']);
    }

    /**
     * submit customer module details
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function submitCustomerModuleDetails(Request $request)
    {
        try {
            DB::beginTransaction();
            $user_id = Auth::id();
            $request['user_id'] = Auth::id();
            $request['customer_id'] = $request->customerId;
            $response = $this->shiftModuleRepository->addCustomerModuleDetails($request, $user_id);
            if ($response) {
                $content['success'] = true;
                $content['message'] = 'ok';
            } else {
                $content['success'] = false;
                $content['message'] = 'Not Saved';
            }
            $content['code'] = $this->successStatus;
            DB::commit();
        } catch (\Exception $e) {
            $content['success'] = false;
            $content['message'] = $e->getMessage() . " at " . $e->getLine() . " in " . $e->getFile();
            $content['code'] = 406;
        }
        return response()->json(['content' => $content], $content['code']);
    }

    /**
     * Get User Details
     *
     * @param Request $request
     */
    public function getUserDetails(Request $request)
    {
        try {
            $userDetails = $this->userRepository->getUserDetails(Auth::id());
            //dd($userDetails);
            $profile['first_name'] = $userDetails->first_name;
            $profile['last_name'] = $userDetails->last_name;
            $profile['username'] = $userDetails->username;
            $profile['email'] = $userDetails->email;
            $profile['phone'] = $userDetails->employee->phone;
            $profile['role'] = $userDetails['roles'][0]['name'] ?? '';
            $profile['employee_no'] = $userDetails->employee->employee_no;
            $profile['phone_ext'] = $userDetails->employee->phone_ext ?? '';
            $profile['work_type'] = $userDetails->employee->work_type->type ?? '';
            $profile['employee_address'] = $userDetails->employee->employee_address ?? '';
            $profile['employee_city'] = $userDetails->employee->employee_city ?? '';
            $profile['work_email'] = $userDetails->employee->employee_work_email ?? '';
            $profile['cell_phone'] = $userDetails->employee->cell_no ?? '';
            if ($userDetails->employee->employee_vet_status == 0) {
                $status = "No";
            } elseif ($userDetails->employee->employee_vet_status == 1) {
                $status = "Yes";
            } else {
                $status = "";
            }

            $profile['employee_vet_status'] = $status;
            $profile['vet_service_number'] = $userDetails->employee->vet_service_number ?? '';
            $profile['vet_release_date'] = ($userDetails->employee->vet_release_date) ? \Carbon\Carbon::parse($userDetails->employee->vet_release_date)->format('d-m-Y') : '';
            $profile['vet_enrollment_date'] = ($userDetails->employee->vet_enrollment_date) ? \Carbon\Carbon::parse($userDetails->employee->vet_enrollment_date)->format('d-m-Y') : '';
            $profile['image'] = (!empty($userDetails->employee->image)) ? url('/') . Config::get('globals.profilePicPath') . $userDetails->employee->image : "";
            $profile['employee_postal_code'] = $userDetails->employee->employee_postal_code ?? '';
            $profile['employee_doj'] = ($userDetails->employee->employee_doj) ? \Carbon\Carbon::parse($userDetails->employee->employee_doj)->format('M d, Y') : '';
            $profile['employee_dob'] = ($userDetails->employee->employee_dob) ? \Carbon\Carbon::parse($userDetails->employee->employee_dob)->format('M d, Y') : '';
            $final_security_clearanace = $certificates = [];
            foreach ($userDetails['securityClearanceUser'] as $key => $value) {
                $security_clearance['date'] = ($value->valid_until) ? \Carbon\Carbon::parse($value->valid_until)->format('M d, Y') : '';
                $security_clearance['name'] = $value->securityClearanceLookups->security_clearance;
                $final_security_clearanace[] = $security_clearance;
            }
            foreach ($userDetails['userCertificate'] as $key => $value) {
                $user_certificate['date'] = ($value->expires_on) ? \Carbon\Carbon::parse($value->expires_on)->format('M d, Y') : '';
                $user_certificate['name'] = $value->trashedCertificateMaster->certificate_name;
                $certificates[] = $user_certificate;
            }

            $content['profile'] = $profile;
            $content['security_clearnce'] = $final_security_clearanace;
            $content['certificates'] = $certificates;
            $content['success'] = true;
            $content['message'] = 'ok';
            $content['code'] = $this->successStatus;
        } catch (\Exception $e) {
            $content['success'] = false;
            $content['message'] = $e->getMessage();
            $content['code'] = 406;
        }
        return response()->json(['content' => $content], $content['code']);
    }

    /**
     * Get Employees current shift
     *
     */
    public function getEmployeeshift(Request $request)
    {
        $employeeid = Auth::user()->id;
        $employeeshift = $this->employeeAvailabilityRepository->getEmployeeshift($employeeid);
        $lastUpdateData = $this->employeeAvailabilityRepository->getLastUpdatedDataByUser($employeeid);
        return response()->json(['availablity_data' => $employeeshift, 'last_update_details' => $lastUpdateData]);
    }

    /**
     * Get Non availability shifts
     *
     */
    public function getNonAvailability(Request $request)
    {
        $employeeid = Auth::user()->id;
        $employeeshift = $this->employeeAvailabilityRepository->getNonAvailability($employeeid);
        return response()->json($employeeshift);
    }

    /**
     * Get Non availability shifts
     *
     */
    public function getUnAvailability(Request $request)
    {
        $employeeid = Auth::user()->id;
        $employeeshift = $this->employeeAvailabilityRepository->getUnAvailability($employeeid);
        $lastUpdateData = $this->employeeAvailabilityRepository->getLastUpdatedDataByUser($employeeid);
        return response()->json(['un_availablity_data' => $employeeshift, 'last_update_details' => $lastUpdateData]);
    }

    /**
     * remove availability shifts
     *
     */
    public function removeUnAvailability(Request $request)
    {
        $employeeid = Auth::user()->id;
        $unavailabilityid = $request->get('unavailabilityid');
        $unavailable = $this->employeeAvailabilityRepository->removeUnAvailability($employeeid, $unavailabilityid);
        return $unavailable;
    }

    /**
     * setUnAvailability
     *
     */
    public function setUnAvailability(Request $request)
    {
        $employeeid = Auth::user()->id;
        $fromdate = $request->get('fromdate');
        $todate = $request->get('todate');
        $comments = $request->get('comments');
        $unavailable = $this->employeeAvailabilityRepository->saveUnAvailability($employeeid, $fromdate, $todate, $comments);
        return $unavailable;
    }

    /**
     * Api for new Shift
     *
     */
    public function startShift(Request $request)
    {
        $params = json_decode($request->getContent());
        $user = Auth::id();
        try {
            DB::beginTransaction();
            $shift_det = $this->employeeShiftRepository->startShift($user, $params);
            $content['shiftId'] = $shift_det->id;
            $content['startTime'] = $shift_det->start;
            $content['success'] = true;
            $content['message'] = 'ok';
            $content['code'] = 200;

            //$this->employeeShiftRepository->saveQRcodeSummary($params->customerId, $shift_det->id, null);
            $this->shiftModuleRepository->shiftStartModule($params->customerId, $shift_det->start, $shift_det->id);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            $content['success'] = false;
            $content['message'] = $e->getMessage();
            $content['code'] = 406;
        }
        return response()->json(['content' => $content], $content['code']);
    }

    /**
     * Api for adding meeting notes
     *
     */
    public function submitShiftNotes(Request $request)
    {

        $params = json_decode($request->getContent());
        //dd($params);
        try {
            DB::beginTransaction();
            if (isset($params->shiftId) && !empty($params->shiftId)) {
                $shiftid = $params->shiftId;
            } else {
                $shift_det = $this->employeeShiftRepository->startShift(Auth::id(), $params);
                $shiftid = $shift_det->id;
            }

            $this->employeeShiftRepository->saveShiftNotes($shiftid, $params);
            $shift = $this->employeeShiftRepository->get($shiftid);
            $content['shiftId'] = $shiftid;
            $content['startTime'] = $shift->start;
            $content['success'] = true;
            $content['message'] = 'ok';
            $content['code'] = 200;

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            $content['success'] = false;
            $content['message'] = $e->getMessage();
            $content['code'] = 406;
        }
        return response()->json(['content' => $content], $content['code']);
    }

    /**
     * Api for End Shift
     *
     */
    public function endShift(Request $request)
    {
        $params = json_decode($request->getContent());
        try {
            DB::beginTransaction();
            $user = Auth::id();
            if (isset($params->shiftId) && !empty($params->shiftId)) {
                $shiftid = $params->shiftId;
            } else {
                $shift_det = $this->employeeShiftRepository->startShift(Auth::id(), $params);
                $shiftid = $shift_det->id;
            }
            $meetingnotes = $params->meetingNotes ?? [];
            $this->employeeShiftRepository->saveMeetingNotes($shiftid, $meetingnotes);
            $end_shift = $this->employeeShiftRepository->endShift($shiftid);
            $content['endTime'] = $end_shift->end;
            $content['shiftId'] = $shiftid;
            $content['startTime'] = $end_shift->start;
            $content['success'] = true;
            $content['message'] = 'ok';
            $content['code'] = 200;

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            $content['success'] = false;
            $content['message'] = $e->getMessage();
            $content['code'] = 406;
        }
        return response()->json(['content' => $content], $content['code']);
    }

    // Shift live Location

    public function shiftLiveLocation(Request $request)
    {

        try {
            DB::beginTransaction();
            $this->timeTrackerRepository->saveShiftLiveLocation($request);
            $content['success'] = true;
            $content['message'] = 'ok';
            $content['code'] = 200;

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            $content['success'] = false;
            $content['message'] = $e->getMessage() . " at " . $e->getLine() . " in " . $e->getFile();
            $content['code'] = 406;
        }
        return response()->json(['content' => $content], $content['code']);
    }

    // its for storing request without image

    public function submitQrcodeWithShift(Request $request)
    {
        $qrDetails = json_decode($request->getContent());

        try {
            DB::beginTransaction();
            if ((isset($qrDetails[0]->shift_id)) && ($qrDetails[0]->shift_id != 0)) {
                $shiftid = $qrDetails[0]->shift_id;
            } else {
                $qrDetails[0]->customerId = $qrDetails[0]->customer_id;
                $shift_det = $this->employeeShiftRepository->startShift(Auth::id(), $qrDetails[0]);
                $shiftid = $shift_det->id;
            }

            $response = $this->employeeShiftRepository->saveQrcode($shiftid, $qrDetails);
            if ($response == 1) {
                $content['success'] = true;
                $content['message'] = 'ok';
                $content['shiftId'] = $shiftid;
            } elseif ($response == 2) {
                $content['success'] = false;
                $content['message'] = 'This QR code no longer exists';
            } else {
                $content['success'] = false;
                $content['message'] = 'Unable to save data';
            }
            $content['code'] = 200;
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            $content['success'] = false;
            $content['message'] = $e->getMessage();
            $content['code'] = 406;
        }
        return response()->json(['content' => $content], $content['code']);
    }

    /**
     * Get Customer Details
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function getCustomerDetails(Request $request)
    {
        // $customer_id = (int) $request->get('customerId');
        // $result=$this->getCustomerModuleDetails($request);
        // $decoded_result = json_decode($result);
        // $decoded_result->qrcode_location = CustomerQrcodeLocation::where('customer_id',$customer_id)->get();
        // return response()->json(['content' => $decoded_result]);
        $customer_id = (int) $request->get('customerId');
        if ($customer_id != null) {
            $content['customer'] = Customer::find($customer_id)->load(
                'geoFenceDetails',
                'employeeCustomerSupervisor',
                'employeeCustomerAreaManager'
            );
            $content['data'] = $this->shiftModuleRepository->getAllModuleDetails($customer_id);
            $content['image_limit'] = MobileAppSetting::first()->shift_module_image_limit;
            $customer_details = Customer::where('id', $customer_id)->first();
            if ($customer_details->qr_patrol_enabled == 1) {
                $content['qr_patrol_enabled'] = true;
                $content['qrcode_location'] = CustomerQrcodeLocation::where('customer_id', $customer_id)->where('qrcode_active', 1)->get();
            }
            $content['incident_subjects'] = $this->customerIncidentSubjectAllocationRepository->getSubjectAllocationForApp($customer_id);

            $content['success'] = true;
            $content['message'] = 'ok';
            $content['code'] = $this->successStatus;
        } else {
            $content['success'] = false;
            $content['message'] = 'Either the input is an Invalid Customer ID or you dont have privilages to access this.';
            $content['code'] = 401;
        }
        return response()->json(['content' => $content], $content['code']);
    }

    // expense claim
    public function submitExpenseClaim(Request $request)
    {

        try {
            DB::beginTransaction();
            $submitExpense = $this->expenseClaimRepository->saveExpenseClaim($request);
            $content['success'] = true;
            $content['message'] = 'ok';
            $content['code'] = $this->successStatus;
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            $content['success'] = false;

            if ($e->getMessage() == 'No reporting person found') {
                $content['message'] = $e->getMessage();
            } else {
                $content['message'] = $e->getMessage() . " at " . $e->getLine() . " in " . $e->getFile();
            }
            $content['code'] = 406;
        }
        return response()->json(['content' => $content], $content['code']);
    }

    public function getExpenseClaim(Request $request)
    {
        try {
            $categoryList = $this->expenseClaimRepository->getCategoryList();
            $paymentList = $this->expenseClaimRepository->getPaymentList();
            $glCodeList = $this->expenseClaimRepository->getGlCodeList();
            $costCenterList = $this->expenseClaimRepository->getCostCenterList();
            $taxList = $this->expenseClaimRepository->getTaxList();
            $taxListLog = $this->expenseClaimRepository->getTaxListLog();

            $content['category'] = $categoryList;
            $content['payment'] = $paymentList;
            $content['glCode'] = $glCodeList;
            $content['costCenter'] = $costCenterList;
            $content['tax'] = $taxList;
            $content['taxListLog'] = $taxListLog;

            $content['success'] = true;
            $content['message'] = 'ok';
            $content['code'] = $this->successStatus;
        } catch (\Exception $e) {
            $content['success'] = false;
            $content['message'] = $e->getMessage();
            $content['code'] = 406;
        }
        return response()->json(['content' => $content], $content['code']);
    }

    // mileage claim
    public function submitMileageClaim(Request $request)
    {
        try {
            DB::beginTransaction();
            $submitExpense = $this->mileageClaimRepository->saveMileageClaims($request);
            $content['success'] = true;
            $content['message'] = 'ok';
            $content['code'] = $this->successStatus;
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            $content['success'] = false;
            if ($e->getMessage() == 'No reporting person found') {
                $content['message'] = $e->getMessage();
            } elseif ($e->getMessage() == 'Rate not found') {
                $content['message'] = $e->getMessage();
            } else {
                $content['message'] = $e->getMessage() . " at " . $e->getLine() . " in " . $e->getFile();
            }
            $content['code'] = 406;
        }
        return response()->json(['content' => $content], $content['code']);
    }

    public function getVehicleLists(Request $request)
    {
        try {
            $vehicleList = $this->mileageClaimRepository->getVehicleList();

            $content['vehicles'] = $vehicleList;
            $content['success'] = true;
            $content['message'] = 'ok';
            $content['code'] = $this->successStatus;
        } catch (\Exception $e) {
            $content['success'] = false;
            $content['message'] = $e->getMessage();
            $content['code'] = 406;
        }
        return response()->json(['content' => $content], $content['code']);
    }

    /**
     * store employee live location on MongoDB
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function storeLiveLocation(Request $request)
    {
        // Log::channel('customlog')->info('------START----- store Live Location');
        // Log::channel('customlog')->info('Inputs => ' . json_encode($request->all()));

        $inputs = json_decode(json_encode($request->all()));
        if (sizeof($inputs) >= 1) {
            try {
                DB::beginTransaction();

                foreach ($inputs as $input) {
                    $params = $input;

                    $liveLocation = new LiveLocation();
                    if (isset($input->shiftTypeId)) {
                        $liveLocation->shift_type_id = $input->shiftTypeId;
                    }
                    if (isset($input->shiftId) && !empty($input->shiftId)) {
                        $liveLocation->shift_id = $input->shiftId;
                    } else {
                        //if shift_id not exists create shift.
                        $shift_det = $this->employeeShiftRepository->startShift(Auth::id(), $params);
                        $liveLocation->shift_id = $shift_det->id;
                    }

                    $liveLocation->user_id = Auth::id();

                    if (isset($input->dispatchRequestId)) {
                        $liveLocation->dispatch_request_id = $input->dispatchRequestId;
                    } else {
                        $liveLocation->dispatch_request_id = '';
                    }
                    $liveLocation->latitude = $input->latitude;
                    $liveLocation->longitude = $input->longitude;
                    $liveLocation->device_time = $input->time;
                    $liveLocation->is_idle = 0;
                    $liveLocation->updated_at = Carbon::now();
                    $liveLocation->created_at = Carbon::now();

                    //**START** setting is_idle flag.
                    $liveLocation_last_entry = LiveLocation::where('user_id', Auth::id())
                        ->orderBy('created_at', 'desc')
                        ->first();
                    if (!empty($liveLocation_last_entry)) {
                        $distance_in_meeter = $this->helper_service->haversineGreatCircleDistance(
                            $liveLocation_last_entry->latitude,
                            $liveLocation_last_entry->longitude,
                            $input->latitude,
                            $input->longitude
                        );

                        if ($distance_in_meeter <= IDLE) {
                            $last_updated_time = Carbon::now()->diffInMinutes($liveLocation_last_entry->created_at);
                            $idle_settings = $this->dispatchCoordinatesIdleSettingsRepository->get();

                            if (!empty($idle_settings)) {
                                if ($last_updated_time >= $idle_settings->idle_time) {
                                    $liveLocation->is_idle = 1;
                                }
                            }
                        }
                    }
                    //**END** setting is_idle flag.
                    $liveLocation->save();
                    // Log::channel('customlog')->info('------STORED----- Live Location');
                }
                DB::commit();
            } catch (\Exception $e) {
                Log::channel('customlog')->info('--------END--------- ' . $e->getMessage());
            }

            $content['success'] = true;
            $content['message'] = 'Data has been successfully added';
            $content['code'] = $this->successStatus;
            $content['data'] = $liveLocation;
        } else {
            // Log::channel('customlog')->info('------MISSING----- Input data mismatching');
            $content['success'] = false;
            $content['message'] = 'Input data mismatching ';
            $content['code'] = 401;
        }
        return response()->json(['content' => $content], $content['code']);
    }

    public function getFlatRates(Request $request)
    {
        try {
            $flatRates = $this->mileageClaimRepository->getFlatRate();

            $content['flatRates'] = $flatRates;
            $content['success'] = true;
            $content['message'] = 'ok';
            $content['code'] = $this->successStatus;
        } catch (\Exception $e) {
            DB::rollBack();
            $content['success'] = false;
            $content['message'] = 'Rate not found';
            $content['code'] = 406;
        }
        return response()->json(['content' => $content], $content['code']);
    }

    public function generateMyTimeSheet(Request $request)
    {
        try {
            $payPeriodId = $request->get('payPeriodId');
            $details = $this->employeeShiftRepository->getEmployeeShiftPayperiod($payPeriodId, Auth::id());
            $timeSheetDetails = array();
            foreach ($details as $key => $each_data) {
                $each_row['assigned'] = $each_data->assigned ?? '';
                $each_row['total_regular_hours'] = $each_data->total_regular_hours ?? '';
                $each_row['total_overtime_hours'] = $each_data->total_overtime_hours ?? '';
                $each_row['total_statutory_hours'] = $each_data->total_statutory_hours ?? '';
                $each_row['start_date'] = \Carbon\Carbon::parse($each_data->payperiod->start_date)->format('d-m-Y') ?? '';
                $each_row['end_date'] = \Carbon\Carbon::parse($each_data->payperiod->end_date)->format('d-m-Y') ?? '';
                $each_row['project_number'] = $each_data->customer->project_number ?? '';
                $each_row['client_name'] = $each_data->customer->client_name ?? '';
                $timeSheetDetails[] = $each_row;
            }
            $content['timeSheetDetails'] = $timeSheetDetails;
            $content['success'] = true;
            $content['message'] = 'ok';
            $content['code'] = $this->successStatus;
        } catch (\Exception $e) {
            $content['success'] = false;
            $content['message'] = $e->getMessage() . " at " . $e->getLine() . " in " . $e->getFile();
            $content['code'] = 406;
        }
        return response()->json(['content' => $content], $content['code']);
    }

    // function for showing latest trip details

    public function latestTripDetails(Request $request)
    {

        try {
            $tripDetails = $this->tripRepository->tripDetailsLatest();

            foreach ($tripDetails as $key => $value) {
                $latestTrip['source'] = $value->starting_location;
                $latestTrip['destination'] = $value->destination;
                $latestTrip['start_time'] = ($value->created_at) ? \Carbon\Carbon::parse($value->start_time)
                    ->format('h:ia') : '';
                $latestTrip['end_time'] = ($value->end_time) ? \Carbon\Carbon::parse($value->end_time)
                    ->format('h:ia') : '';
                $latestTrip['total_km'] = $value->total_km;
                $latestTrip['created_at'] = ($value->created_at) ? \Carbon\Carbon::parse($value->created_at)
                    ->format('l, M d,Y') : '';
                $tripDetailsLatest[] = $latestTrip;
            }

            $content['trip_details'] = $tripDetailsLatest;
            $content['success'] = true;
            $content['message'] = 'ok';
            $content['code'] = $this->successStatus;
        } catch (\Exception $e) {
            DB::rollBack();
            $content['success'] = false;
            $content['message'] = 'Latest Trip not found';
            $content['code'] = 406;
        }
        return response()->json(['content' => $content], $content['code']);
    }

    /**
     * save employee time off details
     */
    public function saveEmployeeTimeOff(Request $request)
    {
        $result['success'] = false;
        try {
            $authUserObject = Auth::user();
            $request['created_by'] = $authUserObject->id;
            $request['employee_id'] = Auth::user()->id; //$authUserObject->id;
            $request['start_date'] = date('Y-m-d', strtotime($request->get('start_date')));
            $request['end_date'] = date('Y-m-d', strtotime($request->get('end_date')));
            $request['backfillstatus'] = 0;
            //            $status = $this->customerEmployeeAllocationRepository->checkUserCustomerAllocationPresence($authUserObject->id, $request->get('project_id'));
            //            if (!$status) {
            //                $result['message'] = 'Client not allotted';
            //                $result['code'] = 401;
            //                return $result;
            //            }
            DB::beginTransaction();
            $result = $this->employeeTimeoffRepository->saveEmployeeTimeOff($request->all(), true);
            DB::commit();
            return $result;
        } catch (\Exception $e) {
            DB::rollBack();
            $result['message'] = $e->getMessage();
            $result['code'] = 406;
        }
        return $result;
    }

    public function getCpidByCustomer(Request $request)
    {
        $customerId = $request['project_id'];
        $data = $this->cpidLookupRepository->getAllCpidByParameters($customerId, true);
        return json_encode($data, true);
    }

    /**
     * To get allocated customer list
     */
    public function getAllocatedCustomers()
    {
        try {
            $customers = $this->customerRepository->getProjectsDropdownList('allocated');
            $customer_array = [];
            foreach ($customers as $key => $value) {
                $object = new \stdClass();
                $object->id = $key;
                $object->name = $value;
                $customer_array[] = $object;
            }
            $content['allocated_customers'] = $customer_array;
            $content['success'] = true;
            $content['message'] = 'ok';
            $content['code'] = $this->successStatus;
        } catch (\Exception $e) {
            $content['success'] = false;
            $content['message'] = $e->getMessage();
            $content['code'] = 406;
        }
        return response()->json(['content' => $content], $content['code']);
    }

    /**
     * To get users allocated to customers and allocated users
     */
    public function getAllocatedUsers(Request $request)
    {
        $customer_id = $request->get('customer_id');
        try {
            $content['allocated_users'] = $this->customerEmployeeAllocationRepository->getUserAndCustomerAllocatedUsers($customer_id);
            $content['success'] = true;
            $content['message'] = 'ok';
            $content['code'] = $this->successStatus;
        } catch (\Exception $e) {
            $content['success'] = false;
            $content['message'] = $e->getMessage();
            $content['code'] = 406;
        }
        return response()->json(['content' => $content], $content['code']);
    }

    /**
     * To get Employee Rating Lookups
     */
    public function getEmployeeRatingLookup()
    {
        try {
            $content['employee_rating_lookup'] = $this->employeeRatingLookupRepository->getAll();
            $content['success'] = true;
            $content['message'] = 'ok';
            $content['code'] = $this->successStatus;
        } catch (\Exception $e) {
            $content['success'] = false;
            $content['message'] = $e->getMessage();
            $content['code'] = 406;
        }
        return response()->json(['content' => $content], $content['code']);
    }

    /**
     * To get rating policy and policy description
     */
    public function getRatingPolicyLookup(Request $request)
    {
        $rating_id = $request->get('rating_id');
        try {
            $content['policy_details'] = $this->employeeRatingPolicyRepository->getRatingPolicyDetails($rating_id);
            $content['success'] = true;
            $content['message'] = 'ok';
            $content['code'] = $this->successStatus;
        } catch (\Exception $e) {
            $content['success'] = false;
            $content['message'] = $e->getMessage();
            $content['code'] = 406;
        }
        return response()->json(['content' => $content], $content['code']);
    }

    /**
     * To submit employee rating
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function submitEmployeeRating(Request $request)
    {
        try {
            DB::beginTransaction();
            $request['user_id'] = Auth::user()->id;
            $response = $this->employeeMapRepository->storeEmployeeRating($request);
            if ($response) {
                $content['success'] = true;
                $content['message'] = 'ok';
            } else {
                $content['success'] = false;
                $content['message'] = 'Not Saved';
            }
            $content['code'] = $this->successStatus;
            DB::commit();
        } catch (\Exception $e) {
            $content['success'] = false;
            $content['message'] = $e->getMessage() . " at " . $e->getLine() . " in " . $e->getFile();
            $content['code'] = 406;
        }
        return response()->json(['content' => $content], $content['code']);
    }

    /**
     * To get employee rating
     */
    public function getEmployeeRating()
    {
        $userid = Auth::user()->id;
        try {
            $content['employee_ratings'] = $this->employeeMapRepository->getEmployeeRating($userid);
            $content['success'] = true;
            $content['message'] = 'ok';
            $content['code'] = $this->successStatus;
        } catch (\Exception $e) {
            $content['success'] = false;
            $content['message'] = $e->getMessage();
            $content['code'] = 406;
        }
        return response()->json(['content' => $content], $content['code']);
    }

    /**
     * To get employee rating
     */
    public function getEmployeeRatingBySupervisor()
    {
        $userid = Auth::user()->id;
        try {
            $content['user_ratings'] = $this->employeeMapRepository->getEmployeeRatingBySupervisor($userid);
            $content['success'] = true;
            $content['message'] = 'ok';
            $content['code'] = $this->successStatus;
        } catch (\Exception $e) {
            $content['success'] = false;
            $content['message'] = $e->getMessage();
            $content['code'] = 406;
        }
        return response()->json(['content' => $content], $content['code']);
    }

    /**
     * To submit employee rating response
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function submitRatingResponse(Request $request)
    {
        try {
            DB::beginTransaction();
            $request['user_id'] = Auth::user()->id;
            $response = $this->employeeMapRepository->storeRatingResponse($request);
            if ($response) {
                $content['success'] = true;
                $content['message'] = 'ok';
            } else {
                $content['success'] = false;
                $content['message'] = 'Not Saved';
            }
            $content['code'] = $this->successStatus;
            DB::commit();
        } catch (\Exception $e) {
            $content['success'] = false;
            $content['message'] = $e->getMessage() . " at " . $e->getLine() . " in " . $e->getFile();
            $content['code'] = 406;
        }
        return response()->json(['content' => $content], $content['code']);
    }

    /**
     * To get all push notifications
     */
    public function getAllPushNotifications(Request $request)
    {
        $userid = Auth::user()->id;
        try {
            $content['push_notifications'] = $this->pushNotificationRepository->getAllPushNotifications($userid, $request);
            $content['success'] = true;
            $content['message'] = 'ok';
            $content['code'] = $this->successStatus;
        } catch (\Exception $e) {
            $content['success'] = false;
            $content['message'] = $e->getMessage();
            $content['code'] = 406;
        }
        return response()->json(['content' => $content], $content['code']);
    }

    public function getCertificateExpiryReminderNotifications()
    {
        try {
            $result = $this->userCertificateExpirySettingsRepository->getCertificateExpiryDetailsByLoggedInUser();
            $content['notifications'] = $result;
            $content['success'] = true;
            $content['message'] = 'ok';
            $content['code'] = $this->successStatus;
        } catch (\Exception $e) {
            $content['success'] = false;
            $content['message'] = $e->getMessage();
            $content['code'] = 406;
        }
        return response()->json(['content' => $content], $content['code']);
    }

    public function getGuardComplianceDashboard(Request $request)
    {
        $content['success'] = false;
        $content['message'] = "Error";
        $content['code'] = 406;
        try {
            $mydata = $this->employeeDashboardRepository
                ->getEmployeeComplianceDashboardData();
            $content['success'] = true;
            $content['message'] = "success";
            $content["data"] = $mydata;
            $content['code'] = 200;
        } catch (\Throwable $th) {
            throw $th;
        }
        return response()->json(['content' => $content], $content['code']);
    }

    public function updatePushNotificationReadFlag(Request $request)
    {
        try {
            $result = $this->pushNotificationRepository->updatePushNotificationReadFlag($request);
            $content['notifications'] = $result;
            $content['success'] = true;
            $content['message'] = 'ok';
            $content['code'] = $this->successStatus;
        } catch (\Exception $e) {
            $content['success'] = false;
            $content['message'] = $e->getMessage();
            $content['code'] = 406;
        }
        return response()->json(['content' => $content], $content['code']);
    }

    public function getUnreadNotifications()
    {
        $userid = Auth::user()->id;
        try {
            $result = $this->pushNotificationRepository->getUnreadNotifications($userid);
            $content['unreadNotifications'] = $result;
            $content['success'] = true;
            $content['message'] = 'ok';
            $content['code'] = $this->successStatus;
        } catch (\Exception $e) {
            $content['success'] = false;
            $content['message'] = $e->getMessage();
            $content['code'] = 406;
        }
        return response()->json(['content' => $content], $content['code']);
    }

    /**
     * Get categories for employee whistle blower
     * @param Response array
     */
    public function getWhistleblowerCategory()
    {
        try {
            $result = $this->employeeWhistleblowerCategoryRepository->getCategoriesForApp();
            $content['category'] = $result;
            $content['success'] = true;
            $content['message'] = 'ok';
            $content['code'] = $this->successStatus;
        } catch (\Exception $e) {
            $content['success'] = false;
            $content['message'] = $e->getMessage();
            $content['code'] = 406;
        }
        return response()->json(['content' => $content], $content['code']);
    }

    /**
     * Get priorities for employee whistleblower
     * @param Response array
     */
    public function getWhistleblowerPriority()
    {
        try {
            $result = $this->employeeWhistleblowerPriorityRepository->getPrioritiesForApp();
            $content['priority'] = $result;
            $content['success'] = true;
            $content['message'] = 'ok';
            $content['code'] = $this->successStatus;
        } catch (\Exception $e) {
            $content['success'] = false;
            $content['message'] = $e->getMessage();
            $content['code'] = 406;
        }
        return response()->json(['content' => $content], $content['code']);
    }

    /**
     * Get employee rating policy for employee whistle blower
     *
     */
    public function getEmployeePolicies()
    {
        try {
            $content['policy'] = $this->employeeRatingPolicyRepository->getEmployeeRatingPolicies();
            $content['success'] = true;
            $content['message'] = 'ok';
            $content['code'] = $this->successStatus;
        } catch (\Exception $e) {
            $content['success'] = false;
            $content['message'] = $e->getMessage();
            $content['code'] = 406;
        }
        return response()->json(['content' => $content], $content['code']);
    }

    public function submitEmployeeWhistleblower(Request $request)
    {
        try {
            DB::beginTransaction();
            $request['user_id'] = Auth::user()->id;
            $user_id = $request->get('user_id');
            $user = User::find($user_id);
            $response = $this->employeeWhistleblowerRepository->submitEmployeeWhistleblowerApp($request);
            if ($response) {
                $customerName = "";
                if ($request->get('projectId') > 0) {
                    $customerDetail = Customer::find($request->get("projectId"));
                    $customerName = $customerDetail->project_number . "-" . $customerDetail->client_name;
                    EmployeeWhistleblowerLogs::create([
                        "whistle_blower_id" => $response->id,
                        "status_id" => ($request->get('status')) ?? $response->status,
                        "created_by" => \Auth::user()->id
                    ]);
                }
                $helper_variable = [
                    '{receiverFullName}' =>  \Auth::user()->getFullNameAttribute(),
                    '{loggedInUserEmployeeNumber}' => \Auth::user()->employee->employee_no,
                    '{loggedInUser}' => \Auth::user()->getFullNameAttribute(),
                    '{contractName}' => $customerName,
                    '{subject}' => $request->get('subject')
                ];
                $emailResult = $this->mailQueueRepository->prepareMailTemplate(
                    "whistle_blower_alert_email",
                    $customerDetail->id,
                    $helper_variable,
                    "Modules\Hranalytics\Models\EmployeeWhistleblower",
                    $requestor = 0,
                    $assignee = 0,
                    $from = null,
                    $cc = null,
                    $bcc = null,
                    $mail_time = null,
                    $created_by = null,
                    $attachment_id = null,
                    $toEmail = $user->email
                );
                $content['success'] = true;
                $content['message'] = 'ok';
            } else {
                $content['success'] = false;
                $content['message'] = 'Not Saved';
            }
            $content['code'] = $this->successStatus;
            DB::commit();
        } catch (\Exception $e) {
            $content['success'] = false;
            $content['message'] = $e->getMessage() . " at " . $e->getLine() . " in " . $e->getFile();
            $content['code'] = 406;
        }
        return response()->json(['content' => $content], $content['code']);
    }

    /**
     * Get MSP Trip details of a user
     *
     */
    public function getUserTripDetails()
    {
        try {
            $content['trips'] = $this->tripRepository->index(10);
            $content['success'] = true;
            $content['message'] = 'ok';
            $content['code'] = $this->successStatus;
        } catch (\Exception $e) {
            $content['success'] = false;
            $content['message'] = $e->getMessage();
            $content['code'] = 406;
        }
        return response()->json(['content' => $content], $content['code']);
    }

    /**
     * To submit timesheet approval rating response
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function submitTimesheetApprovalRatingResponse(Request $request)
    {
        try {
            DB::beginTransaction();
            $request['user_id'] = Auth::user()->id;
            $response = $this->employeeShiftAprovalRatingRepository->storeTimesheetApprovalRatingResponse($request);
            if ($response) {
                $content['success'] = true;
                $content['message'] = 'ok';
            } else {
                $content['success'] = false;
                $content['message'] = 'Not Saved';
            }
            $content['code'] = $this->successStatus;
            DB::commit();
        } catch (\Exception $e) {
            $content['success'] = false;
            $content['message'] = $e->getMessage() . " at " . $e->getLine() . " in " . $e->getFile();
            $content['code'] = 406;
        }
        return response()->json(['content' => $content], $content['code']);
    }

    public function getVideoPosts(Request $request)
    {
        $customerId = $request->get('customerId');
        $videoPostType = $request->get('videoPostType');
        if (\Auth::user()->hasAnyPermission(['admin', 'super_admin', 'view_video_post_in_app'])) {
            $videoPostDetails = $this->videopost
                ->where('customer_id', $customerId)
                ->where('type', $videoPostType)
                ->orderBy('updated_at', 'desc')->get();
        } else {
            $videoPostDetails = [];
        }
        try {
            $content['videoPostDetails'] = $videoPostDetails;
            $content['success'] = true;
            $content['message'] = 'ok';
            $content['code'] = $this->successStatus;
        } catch (\Exception $e) {
            $content['success'] = false;
            $content['message'] = $e->getMessage();
            $content['code'] = 406;
        }
        return response()->json(['content' => $content], $content['code']);
    }

    public function getVideoUrl(Request $request)
    {
        $videoPath = $request->get('videoPath');
        $userid = Auth::user()->id;
        try {
            $content['videoPath'] = $this->s3HelperService->getPresignedUrl(null, $videoPath);
            $content['viewedUserDetails'] = $this->videoPostRepository->storeViewedUserDetails($userid, $videoPath);
            $content['success'] = true;
            $content['message'] = 'ok';
            $content['code'] = $this->successStatus;
        } catch (\Exception $e) {
            $content['success'] = false;
            $content['message'] = $e->getMessage();
            $content['code'] = 406;
        }
        return response()->json(['content' => $content], $content['code']);
    }

    public function getWhistleblowerStatusLookup(Request $request)
    {

        try {
            $content['whistleblowerStatusLookup'] = $this->whistleblowerStatusLookup->get();
            $content['success'] = true;
            $content['message'] = 'ok';
            $content['code'] = $this->successStatus;
        } catch (\Exception $e) {
            $content['success'] = false;
            $content['message'] = $e->getMessage();
            $content['code'] = 406;
        }
        return response()->json(['content' => $content], $content['code']);
    }

    /**
     * Get priorities for employee whistleblower
     * @param Response array
     */
    public function getUniformData(Request $request)
    {
        try {
            $mobile_app_settings = MobileAppSetting::first();
            $result = $this->uniformProductRepository->getUniformData();
            $content['ItemType'] = $result;
            if ((Auth::user()->hasPermissionTo('view_ura_balance') == true) && ($mobile_app_settings->view_ura_balance == true)) {
                $content['view_ura_balance'] = 1;
            } else {
                $content['view_ura_balance'] = 0;
            }
            $content['success'] = true;
            $content['message'] = 'ok';
            $content['code'] = $this->successStatus;
        } catch (\Exception $e) {
            $content['success'] = false;
            $content['message'] = $e->getMessage();
            $content['code'] = 406;
        }
        return response()->json(['content' => $content], $content['code']);
    }

    public function purchaseUniform(Request $request)
    {
        $request->validate([
            'orderDetails.*.itemId' => 'required|exists:uniform_products,id',
            'orderDetails.*.sizeId' => 'nullable|exists:uniform_product_variants,id'
        ], [
            'orderDetails.*.itemId.exists' => 'Product does\'t exists',
            'orderDetails.*.sizeId' => 'Product variant does\'t exists'
        ]);

        try {
            DB::beginTransaction();
            $request['user_id'] = Auth::user()->id;
            $response = $this->uniformOrderRepository->storeUniformOrder($request->all());
            DB::commit();
            return response()->json($response, $response['code']);
        } catch (\Exception $e) {
            DB::rollBack();
            $this->helper_service->logApiError($e);
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong!'
            ]);
        }
    }

    public function uraBalanceInfo()
    {
        return response()->json([
            'content' => $this->uraTransactionRepository->getUserBalanceInfo(auth()->user()->id)
        ], 200);
    }


    /**
     * Get Incident list
     * @param Request type=1 for user incident and type=0 for all incident
     */
    public function getIncidentReports(Request $request)
    {
        return response()->json([
            'content' => $this->incidentReportRepository->getIncidentUser(auth()->user()->id, $request)
        ], 200);
    }


    /**
     * Get all Incident Status list (Open,In progress, Closed)
     */
    public function getIRStatus()
    {
        return response()->json([
            'content' => $this->incidentReportRepository->getIRStatus()
        ], 200);
    }


    /**
     * Store Incident amendment
     * @param reportId: 1,notes: notes,taskStatus: 1
     */
    public function submitAmendment(Request $request)
    {
        // $request->validate([
        //     'notes' => 'nullable|max:150'
        // ], [
        //     'notes.max' => 'Cannot exceed character length of 150'
        // ]);
        try {
            DB::beginTransaction();
            $response = $this->incidentReportRepository->submitIncidentAmendment($request);
            if ($response) {
                $content['success'] = true;
                $content['message'] = 'ok';
            } else {
                $content['success'] = false;
                $content['message'] = 'Not Saved';
            }
            $content['code'] = $this->successStatus;
            DB::commit();
        } catch (\Exception $e) {
            $content['success'] = false;
            $content['message'] = $e->getMessage() . " at " . $e->getLine() . " in " . $e->getFile();
            $content['code'] = 406;
        }
        return response()->json(['content' => $content], $content['code']);
    }

    public function storeCompilanceAcknowledgment(Request $request)
    {
        $userid = Auth::user()->id;
        try {
            DB::beginTransaction();
            if (isset($request['data'])) {
                $compilance_data = $request['data'];
                $response = ComplianceExpiryAcknowledgementLogs::create([
                    "log_data" => isset($compilance_data) ? $compilance_data : " ",
                    "created_at" => Carbon::now(),
                    "created_by" => $userid
                ]);
                if ($response) {
                    $content['success'] = true;
                    $content['message'] = 'ok';
                } else {
                    $content['success'] = false;
                    $content['message'] = 'Not Saved';
                }
            }
            $content['code'] = $this->successStatus;
            DB::commit();
        } catch (\Exception $e) {
            $content['success'] = false;
            $content['message'] = $e->getMessage() . " at " . $e->getLine() . " in " . $e->getFile();
            $content['code'] = 406;
        }
        return response()->json(['content' => $content], $content['code']);
    }



    public function getTimesheetSummary(Request $request)
    {
        try {
            $request['user_id'] = Auth::user()->id;
            $timesheetDetails = $this->employeeShiftRepository->getEmployeeShiftPayperiodDetailsByEmployeeId($request->all());
            $content['timesheetSummary'] = $timesheetDetails;
            $content['customerDetails'] =  $this->employeeShiftRepository->getTimesheetCustomerDetails($timesheetDetails);
            $content['totalHours'] =  $this->employeeShiftRepository->getTotalHours($timesheetDetails);
            $content['success'] = true;
            $content['message'] = 'ok';
            $content['code'] = $this->successStatus;
        } catch (\Exception $e) {
            $content['success'] = false;
            $content['message'] = $e->getMessage();
            $content['code'] = 406;
        }
        return response()->json(['content' => $content], $content['code']);
    }
}
