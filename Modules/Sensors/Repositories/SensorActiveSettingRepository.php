<?php

namespace Modules\Sensors\Repositories;

use Modules\Sensors\Models\SensorActiveSetting;
use Modules\Admin\Models\Days;
use Modules\Admin\Models\CustomerRoom;
use Modules\Admin\Models\Sensor;
use Modules\Sensors\Models\SensorConfigSetting;
use Aws\Lambda\LambdaClient;
use Illuminate\Support\Facades\Log;
use Modules\Admin\Http\Exceptions\CglLambdaException;
use Carbon\Carbon;
use Auth;

class SensorActiveSettingRepository
{
    /**
     * The Model instance.
     * ''
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $model;
    /**
     * @var SensorActiveSetting
     */

    /**
     * Create a new FeedbackLookupRepository instance.
     *
     * @param SensorTriggerLog $sensorTrigger
     *
     */
    public function __construct(
        SensorActiveSetting $model,
        Days $dayModel,
        CustomerRoom $customerroomModel,
        Sensor $sensorModel

    )
    {
        $this->model = $model;
        $this->daymodel = $dayModel;
        $this->roommodel = $customerroomModel;
        $this->sensormodel = $sensorModel;
    }

    /**
     * Get security clearance lookup list
     *
     * @param empty
     * @return array
     */

    public function getAllActiveSettingList()
    {
        $activesettinglist = $this->roommodel->whereHas('activeSensors', function ($query) {
            $query->where('room_id', "!=", null);
        })->with(['activeSensors', 'customer'])->get();
        return $this->prepareDataForActiveSetting($activesettinglist);
    }

    /**
     * Prepare datatable elements as array.
     * @param  $result
     * @return array
     */

    public function prepareDataForActiveSetting($activesettinglist)
    {
        $datatable_rows = array();
        foreach ($activesettinglist as $key => $each_list) {
            // "project_name" => $each_list->customer ? $customerKey->customer->client_name . ($customerKey->customer ? " (" . $customerKey->customer->project_number . ")" : '') : '',
            $each_row["id"] = isset($each_list->id) ? $each_list->id : "--";
            $each_row["customer_name"] = isset($each_list->customer) ? $each_list->customer->client_name : "--";
            $each_row["room_name"] = isset($each_list->name) ? $each_list->name : "--";
            $weekdayDeatils = $this->getWeekDayDetails($each_list->id, $each_list->customer_id);
            $each_row["weekday_start_time"] = isset($weekdayDeatils->start_time) ? $weekdayDeatils->start_time : "--";
            $each_row["weekday_end_time"] = isset($weekdayDeatils->end_time) ? $weekdayDeatils->end_time : "--";
            $weekendDeatils = $this->getWeekEndDetails($each_list->id, $each_list->customer_id);
            $each_row["weekend_start_time"] = isset($weekendDeatils->start_time) ? $weekendDeatils->start_time : "--";;
            $each_row["weekend_end_time"] = isset($weekendDeatils->end_time) ? $weekendDeatils->end_time : "--";;
            array_push($datatable_rows, $each_row);
        }
        return $datatable_rows;
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param $data
     * @return bool
     */

    public function activeSettingSave($data)
    {
        if(isset($data['id']) && !empty($data['id'])){
            $activeSettings = $this->model->where('room_id', $data['id'])->delete();
            $data['room_id'] = $data['id'];
            $data['customer_id'] = $data['cus_id'];
        }
        if (isset($data['week_day_start_time']) && isset($data['week_day_end_time'])) {
            $weekDays = $this->daymodel->whereBetween('id', [1, 5])->get();
        }
        if (isset($data['week_end_start_time']) && isset($data['week_end_end_time'])) {
            $weekEnddays = $this->daymodel->whereBetween('id', [6, 7])->get();
        }
        if (!isset($data['is_weekday_active'])) {
            $data['is_weekday_active'] = 0;
        }
        if (!isset($data['is_weekend_active'])) {
            $data['is_weekend_active'] = 0;
        }
        if (!$weekDays->isEmpty()) {

            foreach ($weekDays as $weekDayKey => $weekday) {
                $data['is_active'] = $data['is_weekday_active'];
                $data['day_id'] = $weekday->id;
                $data['start_time'] = $data['week_day_start_time'];
                $data['end_time'] = $data['week_day_end_time'];
                $data['created_by'] = Auth::user()->id;
                $data['updated_by'] = Auth::user()->id;
                $data['created_at'] = Carbon::now();
                $this->model->create($data);
            }
        }

        if (!$weekEnddays->isEmpty()) {
            foreach ($weekEnddays as $weekEndDaysKey => $weekendday) {
                $data['is_active'] = $data['is_weekend_active'];
                $data['day_id'] = $weekendday->id;
                $data['start_time'] = $data['week_end_start_time'];
                $data['end_time'] = $data['week_end_end_time'];
                $data['created_by'] = Auth::user()->id;
                $data['updated_by'] = Auth::user()->id;
                $data['created_at'] = Carbon::now();

                $this->model->create($data);
            }
        }
        return true;
    }

    /**
     * Get security clearance lookup list
     *
     * @param empty
     * @return array
     */

    public function getWeekDayDetails($roomId, $customerId)
    {
        return $this->model->select('start_time', 'end_time')
            ->where('customer_id', $customerId)->where('room_id', $roomId)
            ->where('day_id', 1) //Weekday Monday from days table
            ->first();

    }

    /**
     * Get security clearance lookup list
     *
     * @param empty
     * @return array
     */

    public function getWeekEndDetails($roomId, $customerId)
    {
        return $this->model->select('start_time', 'end_time')
            ->where('customer_id', $customerId)
            ->where('room_id', $roomId)
            ->where('day_id', 7) //Weekend sunday from days table
            ->first();

    }

    /**
     * Get room  lookup list
     *
     * @param cusId
     * @return array
     */

    public function getRooms($cusId)
    {
        $activeRoomListIds = $this->model->pluck('room_id')->toArray();
        return $this->roommodel->select('id', 'name')->whereNotIn('id', $activeRoomListIds)->where('customer_id', $cusId)->get();
    }

    /**
     * Get room  lookup list
     *
     * @param cusId
     * @return array
     */

    public function getActiveSetting($id)
    {
        $WEEKDAY = 1; //Weekday Monday from days table
        $WEEKEND = 7; //Weekend sunday from days table
        return $this->roommodel->with(['activeSensors' => function ($q) use ($WEEKDAY, $WEEKEND) {
            // Query the name field in status table
            $q->where('day_id', '=', $WEEKDAY); // '=' is optional
            $q->orWhere('day_id', $WEEKEND);
        }])->where('id', $id)->first();
    }

    /**
     * Get room  lookup list
     *
     * @param $sensorIds
     * @param $roomIds
     * @return array
     */

    public function getSensorActiveDays($sensorIds, $roomIds)
    {
        $sql = $this->sensormodel->withTrashed()->with(['room', 'room.activeSensors']);
        if (!empty($sensorIds) && empty($sensorIds)) {
            $sql->whereHas('room.activeSensors', function ($query) {
                $query->where('room_id', "!=", null);
            });
        }
        $sql->when($sensorIds != null, function ($query) use ($sensorIds) {
            $query->whereIn('id', $sensorIds);
        });
        $sql->when($roomIds != null, function ($query) use ($roomIds) {
            $query->whereIn('room_id', $roomIds);
        });
        $activedayslist = $sql->get();
        return $this->prepareDataForSensorActiveDays($activedayslist);
    }

    /**
     * Prepare for lambda elements as array.
     * @param  $result
     * @return array
     *
     */
    public function prepareDataForSensorActiveDays($activedayslist)
    {
        $datatable_rows = array();
        $dayArr = [
            "mon",
            "tue",
            "wed",
            "thu",
            "fri",
            "sat",
            "sun",
        ];
        foreach ($activedayslist as $key => $each_list) {
            $each_row["NodeMacID"] = isset($each_list->nod_mac) ? $each_list->nod_mac : "--"; //Uppercase N given for compatibility
            $each_row["isEnabled"] = ($each_list->enabled == 1) ? "true" : "false";
            $each_row["sleepAfterTrigger"] = SensorConfigSetting::first()->sleep_after_trigger;
            $each_row["endTriggerAfter"] = SensorConfigSetting::first()->end_trigger_after;
            $each_row["mon"] = [];
            $each_row["tue"] = [];
            $each_row["wed"] = [];
            $each_row["thu"] = [];
            $each_row["fri"] = [];
            $each_row["sat"] = [];
            $each_row["sun"] = [];
            if (!empty($each_list->room->activeSensors)) {
                foreach ($each_list->room->activeSensors as $i => $day) {
                    $dayIndex = $day->day_id - 1;
                    $each_row[$dayArr[$dayIndex]]["isActive"] = $day->is_active;
                    $each_row[$dayArr[$dayIndex]]["activeStartTime"] = $day->start_time;
                    $each_row[$dayArr[$dayIndex]]["activeEndTime"] = $day->end_time;
                }
            }
            array_push($datatable_rows, $each_row);
        }
        return $datatable_rows;
    }

    public function updateLambdaClient($sensorIdArr = null, $roomIdArr = null)
    {
        $settingArray = $this->getSensorActiveDays($sensorIdArr, $roomIdArr);
        foreach ($settingArray as $eachNode) {
            $settingsJson = json_encode([$eachNode]);
            try {
                $this->invokeLambdaClient($settingsJson);
            } catch (CglLambdaException $e) {
                Log::channel('motionSensor')
                    ->error("Error in lambda saving: ".$settingsJson);
            }
        }
        return true;
    }

    /**
     * Pass the settings to lambda to publish MQTT event.
     *
     * @param array|null $sensorIdArr array of sensor Id
     * @param array|null $roomIdArr array of room Id
     * @return bool
     * @throws CglLambdaException
     */
    public function invokeLambdaClient($settingArray)
    {

        $settingsJson = json_encode($settingArray);

        try {
            $client = new LambdaClient(array(
                'version' => 'latest',
                'region' => env('AWS_REGION'),
                'credentials' => [
                    'key' => env('AWS_ACCESS_KEY_ID'),
                    'secret' => env('AWS_SECRET_ACCESS_KEY'),
                ],
            ));
        } catch (\Exception $e) {
            Log::channel('motionSensor')->error("AWS Credential Error" . $e);
            return false;
        }

        $response = $client->invoke([
            'FunctionName' => 'settingsPublisher', // REQUIRED
            'InvocationType' => 'RequestResponse',
            'LogType' => 'Tail',
            'Payload' => $settingsJson,
        ]);
        $responseContents = $response->get('Payload')->getContents();
        $responseContentJson = json_decode($responseContents);
        $responseStatusCode = $responseContentJson->statusCode ?? 500;
        $awsFunctionErr = $response->get('FunctionError');
        if (!empty($awsFunctionErr)) { // error in lambda code
            throw new CglLambdaException($responseContentJson);
        } elseif ($responseContentJson->body->success == false) {
            throw new CglLambdaException($responseContentJson, "Iot error");
        }
        Log::channel('motionSensor')->info("Settings Save: Payload:\n" . $settingsJson . "\nResponse\n" . $responseContents);
        return true;
    }

    public function updateSensorAsDisabled($roomIds)
    {
        if (!empty($roomIds)) {
            $this->sensormodel->whereIn('room_id', $roomIds)->update(['enabled' => false, 'updated_by' => Auth::user()->id, 'updated_at' => Carbon::now()]);
            return true;
        } else {
            $this->helperService->returnFalseResponse();
        }

    }

    public function updateSensorAsEnabled($roomIds)
    {
        if (!empty($roomIds)) {
            $this->sensormodel->whereIn('room_id', $roomIds)->update(['enabled' => true, 'updated_by' => Auth::user()->id, 'updated_at' => Carbon::now()]);
            return true;
        } else {
            $this->helperService->returnFalseResponse();
        }

    }

}
