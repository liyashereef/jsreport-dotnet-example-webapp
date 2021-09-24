<?php

namespace Modules\Sensors\Repositories;

use App\Services\HelperService;
use Carbon\Carbon;
use Auth;
use Modules\Sensors\Models\Sensor;
use Modules\Admin\Models\Days;
use Modules\Admin\Models\CustomerRoom;
use Modules\Sensors\Models\SensorConfigSetting;
use Modules\Sensors\Models\SensorActiveSetting;

class SensorRepository
{
    /**
     * The Model instance.
     * ''
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $model;
    /**
     * @var HelperService
     */
    private $helperService;

    /**
     * Create a new  instance.
     *
     * @param Sensor $sensorModel
     * @param HelperService $helperService
     */
    public function __construct(
        Sensor $sensorModel,
        SensorActiveSetting $sensoractiveModel,
        SensorConfigSetting $senorconfigModel,
        Days $dayModel,
        CustomerRoom $customerroomModel,
        HelperService $helperService
        )
    {
        $this->model = $sensorModel;
        $this->helperService = $helperService;
        $this->daymodel = $dayModel;
        $this->sensoractivesetting = $sensoractiveModel;
        $this->sensorconfig = $senorconfigModel;
        $this->roommodel = $customerroomModel;

    }

    /**
     * Get lookup list
     *
     * @param empty
     * @return array
     */
    public function getAll($id = null)
    {

        $sensorList = $this->model->with(['room', 'room.customer'])
            ->when($id !== null, function ($query) use ($id) {
                $query->where('room_id', '=', $id);
            })->get();
        return $this->prepareDataForSensorList($sensorList);
    }

    /**
     * Prepare datatable elements as array.
     * @param $sensorList
     * @return array
     */

    public function prepareDataForSensorList($sensorList)
    {

        $datatable_rows = array();
        foreach ($sensorList as $key => $each_list) {
            $each_row["id"] = isset($each_list->id) ? $each_list->id : "--";
            $each_row["name"] = isset($each_list->name) ? $each_list->name : "--";
            $each_row["room_name"] = isset($each_list->room) ? $each_list->room->name : "--";
            $each_row["customer_name"] = isset($each_list->room->customer) ? $each_list->room->customer->client_name : "--";
            $each_row["nod_mac"] = isset($each_list->nod_mac) ? $each_list->nod_mac : "--";
            $each_row["online"] = isset($each_list->online) ? $each_list->online : "--";
            $each_row["low_battery"] = isset($each_list->low_battery) ? $each_list->low_battery : "--";
            $each_row["enabled"] = isset($each_list->enabled) ? $each_list->enabled : "--";

            array_push($datatable_rows, $each_row);
        }
        return $datatable_rows;
    }


    /**
     * Display details of single resource
     *
     * @param $id
     * @return object
     */
    public function get($id)
    {
        return $this->model->with(['room', 'room.customer'])->find($id);
    }

    /**
     * Get details of sensor by node mac
     * @param $nodeMac
     * @return mixed
     */
    public function getByNodeMac($nodeMac)
    {
        return $this->model->where('nod_mac', $nodeMac)->first();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param $request
     * @return object
     */
    public function save($request)
    {
        if (!isset($request['enabled'])) {
            $request['enabled'] = 0;
        }

        $data = [
            'name' => $request['name'],
            'nod_mac' => $request['nod_mac'],
            'pan_mac' => $request['pan_mac'],
            'gateway_mac' => $request['gateway_mac'],
            'enabled' => $request['enabled'],
        ];
        if (!isset($request['id']) && empty($request['id'])) {
            $data['machine_name'] = HelperService::generateMachineCode($request['name']);
            $data['created_by'] = \Auth::user()->id;
            $data['created_at'] = Carbon::now();
        } else {
            $data['updated_by'] = \Auth::user()->id;
            $data['updated_at'] = Carbon::now();
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
        $isSensorAllocated = $this->model->select('id','room_id','room_allocated_at')->where('id', $id)->first();
        if(isset($isSensorAllocated->room_id) && !empty($isSensorAllocated->room_id)){
            $this->model->where('id', $id)->update(['enabled' => false,'room_id' => null, 'room_allocated_at' => null,'room_allocated_at' => null]);
        }
        return $this->model->destroy($id);
    }

    /**
     * Update sensor detection time
     * @param $sensorId
     * @param $dateTime
     * @return
     */
    public function updateInitialTriggerTime($sensorId, $dateTime)
    {
        $currentUserId = \Auth::user()->id;
        $data = [
            'latest_detection_at' => $dateTime,
            'updated_by' => $currentUserId,
        ];
        return $this->model
            ->find($sensorId)
            ->update($data);
    }

    /**
     * Update sensor presence
     * @param $nodeId
     * @param $presence
     * @return
     * @throws \Exception
     */
    public function updatePresence($nodeId, $presence)
    {
        $sensor = $this->getByNodeMac($nodeId);
        if(!is_bool($presence)) {
            throw new \Exception("Invalid presence");
        }
        $currentUserId = \Auth::user()->id;
        $data = [
            'online' => $presence,
            'online_updated_at' => Carbon::now(),
            'updated_by' => $currentUserId,
        ];
        return $this->model
            ->find($sensor->id)
            ->update($data);
    }

    /**
     * Update sensor presence
     * @param $nodeId
     * @param $batteryPercentage
     * @return
     * @throws \Exception
     */
    public function updateBattery($nodeId, $batteryPercentage)
    {
        $sensor = $this->getByNodeMac($nodeId);
        if(!is_bool($batteryPercentage)) {
            throw new \Exception("Invalid");
        }
        $currentUserId = \Auth::user()->id;
        $data = [
            'low_battery' => $batteryPercentage,
            'low_battery_updated_at' => Carbon::now(),
            'updated_by' => $currentUserId,
        ];
        return $this->model
            ->find($sensor->id)
            ->update($data);
    }


}
