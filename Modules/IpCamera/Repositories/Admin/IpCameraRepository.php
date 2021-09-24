<?php

namespace Modules\IpCamera\Repositories\Admin;

use App\Services\HelperService;
use Auth;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Modules\Admin\Models\CustomerRoom;
use Modules\IpCamera\Models\IpCamera;
use Modules\IpCamera\Models\IpCameraRoomAllocationHistories;

class IpCameraRepository
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
     * @param IpCamera $ipCamerarModel
     * @param HelperService $helperService
     */
    public function __construct(
        IpCamera $ipCamera,
        IpCameraRoomAllocationHistories $ipCameraRoomAllocationHistories,
        CustomerRoom $customerRoomModel,
        HelperService $helperService
    )
    {
        $this->model = $ipCamera;
        $this->helperService = $helperService;
        $this->ipCameraRoomAllocationHistories = $ipCameraRoomAllocationHistories;
        $this->roomModel = $customerRoomModel;
    }

    public function getAll($id = null): array
    {

        $cameraList = $this->model->with(['room', 'room.customer'])
            ->when($id !== null, function ($query) use ($id) {
                $query->where('room_id', '=', $id);
            })->get();
        return $this->prepareDataForIpCameraList($cameraList);
    }

    public function prepareDataForIpCameraList($sensorList): array
    {

        $datatable_rows = array();
        foreach ($sensorList as $key => $each_list) {
            $each_row["id"] = isset($each_list->id) ? $each_list->id : "--";
            $each_row["name"] = isset($each_list->name) ? $each_list->name : "--";
            $each_row["room_name"] = isset($each_list->room) ? $each_list->room->name : "--";
            $each_row["customer_name"] = isset($each_list->room->customer) ? $each_list->room->customer->client_name : "--";
            $each_row["ip_port"] = isset($each_list->ip) ? $each_list->ip . ":" . $each_list->rtsp_port : "--";
            $each_row["unique_id"] = isset($each_list->unique_id) ? $each_list->unique_id : "--";

            array_push($datatable_rows, $each_row);
        }
        return $datatable_rows;
    }

    public function save($request)
    {
//        if (!isset($request['enabled'])) {
//            $request['enabled'] = 0;
//        }

        $data = [
            'name' => $request['name'],
            'credential_username' => $request['credential_username'],
            'credential_password' => $request['credential_password'],
            'ip' => $request['ip'],
            'rtsp_port' => $request['rtsp_port'],
            'controller_port' => $request['controller_port'],
            'enabled' => 1
        ];
        if (!isset($request['id']) && empty($request['id'])) {
            $data['machine_name'] = HelperService::generateMachineCode($request['name']);
            $data['unique_id'] = Str::uuid()->toString();
            $data['created_by'] = Auth::user()->id;
            $data['created_at'] = Carbon::now();
        } else {
            $data['updated_by'] = Auth::user()->id;
            $data['updated_at'] = Carbon::now();
        }
        return $this->model->updateOrCreate(array('id' => $request['id']), $data);
    }

    /**
     * @throws \Exception
     */
    public function updateMediaServer($cameraObj, $operation)
    {
        try {
            $url = config('globals.ip_cam_ms_ip') . '/stream/' . $cameraObj->unique_id . '/' . $operation;

            $rtspUrl = 'rtsp://' . $cameraObj->credential_username . ':' . $cameraObj->credential_password;
            $rtspUrl .= '@' . $cameraObj->ip . ':' . $cameraObj->rtsp_port . '/unicast';

            $ch = curl_init($url);

            if ($operation !== 'delete') {
                $data = [
                    "name" => $cameraObj->name,
                    "channels" => array(
                        "0" . " " => [
                            "name" => "ch1",
                            "url" => $rtspUrl,
                            "on_demand" => true,
                            "debug" => false,
                            "status" => 0
                        ]
                    )
                ];

                $payload = json_encode($data);
                $payload = str_replace(" ", "", $payload); // added this logic to make key 0 string
                curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
            }

            curl_setopt($ch, CURLOPT_USERPWD, config('globals.ip_cam_api_username') . ":" . config('globals.ip_cam_api_password'));
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json', 'Accept:application/json'));
            # Return response instead of printing.
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            # Send request.
            $result = curl_exec($ch);
            curl_close($ch);
            $resultObj = json_decode($result);
            if (isset($resultObj->payload) && $resultObj->payload === "success") {
                return true;
            } elseif ($operation == 'delete' &&  $resultObj->payload === "stream not found") {
                \Log::error("IP Camera stream not found - \n" . json_encode($resultObj));
                return true;
            } else {
                \Log::error("IP Camera save in media server failed - curl response not success \n" . json_encode($resultObj));
                throw new \Exception("IP Camera save in media server failed - curl response not success");
                return false;
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function get($id)
    {
        return $this->model->with(['room', 'room.customer'])->find($id);
    }

    public function delete($id)
    {
        $isIpCameraAllocated = $this->model->select('id', 'room_id', 'room_allocated_at')->where('id', $id)->first();
        if (isset($isIpCameraAllocated->room_id) && !empty($isIpCameraAllocated->room_id)) {
            $this->model->where('id', $id)->update(['enabled' => false, 'room_id' => null, 'room_allocated_at' => null, 'room_allocated_at' => null]);
        }
        return $this->model->destroy($id);
    }

    public function getIpCameraTabs($id = null)
    {
        $cameraList = $this->model
            ->with(['room', 'room.customer'])
            ->when($id !== null, function ($query) use ($id) {
                $query->where('id', '=', $id);
            })
            ->get();
        return $cameraList;
    }

    public function generateUniqueId($prefix = null, $more_entropy)
    {
        return uniqid($prefix, $more_entropy);
    }
}
