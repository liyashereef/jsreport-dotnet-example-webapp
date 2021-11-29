<?php

namespace Modules\VisitorLog\Http\Controllers\API;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;

use App\Services\HelperService;
use Modules\VisitorLog\Events\DeviceConfigUpdated;
use Modules\VisitorLog\Events\VisitorLogNotify;
use Modules\VisitorLog\Events\VisitorNotify;
use Modules\VisitorLog\Repositories\VisitorLogDeviceRepository;
class VisitorLogDeviceController extends Controller
{
    protected $helperService;
    protected $visitorLogDeviceRepository;

    public function __construct(
        HelperService $helperService,
        VisitorLogDeviceRepository $visitorLogDeviceRepository
    ) {
        $this->helperService = $helperService;
        $this->visitorLogDeviceRepository = $visitorLogDeviceRepository;
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
                    $configData = $this->visitorLogDeviceRepository->setConfigData($deviceDetails->id);
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

        $customerId = $request->input('x-ci');
        $deviceUID = $request->input('x-dui');
        $device = $this->visitorLogDeviceRepository->getByUID($deviceUID);
        if ($device != null) {
            $configData = $this->visitorLogDeviceRepository->setConfigData($device->id);
            DeviceConfigUpdated::dispatch($configData);
        }

        VisitorLogNotify::dispatch($customerId, 'unknown');
        VisitorNotify::dispatch($customerId);
    }
}
