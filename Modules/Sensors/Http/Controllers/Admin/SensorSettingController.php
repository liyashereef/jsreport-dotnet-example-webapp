<?php

namespace Modules\Sensors\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\HelperService;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Modules\Sensors\Models\SensorConfigSetting;
use Modules\Admin\Models\CustomerRoom;
use Modules\Admin\Repositories\CustomerRepository;
use Modules\Sensors\Repositories\SensorRepository;
use Modules\Sensors\Http\Requests\SensorActiveSettingRequest;
use Modules\Sensors\Repositories\SensorConfigSettingRepository;
use Modules\Sensors\Repositories\SensorActiveSettingRepository;


class SensorSettingController extends Controller
{

    /**
     * The Repository instance.
     *
     * @var \App\Services\HelperService
     */
    protected $helperService, $customerRepository;
    /**
     * @var CustomerRoom
     */
    private $customerRoomModel;

    /**
     * Create  instance.
     *
     * @param \App\Services\HelperService $helperService
     * @param CustomerRoom $customerRoom
     * @param CustomerRepository $customerRepository
     * @param SensorConfigSettingRepository $sensorConfigSettingRepository
     * @param SensorRepository $sensorRepository
     * @param SensorActiveSettingRepository $sensorActiveSettingRepository
     */
    public function __construct(
        HelperService $helperService,
        CustomerRoom $customerRoom,
        CustomerRepository $customerRepository,
        SensorConfigSettingRepository $sensorConfigSettingRepository,
        SensorRepository $sensorRepository,
        SensorActiveSettingRepository $sensorActiveSettingRepository

        )
    {
        $this->helperService = $helperService;
        $this->customerRoomModel = $customerRoom;
        $this->customerRepository = $customerRepository;
        $this->sensorRepository = $sensorRepository;
        $this->sensorConfigSettingRepository = $sensorConfigSettingRepository;
        $this->sensorActiveSettingRepository = $sensorActiveSettingRepository;
    }

    /**
     * Display a listing of the template settings.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Foundation\Application|\Illuminate\View\View
     */
    public function index()
    {
        $motion_sensor_settings = []; // = SensorConfigSetting::first();
        $customerlist = array();
        $roomlist = array();
        $roomlist = $this->customerRoomModel->orderBy('name')->pluck('name', 'id')->toArray();
        $customerlist = array();
        $customer_arr = $this->customerRepository->getCustomerList();
        foreach ($customer_arr as $key => $customer) {
            $id = $customer['id'];
            $customerlist[$id] = $customer['project_number'] . ' - ' . $customer['client_name'];
        }
        $motion_sensor_settings = SensorConfigSetting::first();
        return view(
            'sensors::sensor-settings.sensor-settings',
            compact('motion_sensor_settings', 'customerlist', 'roomlist')
        );
    }

    public function activeSettingStore(SensorActiveSettingRequest $request)
    {
        try {
            DB::beginTransaction();
            if(isset($request->id) && !empty($request->id)){
                $roomId = $request->id;
            } else {
                $roomId = $request->room_id;
            }
            $activeSettingSave =  $this->sensorActiveSettingRepository->activeSettingSave($request->all());
            if($activeSettingSave){
                $this->sensorActiveSettingRepository->updateLambdaClient(null,[$roomId]);
            }
            DB::commit();
            return response()->json($this->helperService->returnTrueResponse());
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json($this->helperService->returnFalseResponse($e));
        }
    }

    public function configSettingStore(Request $request)
    {
        try {
            DB::beginTransaction();
            $configsetting = $this->sensorConfigSettingRepository->configSettingSave($request->all());
            DB::commit();
            if($configsetting){
                $this->sensorActiveSettingRepository->updateLambdaClient();
            }
            return response()->json($this->helperService->returnTrueResponse());
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json($this->helperService->returnFalseResponse($e));
        }
    }

     /**
     *Get a listing of the Request Type Master for Datatable.
     *
     * @return Json
     */
    public function getActiveSettingList()
    {
        return datatables()->of($this->sensorActiveSettingRepository->getAllActiveSettingList())->addIndexColumn()->toJson();
    }

    public function getRoomList($cusId)
    {
        return response()->json($this->sensorActiveSettingRepository->getRooms($cusId));
    }

    /**
     * Display details of single resource
     *
     * @param $id
     * @return json
     */

    public function getActiveSettingSingle($id)
    {
        return response()->json($this->sensorActiveSettingRepository->getActiveSetting($id));
    }


}
