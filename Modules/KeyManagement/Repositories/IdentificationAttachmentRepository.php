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

class IdentificationAttachmentRepository
{
    /**
     * The Model instance.
     *
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $model,$helperService,$attachmentRepository,$customerRepository,$keyLogModel;
    
    public function __construct(
        CustomerKeyDetail $CustomerkeyModel,
        AttachmentRepository $attachmentRepository,
        HelperService $helperService,
        CustomerRepository $customerRepository,
        KeyLogDetail $keyLogModel,
        ImageRepository $imageRepository
        )
    {   $this->helperService = $helperService;
        $this->model = $CustomerkeyModel;
        $this->attachmentRepository = $attachmentRepository;
        $this->customerRepository = $customerRepository;
        $this->keylogmodel = $keyLogModel;
        $this->imageRepository = $imageRepository; 

    }

    
    /**
     * Store a newly created identification attachments in storage.
     *
     * @param  $data
     * @return object
     */

    public function storeIdentications($checkoutStore,$request)
    {
        foreach ($request->identification_attachment as $imgkey => $eachimage) {
            $imagefile = $this->imageRepository->imageFromBase64($eachimage);
            $attachment_id = $this->attachmentRepository->saveBase64ImageFile('keymanagement-identification',$checkoutStore, $imagefile);
            $file_path = implode('/', $this->getAttachmentPathArr($checkoutStore));
            $data = [
                'key_log_detail_id' => $checkoutStore->id,
                'identification_id' =>  $request->identification_id,
                'identification_attachment_id' =>  $attachment_id,
                'identification_attachment_path' =>  $file_path,
             ];
            $storeAttachment = KeymanagementIdentificationAttachment::updateOrCreate($data);
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
            $path = 'transactions/'.$date.'/'.$request->customer_key_detail_id;
            return array(config('globals.keymanagement'), $path);
        
    }

    
    /**
     * Function to Download attachments
     * @param $file_id
     * @return array
     */

    public static function getAttachmentPathArrFromFile($file_id)
    {  
        $identification_attachments = KeymanagementIdentificationAttachment::with('keylog')
                ->where('identification_attachment_id', $file_id)
                ->first();
        if ($identification_attachments) {
            $path = $identification_attachments->identification_attachment_path;
        }
        return array($path);
    }

   
    

}
