<?php


namespace Modules\ClientApp\Services\Client;


use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Modules\Client\Repositories\ClientRepository;
use Modules\Client\Repositories\ConcernRepository;
use Modules\ClientApp\Http\Resources\V1\Client\ClientConcernResource;
use Modules\ClientApp\Http\Resources\V1\Client\ClientFeedbackResource;

class ClientService
{

    protected $concernRepository;
    protected $clientRepository;

    public function __construct(
        ConcernRepository $concernRepository,
        ClientRepository $clientRepository
    )
    {
        $this->concernRepository = $concernRepository;
        $this->clientRepository = $clientRepository;
    }


    public function storeConcern($request)
    {
        $dbRequest = (object)([
            "id" => null,
            "customer_id" => $request->customerId,
            "severity" => $request->severity,
            "concern" => $request->info,
        ]);
        $storeResponse = $this->concernRepository->store(Auth::user()->id, $dbRequest);
        if(is_array($storeResponse) && isset($storeResponse["success"])) {
            return new ClientConcernResource($storeResponse["success"]);
        } else {
            throw new \Exception($storeResponse->getData()->error);
        }
    }

    public function storeFeedback($request)
    {
        $dbRequest = (object)([
            "id" => null,
            "customer_id" => $request->customerId,
            "employee_id" => $request->employeeId,
            "employee_rating_lookup_id" => $request->ratingId,
            "feedback_id" => $request->feedbackTypeId,
            "customer_feedback" => $request->info,
        ]);
        $storeResponse = $this->clientRepository->store(Auth::user()->id, $dbRequest);
        if(is_array($storeResponse) && isset($storeResponse["success"])) {
            return new ClientFeedbackResource($storeResponse["success"]);
        } else {
            throw new \Exception($storeResponse->getData()->error);
        }
    }

}
