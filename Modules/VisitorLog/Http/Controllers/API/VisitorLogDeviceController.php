<?php

namespace Modules\VisitorLog\Http\Controllers\API;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use App\Services\HelperService;
//Event List
use Modules\VisitorLog\Events\DeviceConfigUpdated;
use Modules\VisitorLog\Events\VisitorLogNotify;
use Modules\VisitorLog\Events\VisitorNotify;
//Repo List
use Modules\VisitorLog\Repositories\VisitorLogDeviceRepository;
use Modules\Client\Repositories\VisitorRepository;
use Modules\Client\Repositories\VisitorLogRepository;

class VisitorLogDeviceController extends Controller
{
    protected $helperService;
    protected $visitorLogDeviceRepository;
    protected $visitorRepository;
    protected $visitorLogRepo;

    public function __construct(
        HelperService $helperService,
        VisitorLogDeviceRepository $visitorLogDeviceRepository,
        VisitorRepository $visitorRepository,
        VisitorLogRepository $visitorLogRepo
    ) {
        $this->helperService = $helperService;
        $this->visitorLogDeviceRepository = $visitorLogDeviceRepository;
        $this->visitorRepository = $visitorRepository;
        $this->visitorLogRepo = $visitorLogRepo;
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
                    $inputs['is_activated'] = 1;
                    $inputs['activated_by'] = \Auth::user()->id;
                    $inputs['activated_at'] = \Carbon::now();
                    $this->visitorLogDeviceRepository->activateDevice($inputs);
                    $configRequest['id'] = $deviceDetails->id;
                    $configData = $this->visitorLogDeviceRepository->setConfigData($configRequest);
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
        return response()->json([
            "config" => ($configData) ? $configData : [],
            "error" => $msg,
            'status' => $status
        ]);
    }

    public function devicePing(Request $request)
    {
        \Log::channel('customlog')->info(json_encode($request->all()));
        //TODO:trigger by timestamp

        $request->validate([
            'x-ci' =>  'required|exists:customers,id',
            'x-di' => 'required|string',
            'x-dui' => 'required'
        ]);
        $inputs = $request->all();
        $customerId = $request->input('x-ci');
        $deviceUID = $request->input('x-dui');
        //Device updation check.
        $device = $this->visitorLogDeviceRepository->getByUID($deviceUID);
        if ($device != null) {
            $inputs['id'] = $device->id;
            $configData = $this->visitorLogDeviceRepository->setConfigData($inputs);
            if($configData){
                DeviceConfigUpdated::dispatch($configData);
            }
        }
        //Visitor updation/creation check.
        $visitorCount = $this->visitorRepository->updateCount($inputs);
        if($visitorCount > 0){
            VisitorNotify::dispatch($customerId);
        }
        //Visitor log updation/creation check.
        $logCount =$this->visitorLogRepo->updateCount($inputs);
        if($logCount > 0){
            VisitorLogNotify::dispatch($customerId, 'unknown');
        }


    }
}
