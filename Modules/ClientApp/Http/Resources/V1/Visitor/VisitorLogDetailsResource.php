<?php

namespace Modules\ClientApp\Http\Resources\V1\Visitor;

use Illuminate\Http\Resources\Json\Resource;
use Illuminate\Support\Collection;
use Modules\Admin\Models\VisitorLogTemplateFields;

class VisitorLogDetailsResource extends Resource
{


    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'visitor'=>  $this->getVisitorDetails($this),
            'forceCheckout' => ($this->force_checkout==1)?true:false,
            'visitorType'=>$this->type->type,
            'fields'=>$this->getVisitorFields($this),    
            'checkInOption'=>null,
            'checkInAt'=>$this->checkin,
            'checkOutAt'=>$this->checkout,
            'createdAt'=> $this->created_at->toDateTimeString(),
            'updatedAt'=> $this->updated_at->toDateTimeString(),
        ];
    }

    public function getVisitorDetails($visitor)
    {
        $url=($visitor->picture_file_name==null)?asset('images').'/no_avatar.jpg':asset('visitor_log').'/'.$visitor->id.'/'.$visitor->picture_file_name;
        return Collection::make(
            [
                'name' => $visitor->first_name,
                'email' => $visitor->email,
                'phone' => $visitor->phone,
                'avatar' =>$url,
            ]
        );
    }

     public function getVisitorFields($visitor) {
        $fields= VisitorLogTemplateFields::where('template_id',$visitor->template_id)
                    ->orderBy('created_at','desc')
                    ->get();  
                     $visitorArr=array();   
                     $excludeAttributesArr=['first_name','visitor_type_id','phone','email','checkin'];
                    foreach ($fields as $key => $each_field) {
                        if(!in_array($each_field->fieldname,$excludeAttributesArr)){
                        $arr['key']=$each_field->field_displayname;
                        $arr['value']=$visitor->{$each_field->fieldname};
                        $visitorArr[]=$arr;
                    }
                    }
        return $visitorArr;
    }
}
