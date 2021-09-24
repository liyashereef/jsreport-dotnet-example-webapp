<?php

namespace Modules\Sensors\Repositories;

use Modules\Admin\Repositories\CustomerRoomRepository;
use Modules\Sensors\Models\SensorTrigger;
use Modules\Admin\Models\CustomerRoom;
use DB;
use Carbon\Carbon;
use App\Services\HelperService;

class SensorTriggerRepository
{
    /**
     * The Model instance.
     * ''
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $model, $customerRoom,$helperService;
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
        CustomerRoomRepository $customerRoomRepository,
        CustomerRoom $customerRoom,
        HelperService $helperService
    ) {
        $this->model = $sensorTrigger;
        $this->customerRoomRepository = $customerRoomRepository;
        $this->customerRoom = $customerRoom;
        $this->helperService = $helperService;
    }

    /**
     * Get security clearance lookup list
     *
     * @param empty
     * @return array
     */
    public function getAll($id = null, $client_id=null)
    {
        $sensorTriggerList =  $this->model
            ->when($id !== null, function ($query) use ($id) {
                $query->where('id', $id);
            })
            ->with(['customer','room'])
            ->orderBy('created_at', 'desc');

            if($client_id!=null){
                $sensorTriggerList= $sensorTriggerList->whereHas('room', function ($query) use ($client_id) {
                     return $query->where('customer_id', '=', $client_id);
                 });
             }

             $sensorTriggerList=$sensorTriggerList->get();

            return $this->prepareDataForSensorTriggerList($sensorTriggerList);
    }

    /**
     * Prepare datatable elements as array.
     * @param $sensorList
     * @return array
     */

    public function prepareDataForSensorTriggerList($sensorTriggerList)
    {
        $datatable_rows = array();
        foreach ($sensorTriggerList as $key => $each_list) {
            $each_row["id"] = isset($each_list->id) ? $each_list->id : "--";
            $each_row["date"] = isset($each_list->trigger_started_at) ? Carbon::parse($each_list->trigger_started_at)->format('M d,Y') : "--";
            $each_row["customer_name"] = isset($each_list->room->customer) ? $each_list->room->customer->client_name : "--";
            $each_row["customer_address"] = isset($each_list->room->customer) ? $each_list->room->customer->address : "--";
            $each_row["room_name"] = isset($each_list->room->name) ? $each_list->room->name : "--";
            $each_row["entry"] = isset($each_list->trigger_started_at) ? Carbon::parse($each_list->trigger_started_at)->format('h:i:s A') : "--";
            $each_row["exit"] = isset($each_list->trigger_ended_at) ? Carbon::parse($each_list->trigger_ended_at)->format('h:i:s A') : "--";
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
        return $this->model->find($id);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param $request
     * @param $incidentId
     * @return bool
     */
    public function save($request)
    {
        $currentUserId = \Auth::user()->id;
        $data = [
            'customer_id' => $request->get('customer')->id,
            'room_id' => $request->get('room')->id,
            'sensor_id' => $request->get('sensor')->id,
            'trigger_started_at' => $request->get('createdAt'),
            'sleep_after_trigger' => $request->get('sensorConfig')->sleep_after_trigger,
            'end_trigger_after' => $request->get('sensorConfig')->end_trigger_after,
            'created_by' => $currentUserId,
        ];
        return $this->model->updateOrCreate($data);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param $sensorTriggerId
     * @param $incidentId
     * @return bool
     */
    public function updateIncident($sensorTriggerId, $incidentId)
    {
        $currentUserId = \Auth::user()->id;
        $data = [
            'incident_id' => $incidentId,
            'updated_by' => $currentUserId,
        ];
        return $this->model
            ->find($sensorTriggerId)
            ->update($data);
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
     * @return object
     */
    public function delete($id)
    {
        return $this->model->destroy($id);
    }

    public function unique_code($name, $limit)
    {
        return strtolower(uniqid(str_replace(' ', '_', $name . '_')));
    }

    /**
     * To get all motion sensors details
     *
     * @param $request
     * @return array
     */
    public function getAllMotionSensorDetails($request)
    {

        $details = [];
        $customers = $request->get("customer-id");
        $start = $request->get("start_date");
        $end = $request->get("end_date");

        ($start == '') ? $startday = date('Y-m-d') : $startday = date('Y-m-d', strtotime($start));
        ($end == '') ? $endday = date('Y-m-d', strtotime("+1 days")) : $endday = date('Y-m-d', strtotime($end . "+1 days"));

        $time = [6 => " 6 AM", 7 => " 7 AM", 8 => " 8 AM", 9 => " 9 AM", 10 => "10 AM", 11 => "11 AM", 12 => "12 PM", 13 => " 1 PM", 14 => " 2 PM", 15 => " 3 PM", 16 => " 4 PM", 17 => " 5 PM", 18 => " 6 PM", 19 => " 7 PM", 20 => " 8 PM", 21 => " 9 PM", 22 => "10 PM", 23 => "11 PM", 24 => "12 AM", 1 => " 1 AM", 2 => " 2 AM", 3 => " 3 AM", 4 => " 4 AM", 5 => " 5 AM"];
        $rooms = $this->customerRoom::where('customer_id', $customers)->pluck('name', 'id')->toArray();
        $severity = $this->customerRoom::where('customer_id', $customers)->pluck('severity_id', 'id')->toArray();

            $sensor_details =  SensorTrigger::with('sensorLog')->select(DB::raw('id,room_id, HOUR(trigger_started_at) as hour'))
            ->where('customer_id', $customers)->whereBetween('trigger_started_at', [$startday, $endday])->get();
            $more_details_html = [];
            if (!empty($sensor_details)) {
                foreach ($sensor_details as $value) {
                $details['sensor_details'][$value->room_id][$value->hour] = $value->id;
                    if((isset($value->sensorLog)) && (!empty($value->sensorLog)) ){
                        $more_details_html = [];
                        foreach ($value->sensorLog as $eachlog) {
                            $sensor = $eachlog['id'];
                            $date = \Carbon::parse($eachlog['trigger_started_at'])->format('d-M-y');
                            $entry = \Carbon::parse($eachlog['trigger_started_at'])->format('h : i A');
                            $exit = ($eachlog['trigger_ended_at'] !=null) ?  (\Carbon::parse($eachlog['trigger_ended_at'])->format('h : i A')) : '--';
                            $more_details_html[] = 'Date &nbsp;' . $date . '</br>Sensor '. $sensor . '</br> Entry ' . $entry . '</br>' . 'Exit &nbsp;&nbsp;' . $exit . '</br></br>';
                        }
                    $details['more_details'][$value->room_id][$value->hour] = implode(" ", $more_details_html);
                    }
                }
            }else {
                   $details['sensor_details'] = [];
            }

        $details['time'] = $time;
        $details['severity'] = $severity;
        $details['rooms'] = $rooms;
        $details['filters']['start_date'] = $startday;
        $details['filters']['end_date'] = date('Y-m-d', strtotime($endday . " -1 days"));
        return $details;
    }

        public function getKeyLogSummaryByCustomers($customer_session = false) {
            if ($customer_session) {
                $customer_ids = $this->helperService->getCustomerIds();
            }
         //   dd($customer_ids);
            $sensorTriggerList =  $this->model->with(['room','customer'])->when($customer_session != false, function ($q) use ($customer_ids) {
                return $q->whereIn('customer_id', $customer_ids);
            })
            ->orderBy('created_at', 'desc')
            ->get();
            return $this->prepareDataForSensorTriggerList($sensorTriggerList);

      }
}
