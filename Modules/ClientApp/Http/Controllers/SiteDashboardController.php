<?php

namespace Modules\ClientApp\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Modules\Admin\Models\Customer;
use Modules\ClientApp\Http\Resources\V1\Customer\CustomerDetailResource;
use Modules\ClientApp\Http\Resources\V1\Customer\CustomerLocationResource;
use Modules\ClientApp\Services\SiteDashboard\SiteDashboardService;
use Modules\Supervisorpanel\Repositories\CustomerMapRepository;
use Modules\Supervisorpanel\Repositories\CustomerReportRepository;

class SiteDashboardController
{

    private $customerMapRepository;
    private $customerReportRepository;
    private $siteDashboardService;

    public function __construct(
        CustomerReportRepository $customerReportRepository,
        CustomerMapRepository $customerMapRepository,
        SiteDashboardService $siteDashboardService
    ) {
        $this->customerReportRepository = $customerReportRepository;
        $this->customerMapRepository = $customerMapRepository;
        $this->siteDashboardService = $siteDashboardService;
    }

    public function siteDashboard(Request $request)
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
//            $latestTemplate = $this->customerReportRepository->getLatestTemplate();
            //            $currentPayperiod = $this->payPeriodRepository->getCurrentPayperiod();
            //            $siteDetails = $this
            //                ->customerMapRepository
            //                ->getCustomerMapDetails($latestTemplate, null, null, true, $customerId);
            $trendReport = $this->siteDashboardService->trendReport($customerId, $payPeriodStart, $payPeriodEnd);
            $trendHeader = $this->siteDashboardService->trendHeader();
            if (!empty($trendReport)) {
                $totalTrendScore = $trendReport["current_report"]["score"]["total"] ?? null;
                $totalTrendScoreColour = $trendReport["current_report"]["color_class"]["total"] ?? $this->customerMapRepository->getDefaultColor();
                $trends = $trendReport['trend_client'];
                $trendBody = $this->siteDashboardService->trendBody($trendReport);
                $mapPointerColor = $totalTrendScoreColour ?? $this->customerMapRepository->getDefaultColor();
                $matrixArr = array("header" => $trendHeader, "body" => $trendBody);
            } else {
                $mapPointerColor = "";
                $matrixArr = array("header" => $trendHeader, "body" => []);
                $trends = [];
            }
            $siteDashboardResponse = $this->siteDashboardService
                ->siteDashboardResponse($mapPointerColor, $customerLocation, $matrixArr, $trends, $customerDetails);

            return response()->make(["data" => $siteDashboardResponse]);
        } catch (\Exception $e) {
            throw $e;
        }
    }

}
