<?php

namespace Modules\Admin\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\HelperService;
use Modules\Admin\Http\Requests\QrPatrolSettingRequest;
use Modules\Admin\Models\QrPatrolSetting;

class QrPatrolSettingController extends Controller
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
        $qrPatrolSettings = QrPatrolSetting::where('deleted_at', null)->first();
        return view('admin::qr-patrol-settings.index', compact('qrPatrolSettings'));
    }

    /**
     * Store a newly created qr settings.
     *
     * @param  Modules\Admin\Http\Requests\QrPatrolSettingRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(QrPatrolSettingRequest $request)
    {
        $inputs = $request->all();
        $qrPatrolSettings = QrPatrolSetting::where('deleted_at', null)->first();
        if (!empty($qrPatrolSettings)) {
            QrPatrolSetting::destroy($qrPatrolSettings->id);
        }
        $result = QrPatrolSetting::create($inputs);
        return response()->json($this->helperService->returnTrueResponse());
    }
}
