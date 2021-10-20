<?php

namespace Modules\IdsScheduling\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Repositories\MailQueueRepository;
use App\Services\HelperService;
use DateTime;
use App\Services\LocationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Modules\Admin\Models\Days;
use Modules\Admin\Repositories\IdsOfficeRepository;
use Modules\Admin\Repositories\IdsOfficeServiceAllocationRepository;
use Modules\Admin\Repositories\IdsOfficeSlotsBlocksRepositories;
use Modules\Admin\Repositories\IdsOfficeSlotsRepositories;
use Modules\Admin\Repositories\IdsPaymentReasonsRepository;
use Modules\Admin\Repositories\IdsServicesRepository;
use Modules\IdsScheduling\Http\Requests\IdsEntriesRequest;
use Modules\IdsScheduling\Repositories\IdsCustomQuestionRepository;
use Modules\IdsScheduling\Repositories\IdsEntriesRepositories;
use Modules\Admin\Repositories\IdsNoshowSettingsRepository;
use Modules\Admin\Repositories\IdsPassportPhotoServiceRepository;
use Modules\IdsScheduling\Repositories\IdsEntryAmountSplitUpRepository;
use Modules\IdsScheduling\Repositories\IdsPaymentRepository;
use Modules\IdsScheduling\Repositories\IdsTransactionRepository;
use Modules\Admin\Repositories\IdsPaymentMethodsRepository;
use Modules\IdsScheduling\Jobs\CheckPaymentStatusJob;
use Modules\IdsScheduling\Jobs\IDSRemainderEmail;

class IdsSlotBookingController extends Controller
{

    private $idsServicesRepository;
    private $idsOfficeRepository;
    private $idsOfficeSlotsRepositories;
    private $idsOfficeSlotsBlocksRepositories;
    private $idsEntriesRepositories;
    private $mailQueueRepository;
    private $idsCustomQuestionRepository;
    private $idsOfficeServiceAllocationRepository;
    private $idsPaymentReasonsRepository;
    private $idsNoshowSettingsRepository;
    private $idsPassportPhotoServiceRepository;
    private $idsEntryAmountSplitUpRepository;
    private $helperService;
    private $locationService;
    private $idsPaymentRepository;
    private $idsPaymentMethodsRepository;
    private $idsTransactionRepository;


    /**
     * Create a new Model instance.
     *
     * @param IdsServicesRepository $idsServicesRepository
     * @param IdsOfficeRepository $idsOfficeRepository
     * @param IdsOfficeSlotsRepositories $idsOfficeSlotsRepositories
     * @param IdsOfficeSlotsBlocksRepositories $idsOfficeSlotsBlocksRepositories
     * @param IdsEntriesRepositories $idsEntriesRepositories
     * @param MailQueueRepository $mailQueueRepository
     * @param IdsCustomQuestionRepository $idsCustomQuestionRepository
     * @param IdsPaymentReasonsRepository $idsPaymentReasonsRepository
     * @param IdsNoshowSettingsRepository $idsNoshowSettingsRepository
     * @param IdsPassportPhotoServiceRepository $idsPassportPhotoServiceRepository
     * @param IdsEntryAmountSplitUpRepository $idsEntryAmountSplitUpRepository
     * @param IdsPaymentMethodsRepository $idsPaymentMethodsRepository
     * @param HelperService $helperService
     * @param LocationService $locationService
     */
    public function __construct(
        IdsServicesRepository $idsServicesRepository,
        IdsOfficeRepository $idsOfficeRepository,
        IdsOfficeSlotsRepositories $idsOfficeSlotsRepositories,
        IdsOfficeSlotsBlocksRepositories $idsOfficeSlotsBlocksRepositories,
        IdsEntriesRepositories $idsEntriesRepositories,
        IdsCustomQuestionRepository $idsCustomQuestionRepository,
        IdsOfficeServiceAllocationRepository $idsOfficeServiceAllocationRepository,
        IdsNoshowSettingsRepository $idsNoshowSettingsRepository,
        IdsPassportPhotoServiceRepository $idsPassportPhotoServiceRepository,
        IdsEntryAmountSplitUpRepository $idsEntryAmountSplitUpRepository,
        HelperService $helperService,
        LocationService $locationService,
        MailQueueRepository $mailQueueRepository,
        IdsPaymentRepository $idsPaymentRepository,
        IdsPaymentMethodsRepository $idsPaymentMethodsRepository,
        IdsTransactionRepository $idsTransactionRepository,
        IdsPaymentReasonsRepository $idsPaymentReasonsRepository
    ) {
        $this->idsServicesRepository = $idsServicesRepository;
        $this->idsOfficeRepository = $idsOfficeRepository;
        $this->idsOfficeSlotsRepositories = $idsOfficeSlotsRepositories;
        $this->idsOfficeSlotsBlocksRepositories = $idsOfficeSlotsBlocksRepositories;
        $this->idsEntriesRepositories = $idsEntriesRepositories;
        $this->idsCustomQuestionRepository = $idsCustomQuestionRepository;
        $this->idsOfficeServiceAllocationRepository = $idsOfficeServiceAllocationRepository;
        $this->idsNoshowSettingsRepository = $idsNoshowSettingsRepository;
        $this->idsPassportPhotoServiceRepository = $idsPassportPhotoServiceRepository;
        $this->idsEntryAmountSplitUpRepository = $idsEntryAmountSplitUpRepository;
        $this->mailQueueRepository = $mailQueueRepository;
        $this->helperService = $helperService;
        $this->locationService = $locationService;
        $this->idsPaymentRepository = $idsPaymentRepository;
        $this->idsPaymentMethodsRepository = $idsPaymentMethodsRepository;
        $this->idsTransactionRepository = $idsTransactionRepository;
        $this->idsPaymentReasonsRepository = $idsPaymentReasonsRepository;
        $this->logger =  Log::channel('kpiLog');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // dd(now(),now()->addMinutes(15));
        // $offices = $this->idsOfficeRepository->getOffices();
        $days = Days::limit(5)->get();
        $questions = $this->idsCustomQuestionRepository->getCustomQuestionsWithOptions();
        $photoServices = $this->idsPassportPhotoServiceRepository->all();
        $paymentReasons = collect($this->idsPaymentReasonsRepository->getAll())->pluck('name','id')->toArray();
        $paymentReasons[1] = 'Other';
        return view('idsscheduling::public.slotBooking', compact('days', 'questions', 'photoServices','paymentReasons'));
    }
    /**
     * slot booling list
     */
    public function slotArea()
    {
        return view('idsscheduling::public.partials.slot-list');
    }

    /**
     * Fetch all office array.
     */
    public function getAllOffice()
    {
        return $this->idsOfficeRepository->getNameAndId();
    }

    /**
     * Fetch all office array.
     */
    public function getOfficeService($officeId)
    {
        return $this->idsServicesRepository->getByOffice($officeId);
    }

    /**
     * All office sort by pincode distance.
     * @param pincode
     * @return office object with driving distance and time.
     */

    public function getRecommendOfficeByPincode(Request $request)
    {

        $return = [];
        $inputs = [];
        $inputs['destinations'] = [];
        $pincode = $request->input('pincode');
        if ($request->has('pincode')) {
            //Finding lat and long of given pincode.
            $pincodeLatLang = $this->locationService->getLatLongByAddress($pincode);

            if ($pincodeLatLang['lat'] != null || $pincodeLatLang['long'] != null) {
                $inputs['origins'][0] = $pincodeLatLang;
                unset($inputs['origins'][0]['postal_code_address']);

                //Fetching all offices.
                $offices = $this->idsOfficeRepository->getOffices();

                foreach ($offices as $office) {
                    //Making offile lat and long as an array.
                    $destination = [];
                    if ($office->longitude != null && $office->latitude != null) {
                        $destination['lat'] = $office->latitude;
                        $destination['long'] = $office->longitude;
                        array_push($inputs['destinations'], $destination);
                    }
                    //Declare travel time and distance as null.
                    $office->travel_time_text = null;
                    $office->travel_time = null;
                    $office->distance_text = null;
                    $office->distance = null;
                }

                if (!empty($inputs['destinations']) && !empty($inputs['origins'])) {
                    //Finding distance and time.
                    $distances = $this->locationService->getDrivingDistance($inputs);

                    $offices = collect($offices);
                    foreach ($offices as $key => $office) {
                        if (!empty($office->longitude) && !empty($office->latitude)) {
                            $matrixData = $distances['distanceMatrix']->rows[0]->elements[$key];
                            if (!empty($matrixData) && $matrixData->status == 'OK') {
                                $office->distance_text = $matrixData->distance->text;
                                $office->distance = $matrixData->distance->value;
                                $office->travel_time_text = $matrixData->duration->text;
                                $office->travel_time = $matrixData->duration->value;
                            }
                        }
                    }

                    $officeRecomendations = $offices->sortBy('distance');

                    $return['pincodeLatLang'] = $pincodeLatLang;
                    $return['offices'] = [];
                    $return['recomended_office'] = [];
                    if (!empty($officeRecomendations)) {
                        // $return['offices'] = $officeRecomendations->values()->all();
                        $return['offices'] = $officeRecomendations->values()->forget(0);
                        $return['recomended_office'] = $officeRecomendations->values()->first();
                    }
                    $return['success'] = true;
                } else {
                    $return['success'] = false;
                    $return['message'] = 'Origins/destinations not avaliable';
                }
            } else {
                $return['success'] = false;
                $return['message'] = 'Pincode not valid';
            }
        } else {
            $return['success'] = false;
            $return['message'] = 'Pincode required';
        }

        return response()->json($return);
    }

    /**
     * Public : Fetch all office slot and status.
     * @param slot_booked_date,ids_office_id
     * @return object
     */
    public function getOfficeSlotDetails(Request $request)
    {
        $result = [];
        if ($request->has('ids_office_id') && $request->has('slot_booked_date')) {
            //we provide all available times 10 day prior, and 1 day after, to provide a 9 day
            $result = $this->getDateArray($request->all());
            $result['ids_office_id'] = $request->input('ids_office_id');

            //Fetching slot details along with booked and blocked details
            $result['is_admin'] = false;
            $result['slots'] = $this->idsOfficeSlotsRepositories->officeSlotDetails($result);
            unset($result['is_admin']);
        }

        return $result;
    }

    public function getOfficeSlotTimings(Request $request)
    {

        $result = $request->all();
        if ($request->has('ids_office_id') && $request->has('slot_booked_date')) {
            //we provide all available times 10 day prior, and 1 day after, to provide a 9 day
            $result = $this->getDateArray($request->all());

            // $result['start_date'] = date('Y-m-d', strtotime('-9 day', strtotime($request->has('slot_booked_date'))));
            // $result['end_date'] =  date('Y-m-d', strtotime('+10 day', strtotime($request->has('slot_booked_date'))));

            $result['ids_office_id'] = $request->input('ids_office_id');

            //Fetching slot details along with booked and blocked details
            $result['is_admin'] = false;
            // return $this->idsOfficeSlotsRepositories->getOfficeSlot($result);
            $result['daySlotDetails'] = $this->idsOfficeSlotsRepositories->getOfficeSlot($result);
            unset($result['date']);
            unset($result['display_date']);
            $slotHtml = \View::make('idsscheduling::public.partials.slots')->with(compact(['result']))->render();

            return response()->json([
                'html' => $slotHtml,
            ]);
            unset($result['is_admin']);
        }
        return $result;
    }

    /**
     * We will show 20 day window
     */
    public function getDateArray($inputs)
    {

        $date = date('Y-m-d', strtotime($inputs['slot_booked_date']));
        // $start_date = date('Y-m-d', strtotime('-9 day', strtotime($date)));
        $start_date = $date;
        // $end_date = date('Y-m-d', strtotime('+10 day', strtotime($date)));
        $end_date = date('Y-m-d', strtotime('+20 day', strtotime(date('Y-m-d'))));
        $result['start_date'] = $start_date;

        $index = 0;
        $incrementDate = $start_date;

        while (strtotime($end_date) > strtotime($incrementDate)) {
            $result['date'][$index] = $incrementDate;
            $result['end_date'] = $incrementDate;
            //Formated date for Displaying
            $result['display_date'][$index]['name'] = date('l F d, Y', strtotime($incrementDate));
            $result['display_date'][$index]['weekdys'] = false;

            if (date('l', strtotime($incrementDate)) == 'Saturday' || date('l', strtotime($incrementDate)) == 'Sunday') {
                $result['display_date'][$index]['weekdys'] = true;
            }
            $incrementDate = date('Y-m-d', strtotime('+1 day', strtotime($incrementDate)));
            $index++;
        }

        return $result;
    }

    public function lastCancelledEntry(Request $request)
    {
        $entry = $this->idsEntriesRepositories->lastCancelledEntry($request->all());
        $settings = null;
        $isPenalty = false;
        $cancelAndBookDiff = null;
        if (!empty($entry) && empty($entry->isCancelledBooking)) {
            $settings = $this->idsNoshowSettingsRepository->getLatest();
            if (!empty($settings)) {
                if ($entry->is_client_show_up == 0 && $entry->is_canceled  == 0) {
                    $isPenalty = true;
                } elseif ($entry->is_canceled == 1) {
                    $slotBookedDateTime = \Carbon::parse($entry->slot_booked_date.' '.$entry->idsOfficeSlots->start_time);
                    $cancelAndBookDiff = $slotBookedDateTime->diffInHours(\Carbon::parse($entry->deleted_at));
                    if ($cancelAndBookDiff <= $settings->notice_hours) {
                        $isPenalty = true;
                    }
                } else {
                }
            }
        }

        return [
            'entry'=>$entry,
            'settings'=>$settings,
            'cancelAndBookDiff'=>$cancelAndBookDiff,
            'isPenalty'=>$isPenalty
        ];
    }

    /**
     * Store a Slot booking.
     *
     * @param  \Modules\IdsScheduling\Http\Requests\IdsEntriesRequest  $request
     * @return \Illuminate\Http\Response
     */

    public function slotBooking(IdsEntriesRequest $request)
    {  //dd($request->all());
        try {
            \DB::beginTransaction();
            $input = $request->all();
            $photoId = $request->input('passport_photo_service_id');

            $blocked['ids_office_id'] = $request->input('ids_office_id');
            $blocked['slot_block_date'] = $request->input('slot_booked_date');
            $blocked['ids_office_slot_id'] = $request->input('ids_office_slot_id');

            //Check slot already booked/Blocked or not.
            $chechBlocked = $this->idsOfficeSlotsBlocksRepositories->checkAlreadyBlocked($blocked);
            $checkedBooked = $this->idsEntriesRepositories->checkSlotAlreadyBooked($input);
            //Fetching office,service,slot and office allocation of service
            $office = $this->idsOfficeRepository->getById($request->input('ids_office_id'));
            $service = $this->idsServicesRepository->getById($request->input('ids_service_id'));
            $slot = $this->idsOfficeSlotsRepositories->getById($request->input('ids_office_slot_id'));
            $blocked['ids_service_id'] = $request->input('ids_service_id');
            $serviceAllocation = $this->idsOfficeServiceAllocationRepository->checkOfficeServiceAllocation($blocked);
            //If slot not booked and blocked, service have office allocation.
            if ($chechBlocked == 0 && $checkedBooked == 0 && $serviceAllocation >= 1) {
                $input['given_rate'] = 0;
                //fetching lat and long of pincode.
                if (isset($input['postal_code'])) {
                    $pinCodeLatLang = $this->locationService->getLatLongByAddress($input['postal_code']);
                    if (!empty($pinCodeLatLang)) {
                        $input['latitude'] = $pinCodeLatLang['lat'];
                        $input['longitude'] = $pinCodeLatLang['long'];
                    }
                }
                $isCandidate=$request->input('is_candidate');
                $isFederalBilling=$request->input('is_federal_billing');
                if ($isCandidate == 0 && $isFederalBilling == 0) {
                    $input['is_online_payment_received'] = 0;
                }elseif($photoId != null && ($isCandidate != 0 || $isFederalBilling != 0)){
                    $input['is_online_payment_received'] = 0;
                }else{

                }
                $result = $this->idsEntriesRepositories->store($input);
                if ($result) {
                    $serviceName = $service->name;
                    // Service fee add in split up.
                    $splitUp = [];
                    $given_rate = $service->rate;
                    if ($given_rate > 0) {
                        $splitUp[0] = [
                            'type'=>1,
                            'entry_id'=>$result->id,
                            'service_id'=>$request->input('ids_service_id'),
                            'rate'=>$given_rate,
                            'tax_percentage'=>null,
                            'created_at'=>\Carbon::now(),
                            'updated_at'=>\Carbon::now()
                        ];
                    }
                    $taxAmount = 0;
                    $taxPercentage = 0;
                    $photoRate = 0;
                    // Passport photo service fee add in split up.
                    if ($request->has(['passport_photo_service_id']) && $request->filled('passport_photo_service_id')) {
                        $photoService = $this->idsPassportPhotoServiceRepository->getById($request->input('passport_photo_service_id'));
                        if (!empty($photoService)) {
                            $serviceName = $serviceName .' and '.$photoService->name;
                            $given_rate = $given_rate + $photoService->rate;
                            $photoRate = $photoService->rate;
                            $splitUp[sizeof($splitUp)] = [
                                'type'=>2,
                                'entry_id'=>$result->id,
                                'service_id'=>$request->input('passport_photo_service_id'),
                                'rate'=>$photoService->rate,
                                'tax_percentage'=>null,
                                'created_at'=>\Carbon::now(),
                                'updated_at'=>\Carbon::now()
                            ];
                        }
                    }

                    // Adding tax for service fee in split up.
                    if (!empty($service->taxMaster) && !empty($service->taxMaster->taxMasterLog)) {
                        $today = \Carbon::parse($request->input('slot_booked_date'))->format('Y-m-d');
                        $effectiveFrom = \Carbon::parse($service->taxMaster->taxMasterLog->effective_from_date)->format('Y-m-d');

                        if ($today >= $effectiveFrom) {
                            $taxAmount = ($service->taxMaster->taxMasterLog->tax_percentage / 100) * $given_rate;
                            $taxAmount = floor($taxAmount*100)/100;
                            $given_rate = $given_rate + $taxAmount;
                            $taxPercentage = $service->taxMaster->taxMasterLog->tax_percentage;
                            if ($given_rate > 0) {
                                $splitUp[sizeof($splitUp)] = [
                                    'type'=>0,
                                    'entry_id'=>$result->id,
                                    'service_id'=>null,
                                    'rate'=>$taxAmount,
                                    'tax_percentage'=>$service->taxMaster->taxMasterLog->tax_percentage,
                                    'created_at'=>\Carbon::now(),
                                    'updated_at'=>\Carbon::now()
                                ];
                            }
                        }
                    }
                    // $given_rate = floor($given_rate*100)/100;
                    $givenRateArray = explode(".",strval($given_rate));
                    if(sizeof($givenRateArray) == 2){
                        $given_rate = floatval($givenRateArray[0].'.'.substr($givenRateArray[1],0,2));
                    }

                    //Store amount split up values.
                    $this->idsEntryAmountSplitUpRepository->insert($splitUp);
                    //Update entry total fee.
                    $this->idsEntriesRepositories->updateEntry(['given_rate'=>$given_rate,'id'=>$result->id]);
                    //Store custom question answers.
                    $request->request->add(['ids_entry_id' => $result->id]);
                    $storeAnswers = $this->idsCustomQuestionRepository->saveAnswers($request->all());

                    if ($storeAnswers['showMessage'] == true) {
                        return response()->json($storeAnswers);
                    }

                    //Send email
                    if ($storeAnswers['success'] == true &&
                    $photoId == null && ($isCandidate != 0 || $isFederalBilling != 0)
                    ) {

                        $to = $request->input('email');
                        $model_name = 'Modules\IdsScheduling\Models\IdsEntries';
                        $phoneNumber = $office->phone_number;
                        if ($office->phone_number_ext) {
                            $phoneNumber .=" ext.".$office->phone_number_ext;
                        }
                        $helper_variables = array(
                            '{serviceName}' => $serviceName,
                            '{serviceRate}' => '$'.$given_rate,
                            '{bookingDate}' => date('l F d, Y', strtotime($request->input('slot_booked_date'))),
                            '{bookingTime}' => date("h:i A", strtotime($slot->start_time)),
                            '{location}' => $office->name.', '.$office->adress,
                            '{officePhoneNumber}' => $phoneNumber,
                            '{receiverFullName}'=> $request->input('first_name'). ' ' .$request->input('last_name')
                        );
                        $this->mailQueueRepository->prepareMailTemplate(
                            "ids_scheduling_booking",
                            0,
                            $helper_variables,
                            $model_name,
                            $requestor = 0,
                            $assignee = 0,
                            $from = null,
                            $cc = null,
                            $bcc = null,
                            $mail_time = null,
                            $created_by = null,
                            $attachment_id = null,
                            $to
                        );
                        $booked_date = \Carbon::parse($request->input('slot_booked_date'). ' ' .  $slot->start_time);
                        $now = \Carbon::now();
                        $diff = $booked_date->diffInHours($now);
                        // if ($diff>=4) {
                        //     IDSRemainderEmail::dispatch($result->id)->delay($booked_date->subHours(4));
                        // } else {
                        //     IDSRemainderEmail::dispatch($result->id)->delay($booked_date->subHours(2));
                        // }
                    }
                }

                $onlinePay = false;
                $onlineFee = 0;

                if ($isCandidate == 0 && $isFederalBilling == 0 && !empty($result)) {
                    $onlinePay = true;
                    $onlineFee = $given_rate;
                }
                if($photoId != null && ($isCandidate != 0 || $isFederalBilling != 0)){
                    $onlinePay = true;
                    $onlineFee = $photoRate;
                    if($taxPercentage > 0){
                        $taxFee = ($taxPercentage / 100) * $photoRate;
                        $onlineFee = $photoRate + $taxFee;
                        $onlineFeeArray = explode(".",strval($onlineFee));
                        if(sizeof($onlineFeeArray) == 2){
                            $onlineFee = floatval($onlineFeeArray[0].'.'.substr($onlineFeeArray[1],0,2));
                        }
                    }

                }
                if ($onlinePay) {
                    $paymentParams=array('id'=>$result->id,'amount'=>$onlineFee,'office_name'=>$office->name,'service_name'=>$service->name,'photoService'=> $photoService->name ?? '');
                    $session=$this->idsPaymentRepository->doPayment($request, $paymentParams);
                    $return =['sessionId' => $session->id,'message' => 'Redirect to checkout page.'];
                    $jobParams=array('entry_id'=>$result->id,'payment_intent'=>$session->payment_intent);
                    // $this->logger->info('-Before Running Job----- Check Payment Status ---- entry_id -> '.$result->id);
                    CheckPaymentStatusJob::dispatch($jobParams)->delay(now()->addMinutes(11));
                } else {
                    $return = [
                        'success' => true,
                        'modalHide' => true,
                        'reload' => false,
                        'sessionId' =>null,
                        'message' => 'Slot successfully booked.'
                    ];
                }
            } else {
                if ($chechBlocked >= 1) {
                    $messge = 'Slot blocked.Try with another slot.';
                    $reload = false;
                    $modalHide = true;
                }
                if ($checkedBooked >= 1) {
                    $messge = 'Slot already booked.Try with another slot.';
                    $reload = false;
                    $modalHide = true;
                }
                if ($serviceAllocation <= 0) {
                    $messge = 'Something went wrong.Reload and try again';
                    $reload = true;
                    $modalHide = true;
                }
                $return = [
                    'success' => false,
                    'modalHide' => $modalHide,
                    'reload' => $reload,
                    'message' => $messge,
                    'sessionId' =>null
                ];
            }

            \DB::commit();
            return response()->json($return);
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json($this->helperService->returnFalseResponse($e));
        }
    }

    /**
     * Fetch and calculate booking fee.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function feeCalculation(Request $request)
    {
        try {

            // Service fee add in split up.
            $input = $request->all();
            $service = $this->idsServicesRepository->getById($request->input('ids_service_id'));
            $splitUp = [];
            $return['service_fee'] = 0;
            $return['photo_fee'] = 0;
            $return['tax'] = 0;
            $return['photo_fee_tax'] = 0;
            $given_rate = $service->rate;
            $return['service_fee'] = floatval($service->rate);
            if ($given_rate > 0) {
                $splitUp[0] = [
                    'type'=>1,
                    'service_id'=>$request->input('ids_service_id'),
                    'rate'=>$given_rate,
                    'tax_percentage'=>null,
                    'created_at'=>\Carbon::now(),
                    'updated_at'=>\Carbon::now()
                ];
            }
            $taxAmount = 0;
            // Passport photo service fee add in split up.
            if ($request->has(['passport_photo_service_id']) && $request->filled('passport_photo_service_id')) {
                $photoService = $this->idsPassportPhotoServiceRepository->getById($request->input('passport_photo_service_id'));
                if (!empty($photoService)) {
                    $given_rate = $given_rate + $photoService->rate;
                    $return['photo_fee'] = floatval($photoService->rate);
                    $splitUp[sizeof($splitUp)] = [
                        'type'=>2,
                        'service_id'=>$request->input('passport_photo_service_id'),
                        'rate'=>$photoService->rate,
                        'tax_percentage'=>null,
                        'created_at'=>\Carbon::now(),
                        'updated_at'=>\Carbon::now()
                    ];
                }
            }

            // Adding tax for service fee in split up.
            if (!empty($service->taxMaster) && !empty($service->taxMaster->taxMasterLog)) {
                $today = \Carbon::parse($request->input('slot_booked_date'))->format('Y-m-d');
                $effectiveFrom = \Carbon::parse($service->taxMaster->taxMasterLog->effective_from_date)->format('Y-m-d');

                if ($today >= $effectiveFrom) {
                    $return['tax'] = floatval($service->taxMaster->taxMasterLog->tax_percentage);
                    if($return['photo_fee'] > 0){
                        $photoFeePlusTax = ($service->taxMaster->taxMasterLog->tax_percentage / 100) * $return['photo_fee'];
                        $photoFeePlusTax = $return['photo_fee'] + $photoFeePlusTax;
                        $photoFeePlusTaxArray = explode(".",strval($photoFeePlusTax));
                        if(sizeof($photoFeePlusTaxArray) == 2){
                            $photoFeePlusTax = floatval($photoFeePlusTaxArray[0].'.'.substr($photoFeePlusTaxArray[1],0,2));
                        }
                        $return['photo_fee_tax'] =  $photoFeePlusTax;
                    }


                    $taxAmount = ($service->taxMaster->taxMasterLog->tax_percentage / 100) * $given_rate;
                    $taxAmount = floor($taxAmount*100)/100;
                    $given_rate = $given_rate + $taxAmount;
                    if ($given_rate > 0) {
                        $splitUp[sizeof($splitUp)] = [
                            'type'=>0,
                            'service_id'=>null,
                            'rate'=>$taxAmount,
                            'tax_percentage'=>$service->taxMaster->taxMasterLog->tax_percentage,
                            'created_at'=>\Carbon::now(),
                            'updated_at'=>\Carbon::now()
                        ];
                    }
                }
            }
            // $given_rate = floor($given_rate*100)/100;
            $givenRateArray = explode(".",strval($given_rate));
            if(sizeof($givenRateArray) == 2){
                $given_rate = floatval($givenRateArray[0].'.'.substr($givenRateArray[1],0,2));
            }
            $return['given_rate'] = $given_rate;
            if($request->has('split_ups') && $request->input('split_ups') == 1){
                $return['split_ups'] = $splitUp;
            }

            return response()->json($return);
        } catch (\Exception $e) {
            return response()->json($this->helperService->returnFalseResponse($e));
        }
    }
     /**
     * Schedule IDS Booking success Mail
     */
    public function bookingScheduleMail($entryId)
    {

        $entryDetails=$this->idsEntriesRepositories->getEntryById($entryId);
        $to = $entryDetails['email'];
        $office = $this->idsOfficeRepository->getById($entryDetails['ids_office_id']);
        $service = $this->idsServicesRepository->getById($entryDetails['ids_service_id']);
        $slot = $this->idsOfficeSlotsRepositories->getById($entryDetails['ids_office_slot_id']);
        $photoService = $this->idsPassportPhotoServiceRepository->getById($entryDetails['passport_photo_service_id']);

        $model_name = 'Modules\IdsScheduling\Models\IdsEntries';
        $phoneNumber = $office->phone_number;
        if ($office->phone_number_ext) {
            $phoneNumber .=" ext.".$office->phone_number_ext;
        }
        $serviceName = $service->name;
        if (!empty($photoService)) {
            $serviceName = $serviceName .' and '.$photoService->name;
        }
        $helper_variables = array(
            '{serviceName}' => $serviceName,
            '{serviceRate}' => '$'.$entryDetails['given_rate'],
            '{bookingDate}' => date('l F d, Y', strtotime($entryDetails['slot_booked_date'])),
            '{bookingTime}' => date("h:i A", strtotime($slot->start_time)),
            '{location}' => $office->name.', '.$office->adress,
            '{officePhoneNumber}' => $phoneNumber,
            '{receiverFullName}'=> $entryDetails['first_name']. ' ' .$entryDetails['last_name']
        );
        $this->mailQueueRepository->prepareMailTemplate(
            "ids_scheduling_booking",
            0,
            $helper_variables,
            $model_name,
            $requestor = 0,
            $assignee = 0,
            $from = null,
            $cc = null,
            $bcc = null,
            $mail_time = null,
            $created_by = null,
            $attachment_id = null,
            $to
        );
        $booked_date = \Carbon::parse($entryDetails['slot_booked_date']. ' ' .  $slot->start_time);
        $now = \Carbon::now();
        $diff = $booked_date->diffInHours($now);
        // if ($diff>=4) {
        //     IDSRemainderEmail::dispatch($entryId)->delay($booked_date->subHours(4));
        // } else {
        //     IDSRemainderEmail::dispatch($entryId)->delay($booked_date->subHours(2));
        // }
        return true;
    }
    public function bookingScheduleMailBulk($entryIds)
    {
        foreach($entryIds as $entryId){
            $this->bookingScheduleMail($entryId);
            sleep(15);
        }
    }

    public function updateSlotBooking($inputs)
    {
        $paymentIntent=$inputs['payment_intent'];
        $entryId=$inputs['entry_id'];
        $paymentDetails=$this->idsPaymentRepository->getByPaymentIntent($paymentIntent);

        if ($paymentDetails->status !=1) {
            $intentDetails=$this->idsPaymentRepository->retrievePaymentIntent($paymentIntent);
            $sessionDetails=$this->idsPaymentRepository->retrieveSession($paymentDetails->transaction_id);
            $status=$intentDetails->status ?? '';
            $entryDetails=$this->idsEntriesRepositories->getEntryById($entryId);
            if ($status == 'succeeded') {
                //update payment table
                $updateArr=array('status'=>1,'end_time'=>\Carbon\Carbon::now()->format('Y-m-d H:i:s'));
                $condArr=array('payment_intent'=>$paymentIntent);
                $entriArr=array('is_online_payment_received'=>1,'id'=>$paymentDetails->entry_id);
                if(isset($sessionDetails->customer_details->email) && $sessionDetails->customer_details->email !=''){
                    $updateArr['email']=$sessionDetails->customer_details->email;
                }
                if(isset($intentDetails->charges->data[0]->balance_transaction) && $intentDetails->charges->data[0]->balance_transaction !=''){
                    $updateArr['balance_transaction_id']=$intentDetails->charges->data[0]->balance_transaction;
                    $balanceTransaction = $this->idsPaymentRepository->retrieveBalanceTransaction($intentDetails->charges->data[0]->balance_transaction);
                    if(isset($balanceTransaction->fee) && $balanceTransaction->fee !='')
                    {
                        $entriArr['online_processing_fee']=(($balanceTransaction->fee)/100);
                    }
                }
                $this->idsPaymentRepository->updatePaymentDetails($updateArr, $condArr);
                //update entry table
                $this->idsEntriesRepositories->updateEntry($entriArr);
                //store data to transaction history table
                $historyArr=array(
                    'entry_id'=>$entryId,
                    'id'=>$paymentDetails->id,
                    'amount'=>$paymentDetails->amount
                );
                $this->idsPaymentRepository->storeTransactionHistory($historyArr);
                // send mail
                $this->bookingScheduleMail($entryId);
            }elseif ($status == 'processing') {
                //If payment status is processing, Check status again after 5 minutes.
                $jobParams=array('entry_id'=>$entryId,'payment_intent'=>$intentDetails->id);
                CheckPaymentStatusJob::dispatch($jobParams)->delay(now()->addMinutes(5));
            } else {
                $cancelPayment=$this->idsPaymentRepository->cancelPaymentIntent($paymentIntent);
                if (isset($cancelPayment->status) && $cancelPayment->status=='canceled') {
                     $entryArr=array();
                     $entryArr['id'] =$entryId;
                     $entryArr['deleted_at'] = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
                     $this->idsEntriesRepositories->updateEntry($entryArr);
                     //update payment table
                     $updateArr=array('status'=>0,'deleted_at'=>\Carbon\Carbon::now()->format('Y-m-d H:i:s'));
                     $condArr=array('payment_intent'=>$paymentIntent);
                     $paymentDetails=$this->idsPaymentRepository->updatePaymentDetails($updateArr, $condArr);
                     // send mail
                     //$this->bookingCancellationMail($entryDetails); //commented as per requested by Sam to prevent confusion for clients
                }
            }
        }
    }
     /**
     * IDS Booking Cancellation Mail
     */
    public function bookingCancellationMail($entryDetails)
    {

        $to = $entryDetails['email'];
        $office = $this->idsOfficeRepository->getById($entryDetails['ids_office_id']);
        $service = $this->idsServicesRepository->getById($entryDetails['ids_service_id']);
        $slot = $this->idsOfficeSlotsRepositories->getById($entryDetails['ids_office_slot_id']);

        $model_name = 'Modules\IdsScheduling\Models\IdsEntries';
        $phoneNumber = $office->phone_number;
        if ($office->phone_number_ext) {
            $phoneNumber .=" ext.".$office->phone_number_ext;
        }

        $helper_variables = array(
            '{serviceName}'=> $service->name,
            '{bookingDate}' => date('l F d, Y', strtotime($entryDetails['slot_booked_date'])),
            '{bookingTime}' => date("h:i A", strtotime($slot->start_time)),
            '{cancelingDate}' => \Carbon\Carbon::now()->format('Y-m-d'),
            '{cancelingTime}' => \Carbon\Carbon::now()->format('H : i A'),
            '{officePhoneNumber}' => $phoneNumber,
            '{location}' => $office->name.', '.$office->adress,
            '{receiverFullName}'=> $entryDetails['first_name'].' '.$entryDetails['last_name'],
        );
        $this->mailQueueRepository->prepareMailTemplate(
            "ids_payment_incomplete",
            0,
            $helper_variables,
            $model_name,
            $requestor = 0,
            $assignee = 0,
            $from = null,
            $cc = null,
            $bcc = null,
            $mail_time = null,
            $created_by = null,
            $attachment_id = null,
            $to
        );
    }
}
