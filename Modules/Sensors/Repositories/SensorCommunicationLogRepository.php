<?php

namespace Modules\Sensors\Repositories;

use Carbon\Carbon;
use Modules\Admin\Repositories\CustomerRoomRepository;
use Modules\Sensors\Models\SensorTrigger;
use Modules\Timetracker\Models\SensorCommunicationLog;

class SensorCommunicationLogRepository
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
     * @param SensorTrigger $sensorTrigger
     * @param CustomerRoomRepository $customerRoomRepository
     */
    public function __construct(
        SensorTrigger $sensorTrigger,
        CustomerRoomRepository $customerRoomRepository
    )
    {
        $this->model = $sensorTrigger;
        $this->customerRoomRepository = $customerRoomRepository;
    }

    /**
     * Get security clearance lookup list
     *
     * @param empty
     * @return array
     */
    public function get($id = null)
    {

        return $this->model
            ->when($id !== null, function ($query) use ($id) {
                $query->where('id', $id);
            })
            ->get();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param $input
     * @return bool
     */
    public function save($input)
    {
        $user = \Auth::user();
        $sensorCommLog = new SensorCommunicationLog();
        $sensorCommLog->user_id = $user->id;
        $sensorCommLog->user = $user->attributesToArray();
        $sensorCommLog->sensor_id = $input->get('sensor')->id ?? null;
        $sensorCommLog->sensor = $input->get('sensor');
        $sensorCommLog->room = $input->get('room');
        $sensorCommLog->customer = $input->get('customer');
        $sensorCommLog->topic = $input->get('topic');
        $sensorCommLog->data = json_decode($input->getContent());
        $sensorCommLog->timestamps = Carbon::now();
        if($sensorCommLog->save()) {
            return $sensorCommLog->getKey();
        } else {
            return false;
        }
    }
}
