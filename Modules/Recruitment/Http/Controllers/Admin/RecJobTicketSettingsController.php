<?php

namespace Modules\Recruitment\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Services\HelperService;
use Illuminate\Routing\Controller;
use Modules\Recruitment\Models\RecJobTicketSetting;

class RecJobTicketSettingsController extends Controller
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
        $noticeperiod = RecJobTicketSetting::whereIn('setting', ['minNoticePeriodDays', 'maxNoticePeriodDays'])
        ->get()
        ->pluck('value', 'setting')
        ->toArray();

        $min = $noticeperiod['minNoticePeriodDays'];
        $max = $noticeperiod['maxNoticePeriodDays'];

        return view('recruitment::masters.job-ticket-settings', compact('min','max'));
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
            $min = RecJobTicketSetting::where('setting', 'minNoticePeriodDays')
            ->update(['value' => $request->get('min')]);
            $max = RecJobTicketSetting::where('setting', 'maxNoticePeriodDays')
            ->update(['value' => $request->get('max')]);
            \DB::commit();
            return response()->json($this->helperService->returnTrueResponse());
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json($this->helperService->returnFalseResponse($e));
        }

    }
}
