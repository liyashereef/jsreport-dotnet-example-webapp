<?php

namespace Modules\Sensors\Repositories;

use Modules\Sensors\Models\SensorConfigSetting;

class SensorConfigSettingRepository
{
    /**
     * The Model instance.
     * ''
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $model;
    /**
     * @var SensorConfigSetting
     */

    /**
     * Create a new FeedbackLookupRepository instance.
     *
     * @param SensorTriggerLog $sensorTrigger
     * @param CustomerRoomRepository $customerRoomRepository
     */
    public function __construct(SensorConfigSetting $model)
    {
        $this->model = $model;
    }

         /**
     * Store a newly created resource in storage.
     *
     * @param $input
     * @return bool
     */
    public function configSettingSave($request)
    {
        $currentUserId = \Auth::user()->id;
        if(isset($data['id']) && !empty($data['id'])){
            $data = [
                'sleep_after_trigger' => $request['motion_sensor_sleep_after_trigger'],
                'end_trigger_after' => $request['motion_sensor_tigger_end_after'],
                'updated_by' => $currentUserId,
            ];

        }else {

            $data = [
                'sleep_after_trigger' => $request['motion_sensor_sleep_after_trigger'],
                'end_trigger_after' => $request['motion_sensor_tigger_end_after'],
                'updated_by' => $currentUserId,
                'created_by' => $currentUserId,
            ];
        }
        $result =  $this->model->updateOrCreate(array('id' => $request['id']), $data);
        if($result){
            return true;
        } else {
            return false;
        }

    }




}
