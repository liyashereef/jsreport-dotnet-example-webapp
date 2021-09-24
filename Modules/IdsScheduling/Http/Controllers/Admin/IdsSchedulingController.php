<?php

namespace Modules\IdsScheduling\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\IdsScheduling\Http\Requests\RescheduleBookingRequest;
use Modules\IdsScheduling\Models\IdsEntries;
use Modules\IdsScheduling\Models\IdsEntryAmountSplitUp;

use App\Services\HelperService;
use Modules\Admin\Repositories\IdsOfficeRepository;
use Modules\Admin\Repositories\IdsOfficeSlotsRepositories;
use Modules\IdsScheduling\Repositories\IdsEntriesRepositories;
use Modules\Admin\Repositories\IdsServicesRepository;
use Modules\Admin\Repositories\IdsOfficeSlotsBlocksRepositories;
use App\Repositories\MailQueueRepository;
use Modules\IdsScheduling\Repositories\IdsCustomQuestionRepository;
use Modules\Admin\Repositories\IdsPaymentReasonsRepository;
use Modules\IdsScheduling\Models\IdsPaymentMethods;
use Modules\Admin\Repositories\IdsPaymentMethodsRepository;
use Modules\Admin\Repositories\IdsPassportPhotoServiceRepository;
use Modules\IdsScheduling\Repositories\IdsEntryAmountSplitUpRepository;
use Modules\IdsScheduling\Repositories\IdsTransactionRepository;
use Modules\IdsScheduling\Repositories\IdsPaymentRepository;
class IdsSchedulingController extends Controller
{

    private $idsOfficeRepository;
    private $idsOfficeSlotsRepositories;
    private $idsEntriesRepositories;
    private $idsServicesRepository;
    private $idsOfficeSlotsBlocksRepositories;
    private $idsCustomQuestionRepository;
    private $mailQueueRepository;
    private $helperService;
    private $idsPaymentReasonsRepository;
    private $idsPassportPhotoServiceRepository;
    private $idsEntryAmountSplitUpRepository;
    private $idsTransactionHistoryRepository;
    private $idsPaymentRepository;
    private $idsPaymentMethodsRepository;

     /**
     * Create a new Model instance.
     *
     * @param IdsOfficeRepository $idsOfficeRepository
     * @param IdsOfficeSlotsRepositories $idsOfficeSlotsRepositories
     * @param IdsEntriesRepositories $idsEntriesRepositories
     * @param IdsServicesRepository $idsServicesRepository
     * @param IdsOfficeSlotsBlocksRepositories $idsOfficeSlotsBlocksRepositories
     * @param IdsCustomQuestionRepository $idsCustomQuestionRepository
     * @param IdsPaymentReasonsRepository $idsPaymentReasonsRepository
     * @param IdsPassportPhotoServiceRepository $idsPassportPhotoServiceRepository
     * @param IdsEntryAmountSplitUpRepository $idsEntryAmountSplitUpRepository
     * @param IdsTransactionRepository $idsTransactionHistoryRepository
     * @param IdsPaymentRepository $idsPaymentRepository
     * @param IdsPaymentMethodsRepository $idsPaymentMethodsRepository
     * @param MailQueueRepository $mailQueueRepository
     * @param HelperService $helperService
     */
    public function __construct(
        IdsOfficeRepository $idsOfficeRepository,
        IdsOfficeSlotsRepositories $idsOfficeSlotsRepositories,
        IdsEntriesRepositories $idsEntriesRepositories,
        IdsServicesRepository $idsServicesRepository,
        IdsOfficeSlotsBlocksRepositories $idsOfficeSlotsBlocksRepositories,
        IdsCustomQuestionRepository $idsCustomQuestionRepository,
        IdsPaymentReasonsRepository $idsPaymentReasonsRepository,
        IdsPassportPhotoServiceRepository $idsPassportPhotoServiceRepository,
        IdsEntryAmountSplitUpRepository $idsEntryAmountSplitUpRepository,
        IdsTransactionRepository $idsTransactionHistoryRepository,
        IdsPaymentRepository $idsPaymentRepository,
        IdsPaymentMethodsRepository $idsPaymentMethodsRepository,
        MailQueueRepository $mailQueueRepository,
        HelperService $helperService
    )
    {
        $this->idsOfficeRepository = $idsOfficeRepository;
        $this->idsOfficeSlotsRepositories = $idsOfficeSlotsRepositories;
        $this->idsEntriesRepositories = $idsEntriesRepositories;
        $this->idsServicesRepository = $idsServicesRepository;
        $this->idsOfficeSlotsBlocksRepositories = $idsOfficeSlotsBlocksRepositories;
        $this->mailQueueRepository = $mailQueueRepository;
        $this->idsCustomQuestionRepository = $idsCustomQuestionRepository;
        $this->idsPaymentReasonsRepository = $idsPaymentReasonsRepository;
        $this->idsPassportPhotoServiceRepository = $idsPassportPhotoServiceRepository;
        $this->idsEntryAmountSplitUpRepository = $idsEntryAmountSplitUpRepository;
        $this->idsTransactionHistoryRepository = $idsTransactionHistoryRepository;
        $this->idsPaymentRepository = $idsPaymentRepository;
        $this->helperService = $helperService;
        // $this->idsPaymentMethods = new IdsPaymentMethods();
        $this->idsPaymentMethods = $idsPaymentMethodsRepository;

    }

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
         $officeList = $this->idsOfficeRepository->getPermissionBaseLocationList();
         $offices = $officeList->pluck('office_name_and_address','id')->toArray();
         $paymentMethods = $this->idsPaymentMethods->getPaymentMethodsInArray();
         $paymentReasons = collect($this->idsPaymentReasonsRepository->getAll())->pluck('name','id')->toArray();
         $paymentReasons[1] = 'Other';
         $photoServices = $this->idsPassportPhotoServiceRepository->allArray()->toArray();
         return view('idsscheduling::admin.index', compact('officeList','offices','paymentMethods','paymentReasons','photoServices'));
    }

    public function getOfficeSlotTimings(Request $request){

        $result = $request->all();
        if($request->has('ids_office_id')){

            //By default, Current month data will be displayed
            if(!$request->filled('start_date')){
                $result['start_date'] = date('Y-m-01');
            }
            if(!$request->filled('end_date')){
                $result['end_date'] = date("Y-m-t", strtotime(date('Y-m-d')));
            }

            //we provide all available times 10 day prior, and 1 day after, to provide a 9 day
            $result = $this->getDateArray($result);
            $result['ids_office_id'] = $request->input('ids_office_id');

            //Fetching slot details along with booked and blocked details
            $result['is_admin'] = true;
            $result['is_calendar_api'] = false;
            // return $this->idsOfficeSlotsRepositories->getOfficeSlot($result);
            $result['daySlotDetails'] = $this->idsOfficeSlotsRepositories->getOfficeSlot($result);

            unset($result['date']);
            unset($result['display_date']);
            // $slotHtml = \View::make('idsscheduling::public.partials.slots')->with(compact(['result']))->render();

            // return response()->json([
            //             'html' => $slotHtml
            // ]);
            unset($result['is_admin']);
        }
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

    public function getBookingEntryById(Request $request){
        return $this->idsEntriesRepositories->getEntryById($request->input('ids_booking_id'));
    }
    /**
     * Admin/Employees : Fetch all office slot and status.
     * @param ids_office_id,start_date,end_date
     * @return object
     */

    public function getOfficeSlots(Request $request){

        $result = [];
        if($request->has('ids_office_id')){
            $result = $request->all();
            //By default, Current month data will be displayed
            if(!$request->filled('start_date')){
                $result['start_date'] = date('Y-m-01');
            }
            if(!$request->filled('end_date')){
                $result['end_date'] = date("Y-m-t", strtotime($result['start_date']));
            }

            // Date Array
            $result['date'][0] = $result['start_date'];
            $index = 0;
            $incrementDate = $result['start_date'];
            // $result['display_date'][0] = date('l F d, Y', strtotime($incrementDate));
            while(strtotime($result['end_date']) >= strtotime($incrementDate)){

                $result['date'][$index] = $incrementDate;
                // $result['display_date'][$index] = date('l F d, Y', strtotime($incrementDate));

                $result['display_date'][$index]['name'] = date('l F d, Y', strtotime($incrementDate));
                $result['display_date'][$index]['weekdys'] = false;
                if(date('l', strtotime($incrementDate)) == 'Saturday' || date('l', strtotime($incrementDate)) == 'Sunday'){
                    $result['display_date'][$index]['weekdys'] = true;
                }
                $incrementDate = date('Y-m-d', strtotime('+1 day', strtotime($incrementDate)));
                $index++;

            }
               //Fetching slot details along with booked and blocked details
               $result['is_admin'] = true;
               $result['slot-staus'] = ['0=Free','1=Booked','2=Blocked','3=To Be Rescheduled'];
               $result['slots'] = $this->idsOfficeSlotsRepositories->officeSlotDetails($result);

               $result['success'] = true;
               $result['message'] = "Data fetched";

            }else{
                $result['success'] = false;
                $result['message'] = "Office is required";
            }
        return $result;

    }

    /**
     * Admin/Employees : Remove/Cancel slot booking.
     * @param ids_entries_id.
     * @return message.
     */
    public function deleteSlotBooking(Request $request){

        if($request->has(['id','is_canceled'])){
            try {
                \DB::beginTransaction();

                $idsEntryDetails = $this->idsEntriesRepositories->getById($request->input('id'));
                $editUpTo = \Carbon::now()->subDays(7)->format('Y-m-d');

                if($idsEntryDetails && $idsEntryDetails->slot_booked_date >= $editUpTo){

                    $inputs['id'] = $request->input('id');
                    $inputs['is_canceled'] = $request->input('is_canceled');
                    if($request->has(['refund_status'])){
                        $inputs['refund_status'] = $request->input('refund_status');
                        $inputs['refund_initiated_by'] = \Auth::id();
                        $inputs['refund_initiated_date'] = \Carbon::now()->format('Y-m-d H:i:s');
                    }
                    $inputs['deleted_by'] = \Auth::user()->id;
                    $inputs['deleted_at'] = \Carbon::now()->format('Y-m-d H:i:s');

                    // $idsEntryDetails=$this->idsEntriesRepositories->getById($request->input('id'));
                    // $slotDetails = $this->idsOfficeSlotsRepositories->getById($idsEntryDetails->ids_office_slot_id);
                    // $office = $this->idsOfficeRepository->getById($idsEntryDetails->ids_office_id);
                    // $service = $this->idsServicesRepository->getById($idsEntryDetails->ids_service_id);
                    // $office =$idsEntryDetails->load('IdsOffice');
                    // $service =$idsEntryDetails->load('IdsServices');
                    $transaction = collect($idsEntryDetails->idsTransactionHistory)
                    ->where('transaction_type','Received')
                    ->first();
                    $inputs['balance_fee'] = 0;
                    // if($transaction && $request->input('refund_status') ==1){
                    if($transaction){
                        $inputs['balance_fee'] = 0 - $transaction->amount;
                    }

                    $update = $this->idsEntriesRepositories->updateEntry($inputs);

                    if($update){
                        //Refund mail to employee.
                        $refunMail = false;
                        if($transaction && $transaction->amount > 0 && $request->input('refund_status') ==1){
                            // $stripe =$this->idsPaymentMethods->getByShortName('STRIPE');
                            // if($stripe){
                            //     $history['ids_payment_method_id'] = $stripe->id;
                            // }
                            $history['entry_id'] = $request->input('id');
                            $history['amount'] = $transaction->amount;
                            $history['transaction_type'] = 'Refund';
                            // $history['refund_initiate_date'] = \Carbon::now();
                            $history['user_id'] = \Auth::id();
                            $history['refund_note'] = $request->input('refund_note');
                            $history['refund_status'] = $request->input('refund_status');

                            $this->idsTransactionHistoryRepository->store($history);
                            //Refund mail to employee.
                            $refunMail = true;
                        }
                        if($inputs['is_canceled'] == 1){
                            $to = $idsEntryDetails->email;
                            $model_name = 'Modules\IdsScheduling\Models\IdsEntries';
                            $phoneNumber = $idsEntryDetails->IdsOffice->phone_number;
                            if($idsEntryDetails->IdsOffice->phone_number_ext){
                                $phoneNumber .=" ext.".$idsEntryDetails->IdsOffice->phone_number_ext;
                            }
                            $helper_variables = array(
                                '{serviceName}'=> $idsEntryDetails->idsServicesWithTrashed->name,
                                '{bookingDate}' => date('l F d, Y', strtotime($idsEntryDetails->slot_booked_date)),
                                '{bookingTime}' => date("h:i A",strtotime($idsEntryDetails->IdsOfficeSlots->start_time)),
                                '{cancelingDate}' => \Carbon::now()->format('Y-m-d'),
                                '{cancelingTime}' => \Carbon::now()->format('H : i A'),
                                '{officePhoneNumber}' => $phoneNumber,
                                '{receiverFullName}'=> $idsEntryDetails->first_name.' '.$idsEntryDetails->last_name,
                                '{refundFee}' => $inputs['balance_fee'],
                            );

                            $this->mailQueueRepository->prepareMailTemplate(
                                "ids_booking_cancellation",
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

                        //Refund mail to employee.
                        if($refunMail){
                            $this->schedulingRefundMail($request->input('id'));
                        }
                    }

                }else{

                    if(empty($idsEntryDetails)){
                        $msg = array('success' => false, 'message' => 'Booking not avaliable.');
                    } elseif($idsEntryDetails->slot_booked_date <= $editUpTo){
                        $message = 'Booking removal not allowed.';
                        if($request->input('is_canceled') == 1){
                            $message = 'Booking cancel not allowed.';
                        }
                        $msg = array('success' => false, 'message' => $message);
                    }else{
                        $msg = array('success' => false, 'message' => 'Something went wrong. Please try again.');
                    }
                    return response()->json($msg);
                }

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
    *      Update
    *      Reschedule
    *      Remove old entry after reschedule.
    *      After Reschedule send mail.
     * @param  \Modules\IdsScheduling\Http\Requests\RescheduleBookingRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function updateSlotBooking(RescheduleBookingRequest $request){

        try {
            \DB::beginTransaction();
            $inputs = $request->all();
            $rescheduleUpdate = [];
            $booking = $this->idsEntriesRepositories->getById($inputs['id']);
            $editUpTo = \Carbon::now()->subDays(7)->format('Y-m-d');

            if($booking && $booking->slot_booked_date >= $editUpTo){
                $service = $this->idsServicesRepository->getById($inputs['ids_service_id']);
                // $amountSplitUps = $this->idsEntryAmountSplitUpRepository->getByEntryId($inputs['id']);

                /**start** Client show up  */
                if($request->input('is_client_show_up') == 0){
                    $inputs['ids_payment_method_id'] =null;
                    $inputs['ids_payment_reason_id'] =null;
                    $inputs['is_payment_received'] =null;
                    $inputs['payment_reason'] =null;
                    $inputs['is_mask_given'] =null;
                    $inputs['no_masks_given'] =null;
                    $inputs['refund_status'] =null;
                    $inputs['refund_initiated_by'] =null;
                    $inputs['refund_initiated_date'] =null;
                    $inputs['refund_completed_by'] =null;
                    $inputs['refund_completed_date'] =null;
                }
                if($request->input('is_client_show_up') == 1 && $request->input('balance_fee') <= 0){
                    $inputs['ids_payment_method_id'] =null;
                    $inputs['ids_payment_reason_id'] =null;
                    $inputs['is_payment_received'] =null;
                    $inputs['payment_reason'] =null;
                    if($booking->refund_status != 3 && $request->has('refund_status')){
                        $inputs['refund_status'] =null;
                        $inputs['refund_initiated_by'] =null;
                        $inputs['refund_initiated_date'] =null;
                        $inputs['refund_completed_by'] =null;
                        $inputs['refund_completed_date'] =null;
                    }
                }
                /**end** Client show up  */

                /**start** Payment received option  */
                if($request->input('is_payment_received') == 0){
                    $inputs['ids_payment_method_id'] =null;
                }elseif($request->input('is_payment_received') == 1){
                    $inputs['ids_payment_reason_id'] =null;
                    $inputs['payment_reason'] =null;
                }else{
                    $inputs['ids_payment_reason_id'] =null;
                    $inputs['ids_payment_method_id'] =null;
                    $inputs['payment_reason'] =null;
                    $inputs['is_payment_received'] =null;
                }
                if($request->input('ids_payment_reason_id') != 1){
                    $inputs['payment_reason'] =null;
                }

                if($request->input('is_candidate') != 1){
                    unset($inputs['candidate_requisition_no']);
                }
                if($request->input('is_federal_billing') != 1){
                    unset($inputs['federal_billing_employer']);
                }
                if($request->input('is_client_show_up') == 1 && $request->input('is_candidate') != 1){
                    $inputs['candidate_requisition_no'] = null;
                }
                if($request->input('is_client_show_up') == 1 && $request->input('is_federal_billing') != 1){
                    $inputs['federal_billing_employer'] = null;
                }
                if(
                    $request->input('balance_fee') < 0 &&
                    ($request->input('is_candidate') == 1 ||
                    $request->input('is_federal_billing') == 1)
                ){
                    $inputs['ids_payment_reason_id'] = $request->input('ids_payment_reason_id');
                    $inputs['payment_reason'] = $request->input('payment_reason');
                }

                /**end** Payment received option  */
                unset($inputs['id']);

                //For reschedule
                $rescheduleFlag = false;
                if( $request->has(['ids_office_slot_id','slot_booked_date']) &&
                    $request->filled('ids_office_slot_id') && $request->filled('slot_booked_date')
                ){
                    $rescheduleFlag = true;
                    $inputs['postal_code'] = $booking->postal_code;
                    $inputs['ids_recommend_office_id'] = $booking->ids_recommend_office_id;
                    $inputs['longitude'] = $booking->longitude;
                    $inputs['latitude'] = $booking->latitude;
                    $inputs['is_online_payment_received'] = $booking->is_online_payment_received;
                    $inputs['online_processing_fee'] = $booking->online_processing_fee;
                    // $inputs['cancellation_penalty'] = $booking->cancellation_penalty;
                    $inputs['cancelled_booking_id'] = $booking->cancelled_booking_id;
                    $inputs['is_federal_billing'] = $booking->is_federal_billing;
                    $inputs['is_candidate'] = $booking->is_candidate;
                    $inputs['candidate_requisition_no'] = $booking->candidate_requisition_no;
                    $inputs['federal_billing_employer'] = $booking->federal_billing_employer;

                    $entry = $this->idsEntriesRepositories->store($inputs);
                    // unset($inputs['given_rate']);
                    // unset($inputs['postal_code']);
                    // unset($inputs['ids_service_id']);
                    // unset($inputs['passport_photo_service_id']);
                    // unset($inputs['ids_recommend_office_id']);
                    // unset($inputs['longitude']);
                    // unset($inputs['latitude']);
                    // unset($inputs['is_online_payment_received']);
                    // // unset($inputs['cancellation_penalty']);
                    // unset($inputs['cancelled_booking_id']);
                    // unset($inputs['refund_status']);
                    // unset($inputs['refund_initiated_by']);
                    // unset($inputs['refund_initiated_date']);

                    if($entry){
                        $reshedule['old_ids_entry_id'] = $request->input('id');
                        $reshedule['ids_entry_id'] = $entry->id;
                        $this->idsCustomQuestionRepository->resheduleEntry($reshedule);
                        $this->idsPaymentRepository->updateOnlinePayment($reshedule);
                        $this->idsTransactionHistoryRepository->updateEntryId($reshedule);
                        $rescheduleUpdate['is_rescheduled'] = true;
                        $rescheduleUpdate['rescheduled_id'] = $entry->id;
                        $rescheduleUpdate['rescheduled_at'] = date('Y-m-d');
                        $rescheduleUpdate['rescheduled_by'] = \Auth::id();
                        $rescheduleUpdate['deleted_by'] = \Auth::id();
                        $rescheduleUpdate['deleted_at'] = \Carbon::now();
                        $rescheduleUpdate['ids_payment_method_id'] =$booking->ids_payment_method_id;
                        $rescheduleUpdate['payment_reason'] =$booking->payment_reason;
                        $rescheduleUpdate['is_payment_received'] =$booking->is_payment_received;
                        $rescheduleUpdate['id'] = $request->input('id');
                        $store = $this->idsEntriesRepositories->updateEntry($rescheduleUpdate);
                    }
                }else{
                    unset($inputs['ids_office_id']);
                    unset($inputs['ids_office_slot_id']);
                    unset($inputs['slot_booked_date']);
                    unset($inputs['balance_fee']);
                    $inputs['id'] = $request->input('id');
                    $inputs['updated_by'] = \Auth::id();
                    $store = $this->idsEntriesRepositories->updateEntry($inputs);
                }

                if($store){

                /**Start* Amount split  management...  */
                    $entryId = $request->input('id');
                    $lastTaxEntry = $this->idsEntryAmountSplitUpRepository->getTaxByEntryId($entryId);
                    if(isset($rescheduleUpdate['rescheduled_id']) && $rescheduleUpdate['rescheduled_id'] != null){
                        $splitUpDelete = ['entry_id'=>$entryId];
                        $this->idsEntryAmountSplitUpRepository->deleteByEntry($splitUpDelete);
                        $entryId = $rescheduleUpdate['rescheduled_id'];
                    }

                    $splitUp = [];
                    $splitUpDelete = [];
                    $type = [];
                    // Service fee add in split up.
                    $given_rate = $service->rate;
                    $serviceName = $service->name;
                    //If service changes, delete and add service fee and its tax.
                    if($given_rate > 0){
                        $splitUp[0] = [
                            'type'=>1,
                            'entry_id'=>$entryId,
                            'service_id'=>$request->input('ids_service_id'),
                            'rate'=>$given_rate,
                            'tax_percentage'=>null,
                            'created_at'=>\Carbon::now(),
                            'updated_at'=>\Carbon::now()
                        ];
                    }
                    //Rescheduled or service change, delete splitup amount entry.
                    if($request->input('ids_service_id') != $booking->ids_service_id || isset($rescheduleUpdate['rescheduled_id'])){
                        $type = [1,0];
                    }

                    // Passport photo service fee add in split up.
                    if($request->has(['passport_photo_service_id']) && $request->filled('passport_photo_service_id') ){
                        $photoService = $this->idsPassportPhotoServiceRepository->getById($request->input('passport_photo_service_id'));
                        if(!empty($photoService)){
                            $serviceName = $serviceName .' and '.$photoService->name;
                            $given_rate = $given_rate + $photoService->rate;
                            $splitUp[sizeof($splitUp)] = [
                                'type'=>2,
                                'entry_id'=>$entryId,
                                'service_id'=>$request->input('passport_photo_service_id'),
                                'rate'=>$photoService->rate,
                                'tax_percentage'=>null,
                                'created_at'=>\Carbon::now(),
                                'updated_at'=>\Carbon::now()
                            ];
                        }
                    }
                    //Rescheduled or photo service changed, delete splitup amount entry.
                    if($request->input('passport_photo_service_id') != $booking->passport_photo_service_id || isset($rescheduleUpdate['rescheduled_id'])){
                        array_push($type,2);
                        array_push($type,0);
                    }

                    // Adding tax for service fee in split up.
                    $taxAmount = 0;

                    if(
                        !empty($lastTaxEntry) &&
                        $request->input('ids_service_id') == $booking->ids_service_id
                    ){
                        if($given_rate > 0){
                            $taxAmount = ($lastTaxEntry->tax_percentage / 100) * $given_rate;
                            $taxAmount = floor($taxAmount*100)/100;
                            $given_rate = $given_rate + $taxAmount;
                            $splitUp[sizeof($splitUp)] = [
                                'type'=>0,
                                'entry_id'=>$entryId,
                                'service_id'=>null,
                                'rate'=>$taxAmount,
                                'tax_percentage'=>$lastTaxEntry->tax_percentage,
                                'created_at'=>\Carbon::now(),
                                'updated_at'=>\Carbon::now()
                            ];
                        }

                    }elseif(
                        $request->input('ids_service_id') != $booking->ids_service_id &&
                        !empty($service->taxMaster) &&
                        !empty($service->taxMaster->taxMasterLog)
                    ){

                        $today = \Carbon::parse($request->input('slot_booked_date'))->format('Y-m-d');
                        $effectiveFrom = \Carbon::parse($service->taxMaster->taxMasterLog->effective_from_date)->format('Y-m-d');
                        if($today >= $effectiveFrom){
                            if($given_rate > 0){
                                $taxAmount = ($service->taxMaster->taxMasterLog->tax_percentage / 100) * $given_rate;
                                $taxAmount = floor($taxAmount*100)/100;
                                $given_rate = $given_rate + $taxAmount;
                                $splitUp[sizeof($splitUp)] = [
                                    'type'=>0,
                                    'entry_id'=>$entryId,
                                    'service_id'=>null,
                                    'rate'=>$taxAmount,
                                    'tax_percentage'=>$service->taxMaster->taxMasterLog->tax_percentage,
                                    'created_at'=>\Carbon::now(),
                                    'updated_at'=>\Carbon::now()
                                ];
                            }
                        }
                    }else{
                        array_push($type,0);
                    }

                    // $given_rate = number_format($given_rate,2);
                    // dd($given_rate,$splitUp);
                    //Delete old amount split up values.
                    if(sizeof($type) > 0){
                        $splitUpDelete = [
                            'type'=>$type,
                            'entry_id'=>$entryId
                        ];
                        $this->idsEntryAmountSplitUpRepository->deleteByEntry($splitUpDelete);
                    }
                    //Store amount split up values.
                    if(sizeof($splitUp) > 0){
                        $this->idsEntryAmountSplitUpRepository->updateOrCreate($splitUp);

                        $updates['balance_fee'] = 0;
                        $updates = [
                            'given_rate'=>$given_rate,
                            'id'=>$entryId
                        ];
                        if($request->input('balance_fee') && $booking->is_online_payment_received != 2){
                            $updates['balance_fee'] = $request->input('balance_fee');
                        }
                        //refund_status is rejected and No change in `Request for refund` no need to updates.
                        if($booking->refund_status != 3 && $request->has('refund_status')){
                            $updates['refund_status'] = null;
                            $updates['refund_initiated_by'] = null;
                            if($request->input('is_client_show_up') == 1 &&  $request->filled(['refund_status']))
                            {
                                $updates['refund_status'] = $request->input('refund_status');
                                $updates['refund_initiated_by'] = \Auth::id();
                                $updates['refund_initiated_date'] = \Carbon::now()->format('Y-m-d H:i:s');
                            }
                        }
                        if($updates){
                            //Update entry total fee.
                            $this->idsEntriesRepositories->updateEntry($updates);
                        }

                    }
                /**End* Amount split  management...  */

                /**Start* Store transaction history...  */

                    // $this->idsTransactionHistoryRepository->deleteWithOutOnlinePayment($request->input('id'));
                    $history = [];
                    if($rescheduleFlag){
                        $idsEntryId = $entry->id;
                    }else{
                        $idsEntryId = $request->input('id');
                    }
                    $refunMail = false;
                    if($request->input('is_payment_received') == 1 && $request->input('is_client_show_up') == 1 &&
                    $request->input('balance_fee') > 0 && $request->filled('ids_payment_method_id')){
                        $history['ids_payment_method_id'] = $request->input('ids_payment_method_id');
                        $history['amount'] = $request->input('balance_fee');
                        $history['transaction_type'] = 'Received';
                    }
                    if($request->input('is_client_show_up') == 1 &&
                        $request->input('balance_fee') < 0
                        //&& $request->filled('refund_status')
                        // $request->input('refund_status') == 1
                    ){
                        $history['amount'] = str_replace('-','',$request->input('balance_fee'));
                        $history['transaction_type'] = 'Refund';
                        $history['refund_note'] = $request->input('ids_refund_note');
                        if($request->input('refund_status') == 1){
                            //Refund mail to employee.
                            $refunMail = true;
                        }
                    }

                    if(sizeof($history)>0){
                        $history['entry_id'] =$idsEntryId;
                        $history['user_id'] = \Auth::id();
                        $history['refund_status'] = $request->input('refund_status');
                        $transactionHistory = $this->idsTransactionHistoryRepository->getLastEntry($idsEntryId);
                        $submitHistory = false;
                        if($transactionHistory && ($transactionHistory->amount != $request->input('balance_fee') ||
                         $transactionHistory->refund_status != $request->input('refund_status'))
                         ){
                            $submitHistory = true;
                            if($transactionHistory->refund_status == 3 && $request->input('refund_status') == 0){
                                $submitHistory = false;
                            }

                        }elseif(empty($transactionHistory)){
                            $submitHistory = true;
                        }else{
                        }
                        if($submitHistory){
                            $this->idsTransactionHistoryRepository->store($history);
                        }
                    }

                /**End* Store transaction history...  */

                    $sentMail = false;
                    if($store && $request->has(['ids_office_slot_id','slot_booked_date']) &&
                        $request->filled('ids_office_slot_id') && $request->filled('slot_booked_date'))
                    {
                        $sentMail = true;
                    }
                    if($request->input('ids_service_id') != $booking->ids_service_id ||
                        $request->input('passport_photo_service_id') != $booking->passport_photo_service_id
                    ){
                        $sentMail = true;
                    }

                    if($sentMail == true)
                    {
                        if($request->filled('ids_office_slot_id')){
                            $slot = $this->idsOfficeSlotsRepositories->getById($request->input('ids_office_slot_id'));
                        }else{
                            $slot = $this->idsOfficeSlotsRepositories->getById($booking->ids_office_slot_id);
                        }
                        if($request->has(['slot_booked_date']) && $request->filled('slot_booked_date')){
                            $slot_booked_date = $request->input('slot_booked_date');
                        }else{
                            $slot_booked_date = $booking->slot_booked_date;
                        }
                        $office = $this->idsOfficeRepository->getById($request->input('ids_office_id'));
                        $to = $request->input('email');
                        $model_name = 'Modules\IdsScheduling\Models\IdsEntries';
                        $phoneNumber = $office->phone_number;
                        if($office->phone_number_ext){
                            $phoneNumber .=" ext.".$office->phone_number_ext;
                        }
                        $helper_variables = array(
                            '{serviceName}' => $serviceName,
                            '{serviceRate}' => '$'.$given_rate,
                            '{bookingDate}' => date('l F d, Y', strtotime($slot_booked_date)),
                            '{bookingTime}' => date("h:i A",strtotime($slot->start_time)),
                            '{location}' => $office->name.', '.$office->adress,
                            '{officePhoneNumber}' => $phoneNumber,
                            '{receiverFullName}'=> $request->input('first_name').' '.$request->input('last_name')
                        );
                        $this->mailQueueRepository->prepareMailTemplate(
                            "ids_rescheduling",
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

                    //Refund mail to employee.
                    if($refunMail){
                        $this->schedulingRefundMail($idsEntryId);
                    }

                }
                $msg = $this->helperService->returnTrueResponse();
            }else{

                if(empty($booking)){
                    $msg = array('success' => false, 'message' => 'Booking not avaliable.');
                } elseif($booking->slot_booked_date <= $editUpTo){
                    $msg = array('success' => false, 'message' => 'Booking update not allowed.');
                }else{
                    $msg = array('success' => false, 'message' => 'Something went wrong. Please try again.');
                }
            }

            \DB::commit();
            return response()->json($msg);

        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json($this->helperService->returnFalseResponse($e));
        }
    }

    /**
     * Admin/Employees : Block all slot for a day.
     * Set to_be_rescheduled as true for booked entries.
     * @param  office_id, schedule_date
     * @return \Illuminate\Http\Response,
     */

    public function setToBeReshedule(Request $request){
        $rules = [
            'office_id' => "required",
            'schedule_date' => "required|date|date_format:Y-m-d|after_or_equal:today",
        ];
        //Custom error messages
        $messages = [
            'office_id.required' => 'Office is required.',
            'schedule_date.required' => 'Booking date is required.',
        ];
        $validator = \Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            $return = [
                'success' => false,
                'message'=>'All fields are required',
                'error'=>$validator->errors()
                ];
                return response()->json($return);
        }

        try {
            \DB::beginTransaction();

            $inputs['ids_office_id'] = $request->input('office_id');
            $inputs['slot_booked_date'] = $request->input('schedule_date');
            $this->idsEntriesRepositories->setToBeReshedule($inputs);

            $block['ids_office_id'] = $request->input('office_id');
            $block['slot_block_date'] = $request->input('schedule_date');
            $block['created_by'] = \Auth::user()->id;
            $this->idsOfficeSlotsBlocksRepositories->store($block);

            \DB::commit();
            $return = ['success' => true,'message'=>'Success'];

            return response()->json($return);
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
        $input['date'] = $request->input('slot_booked_date');
        $input['today'] = false;
        if(strtotime($input['date']) == strtotime(date('Y-m-d'))){
            $input['today'] = true;
        }
        return $this->idsOfficeSlotsRepositories->getOfficeFreeSlot($input);
    }

    /**
     * Get Calender page with office list
     */
    public function getCalendar(){
        $officeList = $this->idsOfficeRepository->getPermissionBaseLocationList();
        $offices = $officeList->pluck('office_name_and_address','id')->toArray();
        $paymentMethods = $this->idsPaymentMethods->getPaymentMethodsInArray();
        $paymentReasons = collect($this->idsPaymentReasonsRepository->getAll())->pluck('name','id')->toArray();
        $paymentReasons[1] = 'Other';
        $photoServices = $this->idsPassportPhotoServiceRepository->allArray()->toArray();
        return view('idsscheduling::admin.calendar', compact('officeList','offices','paymentMethods','paymentReasons','photoServices'));
    }

    /**
     * Calendar data.
     * @param ids_office_id.
     */
    public function getCalendarData(Request $request){
        $inputs = $request->all();
        $inputs['startDate'] = \Carbon::parse($request->input('date'))->subMonths(1);
        $inputs['endDate'] = \Carbon::parse($request->input('date'))->addMonth()->endOfMonth();
        // $inputs['startDate'] = date('Y-m-d');
        // $inputs['endDate'] = date('Y-m-d');
        $data = $this->idsEntriesRepositories->getCalendarData($inputs);
        return $this->formatCalendarData($data);
    }

    /**
     * Calendar data formating.
     */
    public function formatCalendarData($data){
        $result = [];
        foreach($data as $key=>$d){
            if($d->is_online_payment_received == 1 || is_null($d->is_online_payment_received)){
                $result[$key]['start'] = $d->slot_booked_date;
                $result[$key]['title'] = $d->total.' - '.$d->IdsServices->name;
                if(!empty($d->IdsServices->short_name)){
                    $result[$key]['title'] = $d->total.' - '.$d->IdsServices->short_name;
                }
            }
        }
        return $result;
    }

    /**
     * Calendar day slot details.
     */
    public function getDaySlotDetails(Request $request){

        if($request->has(['ids_office_id','calendar_date']) && $request->filled(['ids_office_id','calendar_date'])){

            $inputs = $request->all();
            $inputs['date'] = [];
            array_push($inputs['date'],$request->input('calendar_date'));
            $date = $request->input('calendar_date');
            $titleStart = str_split(date('l', strtotime($date)),3);
            $result['slotTitle'] = $titleStart[0].', '.date('d F Y', strtotime($date));
            $inputs['start_date'] = $request->input('calendar_date');
            $inputs['end_date'] = $request->input('calendar_date');
            //Fetching slot details along with booked and blocked details
            $inputs['is_admin'] = true;
            $inputs['is_calendar_api'] = true;
            // $result['slotStaus'] = ['0=Free','1=Booked','2=Blocked','3=To Be Rescheduled'];
            // $result['slots'] = $this->idsOfficeSlotsRepositories->officeSlotDetails($inputs);
            $result['slots'] = $this->idsOfficeSlotsRepositories->getOfficeSlot($inputs);
            // dd($result['slots']);
            $result['success'] = true;
            $result['message'] = "Data fetched";
        }else{
            $result['success'] = false;
            $result['message'] = "Office and date is required";
        }

        return $result;

    }

    /**
     * Get canceled schedule list page
     */
    public function getCancelledSchedule(){
        $officeList = $this->idsOfficeRepository->getPermissionBaseLocation(false);
        $services = $this->idsServicesRepository->getAllServices()->pluck('name','id')->toArray();;
        return view('idsscheduling::admin.cancelled-list', compact('officeList','services'));
    }

    public function getCancelledScheduleData(Request $request){
        $inputs = $request->all();
        $inputs['is_canceled'] = 1;
        if(!$request->filled('ids_office_id')){
            $officeList = $this->idsOfficeRepository->getPermissionBaseLocation(false);
            $inputs['ids_office_id'] = array_keys( $officeList);
        }
        return $this->idsEntriesRepositories->getReports($inputs);
    }
     /**
     * IDS Sheduling Refund Mail
     */
    public function schedulingRefundMail($entryId){
        $entryDetails=$this->idsEntriesRepositories->getByIdWithTrashed($entryId);
        $model_name = 'Modules\IdsScheduling\Models\IdsEntries';
        $phoneNumber = $entryDetails->IdsOffice->phone_number;
        if($entryDetails->IdsOffice->phone_number_ext){
            $phoneNumber .=" ext.".$entryDetails->IdsOffice->phone_number_ext;
        }
        $serviceName = $entryDetails->idsServicesWithTrashed->name;
        if(!empty($entryDetails->idsPassportPhotoServiceWithTrashed)){
            $serviceName = $serviceName .' and '.$entryDetails->idsPassportPhotoServiceWithTrashed->name;
        }
        $transaction = collect($entryDetails->idsTransactionHistory)
                ->where('transaction_type','Refund')
                ->first();
        $helper_variables = array(
            '{serviceName}' => $serviceName,
            '{serviceRate}' => '$'.$entryDetails['given_rate'],
            '{refundRate}' => '$'.$transaction->amount,
            '{bookingDate}' => date('l F d, Y', strtotime($entryDetails['slot_booked_date'])),
            '{refundDate}' => \Carbon\Carbon::now()->format('l F d, Y'),
            '{bookingTime}' => date("h:i A",strtotime($entryDetails->IdsOfficeSlots->start_time)),
            '{location}' => $entryDetails->IdsOffice->name.', '.$entryDetails->IdsOffice->adress,
            '{officePhoneNumber}' => $phoneNumber,
            '{clientFullName}'=> $entryDetails['first_name']. ' ' .$entryDetails['last_name'],
            '{email}'=> $entryDetails['email'],
            '{phoneNumber}'=> $entryDetails['phone_number'],
            '{refundInitiatedBy}'=> $entryDetails->refundInitiatedBy->name_with_emp_no ?? '',
            '{paymentId}'=> $entryDetails->idsOnlinePayment->payment_intent ?? '',
            '{onlinePaid}'=> $entryDetails->idsOnlinePayment->amount ?? ''
        );
        $this->mailQueueRepository->prepareMailTemplate(
            "ids_scheduling_refund",
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
            $to= null
        );
    }

    public function getRefundConfirm(Request $request)
    {
        $entries = $this->idsEntriesRepositories->getByIdWithTrashed($request->input('entry_id'));
        //$onlinePaid=$entries->idsOnlinePayment->amount;

        $rules = [
            'entry_id' => "required",
            'refund_status' => "required",
           // 'refund_amount' => 'bail|required_if:refund_status,==,2|lte:'.$onlinePaid
        ];
        //Custom error messages
        $messages = [
            'entry_id.required' => 'Something went wrong. Reload and Try again.',
            'refund_status.required' => 'Refund status is required.',
            // 'refund_amount.required_if' => 'Refund Amount is required.',
            // 'refund_amount.lte' => 'Refund Amount should be valid',
        ];
        $validator = \Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            $return = [
                'success' => false,
                'message'=>'All fields are required',
                'error'=>$validator->errors()
                ];
                return response()->json($return);
        }

        try {
            \DB::beginTransaction();

            if($entries && $entries->refund_status == 1){
                $inputs = $request->all();
                unset($inputs['_token']);
                unset($inputs['rejected_reason']);

                $inputs['refund_completed_by'] = \Auth::user()->id;
                $inputs['refund_completed_date'] = \Carbon\Carbon::now();

                // Calling stripe Refund Api //
                // if($entries->idsOnlinePayment->payment_intent)
                // {
                //     $refundDetails=$this->idsPaymentRepository->initiateRefund($entries->idsOnlinePayment->payment_intent,$request->input('refund_amount'));
                //     $retrieveRefund=$this->idsPaymentRepository->retrieveRefund($entries->idsOnlinePayment->payment_intent);
                // }
                // // Calling stripe Refund Api //
                // if($refundDetails['success'] ==true){
                //     if(isset($refundDetails->result->status) && $refundDetails->result->status =='succeeded')
                //     {

                //     }
                // }else{
                //     return $refundDetails;
                // }

                $update = $this->idsEntriesRepositories->updateRefundStatus($inputs);
                if($update){
                    $history['amount'] = str_replace('-','',$entries->balance_fee);
                    $history['transaction_type'] = 'Refund';
                    $history['entry_id'] = $request->input('entry_id');
                    $history['user_id'] = \Auth::id();
                    $history['refund_status'] = $request->input('refund_status');
                    $history['refund_note'] = $request->input('rejected_reason');
                    $this->idsTransactionHistoryRepository->store($history);
                }
                $return = ['success' => true,'message'=>'Success'];
            }else{
                if(!$entries){
                    $return = ['success' => false,'message'=>'Data not found.'];
                }elseif($entries && $entries->refund_status != 1){
                    $return = ['success' => false,'message'=>'Refund request canceled.'];
                }else{

                }

            }

            \DB::commit();
            return response()->json($return);
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json($this->helperService->returnFalseResponse($e));
        }
    }
    public function getEntryByIdWithTrashed(Request $request){
        return $this->idsEntriesRepositories->getByIdWithTrashed($request->input('ids_booking_id'));
    }

    public function rescheduleBugFix(){
        $entrySplitUps =  \DB::select("select * from ids_entry_amount_split_ups
                where id not in (select
                                    ieasu1.id as id1
                                from
                                    ids_entry_amount_split_ups ieasu1
                                        join ids_entry_amount_split_ups ieasu2
                                            on ieasu1.entry_id = ieasu2.entry_id and (ieasu2.type = (0) and (ieasu1.type =1 or ieasu1.type=2))
                    union
                                select
                                    ieasu2.id as id2
                                from
                                    ids_entry_amount_split_ups ieasu1
                                        join ids_entry_amount_split_ups ieasu2
                                            on ieasu1.entry_id = ieasu2.entry_id and (ieasu2.type = (0) and (ieasu1.type =1 or ieasu1.type=2))
                )
                and created_at >= '2021-04-28'
                and type =1");

            $entryids = collect($entrySplitUps)->pluck('entry_id')->unique();

            foreach($entryids as $entryid){
                $inputs = [];
                $splitUp = [];
                $entry = IdsEntries::withTrashed()->find($entryid);
                if(!isset($entry)) {
                    continue;
                }
                $given_rate = (float)$entry->given_rate;
                $taxAmount = floor(($given_rate * 0.13)*100)/100;
                $given_rate = $given_rate + $taxAmount;
                $givenRateArray = explode(".",strval($given_rate));
                if(sizeof($givenRateArray) == 2){
                    $given_rate = floatval($givenRateArray[0].'.'.substr($givenRateArray[1],0,2));
                }

                $rescheduledEntry = IdsEntries::where('rescheduled_id',$entryid)->withTrashed()->first();
                if(isset($rescheduledEntry)) {
                    $inputs['online_processing_fee'] = $rescheduledEntry->online_processing_fee;
                }
                $inputs['given_rate'] = $given_rate;
                // $inputs['id'] = $entryid;

                IdsEntries::withTrashed()->where('id',$entryid)->update($inputs);

                $splitUp = [
                    'type'=>0,
                    'entry_id'=>$entryid,
                    'service_id'=>null,
                    'rate'=>$taxAmount,
                    'tax_percentage'=>13.00,
                    'created_at'=>\Carbon::now(),
                    'updated_at'=>\Carbon::now()
                ];


                $idsEntryAmountSplitUp = new IdsEntryAmountSplitUp();
                $idsEntryAmountSplitUp->type = 0;
                $idsEntryAmountSplitUp->entry_id = $entryid;
                $idsEntryAmountSplitUp->service_id = null;
                $idsEntryAmountSplitUp->rate = $taxAmount;
                $idsEntryAmountSplitUp->tax_percentage = 13.00;
                $idsEntryAmountSplitUp->created_at = \Carbon::now();
                $idsEntryAmountSplitUp->updated_at = \Carbon::now();
                if(isset($entry->deleted_at)){
                    $idsEntryAmountSplitUp->deleted_at = \Carbon::now();
                }
                $idsEntryAmountSplitUp->save();
                // IdsEntryAmountSplitUp::create($splitUp);
                // dd($entryid,$given_rate,$taxAmount,$inputs,$splitUp,$entry,$rescheduledEntry);
            }

      }

}
