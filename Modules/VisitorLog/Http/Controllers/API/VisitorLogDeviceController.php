<?php

namespace Modules\VisitorLog\Http\Controllers\API;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;

use App\Services\HelperService;
use Modules\VisitorLog\Transformers\VisitorLogDeviceResources;
use Modules\VisitorLog\Repositories\VisitorLogDeviceRepository;
use Modules\Admin\Repositories\VisitorLogScreeningTemplateCustomerAllocationRepository;
use Modules\Admin\Repositories\VisitorLogTemplateRepository;

class VisitorLogDeviceController extends Controller
{
    protected $helperService;
    protected $visitorLogDeviceRepository;
    protected $visitorLogScreeningTemplateCustomerAllocationRepository;
    protected $visitorLogTemplateRepository;

    public function __construct(
        HelperService $helperService,
        VisitorLogDeviceRepository $visitorLogDeviceRepository,
        VisitorLogScreeningTemplateCustomerAllocationRepository $visitorLogScreeningTemplateCustomerAllocationRepository,
        VisitorLogTemplateRepository $visitorLogTemplateRepository
    ) {
        $this->helperService = $helperService;
        $this->visitorLogDeviceRepository = $visitorLogDeviceRepository;
        $this->screeningtemplateCustomerAllocationRepository = $visitorLogScreeningTemplateCustomerAllocationRepository;
        $this->visitorLogTemplateRepository = $visitorLogTemplateRepository;

    }

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function activateDevice(Request $request)
    {

        $status = true;
        $configData = '';
        try {
            if ($request->has('code')) {
                $deviceDetails = $this->visitorLogDeviceRepository->getByActivateCode($request->input('code'));

                if ($deviceDetails) {
                    $inputs = $request->all();
                    $inputs['activation_code'] = $request->input('code');
                    $inputs['device_id'] = $request->input('deviceId');
                    // $inputs['is_activated'] = 1;
                    $this->visitorLogDeviceRepository->activateDevice($inputs);
                    $configData = $this->visitorLogDeviceRepository->getById($deviceDetails->id);
                    $configData->questions = $this->visitorLogTemplateRepository->fetchTemplateDetails($deviceDetails->visitorLogDeviceSettings->template_id);

                    // $r['customerId'] = $deviceDetails->customer_id;
                    // $configData->screening = $this->screeningtemplateCustomerAllocationRepository->getTemplateByCustomerId($r);
                    // dd($configData->screening->VisitorLogScreeningTemplate->VisitorLogScreeningTemplateQuestion);
                    $msg = '';
                } else {
                    $msg = 'Activation code not found/Already activated';
                    $status = false;
                }
            } else {
                $msg = 'Activation code not found';
                $status = false;
            }
        } catch (\Exception $e) {
            $msg = $e->getMessage();
            $status = false;
        }
// dd($configData);
        return response()->json([
            "config" => ($configData)? new VisitorLogDeviceResources($configData) : [],
            "error" => $msg,
            'status'=>$status
        ]);
    }
}
