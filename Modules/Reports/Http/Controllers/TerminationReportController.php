<?php

namespace Modules\Reports\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Reports\Repositories\TerminationReportRepository;

class TerminationReportController extends Controller
{
    protected $terminationReportRepository;

    public function __construct(TerminationReportRepository $terminationReportRepository) {
        $this->terminationReportRepository = $terminationReportRepository;
    }

    /**
     * Display termination report
     * @return Response
     */
    public function terminationReport()
    {
        return view('reports::terminationreport.terminationreport');
    }

    public function getTerminationReport(Request $request) {
        $terminationReportData = $this->terminationReportRepository->getTerminationReportData($request);
        return datatables()->of($terminationReportData)->toJson();
    }


}
