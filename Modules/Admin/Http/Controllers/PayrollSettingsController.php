<?php

namespace Modules\Admin\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Services\HelperService;
use Illuminate\Routing\Controller;
use Modules\Admin\Models\PayrollSettings;

class PayrollSettingsController extends Controller
{
    protected $helperService;

    public function __construct(HelperService $helperService)
    {
        $this->helperService = $helperService;
    }

    /**
     * Display a listing of the payroll settings
     * @return Response
     */
    public function index()
    {
        $threshold = PayrollSettings::where('setting', 'manualTimesheetThresold')->first()->value('value');
        $hours = $this->helperService->convertToHoursMins($threshold);
        return view('admin::payroll-settings.index', compact('hours'));
    }

    /**
     * Store a newly created resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'hours' => 'required|string'
        ],[
            'hours.required' => 'Hours is required'
        ]);

        try {
            \DB::beginTransaction();
            $hours = $this->helperService->h2m($request->get('hours'));
            $threshold = PayrollSettings::where('setting', 'manualTimesheetThresold')
            ->update(['value' => $hours]);
            \DB::commit();
            return response()->json($this->helperService->returnTrueResponse());
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json($this->helperService->returnFalseResponse($e));
        }
    }
}
