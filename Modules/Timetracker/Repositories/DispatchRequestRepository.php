<?php

namespace Modules\Timetracker\Repositories;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\Timetracker\Models\DispatchRequest;
use Modules\Timetracker\Repositories\DispatchRequestStatusRepository;
use Modules\Timetracker\Models\DispatchRequestPushNotificationCustomers;
use Modules\Timetracker\Models\PushNotificationLog;

class DispatchRequestRepository
{
    protected $model, $dispatchRequestStatusRepository, $push_notification_customers;

    public function __construct(
        DispatchRequest $dispatchRequest,
        DispatchRequestStatusRepository $dispatchRequestStatusRepository,
        DispatchRequestPushNotificationCustomers $push_notification_customers
    ) {
        $this->model = $dispatchRequest;
        $this->dispatchRequestStatusRepository = $dispatchRequestStatusRepository;
        $this->push_notification_customers = $push_notification_customers;
    }

    public function all($client_id=null)
    {
        $data = $this->model->orderBy('dispatch_request_status_id')->get();
        $data = $data->when($client_id!=null, function ($q) use ($client_id) {
            return $q->where('customer_id', $client_id);
        });
        return $data
        ->load('dispatchRequestType', 'customer_trashed', 'dispatchRequestStatus');
    }

    public function getAllByUserIds($user_id = [], $status = [])
    {
        return $this->model
            ->where(function ($query) use ($user_id) {
                if (!empty($user_id)) {
                    $query->whereIn('respond_by', $user_id);
                }
            })
            ->where(function ($query) use ($status) {
                if (!empty($status)) {
                    $query->whereIn('dispatch_request_status_id', $status);
                }
            })->get()
            ->load('dispatchRequestType', 'customer', 'dispatchRequestStatus');
    }

    public function save($data)
    {
        $inputs = $data;

        if (is_null($inputs['customer_id'])) {
            $inputs['is_existing_customer'] = 0;
        }

        if (!empty($inputs['notify_customer_ids'])) {
            unset($inputs['notify_customer_ids']);
        }

        $result = $this->model->updateOrCreate($inputs);

        if ($result) {
            if (!empty($data['notify_customer_ids'])) {

                $push['dispatch_request_id'] = $result->id;
                foreach ($data['notify_customer_ids'] as $notify_customer_id) {
                    $push['customer_id'] = $notify_customer_id;
                    $this->push_notification_customers->create($push);
                }
            }
        }

        $created = $result->wasRecentlyCreated;
        $result = $result->fresh();
        $result['created'] = $created;
        $result['dispatch_request_id'] = $result->id;
        return $result;
    }
    public function show($id)
    {
        return $this->model->findOrFail($id);
    }

    public function getById($id)
    {
        return $this->model->find($id);
    }

    public function delete($id)
    {
        return $this->model->destroy($id);
    }


    public function update(array $data, $id)
    {

        $inputs = $data;


        if (!empty($inputs['notify_customer_ids'])) {
            unset($data['notify_customer_ids']);
        }
        $record = $this->model->find($id);

        if (!isset($inputs['customer_id']) || is_null($inputs['customer_id'])) {
            $data['is_existing_customer'] = 0;
            $data['customer_id'] = null;
        } else {
            $data['is_existing_customer'] = 1;
            $data['name'] = '';
        }
        // dd( $inputs ,$data,$id);
        $result = $record->update($data);
        if ($result) {
            $this->push_notification_customers->where('dispatch_request_id', $id)->delete();
            if (!empty($inputs['notify_customer_ids'])) {
                $push['dispatch_request_id'] = $id;
                foreach ($inputs['notify_customer_ids'] as $notify_customer_id) {
                    $push['customer_id'] = $notify_customer_id;
                    $this->push_notification_customers->create($push);
                }
            }
        }
    }

    /**
     * Get All Request by status
     * @param $status 0= All, 1= Open, 2= In Progress, 3=Arrived & Started Investigation, 4=Closed
     * Based on dispatch_request_statuses table
     * @return
     */
    public function getAllByStatus($inputs)
    {

        return $this->model
            ->where(function ($query) use ($inputs) {
                if (isset($inputs) && $inputs != 0) {
                    $query->where('dispatch_request_status_id', $inputs);
                }
            })->get()
            ->load('dispatchRequestType', 'customer', 'dispatchRequestStatus');
    }

    /**
     * Get all Request details by dispatch_request_status_id array
     * @param dispatch_request_status_ids
     *
     * @return object
     */

    public function getAllByStatusArray($status)
    {

        return $this->model
            ->where(function ($query) use ($status) {
                if (isset($status) && $status != 0) {
                    $query->whereIn('dispatch_request_status_id', $status);
                }
            })->get()
            ->load('dispatchRequestType', 'customer');
    }

    /**
     * Get all Request details respond_by logged user.
     * @param Auth::id()
     *
     * @return object
     */
    public function getAllMyRequest()
    {
        return $this->model
            ->where('respond_by', Auth::id())
            ->get()
            ->load('dispatchRequestType', 'customer');
    }

    /**
     * Get Single Request details by Id for mobile api
     * @param $dispatch_request_id
     *
     * @return object
     */
    public function getDetailsById($id)
    {
        $notification_log = PushNotificationLog::where('request_id',(int)$id)->orderBy('created_at', 'desc')
        ->first();
        $result = $this->model->find($id)->load('dispatchRequestType', 'customer_data', 'respondby');
        if($notification_log){
            $result->received_time =date("h:i A",strtotime($notification_log->created_at));
            $result->received_date =date("l, M d, Y",strtotime($notification_log->created_at));
        }else{
            $result->received_time = '';
            $result->received_date = '';
        }
        return $result;
    }

    public function updateRespondByStatus($request)
    {

        try {
            DB::beginTransaction();

            $id = $request['dispatch_request_id'];
            $status_id = $request['dispatch_request_status_id'];
            $request_details = $this->getDetailsById($id);
            if (isset($request_details)) {
                if ($status_id == 2) {
                    $dispatch = $this->model->where('id', $id)->update(['dispatch_request_status_id' => $status_id, 'respond_by' => Auth::id(), 'respond_at' => Carbon::now()]);
                } else {
                    $dispatch = $this->model->where('id', $id)->update(['dispatch_request_status_id' => $status_id]);
                }
                if ($dispatch) {
                    $dispatch_request_status_entry = $this->dispatchRequestStatusRepository->dispatchResquestStatusEntry($request);
                    $content['success'] = true;
                    $content['message'] = 'Respond Details updated';
                    $content['code'] = 200;
                } else {
                    $content['success'] = false;
                    $content['message'] = 'Respond Details not updated. Please try again.';
                    $content['code'] = 406;
                }
            } else {
                $content['success'] = true;
                $content['message'] = 'Respond Details already updated.';
                $content['code'] = 406;
            }

            DB::commit();
        } catch (\Exception $e) {
            $content['success'] = false;
            $content['message'] = $e->getMessage();
            $content['code'] = 406;
        }

        return response()->json(['content' => $content], $content['code']);
    }

    public function getActiveRespondUserIds()
    {
        return data_get($this->model->whereIn('dispatch_request_status_id', DISPATCH_PROGRESS_STATUS)
            ->select('respond_by as user_id')->get()->toArray(), "*.user_id");
    }

    public function close($id)
    {
        $data = ['dispatch_request_status_id' => 4,'id'=>$id];
        $status = $this->model->updateOrCreate(array('id' => $data['id']), $data);
        return  $status;
    }
}
