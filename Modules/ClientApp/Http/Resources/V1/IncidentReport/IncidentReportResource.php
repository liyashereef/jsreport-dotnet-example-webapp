<?php

namespace Modules\ClientApp\Http\Resources\V1\IncidentReport;

use Illuminate\Http\Resources\Json\Resource;
use Modules\ClientApp\Http\Resources\V1\PayPeriod\PayPeriodResource;
use Modules\ClientApp\Http\Resources\V1\User\UserResource;

class IncidentReportResource extends Resource
{

    protected $attachmentRepository;

    function getAttachmentUrl($id) {
        return route('client.filedownload',[$id,'incident']);
    }

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        $status = $this->latestIncidentStatusLogWtihList;

        return [
            'id' => $this->id,
            'title' => $this->title,
            'subject' => data_get($this->incident_report_subject,'subject'),
            'description' => $this->incidentDescription,
            'status' => $status->incidentStatusList->status,
            'attachments' =>  IncidentAttachmentResource::collection($this->incidentAttachment),
            'priority' => $this->priority->value ?? '',
            'payperiod' => new PayPeriodResource($this->payperiod), //$this->payperiod_id,
            'reportedBy' => new UserResource($this->reporter),
            'createdAt' => $this->created_at->toDateTimeString(),
            'updatedAt' => $this->updated_at->toDateTimeString(),
        ];
    }
}
