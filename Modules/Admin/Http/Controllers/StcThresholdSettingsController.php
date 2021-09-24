<?php

namespace Modules\Admin\Http\Controllers;

use App\Services\HelperService;
use Illuminate\Routing\Controller;
use Modules\Admin\Http\Requests\StcThresholdSettingRequest;
use Modules\Admin\Models\StcThresholdSetting;
use Modules\Admin\Repositories\StcThresholdSettingsRepository;

class StcThresholdSettingsController extends Controller
{
    protected $repository;

    public function __construct(StcThresholdSettingsRepository $repository, HelperService $helperService)
    {
        $this->repository = $repository;
        $this->helperService = $helperService;
    }

    public function index()
    {
        $stcThresholdSettings = StcThresholdSetting::where('deleted_at', null)->first();
        return view('admin::stc-threshold.index', compact('stcThresholdSettings'));
    }

    public function store(StcThresholdSettingRequest $request)
    {
        $inputs = $request->all();
        $stcThresholdSettings = StcThresholdSetting::where('deleted_at', null)->first();
        if (!empty($stcThresholdSettings)) {
            StcThresholdSetting::destroy($stcThresholdSettings->id);
        }
        $result = StcThresholdSetting::create($inputs);
        return response()->json($this->helperService->returnTrueResponse());
    }

    public function getSettings()
    {
        $stcThresholdSettings = StcThresholdSetting::where('deleted_at', null)->first();
        return response()->json(['data' => $stcThresholdSettings]);
    }
}
