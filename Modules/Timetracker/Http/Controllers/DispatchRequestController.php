<?php

namespace Modules\Timetracker\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Timetracker\Jobs\DispatchRequestPushNotification;
use Modules\Timetracker\Models\DispatchRequest;
use Modules\Timetracker\Models\DispatchRequestType;
use Modules\Admin\Models\Customer;
use Modules\Timetracker\Repositories\DispatchRequestRepository;
use Modules\Admin\Repositories\CustomerRepository;
use Modules\Timetracker\Repositories\DispatchRequestTypeRepository;

use Modules\Timetracker\Repositories\DispatchRequestStatusRepository;
use App\Services\HelperService;
use App\Services\LocationService;
use Modules\Timetracker\Http\Requests\DispatchRequestForRequest;
use Modules\Timetracker\Models\DispatchRequestPushNotification as ModulesDispatchRequestPushNotification;
use Modules\Timetracker\Models\DispatchRequestPushNotificationCustomers;
use Modules\Timetracker\Repositories\DispatchRequestDeclineRepository;
use Modules\Timetracker\Repositories\EmployeeShiftRepository;
use Modules\Admin\Repositories\CustomerEmployeeAllocationRepository;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Queue;
use App\Repositories\PushNotificationRepository;

class DispatchRequestController extends Controller
{

    protected $dispatchRequestRepository;
    protected $customerRepository;
    protected $dispatchRequestTypeRepository;
    protected $dispatchRequestStatusRepository;
    protected $pushNotificationRepository;


    public function __construct(
        DispatchRequestRepository $dispatchRequestRepository,
        CustomerRepository $customerRepository,
        DispatchRequestTypeRepository $dispatchRequestTypeRepository,
        DispatchRequestDeclineRepository $dispatchRequestDeclineRepository,
        DispatchRequestStatusRepository $dispatchRequestStatusRepository,
        EmployeeShiftRepository $employeeShiftRepository,
        PushNotificationRepository $pushNotificationRepository,
        CustomerEmployeeAllocationRepository $customerEmployeeAllocationRepository,
        HelperService $helper_service,
        LocationService $locationService
    ) {
        $this->dispatchRequestRepository = $dispatchRequestRepository;
        $this->customerRepository = $customerRepository;
        $this->dispatchRequestTypeRepository = $dispatchRequestTypeRepository;
        $this->dispatchRequestDeclineRepository = $dispatchRequestDeclineRepository;
        $this->dispatchRequestStatusRepository = $dispatchRequestStatusRepository;
        $this->helper_service = $helper_service;
        $this->employee_shift_repository = $employeeShiftRepository;
        $this->push_notification_repository = $pushNotificationRepository;
        $this->customerEmployeeAllocationRepository = $customerEmployeeAllocationRepository;
    }

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        $customer_details_arr = $this->customerRepository->getProjectsDropdownList('all');
        return view('timetracker::admin.dispatch_request.list',compact('customer_details_arr'));
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {
        $requestTypes = $this->dispatchRequestTypeRepository->getAll();
        //dd($requestTypes);
        $customers = $this->customerRepository->getProjectsDropdownList('all');

        return view('timetracker::admin.dispatch_request.form', compact('customers', 'requestTypes'));
    }

    public function getCustomerDetails($id)
    {
        return $this->customerRepository->getSingleCustomer($id);
    }

    public function getRequestTypeDetails($id)
    {
        return $this->dispatchRequestTypeRepository->getById($id);
    }

    /**
     * Store a newly created resource in storage.
     * @param DispatchRequestForRequest $request
     * @return Response
     */
    public function store(DispatchRequestForRequest $request)
    {

        try {
            \DB::beginTransaction();
            $request->request->remove('_token');

            $createdBy = \Auth::id();
            $request->request->add(['created_by' => $createdBy]);
            $request->request->add(['dispatch_request_status_id' => 1]);
             $dispatch_request = $this->dispatchRequestRepository->save($request->all());
            if ($dispatch_request) {
                $request->request->add(['dispatch_request_id' => $dispatch_request->dispatch_request_id]);
                $dispatch_request_status_entry = $this->dispatchRequestStatusRepository->dispatchResquestStatusEntry($request->all());
                 $this->triggerPushNotification($dispatch_request->dispatch_request_id);
            }
            \DB::commit();
            \Session::flash('flash_message', 'Successfully saved.');
            return redirect()->route('dispatchrequest.index')->with('message', 'Success');;
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json($this->helper_service->returnFalseResponse($e));
        }
    }

    /**
     * Show the specified resource.
     * @return Response
     */
    public function show($id)
    {
        //dd($id);
        $dispatchRequestShows = $this->dispatchRequestRepository->getDetailsById($id);
        //dd($dispatchRequestShows->dispatchRequestType->name);
        return view('timetracker::admin.dispatch_request.show', compact('dispatchRequestShows', 'id'));
    }

    /**
     * Show the form for editing the specified resource.
     * @return Response
     */
    public function edit($id)
    {
        $requestTypes = $this->dispatchRequestTypeRepository->getAll();

        $customers = $this->customerRepository->getProjectsDropdownList('all');

        $dispatchRequestEdits = $this->dispatchRequestRepository->getById($id);
         $notify_customers = data_get(DispatchRequestPushNotificationCustomers::where('dispatch_request_id', $id)->get()->toArray(), "*.customer_id");

        return view('timetracker::admin.dispatch_request.edit', compact('customers', 'requestTypes', 'dispatchRequestEdits','notify_customers'));
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @return Response
     */
    public function update(Request $request, $id)
    {

        try {
            \DB::beginTransaction();
            $request->request->remove('_token');
            $request->request->remove('_method');
            $dispatch_request = $this->dispatchRequestRepository->update($request->all(), $id);
            //dd($dispatch_request);
            \DB::commit();
            \Session::flash('flash_message', 'Updated successfully.');
            return redirect()->route('dispatchrequest.index')->with('message', 'Success');;
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json($this->helper_service->returnFalseResponse($e));
        }
    }

    /**
     * Remove the specified resource from storage.
     * @return Response
     */
    public function destroy()
    { }

    public function list(Request $request)
    {
        $client_id = $request->get('client_id')?:null;
        $dispatchRequest = $this->dispatchRequestRepository->all($client_id);
        return datatables()->of($dispatchRequest)->toJson();
    }

    // REQUEST DECLINE LIST
    public function declineList($dispatch_request_id)
    {
        $dispatchRequestDeclineList = $this->dispatchRequestDeclineRepository->getByRequestId($dispatch_request_id);
        return datatables()->of($dispatchRequestDeclineList)->addIndexColumn()->toJson();
    }

    public function getLoacationByPostalCode($postal_code)
    {
        return $this->locationService->getLatLongByAddress($postal_code);
    }

    /**
     * Initiate Push notification
     * @param dispatch_request_id
     * @return status
     */
    public function triggerPushNotification($dispatch_request_id)
    {
        // Log::channel('customlog')->info('------START----- triggerPushNotification');
        $data = [];
        $dispatch_request = DispatchRequest::find($dispatch_request_id);

        if ($dispatch_request) {

            $subject = $dispatch_request->subject;
            $shift_type_id = SHIFT_TYPE_MSP_ARRAY;
            $live_status_id = [AVAILABLE, MEETING];
            $title = 'Dispatch Request';
            //Find employees to send notification
            $customerIds = DispatchRequestPushNotificationCustomers::where('dispatch_request_id', $dispatch_request_id)
                ->pluck('customer_id')
                ->toArray();
            $userIds = $this->employee_shift_repository->getActiveShiftEmployes($customerIds, $shift_type_id,$live_status_id);

            //remove employees already processing request
            $request_processing_user_ids = $this->dispatchRequestRepository->getActiveRespondUserIds();
            if (!empty($request_processing_user_ids)) {
                $userIds = array_diff($userIds, $request_processing_user_ids);
            }

            //Initiate Push notification
            if (!empty($userIds)) {
                // Log::channel('customlog')->info('------Call----- JOB');
                // $result = DispatchRequestPushNotification::dispatch($userIds, $dispatch_request_id,$subject);
                $result = $this->push_notification_repository->sendPushNotification($userIds, $dispatch_request_id,PUSH_MST,$title,$subject);
            }
            // Log::channel('customlog')->info('------End----- triggerPushNotification');
            $data['success'] = 1;
        }
        return $data;
    }

    public function statusClose($id)
    {
        try {
            \DB::beginTransaction();
            $status_close = $this->dispatchRequestRepository->close($id);
            \DB::commit();
            return response()->json($this->helper_service->returnTrueResponse());
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json($this->helper_service->returnFalseResponse());
        }
    }


}
