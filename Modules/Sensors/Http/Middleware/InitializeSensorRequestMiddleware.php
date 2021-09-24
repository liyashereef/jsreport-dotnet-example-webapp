<?php

namespace Modules\Sensors\Http\Middleware;

use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;
use Modules\Sensors\Models\SensorConfigSetting;
use Modules\Sensors\Repositories\SensorCommunicationLogRepository;
use Modules\Sensors\Repositories\SensorRepository;

class InitializeSensorRequestMiddleware
{

    /**
     * @var SensorCommunicationLogRepository
     */
    private $sensorComm;
    /**
     * @var SensorRepository
     */
    private $sensor;
    /**
     * @var SensorConfigSetting
     */
    private $sensorConfig;

    public function __construct(
        SensorCommunicationLogRepository $sensorComm,
        SensorRepository $sensor,
        SensorConfigSetting $sensorConfig)
    {
        $this->sensorComm = $sensorComm;
        $this->sensor = $sensor;
        $this->sensorConfig = $sensorConfig;
    }

    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @param String $topic
     * @return mixed
     */
    public function handle(Request $request,
                           Closure $next,
                           string $topic)
    {
        $nodeMacId = $request->get('nodeMacID');
        //get sensor values
        $sensorData = $this->sensor->getByNodeMac($nodeMacId);
        $sensorConfig = SensorConfigSetting::latest()->first();
        $request->request->add(['topic' => $topic]);
        $request->request->add(['createdAt' => (object)Carbon::now()]);
        if(isset($sensorData) && isset($sensorConfig)){
            $request->request->add(['sensor' => ((object)$sensorData->toArray())]);
            $request->request->add(['sensorConfig' => ((object)$sensorConfig->toArray())]);
            if(isset($sensorData->room)) {
                $roomData = (object)$sensorData->room->toArray();
                $request->request->add(['room' => $roomData]);
                if(isset($sensorData->room->customer->employeeLatestCustomerSupervisor))
                    $sensorData->room->customer->employeeLatestCustomerSupervisor->name_with_emp_no;
                if(isset($sensorData->room->customer->employeeLatestCustomerAreaManager))
                    $sensorData->room->customer->employeeLatestCustomerAreaManager->name_with_emp_no;
                $customerData = (object)$sensorData->room->customer->toArray();
                $request->request->add(['customer' => $customerData]);
            }
        }
        // save log to mongo
        $commLog = $this->sensorComm->save($request);
        $request->request->add(['commLogId' => $commLog]);
        // forward
        return $next($request);
    }
}
