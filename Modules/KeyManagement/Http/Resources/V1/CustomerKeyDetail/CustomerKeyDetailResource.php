<?php

namespace Modules\KeyManagement\Http\Resources\V1\CustomerKeyDetail;

use Illuminate\Http\Resources\Json\Resource;
use Illuminate\Support\Collection;
use Modules\KeyManagement\Http\Resources\V1\Attachment\AttachmentResource;
use Modules\KeyManagement\Http\Resources\V1\Customer\CustomerResource;
use Modules\KeyManagement\Http\Resources\V1\Log\LogResource;
use App\Repositories\AttachmentRepository;
use Modules\KeyManagement\Http\Resources\V1\User\UserResource;
use Modules\Admin\Models\User;



class CustomerKeyDetailResource extends Resource
{
    protected $attachmentRepository;
    public function __construct($resource)
    {
        parent::__construct($resource);
        $this->attachmentRepository = new AttachmentRepository;
    }


    function getAttachmentUrl($id) {
        return route('filedownload',[$id,'keymanagement']);
    }
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        if(isset($this->attachment->id) && !empty($this->attachment->id)){
            $attachmentPath = $this->attachmentRepository->downloadDetails(null,$this->attachment->id,'keymanagement-key-image');
        } 

        return   [
            'id' => $this->id,
            'customer_id' => new CustomerResource($this->customer),
            'room_name' => $this->room_name,
            'key_status' => $this->key_availability,
            'attachments_path' =>   isset($attachmentPath) ? base64_encode(file_get_contents($attachmentPath['path'])) : '',
            'attachment_ext' =>  isset($attachmentPath) ? $attachmentPath['ext'] : '',
            'key_checkout_details' =>  $this->getKeyInfo($this->info), 
            'createdAt' => $this->created_at->toDateTimeString(),
            'updatedAt' =>  $this->getUpdatedAt($this->info,$this->created_at->toDateTimeString()),
        ];
    }

    public function getKeyInfo($keyinfo) {

        if(!empty($keyinfo)){
            if(isset($keyinfo->created_by) && !empty($keyinfo->created_by)){
                $checkout_user = User::findOrFail($keyinfo->created_by);
            }else{
                $checkout_user = [];
            }
            if(isset($keyinfo->created_by) && !empty($keyinfo->updated_by)){
                $checkin_user = User::findOrFail($keyinfo->updated_by);
            }else{
                $checkin_user = [];
            }
            
            return  Collection::make(
                [ 
                    'id' => $keyinfo->id,
                    'checkout_by' => $checkout_user,
                    'checkout_to' => $keyinfo->checked_out_to,
                    'checked_out_notes' => $keyinfo->notes,
                    'checked_from' => $keyinfo->checked_in_from,
                    'checked_in_by' => $checkin_user,
                    'checked_in_notes' => $keyinfo->check_in_notes,
                    'createdAt' => $keyinfo->created_at->toDateTimeString(),
                    'updatedAt' => $this->getUpdatedAt($this->info,$this->created_at->toDateTimeString()),
                ]
            );
        }
    }

    public function getUpdatedAt($keyinfo,$keycreated){
               
        $checked_details = null;
        if(!empty($keyinfo->key_availablity_id) && ($keyinfo->key_availablity_id == 1)){
            if(isset($keyinfo->checked_in_date_time) && !empty($keyinfo->checked_in_date_time)) {
                $checked_details = $keyinfo->checked_in_date_time->toDateTimeString();
            }else{
                $checked_details = $keycreated;
            }
        } else {
            if(isset($keyinfo->checked_out_date_time) && !empty($keyinfo->checked_out_date_time)) {
                $checked_details = $keyinfo->checked_out_date_time->toDateTimeString();
            }else{
                $checked_details =  $keycreated;
            }
        }
        return $checked_details;


    }
}
