<?php

namespace Modules\Sensors\Repositories;

use Modules\Admin\Repositories\CustomerRoomRepository;
use Modules\Sensors\Models\SensorTriggerLog;

class SensorTriggerLogRepository
{
    /**
     * The Model instance.
     * ''
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $model;
    /**
     * @var CustomerRoomRepository
     */
    private $customerRoomRepository;

    /**
     * Create a new FeedbackLookupRepository instance.
     *
     * @param SensorTriggerLog $sensorTrigger
     * @param CustomerRoomRepository $customerRoomRepository
     */
    public function __construct(
        SensorTriggerLog $sensorTrigger,
        CustomerRoomRepository $customerRoomRepository
    )
    {
        $this->model = $sensorTrigger;
        $this->customerRoomRepository = $customerRoomRepository;
    }

    /**
     * Get security clearance lookup list
     *
     * @param null $id
     * @return array
     */
    public function getAll($id = null)
    {
        return $this->model
            ->when($id !== null, function ($query) use ($id) {
                $query->where('id', $id);
            })
            ->get();
    }


    /**
     * Display details of single resource
     *
     * @param $id
     * @return object
     */
    public function get($id)
    {
        return $this->model->find($id);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param $request
     * @param $sensorTriggerId
     * @return bool
     */
    public function save($request, $sensorTriggerId)
    {
        $currentUserId = \Auth::user()->id;
        $data = [
            'sensor_trigger_id' => $sensorTriggerId,
            'sensor_id' => $request->get('sensor')->id,
            'trigger_started_at' => $request->get('createdAt'),
            'created_by' => $currentUserId,
        ];
        return $this->model->updateOrCreate($data);
    }

    /**
     * Function to get all trigger active
     * @param $sensorTriggerId
     */
    public function triggerActive(int $sensorTriggerId)
    {
        return $this->model
            ->where('sensor_trigger_id', $sensorTriggerId)
            ->whereNull('trigger_ended_at')
            ->get();
    }

    /**
     * Function to get all active sensors
     * @param integer $sensorId
     * @param bool $latest
     * @param int|null $sensorTriggerId
     * @return
     */
    public function getActiveTriggerSensors(
        int $sensorId,
        bool $latest = true,
        int $sensorTriggerId = null)
    {
        return $this->model
            ->whereNull('trigger_ended_at')
            ->when($sensorId != null, function ($query) use ($sensorId) {
                return $query->where('sensor_id', $sensorId);
            })
            ->when($sensorTriggerId != null, function ($query) use ($sensorTriggerId) {
                return $query->where('sensor_trigger_id', $sensorTriggerId);
            })
            ->when($latest, function ($query) use ($sensorId) {
                return $query->latest();
            })
            ->get();
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param $request
     * @param $id
     * @return bool
     */
    public function updateEventEnd($request, $id)
    {
        $currentUserId = \Auth::user()->id;
        $data = [
            'trigger_ended_at' => $request->get('createdAt'),
            'updated_by' => $currentUserId,
        ];
        return $this->model
            ->find($id)
            ->update($data);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  $id
     * @return int
     */
    public function delete($id)
    {
        return $this->model->destroy($id);
    }
}
