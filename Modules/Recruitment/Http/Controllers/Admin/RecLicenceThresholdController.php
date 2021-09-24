<?php

namespace Modules\Recruitment\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Services\HelperService;
use Illuminate\Routing\Controller;
use Modules\Recruitment\Models\RecSecurityGuardLicenceThresholds;

class RecLicenceThresholdController extends Controller
{
    protected $repository, $helperService;

    /**
     * Create Repository instance.
     * @param
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
        $thresholdData = RecSecurityGuardLicenceThresholds::first();
        if (is_null($thresholdData)) {
            $thresholdData = new RecSecurityGuardLicenceThresholds();
            $thresholdData->threshold = 1;
            $thresholdData->save();
        }
        return view('recruitment::masters.licence-threshold', compact('thresholdData'));
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
            RecSecurityGuardLicenceThresholds::first()->update($data);
             \DB::commit();
            return response()->json($this->helperService->returnTrueResponse());
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json($this->helperService->returnFalseResponse());
        }
    }
}
