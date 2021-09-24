<?php

namespace Modules\Timetracker\Repositories;


use Modules\Timetracker\Models\DispatchRequestStatus;
use Modules\Timetracker\Models\DispatchRequestStatusEntry;

use Illuminate\Support\Facades\Auth;

class DispatchRequestStatusRepository
{
    protected $dispatchRequestStatus,$dispatchRequestStatusEntry;

    public function __construct(DispatchRequestStatus $dispatchRequestStatus,DispatchRequestStatusEntry $dispatchRequestStatusEntry)
    {
        $this->dispatchRequestStatus = $dispatchRequestStatus;
        $this->dispatchRequestStatusEntry = $dispatchRequestStatusEntry;
    }

    public function getAll()
    {
        return $this->dispatchRequestStatus->get();
    }

    public function dispatchResquestStatusEntry($request)
    {
        $id = $request['dispatch_request_id'];
        $status_id = $request['dispatch_request_status_id'];
        $created_by = Auth::id();
        $result = $this->dispatchRequestStatusEntry->updateOrCreate( ['dispatch_request_id' => $id,'dispatch_request_status_id' => $status_id,'respond_by' => $created_by]);
        return $result;

       
    }

}