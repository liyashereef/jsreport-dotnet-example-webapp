<?php

namespace Modules\ClientApp\Services\SiteDashboard;

use Illuminate\Support\Collection;
use Modules\Admin\Repositories\PayPeriodRepository;
use Modules\Supervisorpanel\Repositories\CustomerReportRepository;

class SiteDashboardService
{

    private $darkColours;
    private $headerColour;
    private $trendReportHeader;
    private $negativeTrendColor;
    private $positiveTrendColor;
    private $neutralTrendColor;
    private $payPeriodRepository;
    private $trendPayPeriodCount;
    private $customerReportRepository;

    public function __construct(
        CustomerReportRepository $customerReportRepository,
        PayPeriodRepository $payPeriodRepository
    ) {
        $this->customerReportRepository = $customerReportRepository;
        $this->payPeriodRepository = $payPeriodRepository;
        $this->trendPayPeriodCount = 4;

        $this->headerColour = "deepOrange";
        $this->negativeTrendColor = "red";
        $this->positiveTrendColor = "green";
        $this->neutralTrendColor = "yellow";
        $this->trendReportHeader = array("Measure", "Current", "Average", "Trend");
        $this->darkColours = array("deepOrange", "green", "black", "red");
    }

    public function trendReport($customerId, $startDate = null, $endDate = null)
    {
        $payperiodColl = [];
        if (empty($startDate) && empty($endDate)) {
            $payperiodColl = $this->payPeriodRepository->getLastNPayperiodWithCurrent($this->trendPayPeriodCount);
        } else {
            $payperiodColl = $this->payPeriodRepository->getAllActivePayPeriodsBetweenDates($startDate, $endDate);
        }

        if (count($payperiodColl) == 0) {
            return [];
        } else {
            $payperiodStartDate = $payperiodColl->last()->start_date;
            $payperiodEndDate = $payperiodColl->first()->end_date;
        }

        $trendReport = $this->customerReportRepository->customerPayperiodTrendReport($customerId, $payperiodStartDate, $payperiodEndDate);
        return $trendReport;
    }

    public function trendHeader()
    {
        $headerCollectionArr = array();
        foreach ($this->trendReportHeader as $eachHeader) {
            $headerCollection = $this->trendRowCollection($eachHeader, "deepOrange");
            array_push($headerCollectionArr, $headerCollection);
        }
        return $headerCollectionArr;
    }

    public function trendBody($trendReport)
    {
        $trendBodyArr = array();

        if (!isset($trendReport['average_report']["score"])) {
            return $trendBodyArr;
        }

        foreach ($trendReport['average_report']["score"] as $key => $eachRow) {
            $trendBodyRow = [];
            $categoryName = $key;
            $currentScore = $trendReport['current_report']["score"][$categoryName];
            $currentScore = ($currentScore) ? round($currentScore, 2) : 0;
            $currentScoreColour = $trendReport['current_report']["color_class"][$categoryName];
            $averageScore = round($trendReport['average_report']["score"][$categoryName], 2);
            $averageScoreColour = $trendReport['average_report']["color_class"][$categoryName];
            $trendScore = $this->trendValue($currentScore, $averageScore);
            $trendScoreColour = $this->trendValueColour($trendScore);

            $trendBodyRow[] = $this->trendRowCollection(ucfirst($categoryName), $this->headerColour);
            $trendBodyRow[] = $this->trendRowCollection($currentScore, $currentScoreColour);
            $trendBodyRow[] = $this->trendRowCollection($averageScore, $averageScoreColour);
            $trendBodyRow[] = $this->trendRowCollection($trendScore, $trendScoreColour);
            array_push($trendBodyArr, $trendBodyRow);
        }
        return $trendBodyArr;
    }

    public function trendRowCollection($value, $colour)
    {
        $colour = ($colour) ?? "black";
        return
            [
            'value' => $value,
            'background' => $colour,
            'isDarkText' => $this->isDarkText($colour),
        ];
    }

    public function siteDashboardResponse($mapPointerColor, $customerLocation, $matrixArr, $trendArr, $customerDetails)
    {
        return Collection::make(
            [
                "color" => $mapPointerColor,
                "location" => $customerLocation,
                "matrix" => ["header" => $matrixArr["header"], "body" => data_get($matrixArr["body"], "*.*")],
                "trendAnalysis" => ["trends" => $trendArr],
                "customerDetails" => $customerDetails,
            ]
        );
    }

    private function isDarkText($colour)
    {
        $darkText = true;
        if (in_array($colour, $this->darkColours)) {
            $darkText = false;
        }
        return $darkText;
    }

    private function trendValue($currentValue, $averageValue)
    {
        return round($currentValue - $averageValue, 2);
    }

    private function trendValueColour($trendValue)
    {
        return ($trendValue < 0) ?
        $this->negativeTrendColor :
        (($trendValue > 0) ? $this->positiveTrendColor : $this->neutralTrendColor);
    }

}
