<?php

namespace Modules\Admin\Repositories;

use App\Services\HelperService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Modules\Admin\Models\Customer;
use Modules\Admin\Models\CustomerRoom;
use Modules\Admin\Models\Sensor;
use Modules\Admin\Models\SensorRoomAllocationHistory;
use Modules\Sensors\Models\SensorActiveSetting;
use Modules\IpCamera\Models\IpCameraRoomAllocationHistories;
use Modules\IpCamera\Models\IpCamera;

class CustomerRoomRepository
{
    /**
     * The Model instance.
     * ''
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $model, $sensorModel, $sensoractiveModel;

    /**
     * Create a new FeedbackLookupRepository instance.
     *
     * @param \App\Models\FeedbackLookup $feedbackLookup
     */
    public function __construct(Customer $customerModel, CustomerRoom $customerRoom, Sensor $sensorModel,SensorActiveSetting $sensoractiveModel, SensorRoomAllocationHistory $sensorRoomAllocationModel, IpCamera $ipCameraModel, IpCameraRoomAllocationHistories $ipCameraRoomAllocationHistories, HelperService $helperService)
    {
        $this->model = $customerRoom;
        $this->customerModel = $customerModel;
        $this->sensorModel = $sensorModel;
        $this->sensorRoomAllocationModel = $sensorRoomAllocationModel;
        $this->sensoractivesetting = $sensoractiveModel;
        $this->ipCameraModel = $ipCameraModel;
        $this->ipCameraRoomAllocationHistories = $ipCameraRoomAllocationHistories;
        $this->helperService = $helperService;
    }

    /**
     * Get security clearance lookup list
     *
     * @param empty
     * @return array
     */
    public function getAll($id=null,$client_id=null)
    {
        $roomList =  $this->model->select(['id', 'customer_id', 'name','severity_id', 'created_at', 'updated_at'])->with(['customer','linkedSensors','linkedIpCameras'])->get();
        $roomList=$roomList->when($client_id!=null, function ($q) use ($client_id) {
            return $q->where('customer_id', $client_id);
        });
        return $this->prepareDataForRoomList($roomList);
    }

    /**
     * Prepare datatable elements as array.
     * @param $sensorList
     * @return array
     */

    public function prepareDataForRoomList($roomList)
    {
        $room_severity = config('globals.room_severity');
        $datatable_rows = array();
        foreach ($roomList as $key => $each_list) {
            $each_row["id"] = isset($each_list->id) ? $each_list->id : "--";
            $each_row["name"] = isset($each_list->name) ? $each_list->name : "--";
            $each_row["customer_name"] = isset($each_list->customer) ? $each_list->customer->client_name : "--";
            $each_row["total_assigned_sensors"] = (count($each_list->linkedSensors) > 0) ? count($each_list->linkedSensors) : "none";
            $each_row["severity_id"] = $room_severity[$each_list->severity_id];
            $each_row["motion_sensor_enabled"] = isset($each_list->customer) ? $each_list->customer->motion_sensor_enabled : "--";
            $each_row["motion_sensor_incident_subject"] = isset($each_list->customer) ? $each_list->customer->motion_sensor_incident_subject : "--";
            $each_row["total_assigned_ipcameras"] = (count($each_list->linkedIpCameras) > 0) ? count($each_list->linkedIpCameras) : "none";
            array_push($datatable_rows, $each_row);
        }
        return $datatable_rows;
    }



    /**
     * Get Position lookup list
     *
     * @param empty
     * @return array
     */
    // public function getList()
    // {
    //     $result = $this->model
    //         ->orderBy(\DB::raw("FIELD(feedback,'Poor','Average','Below','Good','Excellent')"))
    //         ->pluck('feedback', 'id')
    //         ->toArray();
    //     return $result;
    // }

    /**
     * Display details of single resource
     *
     * @param $id
     * @return object
     */
    public function get($id)
    {
        return $this->model->with(['customer'])->find($id);
    }

    public function getIpCameraAllocatedRoom($customerId){
        if (is_array($customerId)) {
            $customer_arr=$customerId;
        }else{
            $customer_arr[]=$customerId;
        }
        $allocatedRoom = $this->model->with(['linkedIpCameras'])->whereIn('customer_id', $customer_arr)->get();
        return $allocatedRoom;
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  $data
     * @return object
     */
    public function save($request)
    {
        if (isset($request['id']) && !empty($request['id'])) {
            $data = ['name' => $request['name'],'severity_id' => $request['severity_id'], 'updated_by' => Auth::user()->id, 'updated_at' => Carbon::now()];
        } else {
            $data = [
                'name' => $request['name'],
                'severity_id' => $request['severity_id'],
                'customer_id' => $request['customer_id'],
                'machine_name' => HelperService::generateMachineCode($request['name']),
                'created_by' => Auth::user()->id,
                'created_at' => Carbon::now()
            ];
        }
        return $this->model->updateOrCreate(array('id' => $request['id']), $data);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  $id
     * @return object
     */
    public function delete($id)
    {
        $allocatedSensorList = $this->sensorModel->select('id')->where('room_id', $id)->get();
        if (!$allocatedSensorList->isEmpty()) {
            foreach ($allocatedSensorList as $sensorKey => $sensors) {
                $this->sensorModel->where('id', $sensors->id)->update(['room_id' => null, 'room_allocated_at' => null,'enabled' => false]);
            }
        }
        $result =  $this->model->destroy($id);
        if($result){
            $this->sensoractivesetting->where('room_id',$id)->delete();
            return $allocatedSensorList->pluck('id')->toArray();
        } else {
            return false;
        }

    }

    /**
     * Display details of single resource
     *
     * @param $id
     * @return object
     */
    public function getLinkSensor($id)
    {
        return $this->model->with(['customer','activeSensors'])->find($id);
    }

    public function getLinkIpCamera($id)
    {
        return $this->model->with(['customer'])->find($id);
    }

    /**
     * Display details of single resource
     *
     * @param $id
     * @return object
     */
    public function getUnLinkSensor($id)
    {
        return $this->model->with(['linkedSensors'])->find($id);
    }

    public function getUnLinkIpCamera($id)
    {
        return $this->model->with(['linkedIpCameras'])->find($id);
    }

    public function sensorsList()
    {
        return $this->sensorModel->where('room_id', null)->get();
    }

    public function IpCamerasList()
    {
        return $this->ipCameraModel->where('room_id', null)->get();
    }

    public function linkSensorSave($request)
    {
        if ($request['sensor_id'] == !null) {
            foreach ($request['sensor_id'] as $sensorKey => $sensorId) {
                $data = ['enabled' => true,'room_id' => $request['id'], 'room_allocated_at' => Carbon::now(), 'updated_by' => Auth::user()->id, 'updated_at' => Carbon::now()];
                $result = $this->sensorModel->updateOrCreate(array('id' => $sensorId), $data);
                if ($result) {
                    $sensorRoomAllocationHistory = new SensorRoomAllocationHistory;
                    $sensorRoomAllocationHistory->sensor_id = $sensorId;
                    $sensorRoomAllocationHistory->room_id = $request['id'];
                    $sensorRoomAllocationHistory->is_linked = true;
                    $sensorRoomAllocationHistory->save();
                }
            }
            return $result;
        } else {
            $this->helperService->returnFalseResponse();
        }
    }

    public function linkIpCameraSave($request)
    {
        if ($request['ipcamera_id'] == !null) {
            foreach ($request['ipcamera_id'] as $ipCameraKey => $ipCameraId) {
                $data = ['enabled' => true,'room_id' => $request['id'], 'room_allocated_at' => Carbon::now(), 'updated_by' => Auth::user()->id, 'updated_at' => Carbon::now()];
                $result = $this->ipCameraModel->updateOrCreate(array('id' => $ipCameraId), $data);
                if ($result) {
                    $ipCameraRoomAllocationHistory = new IpCameraRoomAllocationHistories;
                    $ipCameraRoomAllocationHistory->ipcamera_id = $ipCameraId;
                    $ipCameraRoomAllocationHistory->room_id = $request['id'];
                    $ipCameraRoomAllocationHistory->is_linked = true;
                    $ipCameraRoomAllocationHistory->save();
                }
            }
            return $result;
        } else {
            $this->helperService->returnFalseResponse();
        }
    }


    public function unLinkSensorSave($request)
    {
        $linkedsensorList = $this->sensorModel->where('room_id', $request['room_id'])->pluck('id')->toArray();
        $unlinkedSensorList = isset($request['unlink_sensor_id']) ? $request['unlink_sensor_id'] : [];
        $diffList = array_diff($linkedsensorList, $unlinkedSensorList);
        if (!empty($diffList)) {
            foreach ($diffList as $sensorKey => $sensorId) {
                $result = $this->sensorModel->where('id', $sensorId)->update(['enabled' => false,'room_id' => null, 'room_allocated_at' => null, 'updated_by' => Auth::user()->id, 'updated_at' => Carbon::now()]);
                if ($result) {
                    $sensorRoomAllocationHistory = new SensorRoomAllocationHistory;
                    $sensorRoomAllocationHistory->sensor_id = $sensorId;
                    $sensorRoomAllocationHistory->room_id = $request['room_id'];
                    $sensorRoomAllocationHistory->is_linked = false;
                    $sensorRoomAllocationHistory->save();
                }
            }
            return $diffList;
        } else {
            $this->helperService->returnFalseResponse();
        }
    }

    public function unLinkIpCameraSave($request){
        $linkedIpCameraList = $this->ipCameraModel->where('room_id', $request['room_id'])->pluck('id')->toArray();
        $unlinkedIpCameraList = isset($request['unlink_ipcamera_id']) ? $request['unlink_ipcamera_id'] : [];
        $diffList = array_diff($linkedIpCameraList, $unlinkedIpCameraList);
        if (!empty($diffList)) {
            foreach ($diffList as $ipCameraKey => $ipCameraId) {
                $result = $this->ipCameraModel->where('id', $ipCameraId)->update(['enabled' => false,'room_id' => null, 'room_allocated_at' => null, 'updated_by' => Auth::user()->id, 'updated_at' => Carbon::now()]);
                if ($result) {
                    $sensorRoomAllocationHistory = new IpCameraRoomAllocationHistories;
                    $sensorRoomAllocationHistory->ipcamera_id = $ipCameraId;
                    $sensorRoomAllocationHistory->room_id = $request['room_id'];
                    $sensorRoomAllocationHistory->is_linked = false;
                    $sensorRoomAllocationHistory->save();
                }
            }
            return $diffList;
        } else {
            $this->helperService->returnFalseResponse();
        }
    }

    /**
     * Set active event status of the room
     *
     * @param $request
     * @param null $sensorTriggerId
     * @param bool $startEvent
     * @return false|object
     */
    public function updateSensorEvent($request, $sensorTriggerId = null, $startEvent = true)
    {
        //get parameters from request
        $customerId = $request->get('customer')->id;
        $room_id = $request->get('room')->id;
        $eventTime = $request->get('createdAt');
        //validate data
        $customerRoom = $this->model->select('id')
            ->where('customer_id', $customerId)
            ->where('id', $room_id)->latest();
        $customerRoomId = $customerRoom->first();
        // if okay
        if ($customerRoomId !== null && $eventTime !== null) {
            // update event
            $data = [
                'active_event' => $startEvent,
                'event_updated_at' => $eventTime,
                'updated_by' => Auth::user()->id,
            ];
        } else {
            Log::channel('motionSensor')
                ->error("Event Update start error: Room id / time not identified");
            return false;
        }
        if(isset($sensorTriggerId)) {
            $data['sensor_trigger_id'] =  $sensorTriggerId;
        }
        return $customerRoom->update($data);
    }

    /**
     * Check if the room has any active events
     * @param $request
     * @return
     */
    public function getActiveEvent($request)
    {
        //get parameters from request
        $customerId = $request->get('customer')->id;
        $room_id = $request->get('room')->id;
        $eventTime = $request->get('createdAt');

        // get data
        return $this->model
            ->where('customer_id', $customerId)
            ->where('id', $room_id)->latest()->first();
    }

    /**
     * Display details of single resource
     *
     * @param $id
     * @return object
     */
    public function getSensorTriggerByRoom($id)
    {
        return $this->model->select('sensor_trigger_id')->find($id);
    }
}
