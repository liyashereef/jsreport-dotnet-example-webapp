<?php

namespace Modules\VisitorLog\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;

use App\Services\HelperService;
use Modules\Admin\Models\Customer;
use Modules\Admin\Repositories\CustomerEmployeeAllocationRepository;
use Modules\Admin\Repositories\VisitorLogTemplateRepository;
use Modules\VisitorLog\Repositories\VisitorLogDeviceRepository;
use Modules\VisitorLog\Repositories\VisitorLogDeviceSettingsRepository;
use Modules\VisitorLog\Http\Requests\VisitorLogDeviceRequests;
use Illuminate\Support\Str;
use Modules\VisitorLog\Events\DeviceConfigUpdated;
use Modules\VisitorLog\Events\VisitorNotify;

class VisitorLogDeviceController extends Controller
{

    protected $helperService;
    protected $customerEmployeeAllocationRepository;
    protected $visitorLogTemplateRepository;
    protected $repository;
    protected $visitorLogDeviceSettingsRepository;


    public function __construct(
        HelperService $helperService,
        CustomerEmployeeAllocationRepository $customerEmployeeAllocationRepository,
        VisitorLogTemplateRepository $visitorLogTemplateRepository,
        VisitorLogDeviceRepository $repository,
        VisitorLogDeviceSettingsRepository $visitorLogDeviceSettingsRepository
    ){
        $this->helperService = $helperService;
        $this->customerEmployeeAllocationRepository = $customerEmployeeAllocationRepository;
        $this->visitorLogTemplateRepository = $visitorLogTemplateRepository;
        $this->repository = $repository;
        $this->visitorLogDeviceSettingsRepository = $visitorLogDeviceSettingsRepository;
    }

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        $customers = $this->customerEmployeeAllocationRepository->getAllocatedCustomersList(\Auth::user());
        return view('visitorlog::admin.devices',compact('customers'));
    }

    public function getAllocatedTemplates($customerId){
        return $this->visitorLogTemplateRepository->allocationTemplateList($customerId);
    }

    public function getAll(){
        return datatables()->of($this->repository->getAll())->addIndexColumn()->toJson();
    }

    public function fetchById($id){
        return $this->repository->fetchById($id);
     }

    /**
     * Store a newly created resource in storage.
     * @param  VisitorLogDeviceRequests $request
     * @return Response
     */
    public function store(VisitorLogDeviceRequests $request)
    {
        try {
            \DB::beginTransaction();
            $trigger = false;
            $inputs = $request->all();
            $settings['camera_mode'] = $request->input('camera_mode');
            $settings['scaner_camera_mode'] = $request->input('scaner_camera_mode');
            $settings['template_id'] = $request->input('template_id');
            $inputs['screening_enabled'] = isset($inputs['screening_enabled']) ? 1 : 0;

            if($request->filled('id')){
                $deviceId = $request->input('id');
                $this->repository->updateEntry($inputs);
                $settings['visitor_log_device_id'] = $request->input('id');
                $this->visitorLogDeviceSettingsRepository->updateByDeviceId($settings);
                $trigger = true;
            }else{
                $inputs['created_by'] = \Auth::id();
                $inputs['activation_code'] = uniqid();
                $inputs['uid'] = (string)Str::uuid();
                $device = $this->repository->store($inputs);
                if($device){
                    $deviceId = $device->id;
                    $settings['visitor_log_device_id'] = $device->id;
                    $settings['pin'] = rand(10000,99999);
                    $this->visitorLogDeviceSettingsRepository->store($settings);
                    $trigger = true;
                }
            }
            if($trigger){
                $this->trigerBroadcasting($deviceId);
            }

            \DB::commit();
            return response()->json($this->helperService->returnTrueResponse());
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json($this->helperService->returnFalseResponse($e));
        }
    }


    /**
     * Update the specified resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function changeStatus($id)
    {
        try {
            \DB::beginTransaction();
            $data = $this->repository->fetchById($id);
            $inputs['id'] = $data->id;
            if($data->is_blocked == 1){
                $inputs['is_blocked'] = 0;
            }else{
                $inputs['is_blocked'] = 1;
            }

            $this->repository->updateEntry($inputs);
            $this->trigerBroadcasting($data->id);
            \DB::commit();
        return response()->json($this->helperService->returnTrueResponse());
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json($this->helperService->returnFalseResponse($e));
        }
    }

    public function trigerBroadcasting($id){
        $configData = $this->repository->setConfigData($id);
        DeviceConfigUpdated::dispatch($configData);
        // VisitorNotify::dispatch(129);
    }

    /**
     * Remove the specified resource from storage.
     * @return Response
     */
    public function destroy($id)
    {
        try {
            \DB::beginTransaction();
            $this->visitorLogDeviceSettingsRepository->delete($id);
            $this->repository->delete($id);
            \DB::commit();
            return response()->json($this->helperService->returnTrueResponse());
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json($this->helperService->returnFalseResponse($e));
        }
    }


}
