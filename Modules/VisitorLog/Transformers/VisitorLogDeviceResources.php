<?php

namespace Modules\VisitorLog\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Admin\Models\VisitorLogTypeLookup;
use Modules\VisitorLog\Transformers\ScreeningQuestionsResource;

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
        $result = [
                'id'=>$this->id,
                'isBlocked'=> ($this->is_blocked == 1)? true : false,
                'isScreeningEnabled'=> ($this->is_blocked == 1)? true : false,
                'scannerCameraMode'=> $this->visitorLogDeviceSettings->scaner_camera_mode,
                'cameraMode' => $this->visitorLogDeviceSettings->camera_mode,
                'customerId'=> $this->customer_id,
                'deviceId'=> $this->device_id,
                'deviceUID'=> $this->uid,
                'pin'=>$this->visitorLogDeviceSettings->pin,
                'visitorTypes' => VisitorLogTypeLookup::orderBy('type')->select('id', 'type as name')->get(),
                'template' => ($this->template)? $this->template : '',
            ];
            $result['customer']= [
                'id' => $this->customer->id,
                'name' => $this->customer->client_name,
                'projectNo' => $this->customer->project_number
            ];
            $result['screening']['questions'] = [];
            if (!empty($this->screening)) {
                if (!empty($this->screening->VisitorLogScreeningTemplate) && sizeof($this->screening->VisitorLogScreeningTemplate->VisitorLogScreeningTemplateQuestion)) {
                    $result['screening']['questions'] = ($this->screening)? new ScreeningQuestionsResource($this->screening->VisitorLogScreeningTemplate->VisitorLogScreeningTemplateQuestion): '';
                }
            }

          return $result;
    }
}
