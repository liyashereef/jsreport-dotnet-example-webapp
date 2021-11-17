<?php

namespace Modules\VisitorLog\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Admin\Models\VisitorLogTypeLookup;

class VisitorLogDeviceResources extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id'=>$this->id,
            'isBlocked'=> ($this->is_blocked == 1)? true : false,
            'isScreeningEnabled'=> ($this->is_blocked == 1)? true : false,
            'scannerCameraMode'=> $this->visitorLogDeviceSettings->scaner_camera_mode,
            'cameraMode' => $this->visitorLogDeviceSettings->camera_mode,
            'customerId'=> $this->customer_id,
            'deviceId'=> $this->device_id,
            'deviceUID'=> $this->uid,
            'visitorTypes' => VisitorLogTypeLookup::orderBy('type')->select('id', 'type as name')->get(),
            'template' => $this->visitorLogDeviceSettings->visitorLogTemplates,
            'screening' => ($this->screening->VisitorLogScreeningTemplate->VisitorLogScreeningTemplateQuestion)? $this->screening->VisitorLogScreeningTemplate->VisitorLogScreeningTemplateQuestion : '',

          ];
    }
}
