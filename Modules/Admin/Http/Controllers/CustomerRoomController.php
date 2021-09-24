<?php

namespace Modules\Admin\Http\Controllers;

use App\Services\HelperService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\Admin\Http\Requests\CustomerRoomRequest;
use Modules\Admin\Http\Requests\LinkSensorRequest;
use Modules\Admin\Http\Requests\LinkIpCameraRequest;
use Modules\Admin\Models\CustomerRoom;
use Modules\Admin\Models\Sensor;
use Modules\Admin\Repositories\CustomerRepository;
use Modules\Admin\Repositories\CustomerRoomRepository;
use Modules\Sensors\Repositories\SensorActiveSettingRepository;

class CustomerRoomController extends Controller
{
    protected $repository, $customerRepository;
    /**
     * Create Repository instance.
     * @param  \App\Repositories\CustomerRoomRepository $customerRoomRepository
     * @return void
     */
    public function __construct(
        CustomerRoomRepository $customerRoomRepository,
        CustomerRoom $customerRoomModel,
        Sensor $sensorModel,
        CustomerRepository $customerRepository,
         HelperService $helperService,
        SensorActiveSettingRepository $sensorActiveSettingRepository
         )
    {
        $this->repository = $customerRoomRepository;
        $this->customerRepository = $customerRepository;
        $this->model = $customerRoomModel;
        $this->sensorModel = $sensorModel;
        $this->helperService = $helperService;
        $this->sensorActiveSettingRepository = $sensorActiveSettingRepository;
    }

    /**
     * Load the resource listing Page
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $customer_arr = $this->customerRepository->getCustomerList();
        $room_severity = config('globals.room_severity');
        $customerlist = array();
        foreach ($customer_arr as $key => $customer) {
            $id = $customer['id'];
            $customerlist[$id] = $customer['project_number'] . ' - ' . $customer['client_name'];
        }
        return view('admin::customer.customer-room', compact('customerlist','room_severity'));
    }

    /**
     *Get a listing of the Request Type Master for Datatable.
     *
     * @return Json
     */
    public function getList($id = null, Request $request)
    {
        $client_id = $request->get('client_id');
        $rooms = $this->repository->getAll($id,$client_id);
        return datatables()->of($rooms)->addIndexColumn()->toJson();
    }

    public function store(CustomerRoomRequest $request)
    {
        try {
            DB::beginTransaction();
            $customerQrCode =   $this->repository->save($request->all());
            DB::commit();
            return response()->json($this->helperService->returnTrueResponse());
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json($this->helperService->returnFalseResponse($e));
        }
    }

    /**
     * Display details of single resource
     *
     * @param $id
     * @return json
     */
    public function getSingle($id)
    {
        return response()->json($this->repository->get($id));
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  $id
     * @return json
     */
    public function destroy($id)
    {
        try {
            DB::beginTransaction();
            $disabledSensors = $this->repository->delete($id);
            if($disabledSensors){
                $this->sensorActiveSettingRepository->updateLambdaClient($disabledSensors);
            }
            DB::commit();
            return response()->json($this->helperService->returnTrueResponse());
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json($this->helperService->returnFalseResponse());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  $id
     * @return json
     */
    public function getLinkSensor($id)
    {
        $mergedArray = [];
        $test = [];
        $array1 = $this->repository->getLinkSensor($id)->toArray();
        $array2['unlinked_sensors'] = $this->repository->sensorsList()->toArray();
        $mergedArray = array_replace_recursive($array1, $array2);
        return response()->json($mergedArray);
    }

    public function getLinkIpCamera($id)
    {
        $mergedArray = [];
        $array1 = $this->repository->getLinkIpCamera($id)->toArray();
        $array2['unlinked_ipcameras'] = $this->repository->IpCamerasList()->toArray();
        $mergedArray = array_replace_recursive($array1, $array2);
        return response()->json($mergedArray);
    }

    public function linkSensorstore(LinkSensorRequest $request)
    {
        try {
            DB::beginTransaction();
            $linkDetails =   $this->repository->linkSensorSave($request->all());
            if($linkDetails){
                $this->sensorActiveSettingRepository->updateLambdaClient($request->sensor_id);
            }
            DB::commit();
            return response()->json($this->helperService->returnTrueResponse());
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json($this->helperService->returnFalseResponse($e));
        }
    }

    public function linkIpCamerastore(LinkIpCameraRequest $request)
    {
        try {
            DB::beginTransaction();
            $linkDetails =   $this->repository->linkIpCameraSave($request->all());
            DB::commit();
            return response()->json($this->helperService->returnTrueResponse());
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json($this->helperService->returnFalseResponse($e));
        }
    }

    public function getUnLinkSensor($id)
    {
        return response()->json($this->repository->getUnLinkSensor($id));
    }

    public function getUnLinkIpCamera($id)
    {
        return response()->json($this->repository->getUnLinkIpCamera($id));
    }

    public function unLinkSensorstore(Request $request)
    {
        try {
            DB::beginTransaction();
            $unlinkIds =   $this->repository->unLinkSensorSave($request->all());
            if($unlinkIds){
                $this->sensorActiveSettingRepository->updateLambdaClient($unlinkIds);
            }
            DB::commit();
            return response()->json($this->helperService->returnTrueResponse());
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json($this->helperService->returnFalseResponse($e));
        }
    }

    public function unlinkIpCamerastore(Request $request)
    {
        try {
            DB::beginTransaction();
            $unlinkIds =   $this->repository->unLinkIpCameraSave($request->all());
            DB::commit();
            return response()->json($this->helperService->returnTrueResponse());
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json($this->helperService->returnFalseResponse($e));
        }
    }
}
