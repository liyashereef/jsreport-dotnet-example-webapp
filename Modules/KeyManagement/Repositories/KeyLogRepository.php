<?php

namespace Modules\KeyManagement\Repositories;

use Auth;
use Carbon\Carbon;
use Modules\KeyManagement\Models\CustomerKeyDetail;
use Modules\KeyManagement\Models\KeyLogDetail;
use Modules\KeyManagement\Models\KeymanagementIdentificationAttachment;
use App\Services\HelperService;
use App\Repositories\AttachmentRepository;
use Modules\Timetracker\Repositories\ImageRepository;
use Modules\Admin\Repositories\CustomerRepository;
use Modules\KeyManagement\Repositories\IdentificationAttachmentRepository;

class KeyLogRepository
{
    /**
     * The Model instance.
     *
     * @var \Illuminate\Database\Eloquent\Model
     */

    protected $model,$helperService,$attachmentRepository,$customerRepository,$keyLogModel;

    /**
     * Create a new Model instance.
     *
     * @param  \Modules\KeyManagement\Models\KeyLogDetail $keyLogModel
     */

    public function __construct(
        CustomerKeyDetail $CustomerkeyModel,
        AttachmentRepository $attachmentRepository,
        HelperService $helperService,
        CustomerRepository $customerRepository,
        KeyLogDetail $keyLogModel,
        ImageRepository $imageRepository,
        IdentificationAttachmentRepository $identificationAttachmentRespository
        )
    {   $this->helperService = $helperService;
        $this->model = $CustomerkeyModel;
        $this->attachmentRepository = $attachmentRepository;
        $this->customerRepository = $customerRepository;
        $this->keylogmodel = $keyLogModel;
        $this->imageRepository = $imageRepository;
        $this->identificationAttachmentRespository = $identificationAttachmentRespository;

    }

   /**
     * Store a newly created checkout in storage.
     *
     * @param  $request
     * @return object
     */


    public function storeCheckout($request){
        $data =[];
        $logged_in_user = \Auth::id();
        $data = [
            'customer_key_detail_id' => $request->key_id,
            'checked_out_to' => $request->checked_out_to,
            'checked_out_by' => $logged_in_user,
            'checked_out_date_time' => $request->checked_out_date_time,
            'company_name' => $request->company_name,
            'key_availablity_id' => 0,
            'notes' => $request->notes,
            'created_by' => $logged_in_user
        ];
           $checkoutStore = $this->keylogmodel->updateOrCreate(array('id' => $request->get('id')), $data);
            CustomerKeyDetail::updateOrCreate(array('id' => $checkoutStore->customer_key_detail_id), ['key_availability' => 0,'updated_at'=>carbon::now()]);
           if($checkoutStore){
            if(isset($request->identification_attachment) && !empty($request->identification_attachment)){
              $identification_attachment = $this->identificationAttachmentRespository->storeIdentications($checkoutStore,$request);
            }
         }
         if($checkoutStore){
            if(isset($request->signature_attachment) && !empty($request->signature_attachment)){
              foreach ($request->signature_attachment as $imgkey => $eachimage) {
               $imagefile = $this->imageRepository->imageFromBase64($eachimage);
               $attachment_id = $this->attachmentRepository->saveBase64ImageFile('keymanagement-signature',$checkoutStore, $imagefile);
               $file_path = implode('/', $this->getAttachmentPathArr($checkoutStore));
               $data = ['signature_attachment_id' => $attachment_id,'check_out_signature_path' =>$file_path];
               $storeAttachment = KeyLogDetail::updateOrCreate(array('id' => $checkoutStore->id), $data);
             }
            }
         }
        return $checkoutStore;

    }

    /**
     * Store a newly created checkin in storage.
     *
     * @param  $request
     * @return object
     */

    public function storeCheckin($request){
        $key_checkedout_details = $this->keylogmodel->where('customer_key_detail_id',$request->key_id)->orderBy('id', 'DESC')->first();

        if(!empty($key_checkedout_details)){
            $data = [
                'checked_in_by' => \Auth::id(),
                'checked_in_date_time' => $request->checked_in_date_time,
                'key_availablity_id' => 1,
                'check_in_notes' => $request->check_in_notes,
                'updated_by' => \Auth::id()
            ];
        $result = $this->keylogmodel-> updateOrCreate(array('id' =>$key_checkedout_details->id), $data);
        if($result){
            CustomerKeyDetail::updateOrCreate(array('id' => $request->key_id), ['key_availability' => 1,'updated_at'=>carbon::now()]);
            if(isset($request->signature_attachment) && !empty($request->signature_attachment)){
              foreach ($request->signature_attachment as $imgkey => $eachimage) {
               $imagefile = $this->imageRepository->imageFromBase64($eachimage);
               $attachment_id = $this->attachmentRepository->saveBase64ImageFile('keymanagement-signature',$result, $imagefile);
               $file_path = implode('/', $this->getAttachmentPathArr($result));
               $data = ['check_in_signature_attachment_id' => $attachment_id,'check_in_signature_path' => $file_path];
               KeyLogDetail::where('id', $result->id)->update($data);

             }
            }
         }
        return $result;
        }
    }

    /**
     * Function to prepare and give attachment path array
     * @param $request
     * @return array
     */

    public static function getAttachmentPathArr($request)
    {
            $date = date('Ymd', strtotime(carbon::now()));
            return array(config('globals.keymanagement'), 'transactions/'.$date.'/'.$request->customer_key_detail_id);

    }


    /**
     * Function to Download attachments
     * @param $file_id
     * @return array
     */

    public static function getAttachmentPathArrFromFile($file_id)
    {
        $attachment = KeyLogDetail::where('signature_attachment_id', $file_id)
                                        ->orwhere('check_in_signature_attachment_id', $file_id)
                                        ->first();
        if (($attachment->signature_attachment_id == $file_id)&&!empty($attachment)) {
            $path = $attachment->check_out_signature_path;
        }else{
            $path = $attachment->check_in_signature_path;
        }
        return array($path);
    }


    /**
     * Display details of single key log
     *
     * @param $id
     * @return object
     */

    public function getKeyLogSingle($id)
    {
        return $this->keylogmodel->with('keyinfo','keyinfo.customer','identifications','checkedoutuser','checkedinuser')->find($id);
    }

    /**
     * Get  Customer Key Log list
     *
     * @param $key_id,$from_date,$to_date
     * @return array
     */

    public function getKeyLogList($key_id,$from_date,$to_date,$client_id=null){

        $customer_ids = $this->customerRepository->getAllAllocatedCustomerId([\Auth::user()->id]);
        if((\Auth::user()->can('view_all_keylog_summary')) || \Auth::user()->hasAnyPermission(['admin', 'super_admin'])){
            $query = $this->keylogmodel->with(['keyinfo','keyinfo.customer']);
        }else{
            $query = $this->keylogmodel->with(['keyinfo','keyinfo.customer'])
            ->whereHas('keyinfo', function($q) use($customer_ids){
                $q->whereIn('customer_id',$customer_ids);
            });
        }
        $query->when($key_id != null, function ($q) use ($key_id) {
            return $q->where('customer_key_detail_id', $key_id);
        });

        if($client_id != null){
            $query->whereHas('keyinfo', function($q) use($client_id){
                return $q->where('customer_id',$client_id);
            });
        }

        $query->when($from_date != null && $to_date != null, function ($q) use ($from_date, $to_date) {
            return $q->whereDate('created_at', '>=', $from_date)
                    ->whereDate('created_at', '<=', $to_date);
        });
        $query->orderBy('created_at', 'desc');
        $logList = $query->get();

        return $this->prepareDataForKeyLogList($logList);

    }

    /**
     * Prepare datatable elements as array.
     * @param  $logList
     * @return array
     */

    Public function prepareDataForKeyLogList($logList){

        $datatable_rows = array();
        foreach ($logList as $key => $each_list) {
            $each_row["id"] = isset($each_list->id) ? $each_list->id : "--";
            $each_row["project_number"] = isset($each_list->keyinfo) ? $each_list->keyinfo->customer->project_number : "--";
            $each_row["client_name"] = isset($each_list->keyinfo) ? $each_list->keyinfo->customer->client_name : "--";
            $each_row["keycheckedout_date"] = isset($each_list->checked_out_date_time) ? $each_list->checked_out_date_time->toFormattedDateString() : "--";
            $each_row["keycheckedout_time"] = isset($each_list->checked_out_date_time) ? $each_list->checked_out_date_time->format('h:i A') : "--";
            $each_row["keycheckedin_time"] = isset($each_list->checked_in_date_time) ? $each_list->checked_in_date_time->format('h:i A') : "--";
            $each_row["key_details"] = isset($each_list->keyinfo) ? ucfirst($each_list->keyinfo->room_name).' ('.$each_list->keyinfo->key_id.')': "--";
            $each_row["key_checked_out_to"] = isset($each_list->checked_out_to) ? $each_list->checked_out_to : "--";
            $each_row["key_checked_in_from"] = isset($each_list->checked_in_from) ? $each_list->checked_in_from : "--";
            $each_row["key_checked_out_note"] = isset($each_list->notes) ? $each_list->notes : "--";
            $each_row["key_checked_in_note"] = isset($each_list->check_in_notes) ? $each_list->check_in_notes : "--";
            $each_row["key_checked_status"] = ($each_list->key_availablity_id == 1) ? "Checked In" : "Checked Out";
            $each_row["identification_attachment_id"] = isset($each_list->identification_attachment_id) ? $each_list->identification_attachment_id : "--";
            $each_row["signature_attachment_id"] = isset($each_list->signature_attachment_id) ? $each_list->signature_attachment_id : "--";

            array_push($datatable_rows, $each_row);
        }

        return $datatable_rows;

    }




}
