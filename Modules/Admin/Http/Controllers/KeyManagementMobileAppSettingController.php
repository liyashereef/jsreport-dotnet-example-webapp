<?php

namespace Modules\Admin\Http\Controllers;

use App\Http\Controllers\Controller;
use DB;
use App\Services\HelperService;
use Modules\Admin\Http\Requests\KeyMangementMobileAppSettingRequest;
use Modules\Admin\Models\KeyManagementMobileAppSetting;

class KeyManagementMobileAppSettingController extends Controller
{

     /**
     * The Repository instance.
     *
     * @var \App\Services\HelperService
     */
    protected $helperService;

    /**
     * Create  instance.
     *
     * @param  \App\Services\HelperService $helperService
     * @return void
     */
    public function __construct(HelperService $helperService)
    {
        $this->helperService = $helperService;
    }

    /**
     * Display a listing of the template settings.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $mobile_app_settings = KeyManagementMobileAppSetting::first();
        return view('admin::mobile-app-settings.keymanagement-mobile-app-settings',compact('mobile_app_settings'));
    }

    /**
     * Store a newly created template setting in storage.
     *
     * @param  Modules\Admin\Http\Requests\MobileAppSettingRequest $request
     * @return \Illuminate\Http\Response
     */
    public function storeMobileSettings(KeyMangementMobileAppSettingRequest $request)
    {

            $inputs = $request->all();
            $result = KeyManagementMobileAppSetting::first()->update($inputs);
            return response()->json($this->helperService->returnTrueResponse());
        
           
    }
}
