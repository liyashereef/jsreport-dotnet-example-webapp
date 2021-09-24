<?php

namespace App\Http\Controllers;

use App\Repositories\SummaryDashboardRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Modules\Admin\Models\Customer;
use Modules\Admin\Models\DashboardSetting;
use Modules\Admin\Models\EmployeeRatingLookup;
use Modules\Admin\Models\KpiMaster;
use Modules\Admin\Models\QrPatrolSetting;
use Modules\Admin\Models\SummaryDashboardConfiguration;
use Modules\Admin\Models\TemplateQuestionsCategory;
use Modules\Admin\Models\TemplateSettingRules;
use Modules\Admin\Repositories\CustomerEmployeeAllocationRepository;
use Modules\Admin\Repositories\PayPeriodRepository;
use Modules\Employeescheduling\Repositories\SchedulingRepository;
use Modules\Hranalytics\Models\EmployeeSurveyAnswer;
use Modules\Hranalytics\Models\EmployeeSurveyQuestion;
use Modules\KPI\Repositories\KpiAnalyticsRepository;
use Modules\KPI\Services\Jobs\PerfomanceManagementBulk;
use Modules\KPI\Services\KpiFrequencyMap;
use Modules\KPI\Services\KpiJobOption;
use Modules\Supervisorpanel\Repositories\CustomerMapRepository;

class SummaryDashboardController extends Controller
{

    const SUMMARY_DASHBOARD_TRAINING_COMPLIANCE = 3, SUMMARY_DASHBOARD_SITE_TURN_OVER = 2, SUMMARY_DASHBOARD_GUARD_PERFORMANCE = 1;
    const CLIENT_SURVEY_KPID = 4, SITE_METRIC_KPID = 2, TRAINING_COMPLIANCE_KPID = 5, PERFORMANCE_MGMT_KPID = 6, SITE_TURN_OVER = 1, SCHEDULE_INFRACTION = 2, OPERATIONS_DASHBOARD = 3;

    protected $summaryDashboardRepository, $customerEmployeeAllocationRepository;
    protected $kpiAnalyticsRepository;


    public function __construct(
        PayPeriodRepository $pay_period_repository,
        CustomerMapRepository $customerMapRepository,
        SchedulingRepository $schedulingRepository,
        SummaryDashboardRepository $summaryDashboardRepository,
        CustomerEmployeeAllocationRepository $customerEmployeeAllocationRepository,
        KpiAnalyticsRepository $kpiAnalyticsRepository
    ) {
        $this->summaryDashboardRepository = $summaryDashboardRepository;
        $this->customerEmployeeAllocationRepository = $customerEmployeeAllocationRepository;
        $this->schedulingRepository = $schedulingRepository;
        $this->customerMapRepository = $customerMapRepository;
        $this->pay_period_repository = $pay_period_repository;
        $this->kpiAnalyticsRepository = $kpiAnalyticsRepository;
        $this->middleware('auth');
    }

    public function index()
    {
        if (!(\Auth::user()->hasPermissionTo('view_summary_dashboard'))) {
            return redirect()->route('dashboard');
        }

        $customerIds = $this->customerEmployeeAllocationRepository->getAllocatedPermanentCustomers(\Auth::user());
        $customers = Customer::orderBy('client_name')->findMany($customerIds);
        return view('summary-dashboard', compact('customers'));
    }

    public function loadClientSurvey(Request $request)
    {
        $result = $records = [];
        $startDate = Carbon::parse($request->get('start_date'))->startOfDay();
        $endDate = Carbon::parse($request->get('end_date'))->endOfDay();
        $customerIds = $request->get('customer_ids');
        $customerSurveyRecords = $this->summaryDashboardRepository->getKpiDataByParams($startDate, $endDate, $customerIds)->where('kpid', SummaryDashboardController::CLIENT_SURVEY_KPID);
        $startDateStr = strtotime($startDate->format('y-m-01'));
        $endDateStr = strtotime($endDate->format('y-m-01'));
        $i = 0;
        $temp = [];
        while ($startDateStr <= $endDateStr) {
            $processDate = date('01-M-y', $startDateStr);
            $result[$i] = 0;
            $records[$i] = ['total_value' => 0, 'count' => 0, 'average' => 0];
            $temp[$processDate] = ['total_value' => 0, 'count' => 0, 'average' => 0];
            $startDateStr = strtotime('+1 month', $startDateStr);
            $i++;
        }

        if (count($temp) > 4) {
            $temp = array_slice($temp, -4);
            $result = array_slice($result, -4);
        }

        $labels = array_keys($temp);
        if (!empty($customerSurveyRecords)) {
            foreach ($customerSurveyRecords as $customerSurveyRecord) {
                $processDate = Carbon::parse($customerSurveyRecord->process_date)->format('01-M-y');
                if (!in_array($processDate, $labels)) {
                    continue;
                }
                $keyVal = array_search($processDate, $labels);
                $records[$keyVal]['total_value'] += $customerSurveyRecord->value;
                $records[$keyVal]['count'] += 1;
                $result[$keyVal] = round($records[$keyVal]['total_value'] / $records[$keyVal]['count'], 1);
            }
        }

        return response()->json([
            'success' => true,
            'data' => $result,
            'labels' => $labels,
            'start_date' => $request->get('start_date'),
            'end_date' => $request->get('end_date'),
            'customer_ids' => $request->get('customer_ids')
        ]);
    }

    public function loadEmployeeSurvey(Request $request)
    {
        $dashBoardData = DashboardSetting::find(1);
        $returnData = [];
        $returnData["count"] = [];
        $returnData["label"] = "";
        $colorCode = [];
        $employeeRatings = EmployeeRatingLookup::select("id", "rating")->get();

        if ($dashBoardData) {

            if ($dashBoardData->default_employeesurvey > 0) {
                $empQuestions = EmployeeSurveyQuestion::where("survey_id", $dashBoardData->default_employeesurvey)
                    ->orderBy('sequence')->get();
                $empSurveyQuestion = 0;
                $empSurveyQuestionType = 0;
                foreach ($empQuestions as $empQuestion) {
                    if ($empSurveyQuestion === 0) {
                        $empSurveyQuestion = $empQuestion->id;
                        $empSurveyQuestionType = $empQuestion->answer_type;
                    }
                }
                if ($empSurveyQuestionType == 1) {

                    $yesNoData =
                        EmployeeSurveyAnswer::where([
                            "survey_id" => $dashBoardData->default_employeesurvey,
                            "question_id" => $empSurveyQuestion,
                        ])->get();
                    foreach ($yesNoData as $yData) {
                        try {
                            $returnData["label"] = $yData->surveyQuestion->question;

                            if (!isset($returnData["count"][$yData->answer])) {
                                $returnData["count"][$yData->answer] = 1;
                            } else {
                                $returnData["count"][$yData->answer] = $returnData[$yData->answer]["count"] + 1;
                            }
                            //code...
                        } catch (\Throwable $th) {
                            //throw $th;
                        }
                    }
                } else {
                    $cachedData = [];
                    $yesNoData =
                        EmployeeSurveyAnswer::where([
                            "survey_id" => $dashBoardData->default_employeesurvey,
                            "question_id" => $empSurveyQuestion,
                        ])->get();
                    foreach ($yesNoData as $yData) {
                        try {
                            $returnData["label"] = $yData->surveyQuestion->question;

                            if (!isset($cachedData[$yData->answer])) {
                                $cachedData[$yData->answer] = 1;
                            } else {
                                $cachedData[$yData->answer] = $returnData[$yData->answer]["count"] + 1;
                            }
                            //code...
                        } catch (\Throwable $th) {
                            //throw $th;
                        }
                    }
                    foreach ($employeeRatings as $employeeRating) {
                        $shortDescription = $employeeRating->rating;
                        if (isset($cachedData[$employeeRating->id])) {
                            $returnData["count"][$shortDescription] = $cachedData[$employeeRating->id];
                        }
                    }
                }
            }
        }
        $dataSet = [];
        $colors = [
            "#003A63", "#FF0000", "#F55A35", "#348AC7", "#1D617A", "#288386", "#6CAF7F",
            "#185071", "#267C8A", "#B5D568", "#EEE9BB", "#0E0E0E", "#5C89AE", "#3DBAC5", "#B5D568", "#45D2D1",
            "#E0C769", "#E16A68", "#CDCDCD", "#00000",
        ];
        if (count($returnData) > 0) {
            $i = 0;
            foreach ($returnData["count"] as $key => $value) {
                $dataSet[] = [
                    "label" => $key,
                    "data" => [$value],
                    "backgroundColor" => $colors[$i],

                ];
                $i++;
            }
        }
        $returnDataSet = [
            "labels" => [$returnData["label"]],
            "datasets" => [
                (object) $dataSet,
            ],
            'start_date' => $request->get('start_date'),
            'end_date' => $request->get('end_date'),
            'customer_ids' => $request->get('customer_ids')
        ];
        return json_encode($returnDataSet);
    }

    public function loadOperationsDashboardMatrix(Request $request)
    {
        $start = microtime(true);

        // $a = $this->summaryDashboardRepository->croneOperationsDashboardMatrix();
        $endDate = Carbon::parse($request->get('end_date'))->endOfDay();
        $customerIds = $request->get('customer_ids');
        $payperiodIdArray = [];
        $payperiodIds = [];
        $headerArray = [];
        $payperiodKeyArr = [];
        $payperiods = $this->pay_period_repository->getLastNthPayPeriodsByEndDate($endDate);
        $categories = TemplateQuestionsCategory::where('safety_type', 0)->pluck('description', 'id')->toArray();
        $deletedCategoryIds = TemplateQuestionsCategory::onlyTrashed()->pluck('id')->toArray();
        $safetyCategories = TemplateQuestionsCategory::where('safety_type', 1)->pluck('id')->toArray();
        $deletedCategoriesIds = [];
        if (!empty($safetyCategories)) {
            $deletedCategoriesIds = array_merge($deletedCategoryIds, $safetyCategories);
        }
        if (!empty($payperiods)) {
            foreach ($payperiods as $ky => $payperiod) {
                $payperiodIds[$ky] = $payperiod->id;
                $payperiodKeyArr[$payperiod->id] = $ky;
                $payperiodIdArray[$payperiod->id] = Carbon::parse($payperiod->end_date)->format('d-M-y');
                $headerArray[] = Carbon::parse($payperiod->start_date)->format('d-M-y');
            }
        }

        $result = [];
        $defaultColor = $this->customerMapRepository->getDefaultColor();
        foreach ($categories as $categoryId => $categoryName) {
            foreach ($payperiodIdArray as $payperiodId => $payperiodEndDate) {
                $result[$categoryId][$payperiodKeyArr[$payperiodId]]['count'] = 0;
                $result[$categoryId][$payperiodKeyArr[$payperiodId]]['total'] = 0;
                $result[$categoryId][$payperiodKeyArr[$payperiodId]]['average'] = 0;
                $result[$categoryId][$payperiodKeyArr[$payperiodId]]['color'] = $defaultColor;
            }
        }

        $records = $this->summaryDashboardRepository->fetchOperationsDashboardMatrix(SummaryDashboardController::OPERATIONS_DASHBOARD, $customerIds, $payperiodIds);
        if (!empty($records)) {
            $cnt = count($records);
            $colorObjCol = TemplateSettingRules::with('color')->get();
            foreach ($records as $record) {
                if (!in_array($record->category_id, $deletedCategoriesIds)) {
                    $metricKey = $payperiodKeyArr[$record->payperiod_id];

                    $result[$record->category_id][$metricKey]['count'] += 1;
                    $result[$record->category_id][$metricKey]['total'] += $record->value;
                    $result[$record->category_id][$metricKey]['average'] = round(($result[$record->category_id][$metricKey]['total'] / $result[$record->category_id][$metricKey]['count']), 4);
                    $colorObj = $colorObjCol->where('min_value', '<=', ($result[$record->category_id][$metricKey]['average']))
                        ->where('max_value', '>=', ($result[$record->category_id][$metricKey]['average']))
                        ->first();

                    $result[$record->category_id][$metricKey]['color'] = ($colorObj->color->color_class_name) ?? $defaultColor;
                }
            }
        }

        return response()->json([
            'success' => true,
            'categories' => $categories,
            'payperiods' => $headerArray,
            'result' => $result,
            'start_date' => $request->get('start_date'),
            'end_date' => $request->get('end_date'),
            'customer_ids' => $request->get('customer_ids')
        ]);
    }

    public function loadSafetyDashboardMatrix(Request $request)
    {
        // $a = $this->summaryDashboardRepository->croneOperationsDashboardMatrix();
        $endDate = Carbon::parse($request->get('end_date'))->endOfDay();
        $customerIds = $request->get('customer_ids');
        $payperiodIdArray = [];
        $payperiodIds = [];
        $headerArray = [];
        $payperiodKeyArr = [];
        $payperiods = $this->pay_period_repository->getLastNthPayPeriodsByEndDate($endDate);
        $categories = TemplateQuestionsCategory::where('safety_type', 1)->pluck('description', 'id')->toArray();
        $deletedCategoryIds = TemplateQuestionsCategory::onlyTrashed()->pluck('id')->toArray();
        $safetyCategories = TemplateQuestionsCategory::where('safety_type', 0)->pluck('id')->toArray();
        $deletedCategoriesIds = [];
        if (!empty($safetyCategories)) {
            $deletedCategoriesIds = array_merge($deletedCategoryIds, $safetyCategories);
        }
        if (!empty($payperiods)) {
            foreach ($payperiods as $ky => $payperiod) {
                $payperiodIds[] = $payperiod->id;
                $payperiodKeyArr[$payperiod->id] = $ky;
                $payperiodIdArray[$payperiod->id] = Carbon::parse($payperiod->end_date)->format('d-M-y');
                $headerArray[] = Carbon::parse($payperiod->start_date)->format('d-M-y');
            }
        }

        $result = [];
        $defaultColor = $this->customerMapRepository->getDefaultColor();
        foreach ($categories as $categoryId => $categoryName) {
            foreach ($payperiodIdArray as $payperiodId => $payperiodEndDate) {
                $result[$categoryId][$payperiodKeyArr[$payperiodId]]['count'] = 0;
                $result[$categoryId][$payperiodKeyArr[$payperiodId]]['total'] = 0;
                $result[$categoryId][$payperiodKeyArr[$payperiodId]]['average'] = 0;
                $result[$categoryId][$payperiodKeyArr[$payperiodId]]['color'] = $defaultColor;
            }
        }

        $records = $this->summaryDashboardRepository->fetchOperationsDashboardMatrix(SummaryDashboardController::OPERATIONS_DASHBOARD, $customerIds, $payperiodIds);
        if (!empty($records)) {
            $cnt = count($records);
            foreach ($records as $record) {
                if (!in_array($record->category_id, $deletedCategoriesIds)) {
                    $metricKey = $payperiodKeyArr[$record->payperiod_id];

                    $result[$record->category_id][$metricKey]['count'] += 1;
                    $result[$record->category_id][$metricKey]['total'] += $record->value;
                    $result[$record->category_id][$metricKey]['average'] = round(($result[$record->category_id][$metricKey]['total'] / $result[$record->category_id][$metricKey]['count']), 4);
                    $colorObj = TemplateSettingRules::with('color')
                        ->where('min_value', '<=', ($result[$record->category_id][$metricKey]['average']))
                        ->where('max_value', '>=', ($result[$record->category_id][$metricKey]['average']))
                        ->first();

                    $result[$record->category_id][$metricKey]['color'] = ($colorObj->color->color_class_name) ?? $defaultColor;
                }
            }
        }
        return response()->json([
            'success' => true,
            'categories' => $categories,
            'payperiods' => $headerArray,
            'result' => $result,
            'start_date' => $request->get('start_date'),
            'end_date' => $request->get('end_date'),
            'customer_ids' => $request->get('customer_ids')
        ]);
    }

    public function kpiTileBlocks(Request $request)
    {
        $kpiKeyArray = [
            SummaryDashboardController::SITE_METRIC_KPID => 'site_metric',
            SummaryDashboardController::TRAINING_COMPLIANCE_KPID => 'training_compliance',
            SummaryDashboardController::PERFORMANCE_MGMT_KPID => 'performance_mgmt',
        ];
        $result = array_fill_keys($kpiKeyArray, [
            'average' => 0,
            'color' => '',
        ]);
        $startDate = Carbon::parse($request->get('start_date'))->startOfDay();
        $endDate = Carbon::parse($request->get('end_date'))->endOfDay();
        $customerIds = $request->get('customer_ids');

        $kpiRecords = $this->summaryDashboardRepository->getKpiDataByParams($startDate, $endDate, $customerIds);

        $siteMetricAverageValue = ($kpiRecords->where('kpid', SummaryDashboardController::SITE_METRIC_KPID)->average('value'));
        $result['site_metric']['average'] = ($siteMetricAverageValue > 0) ? number_format($siteMetricAverageValue, 2, '.', '') : 0;

        $trainingComplianceValue = round($kpiRecords->where('kpid', SummaryDashboardController::TRAINING_COMPLIANCE_KPID)->average('value'));
        $result['training_compliance']['average'] = ($trainingComplianceValue > 0) ? $trainingComplianceValue : 0;

        // $perfomanceMgmtValue = ($kpiRecords->where('kpid', SummaryDashboardController::PERFORMANCE_MGMT_KPID)->average('value'));

        $res = $this->kpiAnalyticsRepository->getCustomerRecordsByFreq('custom', $customerIds, $startDate, $endDate);
        $perfAvg = $res['average'];
        $result['performance_mgmt']['average'] = ($perfAvg > 0) ? number_format($perfAvg, 2, '.', '') : 0;

        $configurations = SummaryDashboardConfiguration::where('type', SummaryDashboardController::SUMMARY_DASHBOARD_TRAINING_COMPLIANCE)
            ->where('value', '>=', $result['training_compliance']['average'])
            ->orderBy('value', 'ASC')
            ->first();
        if (!empty($configurations)) {
            $result['training_compliance']['color'] = !empty($configurations) ? $configurations->color : '';
        }

        //site metric color
        $result['site_metric']['color'] = $this->customerMapRepository->getDefaultColor();
        if (array_key_exists('site_metric', $result) && isset($result['site_metric']['average'])) {
            $colorObj = TemplateSettingRules::with('color')
                ->where('min_value', '<=', ($result['site_metric']['average']))
                ->where('max_value', '>=', ($result['site_metric']['average']))
                ->first();

            if (!empty($colorObj)) {
                $result['site_metric']['color'] = ($colorObj->color->color_class_name);
            }
        }

        //guard perfomance color
        $result['performance_mgmt']['color'] = 'red';
        if (array_key_exists('performance_mgmt', $result) && isset($result['performance_mgmt']['average'])) {
            if ($result['performance_mgmt']['average'] <= 2) {
                $result['performance_mgmt']['color'] = 'red';
            } else if ($result['performance_mgmt']['average'] <= 3.5) {
                $result['performance_mgmt']['color'] = 'yellow';
            } else if ($result['performance_mgmt']['average'] <= 4.5) {
                $result['performance_mgmt']['color'] = 'green';
            } else {
                $result['performance_mgmt']['color'] = 'darkgreen';
            }
        }

        $result['client_concern'] = $this->summaryDashboardRepository->fetchClientConcernCount($customerIds, $startDate, $endDate);
        $result['incidents'] = $this->summaryDashboardRepository->fetchIncidentsCount($customerIds, $startDate, $endDate);
        return response()->json([
            'success' => true,
            'data' => $result,
            'start_date' => $request->get('start_date'),
            'end_date' => $request->get('end_date'),
            'customer_ids' => $request->get('customer_ids')
        ]);
    }

    public function summaryTileBlocks(Request $request)
    {
        //site turn over
        $keyArray = [
            SummaryDashboardController::SITE_TURN_OVER => 'site_turn_over',
        ];
        $result = array_fill_keys($keyArray, [
            'total_value' => 0,
            'count' => 0,
            'average' => 0,
        ]);
        $startDate = Carbon::parse($request->get('start_date'))->startOfDay();
        $endDate = Carbon::parse($request->get('end_date'))->endOfDay();
        $customerIds = $request->get('customer_ids');
        $siteTurnOverValue = $this->summaryDashboardRepository->fetchSummaryDashboardData([
            SummaryDashboardController::SITE_TURN_OVER,
        ], $startDate, $endDate, $customerIds, 'view_all_exit_interview')->average('value');
        $result['site_turn_over']['average'] = ($siteTurnOverValue > 0) ? number_format($siteTurnOverValue, 2, '.', '') : 0;

        $configurations = SummaryDashboardConfiguration::where('type', SummaryDashboardController::SUMMARY_DASHBOARD_SITE_TURN_OVER)
            ->where('value', '>=', $result['site_turn_over']['average'])
            ->orderBy('value', 'ASC')
            ->first();
        if (!empty($configurations)) {
            $result['site_turn_over']['color'] = !empty($configurations) ? $configurations->color : '';
        }

        //Recruitment Tickets
        $result['job_tickets'] = $this->summaryDashboardRepository->fetchJobTicketCount($customerIds, $startDate, $endDate);

        //guard tour compliance
        $result['guard_tour_compliance'] = ['total_value' => 0, 'count' => 0, 'average' => 0, 'color' => ''];
        $qrValue = $this->summaryDashboardRepository->fetchGuardTourCompliance($customerIds, $startDate, $endDate)->average('value');

        $average = round($qrValue);
        $average = ($average > 0) ? $average : 0;
        $qrPatrolSettings = QrPatrolSetting::first();
        if (!empty($qrPatrolSettings)) {
            if ($average < $qrPatrolSettings->critical_level_percentage) {
                $result['guard_tour_compliance']['color'] = 'red';
            } elseif ($average >= $qrPatrolSettings->acceptable_level_percentage) {
                $result['guard_tour_compliance']['color'] = 'green';
            } else {
                $result['guard_tour_compliance']['color'] = 'yellow';
            }
        }
        $result['guard_tour_compliance']['average'] = $average;

        //guard tour compliance color
        $configurations = SummaryDashboardConfiguration::where('type', SummaryDashboardController::SUMMARY_DASHBOARD_GUARD_PERFORMANCE)
            ->where('value', '>=', $result['guard_tour_compliance']['average'])
            ->orderBy('value', 'ASC')
            ->first();
        if (!empty($configurations)) {
            $result['guard_tour_compliance']['color'] = !empty($configurations) ? $configurations->color : '';
        }

        //schedule infraction
        if (empty($customerIds)) {
            $customerIds = $this->schedulingRepository->getAllocatedCustomerIds();
        }

        $result['schedule_infraction'] = $this->summaryDashboardRepository->fetchSummaryDashboardData([
            SummaryDashboardController::SCHEDULE_INFRACTION,
        ], $startDate, $endDate, $customerIds)->sum('value');
        return response()->json([
            'success' => true,
            'data' => $result,
            'start_date' => $request->get('start_date'),
            'end_date' => $request->get('end_date'),
            'customer_ids' => $request->get('customer_ids')
        ]);
    }

    public function loadBillingWorkHourDetails(Request $request)
    {
        $startDate = Carbon::parse($request->get('start_date'))->startOfDay();
        $endDate = Carbon::parse($request->get('end_date'))->endOfDay();
        $customerIds = $request->get('customer_ids');
        $data = $this->summaryDashboardRepository->getEmployeeTotalWorkHours($customerIds, $startDate, $endDate);
        return response()->json([
            'success' => true,
            'data' => $data,
            'start_date' => $request->get('start_date'),
            'end_date' => $request->get('end_date'),
            'customer_ids' => $request->get('customer_ids')
        ]);
    }

    //cron for site turn over
    public function croneSiteTurnOver()
    {
        $status = $this->summaryDashboardRepository->croneSiteTurnOver();
        return response()->json([
            'success' => $status,
        ]);
    }

    //crone for operations dashboard
    public function croneOperationsDashboardMatrix()
    {
        $status = $this->summaryDashboardRepository->croneOperationsDashboardMatrix();
        return response()->json([
            'success' => $status,
        ]);
    }

    //crone for Total Work Hours Vs Earned Billings
    public function croneTotalWorkHoursVsEarnedBilling()
    {
        $status = $this->summaryDashboardRepository->croneTotalWorkHoursVsEarnedBilling();
        return response()->json([
            'success' => $status,
        ]);
    }

    //Inner pages
    public function guardPerfomanceInner()
    {
        $freqs = KpiFrequencyMap::getFrequencyList();

        return view('summary-dashboard.guard-perfomance', [
            'freqs' => $freqs
        ]);
    }

    public function guardPerfomanceInfo(Request $request)
    {
        $freq = $request->input('frequency');
        $cIds = $request->input('cIds');
        $from = $request->input('from');
        $to = $request->input('to');

        $datas = $this->kpiAnalyticsRepository->getCustomerRecordsByFreq($freq, $cIds, $from, $to);
        return response()->json([
            'success' => true,
            'data' => $datas
        ]);
    }

    public function guardPerfomanceDetails(Request $request)
    {
        $res = $this->summaryDashboardRepository->guardPerfomanceCluster($request);

        return datatables()->of($res['list'])
            ->with([
                'custAgg' => $res['custAgg']
            ])
            ->addIndexColumn()
            ->toJson();
    }

    public function trainingComplianceInner(Request $request)
    {
        return view('summary-dashboard.training-compliance-inner');
    }
}
