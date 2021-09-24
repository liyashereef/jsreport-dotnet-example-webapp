<?php

namespace Modules\ClientApp\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Modules\Admin\Models\ClientFeedbackLookup;
use Modules\Admin\Models\CustomerEmployeeAllocation;
use Modules\Admin\Models\SeverityLookup;
use Modules\Admin\Models\User;
use Modules\Client\Models\ClientConcern;
use Modules\Client\Models\ClientEmployeeFeedback;
use Modules\ClientApp\Http\Requests\ClientConcernRequest;
use Modules\ClientApp\Http\Requests\ClientFeedbackRequest;
use Modules\ClientApp\Http\Resources\V1\Client\ClientConcernResource;
use Modules\ClientApp\Http\Resources\V1\Client\ClientFeedbackLookupResource;
use Modules\ClientApp\Http\Resources\V1\Client\ClientFeedbackResource;
use Modules\ClientApp\Http\Resources\V1\Client\ClientSeverityLookupResource;
use Modules\ClientApp\Http\Resources\V1\User\UserResource;
use Modules\ClientApp\Services\Client\ClientService;

class ClientController extends Controller {

    protected $clientService;
    public function __construct(
        ClientService $clientService
    ) {
        $this->clientService = $clientService;
    }

    public function clientConcern(Request $request) {
        try {
            return ClientConcernResource::collection(
                            ClientConcern::where('customer_id', $request->customerId)
                                    ->orderBy('created_at', 'desc')
                                    ->get()
            );
        } catch (\Exception $e) {
            throw $e;
        }
    }
     
     public function clientFeedback(Request $request) {
        try {
            return ClientFeedbackResource::collection(
                            ClientEmployeeFeedback::where('customer_id', $request->customerId)
                                    ->orderBy('created_at', 'desc')
                                    ->get()
            );
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function clientFeedbackTypes(Request $request) {
        try {
            return ClientFeedbackLookupResource::collection(
                ClientFeedbackLookup::orderBy('feedback', 'asc')
                    ->get()
            );
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function clientSeverityTypes(Request $request) {
        try {
            return ClientSeverityLookupResource::collection(
                SeverityLookup::orderBy('value', 'asc')
                    ->get()
            );
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function clientEmployeeList(Request $request) {
        $customerId = $request->customerId;
        $customerEmployeeAllocationModel = CustomerEmployeeAllocation::
        where('customer_id', $customerId)
            ->where('user_id','!=',Auth::user()->id)
            ->with('user')
            ->get();
        $userIdArr = data_get($customerEmployeeAllocationModel,"*.user.id");

        return UserResource::collection(
            User::whereIn('id', $userIdArr)->orderBy('first_name')->get()
        );
    }

    public function clientConcernStore(ClientConcernRequest $request) {
        return $this->clientService->storeConcern($request);
    }


    public function clientFeedbackStore(ClientFeedbackRequest $request) {
        return $this->clientService->storeFeedback($request);
    }


}
