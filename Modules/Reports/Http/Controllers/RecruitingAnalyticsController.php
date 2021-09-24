<?php

namespace Modules\Reports\Http\Controllers;

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Exports\UsersExport;
use Illuminate\Http\Response;
use App\Jobs\AnalyticsReport;
use App\Services\HelperService;
use Illuminate\Routing\Controller;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Modules\Reports\Repositories\RecruitingAnalyticsRepository;
use Modules\Reports\Emails\RecrutingAnalyticsReportEmail;

class RecruitingAnalyticsController extends Controller
{
    protected $recruitingAnalyticsRepository, $helperService;

    public function __construct(
        RecruitingAnalyticsRepository $recruitingAnalyticsRepository,
        HelperService $helperService
    ) {
        $this->recruitingAnalyticsRepository = $recruitingAnalyticsRepository;
        $this->helperService = $helperService;
    }


    public function  recruitingAnalyticsReport()
    {
        return view("reports::recruitinganalyticsreport.recruiting-analytics-report");
    }

    public function recruitingAnalyticsReportList()
    {

        $candidate_onboarding_status = $this->recruitingAnalyticsRepository->getCandidateRecruitingStatus();
        return datatables()->of($candidate_onboarding_status)->addIndexColumn()->toJson();
    }

    public function  recruitingAnalyticsExcelReport()
    {
        try {
            $userEmail = Auth::user()->email;
            Log::channel('reportLog')->info("Export Started");
            DB::beginTransaction();
            AnalyticsReport::dispatch($userEmail);
            DB::commit();
            return response()->json($this->helperService->returnTrueResponse());
        } catch (\Exception $e) {
            DB::rollBack();
            Log::channel('reportLog')->error($e);
            return response()->json($this->helperService->returnFalseResponse($e));
        }
    }
}
