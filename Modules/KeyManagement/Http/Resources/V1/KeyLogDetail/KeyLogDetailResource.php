<?php

namespace Modules\KeyManagement\Http\Resources\V1\KeyLogDetail;

use Illuminate\Support\Collection;
use Illuminate\Http\Resources\Json\Resource;
use App\Repositories\AttachmentRepository;
use Modules\KeyManagement\Http\Resources\V1\Attachment\AttachmentResource;
use Modules\KeyManagement\Http\Resources\V1\KeyLogDetail\CustomerKeyDetailResource;
use Modules\KeyManagement\Http\Resources\V1\User\UserResource;
use Modules\Admin\Models\User;

class KeyLogDetailResource extends Resource
{
    protected $attachmentRepository;
    public function __construct($resource)
    {
        parent::__construct($resource);
        $this->attachmentRepository = new AttachmentRepository;
    }
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     *
     */
    public function toArray($request)
    {
        if(isset($this->attachment->id) && !empty($this->attachment->id)){
        $attachmentPath = $this->attachmentRepository->downloadDetails(null,$this->attachment->id,'keymanagement-key-image');
        }
        return [
            'id' => $this->id,
            'key_id' => $this->key_id,
            'room_name' => $this->room_name,
            'key_status' => $this->key_availability,
            'attachments_path' =>isset($attachmentPath) ? base64_encode(file_get_contents($attachmentPath['path'])) : '',
            'attachment_ext' => isset($attachmentPath) ? $attachmentPath['ext'] : '',
            'key_log_details' => $this->getKeyInfo($this->info),
            'createdAt' => $this->created_at->toDateTimeString(),
            'updatedAt' => $this->updated_at->toDateTimeString(),
            
            
        ];
    }

    public function getKeyInfo($keyinfo) {


        if(!empty($keyinfo)){
            if(isset($keyinfo->signature_attachment_id) && !empty($keyinfo->signature_attachment_id)){
                $checkout_signatureattachmentPath = $this->attachmentRepository->downloadDetails(null,$keyinfo->signature_attachment_id,'keymanagement-signature');
            }
            if(isset($keyinfo->check_in_signature_attachment_id) && !empty($keyinfo->check_in_signature_attachment_id)){
                $checkin_signatureattachmentPath = $this->attachmentRepository->downloadDetails(null,$keyinfo->check_in_signature_attachment_id,'keymanagement-signature');
            }

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
                    'checkout_by' =>  $checkout_user,
                    'checkout_to' => $keyinfo->checked_out_to,
                    'company_name' => $keyinfo->company_name,
                    'note' => $keyinfo->notes,
                    'check_out_signature_attachments_path' => isset($checkout_signatureattachmentPath) ? base64_encode(file_get_contents($checkout_signatureattachmentPath['path'])) : '',
                    'check_out_signature_ext' => isset($checkout_signatureattachmentPath) ? $checkout_signatureattachmentPath['ext'] : '',
                    'checked_from' => $keyinfo->checked_in_from,
                    'checked_out_date_time' => $keyinfo->checked_out_date_time,
                    'checked_in_date_time' => $keyinfo->checked_in_date_time,
                    'checked_in_by' => $checkin_user,
                    'checked_in_notes' => $keyinfo->check_in_notes,
                    'check_in_signature_attachments_path' => isset($checkin_signatureattachmentPath) ? base64_encode(file_get_contents($checkin_signatureattachmentPath['path'])) : '',
                    'check_in_signature_ext' => isset($checkin_signatureattachmentPath) ? $checkin_signatureattachmentPath['ext'] : '',
                    'createdAt' => $keyinfo->created_at->toDateTimeString(),
                    'updatedAt' => $keyinfo->updated_at->toDateTimeString(),
                ]
            );

        }
        
       

    }

    function getAttachmentUrl($id) {
        return route('filedownload',[$id,'keymanagement']);
    }
}
