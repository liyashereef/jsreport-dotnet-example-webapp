<?php

namespace Modules\Admin\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\HelperService;
use Illuminate\Http\Request;
use Modules\Admin\Http\Requests\CapacityToolObjectiveRequest;
use Modules\Admin\Repositories\LicenceThresholdRepository;
use Modules\Admin\Models\SecurityGuardLicenceThreshold;
use Carbon;

class LicenceThresholdController extends Controller
{
    protected $repository, $helperService;

    /**
     * Create Repository instance.
     * @param  \App\Repositories\CapacityToolObjectiveLookupRepository $capacityToolObjectiveLookupRepository
     * @return void
     */
    public function __construct(HelperService $helperService)
    {
        $this->helperService = $helperService;
    }

    /**
     * Load the resource listing Page
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $thresholdData = SecurityGuardLicenceThreshold::first();
        if (is_null($thresholdData)) {
            $thresholdData = new SecurityGuardLicenceThreshold();
            $thresholdData->threshold = 1;
            $thresholdData->save();
        }
        return view('admin::masters.licence-threshold', compact('thresholdData'));
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  $request
     * @return json
     */
    public function store(Request $request)
    {
        $request->validate([
            'threshold' => 'required|integer|digits_between:1,8'
        ], [
            'threshold.required' => 'Threshold is required',
            'threshold.integer' => 'Threshold should be a numeric value.',
            'threshold.digits_between' => 'Threshold should be maximum 8 digits.'

        ]);
        try {
            \DB::beginTransaction();
            $data['threshold'] = $request->threshold;
            SecurityGuardLicenceThreshold::first()->update($data);
             \DB::commit();
            return response()->json($this->helperService->returnTrueResponse());
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json($this->helperService->returnFalseResponse());
        }
    }
}
