<?php

namespace Modules\Sensors\Http\Controllers;

use App\Repositories\PushNotificationRepository;
use App\Services\HelperService;
use Auth;
use Exception;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\Admin\Models\Customer;
use Modules\Admin\Models\IncidentReportSubject;
use Modules\Admin\Repositories\CustomerIncidentSubjectAllocationRepository;
use Modules\Admin\Repositories\CustomerRoomRepository;
use Modules\Admin\Repositories\PayPeriodRepository;
use Modules\Sensors\Http\Requests\SensorEventRequest;
use Modules\Sensors\Http\Requests\SensorLowBatteryRequest;
use Modules\Sensors\Repositories\SensorCommunicationLogRepository;
use Modules\Sensors\Repositories\SensorRepository;
use Modules\Sensors\Repositories\SensorTriggerLogRepository;
use Modules\Sensors\Repositories\SensorTriggerRepository;
use Modules\Supervisorpanel\Repositories\IncidentReportRepository;
use Modules\Timetracker\Repositories\EmployeeShiftRepository;


class DeviceController extends Controller
{
    protected $repository;
    /**
     * @var PayPeriodRepository
     */
    private $payperiod;
    /**
     * @var IncidentReportRepository
     */
    private $incidentReport;

    const HIGH_PRIORITY = 3;
    /**
     * @var SensorTriggerRepository
     */
    private $sensorTrigger;
    /**
     * @var CustomerRoomRepository
     */
    private $customerRoom;
    /**
     * @var HelperService
     */
    private $helperService;
    /**
     * @var SensorTriggerLogRepository
     */
    private $sensorTriggerLog;
    /**
     * @var SensorRepository
     */
    private $sensor;
    /**
     * @var EmployeeShiftRepository
     */
    private $employeeShiftRepository;
    /**
     * @var PushNotificationRepository
     */
    private $pushNotificationRepository;
    /**
     * @var CustomerIncidentSubjectAllocationRepository
     */
    private $customerIncidentSubjectAllocationRepository;

    /**
     * Create Repository instance.
     * @param SensorCommunicationLogRepository $sensorLogRepository
     * @param PayPeriodRepository $payperiod
     * @param IncidentReportRepository $incidentReport
     * @param SensorTriggerRepository $sensorTrigger
     * @param HelperService $helperService
     * @param CustomerRoomRepository $customerRoom
     * @param SensorTriggerLogRepository $sensorTriggerLog
     * @param SensorRepository $sensor
     * @param EmployeeShiftRepository $employeeShiftRepository
     * @param PushNotificationRepository $pushNotificationRepository ,
     * @param CustomerIncidentSubjectAllocationRepository $customerIncidentSubjectAllocationRepository
     */
    public function __construct(
        SensorCommunicationLogRepository $sensorLogRepository,
        PayPeriodRepository $payperiod,
        IncidentReportRepository $incidentReport,
        SensorTriggerRepository $sensorTrigger,
        HelperService $helperService,
        CustomerRoomRepository $customerRoom,
        SensorTriggerLogRepository $sensorTriggerLog,
        SensorRepository $sensor,
        EmployeeShiftRepository $employeeShiftRepository,
        PushNotificationRepository $pushNotificationRepository,
        CustomerIncidentSubjectAllocationRepository $customerIncidentSubjectAllocationRepository
    )
    {
        $this->repository = $sensorLogRepository;
        $this->payperiod = $payperiod;
        $this->incidentReport = $incidentReport;
        $this->sensorTrigger = $sensorTrigger;
        $this->sensorTriggerLog = $sensorTriggerLog;
        $this->helperService = $helperService;
        $this->customerRoom = $customerRoom;
        $this->sensor = $sensor;
        $this->employeeShiftRepository = $employeeShiftRepository;
        $this->pushNotificationRepository = $pushNotificationRepository;
        $this->customerIncidentSubjectAllocationRepository = $customerIncidentSubjectAllocationRepository;
    }

    /**
     * Controller function for creating motion detection start workflow
     * @param SensorEventRequest $request
     * @return array|bool[]|false[]|void
     * @throws Exception
     */
    public function motionStart(SensorEventRequest $request)
    {
        try {
            // check if sensor disabled
            if (!$request->get('sensor')->enabled) {
                throw new Exception("Sensor disabled", 0);
            }
            // check if room active
            if (!$request->get('room')->room_active_now) {
                throw new Exception("Inactive timing", 0);
            }
            // check active event in room
            $roomHasActiveEvent = $this->customerRoom->getActiveEvent($request);
            if (
                isset($roomHasActiveEvent->active_event)
                &&
                $roomHasActiveEvent->active_event == 1
            ) {
                // get active trigger in same room from same sensor
                $activeSensorTrigger = $this->sensorTriggerLog->getActiveTriggerSensors(
                    $request->get('sensor')->id,
                    true,
                    $roomHasActiveEvent->sensor_trigger_id);
                $latestActiveTrigger = $activeSensorTrigger->first();
                // if sensor has no active triggers
                if (!isset($latestActiveTrigger->sensor_id)) {
                    // save trigger Log
                    $this->sensorTriggerLog->save($request, $roomHasActiveEvent->sensor_trigger_id);
                    // update latest detection time in sensor
                    $this->sensor->updateInitialTriggerTime($request->get('sensor')->id, $request->get('createdAt'));
                } else {
                    throw new Exception("Active event from same sensor exists", 0);
                }
                throw new Exception("Active event exist in room", 1);
            }

            //save the trigger
            $sensorTriggerId = $this->sensorTrigger->save($request);

            //send push notification
            try {
                $activeShiftUsers = $this->employeeShiftRepository
                    ->getActiveShiftEmployes([$request->customer->id]);
                $this->pushNotificationRepository
                    ->sendPushNotification(
                        $activeShiftUsers,
                        $sensorTriggerId->id,
                        PUSH_MOTION_DETECTION,
                        "Motion Detected",
                        "Check room: " . $request->room->name
                    );
            } catch (Exception $e) {
                Log::channel('motionSensor')->error("Push notification failed " . $e);
            }

            //save trigger log
            $this->sensorTriggerLog->save($request, $sensorTriggerId->id);

            // set active event in room
            $this->customerRoom->updateSensorEvent($request, $sensorTriggerId->id);

            // create incident report
////            $incidentReport = $this->createIncidentReport($request); // commented as per requested by Sam
            // create entry in motion trigger
////            if ($incidentReport->success) {
////                $incidentId = $incidentReport->incident->id;
////                $triggerSave = $this->sensorTrigger->updateIncident($sensorTriggerId->id, $incidentId);
////                if ($triggerSave) {
////                    return $this->helperService->returnTrueResponse();
////                }
////            } else {
////                $error = $incidentReport->error;
////                Log::channel('motionSensor')->error("Incident Error " . $error);
////                return $this->helperService->returnFalseResponse($error);
////            }
        } catch (Exception $e) {
            Log::channel('motionSensor')
                ->error("Motion Sensor Start error " . $e);
            if ($e->getCode() == 1) {
                return $this->helperService->returnTrueResponse($e->getMessage());
            } else if ($e->getCode() == 0) {
                return $this->helperService->returnFalseResponse($e->getMessage());
            } else {
                return $this->helperService->returnFalseResponse($e);
            }
        }
    }

    /**
     * Controller function motion end
     * @param $request
     * @return array|false[]
     */
    public function motionEnd(SensorEventRequest $request)
    {
        // add end time in motion trigger
        try {
            // check active event in room
            $roomHasActiveEvent = $this->customerRoom->getActiveEvent($request);

            if (
                !isset($roomHasActiveEvent->active_event)
                ||
                $roomHasActiveEvent->active_event != 1
            ) {
                // if no active event found
                throw new Exception("Active event do not exist in room", 0);
            } else {
                $activeSensorTrigger = $this->sensorTriggerLog
                    ->getActiveTriggerSensors(
                        $request->get('sensor')->id,
                        true,
                        $roomHasActiveEvent->sensor_trigger_id
                    );
                if (!isset($activeSensorTrigger) || ($activeSensorTrigger->count() == 0)) {
                    // if no active event found
                    throw new Exception("Active event do not exist for sensor");
                }
                $activeTrigger = $this->sensorTriggerLog->triggerActive($roomHasActiveEvent->sensor_trigger_id);
                $this->sensorTriggerLog->updateEventEnd($request, $activeSensorTrigger->first()->id);
                // get total active counts for the trigger
                if ($activeTrigger->count() > 1) {
                    throw new Exception("Active event in room still exists", 1);
                }
            }
            $sensorTriggerId = $roomHasActiveEvent->sensor_trigger_id;
            // update active event in room
            $this->customerRoom->updateSensorEvent($request, $sensorTriggerId, false);
            //update and end trigger
            $this->sensorTrigger->updateEventEnd($request, $sensorTriggerId);
            return $this->helperService->returnTrueResponse();
        } catch (Exception $e) {
            Log::channel('motionSensor')
                ->info("Motion Sensor End error " . $e);
            if ($e->getCode() == 1) {
                return $this->helperService->returnTrueResponse($e->getMessage());
            } else if ($e->getCode() == 0) {
                return $this->helperService->returnFalseResponse($e->getMessage());
            } else {
                return $this->helperService->returnFalseResponse($e);
            }
        }
    }

    /**
     * Controller function online event
     * @param SensorEventRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function online(SensorEventRequest $request)
    {
        //get parameters from request
        $nodeId = $request->get('nodeMacID');
        $presence = true;
        try {
            DB::beginTransaction();
            $presence = $this->sensor->updatePresence($nodeId, $presence);
            DB::commit();
            return response()->json($this->helperService->returnTrueResponse());
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json($this->helperService->returnFalseResponse($e));
        }
    }

    /**
     * Controller function offline event
     */
    public function offline($request)
    {
        //get parameters from request
        $nodeId = $request->get('nodeMacID');
        $presence = false;
        try {
            DB::beginTransaction();
            $presence = $this->sensor->updatePresence($nodeId, $presence);
            DB::commit();
            return response()->json($this->helperService->returnTrueResponse());
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json($this->helperService->returnFalseResponse($e));
        }
    }

    /**
     * Controller function low battery event
     * @param SensorLowBatteryRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function lowBattery(SensorLowBatteryRequest $request)
    {
        $nodeId = $request->get('nodeMacID');
        $lowBattery = $request->get('lowBattery');
        try {
            DB::beginTransaction();
            $presence = $this->sensor->updateBattery($nodeId, $lowBattery);
            DB::commit();
            return response()->json($this->helperService->returnTrueResponse());
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json($this->helperService->returnFalseResponse($e));
        }
    }

    private function createIncidentReport($request)
    {
        $user = Auth::user();
        $source = 'Motion-Sensor';

        $createdAt = $request->get('createdAt');
        $customerData = $request->get('customer');
        $sensorData = $request->get('sensor');
        $roomData = $request->get('room');
        $subjectId = Customer::select('motion_sensor_incident_subject')
            ->where('id', $customerData->id)
            ->first()->motion_sensor_incident_subject;
        $currentPayperiod = $this->payperiod->getCurrentPayperiod();

        $incidentTitle = "Sensor Log " .
            $customerData->client_name_and_number.
            $createdAt->month."/".$createdAt->day."/".$createdAt->year." - ".
            $createdAt->toTimeString();

        $incidentDetails = "Intruder detected from motion sensor " .
            $sensorData->name . " placed at " . $roomData->name . " at " .
            $customerData->client_name_and_number;

        $incidentReport['customer_id'] = $customerData->id;
        $incidentReport['subject_id'] = $subjectId;
        $incidentReport['time_of_day'] = ""; //$incident_report->time_of_day;
        $incidentReport['status'] = 1;
        $incidentReport['notes'] = " notes";
        $incidentReport['details'] = $incidentDetails;
        $incidentReport['title'] = $incidentTitle;
        $incidentReport['month'] = $createdAt->month;
        $incidentReport['year'] = $createdAt->year;
        $incidentReport['date'] = $createdAt->day;
        $incidentReport['time'] = $createdAt->toTimeString();
        $incidentReport['incident_attachments'] = [];
        $incidentReport['incident_id'] = null;
        $incidentReport['priority'] = $roomData->severity_id ?? null;
        $incidentReport['fullname'] = $user->full_name;
        $incidentReport['supervisor'] = $customerData
            ->employee_latest_customer_supervisor["supervisor"]["name_with_emp_no"];
        $incidentReport['area_manager'] = $customerData
            ->employee_latest_customer_area_manager["area_manager"]["name_with_emp_no"];
        $incidentReportCol = (object)$incidentReport;
        return $this->incidentReport
            ->storeReport([$incidentReportCol],
                $user,
                $currentPayperiod,
                $customerData,
                $source,
                true);
    }
}
