<?php

namespace Modules\Admin\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use App\Services\HelperService;
use Modules\Admin\Models\JobTicketSetting;

class JobTicketSettingsController extends Controller
{
    protected $helperService;

    public function __construct(HelperService $helperService)
    {
        $this->helperService = $helperService;
    }

    /**
     * get no of days tolerance between creation and requirement of job setting
     * @return Response
     */
    public function index()
    {
        // get job ticket setting notice period days
        $noticeperiod = JobTicketSetting::whereIn('setting', ['minNoticePeriodDays', 'maxNoticePeriodDays'])
        ->get()
        ->pluck('value', 'setting')
        ->toArray();

        $min = $noticeperiod['minNoticePeriodDays'];
        $max = $noticeperiod['maxNoticePeriodDays'];

        return view('admin::job-ticket-settings.index', compact('min','max'));
    }

    /**
     * Update notice period values for job ticket settings
     * @param  Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        try {
            \DB::beginTransaction();
            $min = JobTicketSetting::where('setting', 'minNoticePeriodDays')
            ->update(['value' => $request->get('min')]);
            $max = JobTicketSetting::where('setting', 'maxNoticePeriodDays')
            ->update(['value' => $request->get('max')]);
            \DB::commit();
            return response()->json($this->helperService->returnTrueResponse());
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json($this->helperService->returnFalseResponse($e));
        }

    }
}
