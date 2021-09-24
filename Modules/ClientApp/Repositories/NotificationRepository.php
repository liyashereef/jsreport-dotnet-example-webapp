<?php

namespace Modules\ClientApp\Repositories;

use App\Services\AppId;
use App\Services\FireBase;
use App\Services\HelperService;
use Modules\Admin\Repositories\CustomerEmployeeAllocationRepository;
use Modules\Supervisorpanel\Models\IncidentReport;
use Modules\Timetracker\Repositories\UserDeviceRepository;

class NotificationRepository
{
    protected $helperService;
    protected $userDeviceRepository;
    protected $customerEmployeeAllocationRepository;

    public function __construct(
        HelperService $helperService,
        UserDeviceRepository $userDeviceRepository,
        CustomerEmployeeAllocationRepository $customerEmployeeAllocationRepository
    ) {
        $this->helperService = $helperService;
        $this->userDeviceRepository = $userDeviceRepository;
        $this->customerEmployeeAllocationRepository = $customerEmployeeAllocationRepository;
    }

    public function incidentNotification($incident)
    {
        // $incident = IncidentReport::find(1062);

        $targetCustomer = $incident->customer;

        if ($targetCustomer == null) {
            return;
        }

        $reportedBy = $incident->reporter->full_name;
        $priority =  $incident->priority ? '(' . $incident->priority->value . ')' : '';
        $description = !empty($incident->description)  ? ("\r\n" . $incident->description) : '';

        $payload = [
            "notification" => [
                "title" => "Incident: $priority " . $incident->title ?? '',
                "body" => (data_get($incident->incident_report_subject, 'subject') ?? '')
                    . $description
                    . "\r\nReported by @$reportedBy"
            ]
        ];

        //Get allocated employees of customer
        //Select employees having client role
        $allocations = $this->customerEmployeeAllocationRepository->allocationList($targetCustomer->id, ['client'], false, true);
        $userIds  = $allocations->pluck('id')->toArray();
        $userDevices = $this->userDeviceRepository->getByAppSpecific(AppId::CGL_M360, $userIds);

        //Each tokens send request
        foreach ($userDevices as $userDevice) {
            $res = FireBase::sendNotification($userDevice->device_token, $payload, AppId::CGL_M360);
        }
    }
}
