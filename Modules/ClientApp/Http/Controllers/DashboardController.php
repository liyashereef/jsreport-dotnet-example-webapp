<?php

namespace Modules\ClientApp\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Admin\Models\Customer;
use Modules\ClientApp\Http\Resources\V1\Customer\CustomerDetailResource;
use Modules\ClientApp\Http\Resources\V1\Customer\CustomerLocationResource;
use Modules\ClientApp\Http\Resources\V1\IncidentReport\IncidentReportResource;
use Modules\ClientApp\Services\SiteDashboard\SiteDashboardService;
use Modules\Supervisorpanel\Models\IncidentReport;
use Modules\Supervisorpanel\Repositories\CustomerMapRepository;

class DashboardController extends Controller
{

    private $trendPayPeriodCount;
    private $siteDashboardService;
    private $incidentListLimit;
    private $customerMapRepository;

    public function __construct(
        SiteDashboardService $siteDashboardService,
        CustomerMapRepository $customerMapRepository
    ) {
        $this->siteDashboardService = $siteDashboardService;
        $this->trendPayPeriodCount = 4;
        $this->incidentListLimit = 5;
        $this->customerMapRepository = $customerMapRepository;
    }

    public function dashboard(Request $request)
    {
        if (($request->has('payPeriodStart') && !empty($request->payPeriodStart)) || ($request->has('payPeriodEnd') && !empty($request->payPeriodEnd))) {
            $request->validate([
                'customerId' => 'required',
                'payPeriodStart' => 'required',
                'payPeriodEnd' => 'required',
            ]);
        } else {
            $request->validate([
                'customerId' => 'required',
            ]);
        }

        try {
            $customerId = $request->customerId;
            $payPeriodStart = null;
            $payPeriodEnd = null;
            if ($request->has('payPeriodStart') && !empty($request->payPeriodStart)) {
                $payPeriodStart = Carbon::createFromFormat('Y-m-d H:i:s', date('Y-m-d H:i:s', strtotime($request->payPeriodStart)));
                $payPeriodStart->setTime(0, 0, 0);
            }

            if ($request->has('payPeriodEnd') && !empty($request->payPeriodEnd)) {
                $payPeriodEnd = Carbon::createFromFormat('Y-m-d H:i:s', date('Y-m-d H:i:s', strtotime($request->payPeriodEnd)));
                $payPeriodEnd->setTime(23, 59, 59);
            }
            $customerModel = Customer::findOrFail($customerId);
            $customerLocation = new CustomerLocationResource($customerModel);
            $customerDetails = new CustomerDetailResource($customerModel);
            $trendReport = $this->siteDashboardService->trendReport($customerId, $payPeriodStart, $payPeriodEnd);
            $trendHeader = $this->siteDashboardService->trendHeader();
            if (!empty($trendReport)) {
                $totalTrendScoreColour = $trendReport["current_report"]["color_class"]["total"] ?? $this->customerMapRepository->getDefaultColor();
                $trendBody = $this->siteDashboardService->trendBody($trendReport);
                $mapPointerColor = $totalTrendScoreColour;
                $trends = $trendReport['trend_client'];
                $matrixArr = array("header" => $trendHeader, "body" => $trendBody);
                $trendArr = $trends;
            } else {
                $mapPointerColor = "";
                $matrixArr = array("header" => $trendHeader, "body" => []);
                $trendArr = [];
            }
            $siteDashboardResponse = $this->siteDashboardService
                ->siteDashboardResponse($mapPointerColor, $customerLocation, $matrixArr, $trendArr, $customerDetails);

            $incident = IncidentReportResource::collection(
                IncidentReport::where('customer_id', $request->customerId)
                    ->orderBy('created_at', 'desc')
                    ->take($this->incidentListLimit)
                    ->get()
            );

            return response()->make(
                [
                    "data" => [
                        "siteDashboard" => $siteDashboardResponse,
                        "incidentReport" => [
                            "data" => $incident,
                        ],
                    ],
                ]);
        } catch (\Exception $e) {
            throw $e;
        }
    }

}
