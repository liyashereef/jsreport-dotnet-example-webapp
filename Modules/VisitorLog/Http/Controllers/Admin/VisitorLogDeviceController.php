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

    /**
     * Store a newly created resource in storage.
     * @param  VisitorLogDeviceRequests $request
     * @return Response
     */
    public function store(VisitorLogDeviceRequests $request)
    {
        try {
            \DB::beginTransaction();
            $inputs = $request->all();
            $inputs['activation_code'] = uniqid();
            $inputs['uid'] = (string)Str::uuid();
            // unset($inputs['camera_mode']);
            // unset($inputs['scaner_camera_mode']);
            // unset($inputs['template_id']);
            $inputs['created_by'] = \Auth::id();
            $device = $this->repository->store($inputs);
            if($device){
                $settings['camera_mode'] = $request->input('camera_mode');
                $settings['scaner_camera_mode'] = $request->input('scaner_camera_mode');
                $settings['template_id'] = $request->input('template_id');
                $settings['visitor_log_device_id'] = $device->id;
                $this->visitorLogDeviceSettingsRepository->store($settings);
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
    public function update(Request $request)
    {
    }

    /**
     * Remove the specified resource from storage.
     * @return Response
     */
    public function destroy()
    {
    }
}
