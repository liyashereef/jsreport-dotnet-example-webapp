<?php

namespace Modules\Reports\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Reports\Repositories\VisitorLogReportRepository;  

class VisitorLogReportController extends Controller
{
    protected $visitorLogReportRepository;
    
    public function __construct(
        VisitorLogReportRepository $visitorLogReportRepository
    ) {
        $this->visitorLogReportRepository = $visitorLogReportRepository;
    }

    public function visitorLogReport() {
        $customerName = $this->visitorLogReportRepository->getCustomerList();
        return view('reports::visitorlogreport.visitorlogreport', compact('customerName'));
    }

    public function getVisitorLogReport(Request $request) {
       $visitorDetail = $this->visitorLogReportRepository->getVisitorLogDetails($request);
       return response()->json($visitorDetail);
    }
}

