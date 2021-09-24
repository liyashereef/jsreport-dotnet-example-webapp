<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Repositories\LandingWidgetRepository;
use App\Services\HelperService;
use Illuminate\Http\Request;
use Modules\Admin\Repositories\CustomerEmployeeAllocationRepository;
use Modules\Admin\Repositories\CustomerRepository;
use Modules\Admin\Repositories\IncidentPriorityLookupRepository;
use Modules\Admin\Repositories\PayPeriodRepository;
use Modules\Admin\Repositories\UserRepository;
use Modules\Client\Repositories\ClientRepository;
use Modules\Client\Repositories\ClientSurveyRepository;
use Modules\FMDashboard\Repositories\DashboardWidgetRepository;
use Modules\Supervisorpanel\Repositories\CustomerReportRepository;
use Modules\Timetracker\Repositories\CustomerQrcodeRepository;
use Modules\Timetracker\Repositories\QrcodeLocationRepository;

class LandingWidgetController extends Controller
{

    protected $clientSurveyRepository, $payPeriodRepository, $customerEmployeeAllocationRepository;
    protected $userRepository, $qrCodeRepository, $landingWidgetRepository, $customerReportRepository, $customerRepository, $incidentPriorityLookupRepository, $dashboardWidgetRepository, $customerQrCodeRepository;

    public function __construct(
        LandingWidgetRepository $landingWidgetRepository,
        CustomerReportRepository $customerReportRepository,
        CustomerRepository $customerRepository,
        IncidentPriorityLookupRepository $incidentPriorityLookupRepository,
        DashboardWidgetRepository $dashboardWidgetRepository,
        ClientSurveyRepository $clientSurveyRepository,
        CustomerQrcodeRepository $customerQrCodeRepository,
        UserRepository $userRepository,
        ClientRepository $clientRepository,
        QrcodeLocationRepository $qrCodeRepository,
        PayPeriodRepository $payPeriodRepository,
        CustomerEmployeeAllocationRepository $customerEmployeeAllocationRepository
    ) {

        $this->helperService = new HelperService();
        $this->landingWidgetRepository = $landingWidgetRepository;
        $this->customerReportRepository = $customerReportRepository;
        $this->customerRepository = $customerRepository;
        $this->incidentPriorityLookupRepository = $incidentPriorityLookupRepository;
        $this->dashboardWidgetRepository = $dashboardWidgetRepository;

        $this->clientSurveyRepository = $clientSurveyRepository;
        $this->customerQrCodeRepository = $customerQrCodeRepository;
        $this->qrCodeRepository = $qrCodeRepository;
        $this->userRepository = $userRepository;
        $this->clientRepository = $clientRepository;
        $this->payPeriodRepository = $payPeriodRepository;
        $this->customerEmployeeAllocationRepository = $customerEmployeeAllocationRepository;
    }

    /**
     * fetch site summary widget data.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getSiteSummary(Request $request)
    {
        $hpw = null;
        $customerId = $request->get('customer-id');
        $payPeriodId = $request->get('payperiod-id');
        $customerLists = $this->customerRepository->getCustomerWithMangers($customerId);

        //Selecting required fields from customerList
        $customerLists = collect($customerLists);
        $customer = $customerLists->map(function ($customerList) {
            return collect($customerList)
                ->only([
                    'id', 'project_number', 'client_name', 'billing_address',
                    'contact_person_name', 'contact_person_phone', 'contact_person_email_id',
                    'first_name', 'last_name', 'email', 'phone',
                ])
                ->all();
        })->toArray();

        if (!empty($customer) && isset($customer['details'])) {
            $hpw = $this->landingWidgetRepository->getSiteSummaryHoursPerWeekDetails($customer['details']['id'], $payPeriodId);
        }
        return response()->json(array('type' => 'json', 'widgetTag' => 'widget-site-summary', 'data' => array('customer' => $customer, 'hpw' => $hpw, 'inner_page_url' => route('customer.edit', $customerId))));
    }

    public function getTrendAnalysis(Request $request)
    {
        $trend = $this->landingWidgetRepository->getTrendAnalysisDetails($request);
        return response()
            ->json(array(
                'type' => 'json',
                'widgetTag' => 'widget-trend-analysis',
                'data' => array('chartDetails' => $trend),
            ));
    }

    public function getSiteMatrix(Request $request)
    {
        $customerId = $request->get('customer-id');
        $data = $this->landingWidgetRepository->getSiteMatrixDetails($customerId);
        return response()->json(array('type' => 'json', 'widgetTag' => 'widget-site-metric', 'data' => array('site_meric_details' => $data, 'inner_page_url' => route('operational-dashboard'))));
    }

    /**
     * fetch site details widget data.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getSiteDetails(Request $request)
    {
        $customerId = $request->get('customer-id');
        $selectedTemplateCategory = $request->get('site-categories');
        $templateCategoryList = $this->customerReportRepository->getCurrentTemplateCategories();
        $templateCategoryList = collect($templateCategoryList);
        $templateCategories = $templateCategoryList->map(function ($templateCategoryList) {
            return collect($templateCategoryList)->only(['id', 'description'])->all();
        })->toArray();
        $data = $this->landingWidgetRepository->getSiteDetailsByQuestionCategory($customerId, $selectedTemplateCategory);
        return response()->json(array('type' => 'json', 'widgetTag' => 'widget-site-details', 'data' => array('site_categories' => $templateCategories, 'site_details' => $data, 'inner_page_url' => route('customer.details', $customerId))));
    }

    public function getScheduleCompliance(Request $request)
    {

        $employeedata = $this->landingWidgetRepository->getScheduletimesheetcomparison($request);
        $returndata["type"] = "json";
        $returndata["widgetTag"] = "widget-schedule-compliance";
        $returndata["data"] = $employeedata;
        return response()->json($returndata);
    }

    public function getIncidentResponseCompliance(Request $request)
    {
        $priorityList = $this->incidentPriorityLookupRepository->getAll();
        $filters = [];
        if (!empty($priorityList) && isset($priorityList[0]) && (!$request->has("selected-priority"))) {
            $filters['selected-priority'] = (string) $priorityList[0]->id;
            $request->merge([
                'selected-priority' => $priorityList[0]->id,
            ]);
        }
        $result = $this->landingWidgetRepository->getIncidentCompliance($request);
        return response()->json(array('type' => 'json', 'widgetTag' => 'widget-incident-response-compliance', 'data' => array('compliance' => $result, 'priorities' => $priorityList), 'filters' => $filters));
    }

    public function getElavatorEntrapmentResponce(Request $request)
    {
        $kpi = $this->landingWidgetRepository->getIncidentKpi($request);
        $employeedata = [];
        $returndata["type"] = "json";
        $returndata["widgetTag"] = "widget-incident-analytics";
        $returndata["data"] = array('kpi' => $kpi);
        return response()->json($returndata);
    }

    public function getIncidentResponceKpi(Request $request)
    {
        $kpi = $this->landingWidgetRepository->getIncidentKpi($request);
        return response()->json(array('type' => 'json', 'widgetTag' => 'widget-response-kpis', 'data' => array('kpi' => $kpi)));
    }

    public function getShiftModulePostOrder(Request $request)
    {
        $customerId = $request->get('customer-id');
        $users = $this->customerEmployeeAllocationRepository->allocationList($customerId);
        $post_orders = $this->landingWidgetRepository->getAllPostOrders($request);
        return response()->json(array('type' => 'json', 'widgetTag' => 'widget-shift-journal-summary', 'data' => array('post_orders' => $post_orders, 'users' => $users)));
    }

    public function getMotionSensorDetails(Request $request)
    {
        $motion_sensors = $this->landingWidgetRepository->getAllMotionSensors($request);
        return response()->json(array('type' => 'json', 'widgetTag' => 'widget-motion-sensor', 'data' => array('motion_sensor' => $motion_sensors)));
    }

    /**
     * To get training widgets permanent and spares
     * @param  Request $request, $mandatory, $spares (spare pool guards)
     * @return Response json
     */
    public function getTrainingWidget(Request $request, $mandatory, $spares)
    {
        $tableDetails = $this->landingWidgetRepository->trainingWidget($request, $mandatory, $spares);

        //to get training widget spares
        if ($spares) {
            return response()
                ->json(array(
                    'type' => 'json',
                    'widgetTag' => 'widget-mandatory-training-spares',
                    'data' => array(
                        'tableDetails' => $tableDetails,
                        'inner_page_url' => route('learningandtraining.dashboard')
                    ),
                ));
        } else {
            return response()
                ->json(array(
                    'type' => 'json',
                    'widgetTag' => 'widget-mandatory-training-full-time',
                    'data' => array(
                        'tableDetails' => $tableDetails,
                        'inner_page_url' => route('learningandtraining.dashboard')
                    ),
                ));
        }
    }

    /**
     * get Timesheet Reconciliation Data.
     * get position base data.
     * @param $customer_id.
     * @return json
     */
    public function getTimesheetReconciliation(Request $request)
    {
        $customerId = $request->get('customer-id');
        $payPeriodId = $request->get('payperiod_id');
        if (isset($payPeriodId) && !empty($payPeriodId)) {
            $payPeriodIds[] = $request->get('payperiod_id');
        } else {
            $payperiodObject = $this->payPeriodRepository->getCurrentPayperiod();
            $payPeriodIds[] = $payperiodObject ? $payperiodObject->id : null;
        }
        $payperiods = $this->payPeriodRepository->getLastNPayperiodWithCurrent(26);
        $resultArray = $this->dashboardWidgetRepository->setTimesheetReconciliationData(['customer_id' => $customerId, 'pay_periods' => $payPeriodIds], true);
        return response()->json(array('type' => 'json', 'widgetTag' => 'widget-timesheet-reconciliation', 'data' => array('result' => $resultArray, 'payperiods' => $payperiods)));
    }

    public function getClientSurvey(Request $request)
    {
        $data = $this->clientSurveyRepository->getSurveyData($request);
        return response()->json(array('type' => 'json', 'widgetTag' => 'widget-client-survey', 'data' => $data));
    }

    public function getClientSurveyAnalytics(Request $request)
    {
        $chartdata = $this->clientSurveyRepository
            ->getClientchartallcustomeranalytics($request);
        return response()->json(array('type' => 'json', 'widgetTag' => 'widget-client-survey-analytics', 'data' => $chartdata));
    }
    /**
     * get Qr Petrol data.
     * get position base data.
     * @param $customer_id, $employee_id.
     * @return json .
     */
    public function getQrPatrolDetails(Request $request)
    {
        $customerId = $request->get('customer-id');
        $employeeId = $request->get('employee-id');
        $users = $this->customerEmployeeAllocationRepository->allocationList($customerId);
        //cron job test
        // $resultArray = $this->customerQrCodeRepository->processQrPatrolWidgetEntries();
        $resultArray = $this->customerQrCodeRepository->getCustomerQrPatrolDetails($customerId, $employeeId);
        return response()->json(array('type' => 'json', 'widgetTag' => 'widget-qr-patrol', 'data' => array('qr_details' => $resultArray, 'users' => $users)));
    }

    public function getKpiAnalytics(Request $request)
    {
    }
}
