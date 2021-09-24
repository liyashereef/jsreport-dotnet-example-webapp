<?php

namespace Modules\Timetracker\Http\Controllers\API\v1;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
// use Illuminate\Support\Facades\DB;
use Modules\Timetracker\Http\Requests\DispatchRequestDeclineRequest;
use Modules\Timetracker\Models\DispatchRequest;
use Modules\Timetracker\Models\PushNotificationLog;
use Modules\Timetracker\Models\LiveLocation;
use Modules\Timetracker\Repositories\DispatchRequestDeclineRepository;
use Modules\Timetracker\Repositories\DispatchRequestRepository;
use DB;
class DispatchRequestApiController extends Controller
{
    protected $repository;
    protected $dispatch_request_decline_repo;

    public function __construct(DispatchRequestRepository $dispatchRequestRepository, DispatchRequestDeclineRepository $dispatchRequestDeclineRepo)
    {
        $this->repository = $dispatchRequestRepository;
        $this->dispatch_request_decline_repo = $dispatchRequestDeclineRepo;
    }

    /**
     * Get All Request by status
     * @param $status 0= All, 1= Open, 2= In Progress, 3=Arrived & Started Investigation, 4=Closed
     * Based on dispatch_request_statuses table
     * @return
     */
    public function getAllByStatus(Request $request)
    {
        $inputs = $request->input('status');
        return $this->repository->getAllByStatus($inputs);
    }

    /**
     * Get All Request by status array
     * @param $status 0= All, 1= Open, 2= In Progress, 3=Arrived & Started Investigation, 4=Closed
     * Based on dispatch_request_statuses table
     * @return
     */
    public function getAllByStatusArray(Request $request)
    {
        $inputs = $request->input('status');
        return $this->repository->getAllByStatusArray($inputs);
    }

    /**
     * Get All Request done by Logged user
     * @param Auth::id
     *
     * @return
     */
    public function getAllMyRequest()
    {
        return $this->repository->getAllMyRequest();
    }

    /**
     * Get single Request buy ID
     * @param $dispatch_request_id
     *
     * @return
     */
    public function getDetailsById(Request $request)
    {
        $id = $request->get('id');
        return $this->repository->getDetailsById($id);
    }

    /**
     * Update Request
     * @param $dispatch_request_id
     *
     * @return
     */
    public function mstRequestStatusUpdate(Request $request)
    { //dd($request->get('dispatch_request_id'));
        return $this->repository->updateRespondByStatus($request->all());
    }

    /**
     * Store decline a request
     * @param $dispatch_request_id
     *
     * @return
     */
    public function declineRequest(DispatchRequestDeclineRequest $request)
    {
        return $this->dispatch_request_decline_repo->store($request->all());
    }

    public function getByStatusStrings(Request $request)
    {

        if ($request->has('status')) {
            $input = $request->input('status');
            if ($input == 0) {
                return $this->repository->all();
            }
            $stats = explode(',', $input);
            return $this->repository->getAllByStatusArray($stats);
        }

        return $this->repository->all();
    }
    
    public function getAllDeviceCoordinates($searchKey)
    {
        
        $userIds = [];
        if(isset($searchKey['user_id']) && $searchKey['user_id']!= null){ 
            $userIds = [(int)$searchKey['user_id']]; 
        }else{
            $userIds = $this->repository->getActiveRespondUserIds();
        }
      
        return LiveLocation::where('dispatch_request_id','>=',1)
        ->whereIn('user_id',$userIds)
        ->orderBy('created_at','DESC')
        ->groupBy('dispatch_request_id')
        ->has('pending_dispatch_request')
        ->select('user_id','dispatch_request_id','latitude','longitude','created_at','is_idle')
        ->get()
        ->load(['user','pending_dispatch_request']);

    }

    public function getEmployeeLiveCoodinates(Request $request)  {
        $inputs = $request->all();
        return response()->json([
            'success' => true,
            'content' => $this->getAllDeviceCoordinates($inputs)
        ], 200);
    }

    public function getDispatchRequestNotifications(Request $request){
         
         return PushNotificationLog::where('user_id',\Auth::id())
         ->where('request_type',PUSH_MST)
        ->select('request_id','created_at')
        ->groupBy('request_id')
        ->orderBy('created_at', 'desc')
        ->with(array(
            'dispatch_request',
            'dispatch_request.customer'=>function($query){
                $query->select('id',
                'project_number',
                'client_name',
                'contact_person_name',
                'contact_person_email_id',
                'contact_person_phone',
                'address',
                'province',
                'city',
                'postal_code',
                'billing_address');
            },
            'dispatch_request.dispatchRequestType',
            'dispatch_request.dispatchRequestStatus'
            ))
        ->get();
  
        //$dispatchRequestIds = data_get($notificationLogs->toArray(), "*.dispatch_request_id");
// dd($notificationLogs,$dispatchRequestIds);


        // return $notifications = DispatchRequest::whereIn('id',$dispatchRequestIds)
        // ->orderBy('dispatch_request_status_id')
        // ->with(array(
        //     'customer'=>function($query){
        //         $query->select('id',
        //         'project_number',
        //         'client_name',
        //         'contact_person_name',
        //         'contact_person_email_id',
        //         'contact_person_phone',
        //         'address',
        //         'province',
        //         'city',
        //         'postal_code',
        //         'billing_address');
        //     },'dispatchRequestType','dispatchRequestStatus'))
        // ->get()->load('PushNotificationLog');
        
        // return response()->json([
        //     'success' => true,
        //     'content' => $notifications
        // ], 200);

    }


}
