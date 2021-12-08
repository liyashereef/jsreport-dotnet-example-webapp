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
        return $this->collection->transform(function ($visitor) {
            $fullName = '';
            $shortName = '';
            if (!empty($visitor->firstName)) {
                $fullName .= $visitor->firstName;
                $shortName .= $visitor->firstName[0];
            }
            if (!empty($visitor->lastName)) {
                $fullName .= (' ' . $visitor->lastName);
                $shortName .= $visitor->lastName[0];
            }

            return [
                'id' => $visitor->id,
                'uid' => $visitor->uid,
                'customerId' => $visitor->customerId,
                'barCode' => $visitor->barCode,
                'fullName' => $fullName,
                'shortName' => $shortName,
                'email' => $visitor->email,
                'phone' => $visitor->phone,
                'avatar' => $visitor->avatar,
                'note' => $visitor->notes,
                'visitorType' => $visitor->visitorType,
                'status' => (!empty($visitor->visitorStatus)) ? $visitor->visitorStatus->name : null,
                'authorised' => (!empty($visitor->visitorStatus)) ? $visitor->visitorStatus->is_authorised : 1,

                'createdAt' => \Carbon::parse($visitor->created_at)->format('Y-m-d H:i:s'),
                'updatedAt' => \Carbon::parse($visitor->updated_at)->format('Y-m-d H:i:s'),
                'deletedAt' => (!empty($visitor->deleted_at)) ? \Carbon::parse($visitor->deleted_at)->format('Y-m-d H:i:s') : null,

            ];
        });
    }
}
