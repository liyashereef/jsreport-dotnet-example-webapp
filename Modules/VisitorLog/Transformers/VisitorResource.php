<?php

namespace Modules\VisitorLog\Transformers;

use Illuminate\Http\Resources\Json\ResourceCollection;

class VisitorResource extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {

        return $this->collection->transform(function($visitor){
                return [
                    'id' => $visitor->id,
                    'uid' => $visitor->uid,
                    'customerId' => $visitor->customerId,
                    'clientName' => $visitor->customer->client_name,
                    'projectNumber' => $visitor->customer->project_number,

                    'barCode'=>$visitor->barCode,
                    'firstName'=>$visitor->firstName,
                    'lastName'=>$visitor->lastName,
                    'fullName'=> $visitor->firstName.' '.$visitor->lastName,
                    'email'=>$visitor->email,
                    'phone'=>$visitor->phone,
                    'createdAt'=> \Carbon::parse($visitor->created_at)->format('Y-m-d H:i:s'),
                    'updatedAt'=>\Carbon::parse($visitor->updated_at)->format('Y-m-d H:i:s'),
                    'deletedAt'=>(!empty($visitor->deleted_at))? \Carbon::parse($visitor->deleted_at)->format('Y-m-d H:i:s') : null,
                    'avatar'=>$visitor->avatar,
                    'note'=>$visitor->notes,

                    'visitorTypeId'=>$visitor->visitorTypeId,
                    'visitorType'=>$visitor->visitorType,
                    'visitorTypeName'=>$visitor->visitorType->name,

                    'visitorStatusId'=>$visitor->visitorStatusId,
                    'status'=>(!empty($visitor->visitorStatus))? $visitor->visitorStatus->name : null,
                    'authorised'=>(!empty($visitor->visitorStatus))? $visitor->visitorStatus->is_authorised : 1,

                ];
            });
    }
}
