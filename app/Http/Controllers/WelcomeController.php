<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\HelperService;
use Auth;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Modules\Admin\Models\Customer;
use Modules\Admin\Repositories\CustomerEmployeeAllocationRepository;
use Modules\Admin\Repositories\CustomerRepository;
use Modules\Admin\Repositories\EmployeeAllocationRepository;
use Modules\Admin\Repositories\LandingPageRepository;
use Modules\Admin\Repositories\PayPeriodRepository;
use Modules\Admin\Repositories\ShiftModuleRepository;
use Modules\Admin\Repositories\SiteNotesRepository;
use Modules\Admin\Repositories\UserRepository;
use Modules\Client\Repositories\ClientRepository;
use Modules\Client\Repositories\ConcernRepository;
use Modules\Client\Repositories\VisitorLogRepository;
use Modules\Contracts\Repositories\PostOrderRepository;
use Modules\Employeescheduling\Repositories\SchedulingRepository;
use Modules\Hranalytics\Repositories\CandidateRepository;
use Modules\Hranalytics\Repositories\JobRepository;
use Modules\KeyManagement\Repositories\CustomerKeyDetailRepository;
use Modules\LearningAndTraining\Repositories\TrainingUserCourseAllocationRepository;
use Modules\Sensors\Repositories\SensorTriggerRepository;
use Modules\Supervisorpanel\Repositories\CustomerMapRepository;
use Modules\Supervisorpanel\Repositories\CustomerReportRepository;
use Modules\Supervisorpanel\Repositories\GuardTourRepository;
use Modules\Supervisorpanel\Repositories\IncidentReportRepository;
use Modules\Supervisorpanel\Repositories\ShiftJournalRepository;
use Modules\Timetracker\Repositories\EmployeeShiftRepository;
use Modules\Timetracker\Repositories\TimetrackerRepository;
use View;

class WelcomeController extends Controller
{
    const PAYPERIOD_PAST = 5;
    const PAYPERIOD_FUTURE = 6;

    protected $employeeAllocationRepository, $userRepository, $jobRepository, $customerEmployeeAllocationRepository, $shiftModuleRepository;
    protected $customer_report_repository, $concernRepository, $clientRepository;
    protected $customer_map_repository;
    protected $landingPageRepository;
    protected $timetrackerRepository;
    protected $payPeriodRepository;
    protected $incident_report_repository, $schedulingRepository;
    protected $siteNotesRepository, $postOrderRepository, $customerKeyDetailRepository, $sensorTriggerRepository, $customerRepository;
    protected $employeeShiftRepository;

    public function __construct(
        ClientRepository $clientRepository,
        ConcernRepository $concernRepository,
        SchedulingRepository $schedulingRepository,
        CustomerRepository $customerRepository,
        CustomerEmployeeAllocationRepository $customerEmployeeAllocationRepository,
        EmployeeAllocationRepository $employeeAllocationRepository,
        UserRepository $userRepository,
        JobRepository $jobRepository,
        CandidateRepository $candidateRepository,
        GuardTourRepository $guard_tour_repository,
        ShiftJournalRepository $shif_journal_repository,
        ShiftModuleRepository $shiftModuleRepository,
        CustomerReportRepository $customer_report_repository,
        CustomerMapRepository $customer_map_repository,
        LandingPageRepository $landingPageRepository,
        TimetrackerRepository $timetrackerRepository,
        PayPeriodRepository $payPeriodRepository,
        IncidentReportRepository $incident_report_repository,
        VisitorLogRepository $visitorLogRepository,
        SiteNotesRepository $siteNotesRepository,
        PostOrderRepository $postOrderRepository,
        CustomerKeyDetailRepository $customerKeyDetailRepository,
        SensorTriggerRepository $sensorTriggerRepository,
        EmployeeShiftRepository $employeeShiftRepository
    ) {
        $this->clientRepository = $clientRepository;
        $this->customerEmployeeAllocationRepository = $customerEmployeeAllocationRepository;
        $this->employeeAllocationRepository = $employeeAllocationRepository;
        $this->userRepository = $userRepository;
        $this->helperService = new HelperService();
        $this->jobRepository = $jobRepository;
        $this->candidateRepository = $candidateRepository;
        $this->guard_tour_repository = $guard_tour_repository;
        $this->shif_journal_repository = $shif_journal_repository;
        $this->shiftModuleRepository = $shiftModuleRepository;
        $this->customer_report_repository = $customer_report_repository;
        $this->customer_map_repository = $customer_map_repository;
        $this->landingPageRepository = $landingPageRepository;
        $this->timetrackerRepository = $timetrackerRepository;
        $this->payPeriodRepository = $payPeriodRepository;
        $this->incident_report_repository = $incident_report_repository;
        $this->visitorLogRepository = $visitorLogRepository;
        $this->siteNotesRepository = $siteNotesRepository;
        $this->user_courses = new TrainingUserCourseAllocationRepository();
        $this->postOrderRepository = $postOrderRepository;
        $this->customerKeyDetailRepository = $customerKeyDetailRepository;
        $this->sensorTriggerRepository = $sensorTriggerRepository;
        $this->schedulingRepository = $schedulingRepository;
        $this->customerRepository = $customerRepository;
        $this->employeeShiftRepository = $employeeShiftRepository;
        $this->concernRepository = $concernRepository;
    }

    public function index(Request $request)
    {
        if (\Auth::user()) {
            $summaryDashBoardCustomers = $request->customer_id;
            $selectedCustomerArray = [];
            if ($summaryDashBoardCustomers != "") {
                foreach (explode(",", $summaryDashBoardCustomers) as $key => $customerId) {
                    $selectedCustomerArray[] = $customerId;
                }
            }
            //reset dashboard filter session TODO check
            session()->put('customer_ids', []);
            $customers_list = $this->customerEmployeeAllocationRepository->getAllAllocatedCustomerId([\Auth::user()->id]);
            $selected_customer = 0;
            if ((count($customers_list) == 1)) {
                $selected_customer = $customers_list[0];
            }
            $customerIds = $this->customerEmployeeAllocationRepository->getAllocatedPermanentCustomers(\Auth::user());
            $customers = Customer::orderBy('client_name')->findMany($customerIds);
            $reloadExcludedWidget = [
                "site-status",
                "key-performance-indicators",
                "qr-patrol"
            ];
            return view('welcome', [
                'selected_customer' => $selected_customer,
                'customers' => $customers,
                "reloadExcludedWidget" => $reloadExcludedWidget,
                'selectedCustomerArray' => $selectedCustomerArray
            ]);
        }

        return view('index');
    }

    public function getDashboardTabs(Request $request)
    {
        try {
            $selectedCustomer = [];
            if (($request->get('customer_id') != "0") && ($request->get('customer_id') != 0) && ($request->get('customer_id') != null)) {
                if (is_array($request->get('customer_id'))) {
                    $selectedCustomer = $request->get('customer_id');
                } else {
                    $selectedCustomer[] = $request->get('customer_id');
                }
            }

            if ((count($selectedCustomer) == 1) && ($selectedCustomer != null)) {
                $tabDetails = $this->landingPageRepository->getTabsByCustomer($selectedCustomer)->get();
            } else {
                $tabDetails = $this->landingPageRepository->fetchLatestTabsByLayoutGroupping($selectedCustomer)->get();
            }

            $tabDetails = View::make('partials.welcome.tab_structure')->with(compact(['tabDetails', 'selectedCustomer']))->render();

            return response()->json([
                'html' => $tabDetails,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'html' => '<tbody><tr style="vertical-align: middle;" valign="middle"><th class="text-center" style="border:0px;text-align:center;vertical-align:middle;">' . (config('globals.landingPageExceptionCustomErrorMessageText')) . '</th></tr></tbody>',
            ]);
        }
    }

    public function getDashboardTabDetails(Request $request)
    {
        try {
            $tabId = $request->get('tab_id');
            $parentDivWidth = $request->get('parentDivWidth');
            $parentDivHeight = $request->get('parentDivHeight');

            $tabInfo = $this->landingPageRepository->getTabDetailsByTabId($tabId);
            $tabUid = ('tabuid_' . $tabInfo['id']);

            $moduleHierarchy = $this->landingPageRepository->fetchModuleLayoutHierarchyByTab($tabId, $parentDivWidth, $parentDivHeight);
            $rowIndex = 0;
            $columnIndex = 0;
            $layoutId = 0;
            $html = '<table id="' . $tabUid . '" class="dashboard-tables" style="table-layout:fixed;margin-bottom:0px !important;width:100% !important;height:100% !important;"><thead>';
            foreach ($moduleHierarchy as $key => $value) {
                $selectedRow = $value['row_index'];
                $apiUrl = $value['api_url'];
                $icon = $value['icon'];
                $detailUrl = ($value['detail_url'] != '') ? $value['detail_url'] : '#';
                $apiType = $value['api_type'];
                $model = $value['model'];
                $id = $value['id'];
                $systemFieldsArr = $value['system_fields_arr'];
                $displayFieldsArr = $value['display_fields_arr'];
                $customerId = $value['customer_id'];
                $viewPermissionLabel = $value['view_permission'];
                $fieldsOrder = $value['fields_order'];
                $sortOrder = $value['sort_order'];
                $layoutId = $value['layoutId'];
                $category = $value['category'];

                $havePermission = 0;
                $widgetCssClass = '';
                $allowedWidgetCss = 'js-widget ' . $category;
                $viewPermissionsCount = 0;
                $widgetContent = '<tbody><tr><th class="custom-dashboard-th" style="border:0px;text-align:center;vertical-align:middle;font-weight:normal;">' . (config('globals.landingPageInvalidPermissionDisplayMessageText')) . '</th></tr></tbody>';
                if (!empty($viewPermissionLabel)) {
                    $viewPermissions = unserialize($viewPermissionLabel);
                    if (isset($viewPermissions['landing_page_dashboard']) && !empty($viewPermissions['landing_page_dashboard'])) {
                        $landingPagePermissions = $viewPermissions['landing_page_dashboard'];
                        $viewPermissions = explode(",", $landingPagePermissions);
                        $viewPermissionsCount = count($viewPermissions);
                        foreach ($viewPermissions as $viewPermission) {
                            $trimmedPermission = trim($viewPermission, " ");
                            if (Auth::user()->can($trimmedPermission)) {
                                $havePermission++;
                            }
                        }
                    }

                    if ($havePermission == $viewPermissionsCount) {
                        $widgetCssClass = 'widget-tables';
                        $widgetContent = '<tbody><tr style="vertical-align: middle;" valign="middle"><th class="text-center" style="vertical-align: middle;">P&nbsp;L&nbsp;E&nbsp;A&nbsp;S&nbsp;E&nbsp;&nbsp;&nbsp;&nbsp;W&nbsp;A&nbsp;I&nbsp;T&nbsp;.&nbsp;.&nbsp;.</th></tr></tbody>';
                    } else {
                        $allowedWidgetCss = '';
                    }
                } elseif ($model == "ShiftModule" && Auth::user()->can("view_shift_module_widgets")) {
                    $widgetCssClass = 'widget-tables';
                    $widgetContent = '<tbody><tr style="vertical-align: middle;" valign="middle"><th class="text-center" style="vertical-align: middle;">P&nbsp;L&nbsp;E&nbsp;A&nbsp;S&nbsp;E&nbsp;&nbsp;&nbsp;&nbsp;W&nbsp;A&nbsp;I&nbsp;T&nbsp;.&nbsp;.&nbsp;.</th></tr></tbody>';
                } elseif ($model != "ShiftModule") {
                    $widgetCssClass = 'widget-tables';
                    $widgetContent = '<tbody><tr style="vertical-align: middle;" valign="middle"><th class="text-center" style="vertical-align: middle;">P&nbsp;L&nbsp;E&nbsp;A&nbsp;S&nbsp;E&nbsp;&nbsp;&nbsp;&nbsp;W&nbsp;A&nbsp;I&nbsp;T&nbsp;.&nbsp;.&nbsp;.</th></tr></tbody>';
                } else {
                    $allowedWidgetCss = '';
                }

                $displayCss = 'display:inline-block;';
                if ($layoutId == 4) {
                    $displayCss = 'display:table-cell;';
                }

                if ($selectedRow != $rowIndex && $rowIndex != 0) {
                    if ($layoutId == 4) {
                        $html .= '</tr>';
                    } else {
                        $html .= '</table></td></tr>';
                    }
                }

                if ($selectedRow != $rowIndex) {
                    if ($layoutId == 4) {
                        $html .= '<tr>';
                    } else {
                        $html .= '<tr><td><table class="table_row" style="width: 100%;table-layout: fixed !important;">';
                    }
                    $rowIndex = $selectedRow;
                }

                $widgetStyle = '';
                $rightPadding = 'padding-right: 0.50em !important;padding-left: 0.15em !important;padding-top: 0.10em !important;padding-bottom: 0.15em !important;';
                if ($model == "widget") {
                    $widgetStyle = 'table-layout: fixed;';
                    $rightPadding = '';
                }

                $moduleName = $value['module_name'];
                if (!empty($moduleName)) {
                    $cardHeightStyle = ($value['no_of_rows'] = 1) ? 'height:' . $value['height'] . ' !important;' : '';
                    $changedModelName = str_replace('/', '', str_replace(' ', '-', 'table-' . strtolower($value['tab_id'] . '-' . $moduleName)));
                    $spanId = str_replace('/', '', str_replace(' ', '-', 'span-' . $value['tab_id'] . '-' . strtolower($moduleName)));
                    $superParentTh = 'dashboard_th_' . $value['tab_id'] . '_' . $id;
                    $widgetName = str_replace('/', '', str_replace(' ', '-', 'widget-' . strtolower($moduleName)));
                    $widgetTitleCssClass = $widgetName . '-tittle';
                    $attributeArray = array(
                        'dataTargetId' => $changedModelName,
                        'hiddenFields' => [],
                        'sortOrder' => $sortOrder,
                        'sortField' => $fieldsOrder,
                        'spanId' => $spanId,
                        'customerId' => $customerId,
                        'dataApiType' => $apiType,
                        'dataDisplayFields' => $displayFieldsArr,
                        'dataSystemFields' => $systemFieldsArr,
                        'dataModuleId' => $id,
                        'dataModel' => $model,
                        'dataApiUrl' => $apiUrl,
                        'dataDetailUrl' => $detailUrl,
                    );
                    $attributeJsonArray = json_encode($attributeArray);
                    $tableAttributes = base64_encode($attributeJsonArray);

                    if ($moduleName == "Scheduling") {
                        if ((!Auth::user()->can('view_all_employee_schedule_requests')) && (!Auth::user()->can('view_allocated_employee_schedule_requests'))) {
                            $widgetContent = '<tbody><tr><th class="custom-dashboard-th" style="border:0px;text-align:center;vertical-align:middle;font-weight:normal;">' . (config('globals.landingPageInvalidPermissionDisplayMessageText')) . '</th></tr></tbody>';
                        }
                        $scrollingStyles = 'overflow-x:hidden !important;overflow-y:hidden !important;';
                    } elseif ($moduleName == 'Motion Sensor') {
                        $scrollingStyles = 'overflow-x:hidden !important;overflow-y:hidden !important;';
                    } else {
                        $scrollingStyles = 'overflow-x:scroll !important;';
                    }

                    $html .= '<td class="custom-dashboard-th" id="' . $superParentTh . '" data-width="' . $value['width'] . '" style="' . $displayCss . 'width:' . $value['width'] . ' !important;padding-right: 10px !important;vertical-align:middle !important;position:relative;border: none !important;padding-top: 10px !important;padding-left: 0px !important;" rowspan="' . $value['rowspan'] . '" colspan="' . $value['colspan'] . '">'
                        . '<div class="card-table ' . $allowedWidgetCss . ' ' . $widgetName . ' ' . $changedModelName . '" data-attr="' . $tableAttributes . '" style="padding-left: 0px !important;padding-right: 0px !important;">'
                        . '<div class="card-header">' . $icon
                        . '<span class="pl-2 widget-title-selector" style="white-space: nowrap;" id="h_span_' . $spanId . '">'
                        . '<a class="inner-page-nav ' . $widgetTitleCssClass . '" id="heading-' . $spanId . '" href="' . $detailUrl . '">' . $moduleName . '</a>'
                        . '</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="span-site-schedule filter-content" id="' . $spanId . '" style="text-align:center;width: 40%;"></span>'
                        . '</div>'
                        . '<div id="tbl_responsive_' . $spanId . '" data-parent-tbl="' . $tabInfo['id'] . '" class="table-responsive widget-div dasboard-card-body" style="' . $rightPadding . $scrollingStyles . 'flex: 1 1 auto !important;' . $cardHeightStyle . '">'
                        . '<table id="' . $changedModelName . '" class="table js-customer-filter auto-refresh dataTable no-footer ' . $widgetCssClass . ' ' . $model . '" style="' . $widgetStyle . 'width:100% !important;height:100% !important;margin-top: 0px !important;margin-bottom: 0px !important;">' . $widgetContent
                        . '</table>'
                        . '</div>'
                        . '</div>'
                        . '</td>';
                }
            }
            if ($layoutId == 4) {
                $html .= '</tr></thead></table>';
            } else {
                $html .= '</table></td></tr></thead></table>';
            }

            $tabView = View::make('partials.welcome.tab_view')->with(compact('html'))->render();

            return response()->json([
                'html' => $tabView,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'html' => '<table><tbody><tr style="vertical-align: middle;" valign="middle"><th class="text-center" style="border:0px;text-align:center;vertical-align:middle;">' . (config('globals.landingPageExceptionCustomErrorMessageText')) . '</th></tr></tbody></table>',
            ]);
        }
    }

    public function getMyTeam(Request $request)
    {
        try {
            $sessionCheck = $this->searchBoxVsSessionValueRecheck($request->input('customer-search'));
            $logged_in_user = \Auth::user();
            $role = $logged_in_user->roles[0]->name;
            // To get searched customer id
            $customer_ids = $this->helperService->getCustomerIds();

            if (!empty($customer_ids)) {
                // List of all employees working for the client
                $records = $this->userRepository->getUserList(
                    true,
                    $role = [],
                    $supervisor_id = null,
                    $role_except = null,
                    $customer_session = true
                );
                $recordUserId = data_get($records, '*.id');
                //List of not allocated but active employees working for the client
                $activeShiftEmployee = $this->employeeShiftRepository
                    ->getActiveShiftEmployes($customer_id = $customer_ids);
                //Combined list of employees
                $combinedUserId = array_unique(array_merge($recordUserId, $activeShiftEmployee));
                $records = $this->userRepository->getUserList(
                    $active = null,
                    $permissionsOrRolesInclude = null,
                    $supervisor_id = null,
                    $permissionsOrRolesExclude = null,
                    $customer_session = false,
                    $isPermissionWise = true,
                    $userId = $combinedUserId
                );
            } else {
                if (\Auth::user()->hasAnyPermission(['admin', 'super_admin'])) {
                    $records = $this->userRepository->getUserList(
                        true,
                        $role = [],
                        $supervisor_id = null,
                        $role_except = null,
                        $customer_session = true
                    );
                } else if (\Auth::user()->hasAnyPermission(['hr_representative', 'hr_manager'])) {
                    $records = $this->userRepository->getUserList(
                        true,
                        $role = ['area_manager', 'supervisor'],
                        $supervisor_id = null,
                        $role_except = null,
                        $customer_session = true
                    );
                } else if (\Auth::user()->hasAnyPermission(['cfo'])) {
                    $records = $this->userRepository->getUserList(
                        true,
                        $role = ['area_manager'],
                        $supervisor_id = null,
                        $role_except = null,
                        $customer_session = true
                    );
                } else if (\Auth::user()->hasAnyPermission(['duty_officer'])) {
                    $records = $this->userRepository->getUserList(
                        true,
                        $role = ['supervisor', 'guard'],
                        $supervisor_id = null,
                        $role_except = null,
                        $customer_session = true
                    );
                } else if (\Auth::user()->hasAnyPermission(['area_manager'])) {
                    $superviosr_id = $logged_in_user->id;
                    $records = $this->employeeAllocationRepository->getEmployeeAssigned(
                        $superviosr_id,
                        ['supervisor', 'guard'],
                        $query_object = false,
                        $customer_session = true,
                        $isPermissionWise = true,
                        $isActiveUsers = true
                    );
                } else {
                    $superviosr_id = $logged_in_user->id;
                    $records = $this->employeeAllocationRepository->getEmployeeAssigned(
                        $superviosr_id,
                        null,
                        $query_object = false,
                        $customer_session = true,
                        $isPermissionWise = true,
                        $isActiveUsers = true
                    );
                }
            }

            $dataArr = [];
            foreach ($records as $ky => $record) {
                $user_arr = [];
                $status = 3;
                $available = $meeting = $unavailable = 0;
                if ((class_basename($record) == "User") && ($record->employee_shift_payperiods != null)) {
                    $user_arr = $record->employee_shift_payperiods;
                } else if ((class_basename($record) != "User") && ($record->user != null)) {
                    $user_arr = $record->user->employee_shift_payperiods;
                } else {
                    $user_arr = [];
                }

                if (!empty($user_arr)) {
                    foreach ($user_arr as $val) {
                        if ($val->availableShift != null) {
                            if ($val->availableShift->live_status_id == 1) {
                                $available = 1;
                            }
                            if ($val->availableShift->live_status_id == 2) {
                                $meeting = 1;
                            }
                            if ($val->availableShift->live_status_id == 3) {
                                $unavailable = 1;
                            }
                        }
                    }
                }

                if ($available == 1) {
                    $status = 1;
                } else if ($meeting == 1) {
                    $status = 2;
                } else {
                    $status = 3;
                }

                $dataArr[$ky]['live_status_color'] = $status;

                if ($record->user != null) {
                    if ($record->user->email != null) {
                        $dataArr[$ky]['email'] = $record->user->email;
                    } else {
                        $dataArr[$ky]['email'] = '--';
                    }

                    if ($record->user->last_name != null) {
                        $dataArr[$ky]['name'] = $record->user->first_name . ' ' . $record->user->last_name;
                    } else {
                        $dataArr[$ky]['name'] = $record->user->first_name;
                    }

                    if ($record->user->employee != null) {
                        $dataArr[$ky]['phone'] = $record->user->employee->phone_ext != null
                            ? ($record->user->employee->phone . ' x' . $record->user->employee->phone_ext)
                            : $record->user->employee->phone;
                        $dataArr[$ky]['employee_no'] = $record->user->employee->employee_no != null
                            ? $record->user->employee->employee_no
                            : '';
                        $dataArr[$ky]['work_type'] = $record->user->employee->work_type_id != null
                            ? ($record->user->employee->work_type != null ? $record->user->employee->work_type->type : '')
                            : '';
                        $dataArr[$ky]['employee_dob'] = $record->user->employee->employee_no != null
                            ? $record->user->employee->employee_dob
                            : '';
                        $dataArr[$ky]['employee_work_email'] = $record->user->employee->employee_work_email != null
                            ? $record->user->employee->employee_work_email
                            : '';

                        $rolesObject = $record->user->roles;
                        $roles_arr = [];
                        $roles = '';
                        if (!empty($rolesObject)) {
                            foreach ($rolesObject as $role) {
                                $roles_arr[] = ucwords(strtolower(str_replace("_", " ", $role->name)));
                            }
                            $roles = implode(",", $roles_arr);
                        }
                        $dataArr[$ky]['role'] = $roles;
                    } else {
                        $dataArr[$ky]['phone'] = '--';
                        $dataArr[$ky]['employee_no'] = '--';
                        $dataArr[$ky]['work_type'] = '--';
                        $dataArr[$ky]['employee_dob'] = '--';
                        $dataArr[$ky]['employee_work_email'] = '--';
                        $dataArr[$ky]['role'] = '--';
                    }
                } else {
                    if ($record->email != null) {
                        $dataArr[$ky]['email'] = $record->email;
                    } else {
                        $dataArr[$ky]['email'] = '--';
                    }

                    if ($record->last_name != null) {
                        $dataArr[$ky]['name'] = $record->first_name . ' ' . $record->last_name;
                    } elseif ($record->first_name != null) {
                        $dataArr[$ky]['name'] = $record->first_name;
                    }

                    if ($record->employee != null) {
                        $dataArr[$ky]['phone'] = $record->employee->phone_ext != null
                            ? ($record->employee->phone . ' x' . $record->employee->phone_ext)
                            : $record->employee->phone;
                        $dataArr[$ky]['employee_no'] = $record->employee->employee_no != null
                            ? $record->employee->employee_no
                            : '';
                        $dataArr[$ky]['work_type'] = $record->employee->work_type_id != null
                            ? ($record->employee->work_type != null ? $record->employee->work_type->type : '')
                            : '';
                        $dataArr[$ky]['employee_dob'] = $record->employee->employee_no != null
                            ? $record->employee->employee_dob
                            : '';
                        $dataArr[$ky]['employee_work_email'] = $record->employee->employee_work_email != null
                            ? $record->employee->employee_work_email
                            : '';

                        $rolesObject = $record->employee->user->roles;
                        $roles_arr = [];
                        $roles = '';
                        if (!empty($rolesObject)) {
                            foreach ($rolesObject as $role) {
                                $roles_arr[] = ucwords(strtolower(str_replace("_", " ", $role->name)));
                            }
                            $roles = implode(",", $roles_arr);
                        }
                        $dataArr[$ky]['role'] = $roles;
                    } else {
                        $dataArr[$ky]['phone'] = '--';
                        $dataArr[$ky]['employee_no'] = '--';
                        $dataArr[$ky]['work_type'] = '--';
                        $dataArr[$ky]['employee_dob'] = '--';
                        $dataArr[$ky]['employee_work_email'] = '--';
                        $dataArr[$ky]['role'] = '--';
                    }
                }
            }

            $dataApiType = $request->input('data-api-type');
            $processedArr = $this->landingPageRepository->sortArrayByKeyField($request);
            $valueArray = $this->landingPageRepository->fetchFieldsByConfiguration($request, $dataArr, false);
            $headerArray = $this->landingPageRepository->fetchFieldsByConfiguration($request, $dataArr, true);
            $view = View::make('partials.welcome.widget_template')
                ->with(compact(['valueArray', 'headerArray', 'dataApiType']))->render();
            return response()->json([
                'html' => $view,
                'processed_array' => $processedArr,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'html' => '<tbody><tr style="vertical-align: middle;" valign="middle"><th class="text-center"
                    style="border:0px;text-align:center;vertical-align:middle;">'
                    . (config('globals.landingPageExceptionCustomErrorMessageText'))
                    . '</th></tr></tbody>',
                'processed_array' => [],
            ]);
        }
    }

    public function getJobTicketStatus(Request $request)
    {
        try {
            $sessionCheck = $this->searchBoxVsSessionValueRecheck($request->input('customer-search'));
            $job_list = $this->jobRepository->getJobs($job_status = null, $filter = false, $customer_session = true);
            $datatableRows = $this->jobRepository->prepareDataForJobTrackingSummary($job_list, true);
            $records = [];
            if (!empty($datatableRows)) {
                foreach ($datatableRows as $key => $datatableRow) {
                    $records[$key]['client_name'] = $datatableRow['client_name'] . ' (' . $datatableRow['customer'] . ')';
                    $records[$key]['position'] = $datatableRow['position'] . ' (' . $datatableRow['no_of_vaccancies'] . ')';
                    $records[$key]['assignee'] = $datatableRow['assignee'];
                    $records[$key]['assignment_type'] = $datatableRow['assignment_type'];
                    $records[$key]['requester'] = $datatableRow['requester'];
                    $records[$key]['wage_rate'] = '$' . $datatableRow['wage_low'] . ' - $' . $datatableRow['wage_high'];
                    $records[$key]['requisition_date'] = $datatableRow['requisition_date'];
                    $records[$key]['process_id']['_value'] = $datatableRow['process_id'] . " - " . $datatableRow['process_name'];

                    $bgColor = 'red';
                    $color = 'black';
                    if ($datatableRow['process_id'] >= 0 && $datatableRow['process_id'] <= 5) {
                        $bgColor = 'red';
                        $color = 'white';
                    } else if ($datatableRow['process_id'] > 5 && $datatableRow['process_id'] <= 10) {
                        $bgColor = 'yellow';
                    } else if ($datatableRow['process_id'] > 10 && $datatableRow['process_id'] <= 14) {
                        $bgColor = 'green';
                        $color = 'white';
                    }
                    $records[$key]['process_id']['_color'] = $color;
                    $records[$key]['process_id']['_bg_color'] = $bgColor;
                    $records[$key]['process_id']['_href'] = '';
                    $records[$key]['process_id']['_title'] = '';
                }
            }

            $dataApiType = $request->input('data-api-type');
            $processedArr = $this->landingPageRepository->sortArrayByKeyField($request);
            $valueArray = $this->landingPageRepository->fetchFieldsByConfiguration($request, $records, false);
            $headerArray = $this->landingPageRepository->fetchFieldsByConfiguration($request, $records, true);
            $view = View::make('partials.welcome.widget_template')->with(compact(['valueArray', 'headerArray', 'dataApiType']))->render();
            return response()->json([
                'html' => $view,
                'processed_array' => $processedArr,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'html' => '<tbody><tr style="vertical-align: middle;" valign="middle"><th class="text-center" style="border:0px;text-align:center;vertical-align:middle;">' . (config('globals.landingPageExceptionCustomErrorMessageText')) . '</th></tr></tbody>',
                'processed_array' => [],
            ]);
        }
    }

    /**
     *  Get Candidate Screening Summary
     *
     * @param null
     * @return json
     */
    public function getCandidateScreeningSummary(Request $request)
    {
        try {
            $sessionCheck = $this->searchBoxVsSessionValueRecheck($request->input('customer-search'));
            $dataApiType = $request->input('data-api-type');
            $requestData = $request;

            $summary = $this->candidateRepository->getCandidates($candidate_selection_status = null, $requestData = null, $type_of_records_request = null, $order_by = 'name', $customer_session = true, true);
            $records = [];
            foreach ($summary as $key => $data) {
                $records[$key]['client_name']['_value'] = $data->latestJobApplied->job->customer->client_name;
                $records[$key]['client_name']['_color'] = '';
                $records[$key]['client_name']['_bg_color'] = '';
                $records[$key]['client_name']['_href'] = 'hranalytics/job/view/' . $data->latestJobApplied->job_id;
                $records[$key]['client_name']['_title'] = 'View';

                $records[$key]['name']['_value'] = $data->name;
                $records[$key]['name']['_color'] = '';
                $records[$key]['name']['_bg_color'] = '';
                $records[$key]['name']['_href'] = 'hranalytics/candidate/' . $data->id . '/' . $data->latestJobApplied->job_id . '/view';
                $records[$key]['name']['_title'] = 'View application';

                $records[$key]['email'] = $data->email;
                $phoneNumber = (!empty($data->phone_home)) ? $data->phone_home : $data->phone_cellular;
                $phoneNumber = str_replace("(", " (", $phoneNumber);
                $records[$key]['phone'] = str_replace(")", ") ", $phoneNumber);
                $records[$key]['city'] = $data->city != null ? $data->city : '';
                $records[$key]['postal_code'] = $data->postal_code != null ? $data->postal_code : '';
                $records[$key]['requisition_date'] = $data->latestJobApplied->created_at != null ? $data->latestJobApplied->created_at : '';
                $records[$key]['wage_rate'] = ($data->wageExpectation->wage_expectations_from != null && $data->wageExpectation->wage_expectations_to != null) ? '$' . $data->wageExpectation->wage_expectations_from . ' - $' . $data->wageExpectation->wage_expectations_to : '';

                if ($data->latestJobApplied->candidate_status == "Proceed") {
                    $records[$key]['candidate_status']["_value"] = $data->latestJobApplied->candidate_status;
                    $records[$key]['candidate_status']["_href"] = 'hranalytics/candidate/' . $data->id . '/' . $data->latestJobApplied->job_id . '/review';
                    $records[$key]['candidate_status']['_color'] = '';
                    $records[$key]['candidate_status']['_bg_color'] = '';
                    $records[$key]['candidate_status']['_title'] = 'Click here to review screening question/add interview notes';
                } else {
                    $records[$key]['candidate_status'] = $data->latestJobApplied->candidate_status;
                }
            }
            $processedArr = $this->landingPageRepository->sortArrayByKeyField($request);
            $valueArray = $this->landingPageRepository->fetchFieldsByConfiguration($request, $records, false);
            $headerArray = $this->landingPageRepository->fetchFieldsByConfiguration($request, $records, true);
            $view = View::make('partials.welcome.widget_template')->with(compact(['valueArray', 'headerArray', 'dataApiType']))->render();
            return response()->json([
                'html' => $view,
                'processed_array' => $processedArr,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'html' => '<tbody><tr style="vertical-align: middle;" valign="middle"><th class="text-center" style="border:0px;text-align:center;vertical-align:middle;">' . (config('globals.landingPageExceptionCustomErrorMessageText')) . '</th></tr></tbody>',
                'processed_array' => [],
            ]);
        }
    }

    /**
     * Fetch the List of customer guard Tour for Dasboardh
     *
     * @param [type] $customer_id
     * @param Request $request
     * @return json
     */
    public function getGuardTourList(Request $request)
    {
        try {
            $sessionCheck = $this->searchBoxVsSessionValueRecheck($request->input('customer-search'));
            $dataApiType = $request->input('data-api-type');
            $customerId = $request->input('customer-id');
            $selected_customer_ids = $this->helperService->getCustomerIds();
            $data = (!empty($selected_customer_ids)) ? $selected_customer_ids : 0;
            $records = $this->guard_tour_repository->getList($data);
            if (count($records) == 1 && $records[0]['Date'] == null) {
                $records = $this->shif_journal_repository->getList($data, $dashboard_view = true);
            }

            if (count($records) == 1 && $records[0]['Date'] == null) {
                $records = [];
            }
            $processedArr = $this->landingPageRepository->sortArrayByKeyField($request);
            $valueArray = $this->landingPageRepository->fetchFieldsByConfiguration($request, $records, false, true, "guard_tour_shift_journal_summary_widget");
            $headerArray = $this->landingPageRepository->fetchFieldsByConfiguration($request, $records, true);
            $view = View::make('partials.welcome.widget_template')->with(compact(['valueArray', 'headerArray', 'dataApiType']))->render();
            return response()->json([
                'html' => $view,
                'processed_array' => $processedArr,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'html' => '<tbody><tr style="vertical-align: middle;" valign="middle"><th class="text-center" style="border:0px;text-align:center;vertical-align:middle;">' . (config('globals.landingPageExceptionCustomErrorMessageText')) . '</th></tr></tbody>',
                'processed_array' => [],
            ]);
        }
    }

    public function getDashboardTimeSheet(Request $request)
    {
        try {
            $sessionCheck = $this->searchBoxVsSessionValueRecheck($request->input('customer-search'));
            $currentPayPeriod = $this->payPeriodRepository->getCurrentPayperiod();
            if ($currentPayPeriod != null) {
                $payperiod = $currentPayPeriod->id;
            } else {
                throw new \Exception('No data found');
            }

            $current_user = \Auth::user();
            $data = $this->timetrackerRepository
                ->timesheetReport($payperiod, null, null, $current_user, $customer_session = true, null, null);

            $records = [];
            foreach ($data as $key => $val) {
                $records[$key]['updated_at'] = $val['updated_at'];
                $records[$key]['full_name'] = $val['full_name'];
                $records[$key]['client_name'] = $val['client_name'];

                $start = '';
                $end = '';
                $workHours = '';
                foreach ($val['shifts'] as $shift) {
                    $start .= $shift['start'] . '<br>';
                    $end .= $shift['end'] . '<br>';
                    $workHours .= $shift['work_hours'] . '<br>';
                }
                $records[$key]['start_date'] = $start;
                $records[$key]['end_date'] = $end;
                $records[$key]['work_hours'] = $workHours;
            }
            $dataApiType = $request->input('data-api-type');
            $processedArr = $this->landingPageRepository->sortArrayByKeyField($request);
            $valueArray = $this->landingPageRepository->fetchFieldsByConfiguration($request, $records, false, true, "time_sheet_widget");
            $headerArray = $this->landingPageRepository->fetchFieldsByConfiguration($request, $records, true);
            $view = View::make('partials.welcome.widget_template')->with(compact(['valueArray', 'headerArray', 'dataApiType']))->render();
            return response()->json([
                'html' => $view,
                'processed_array' => $processedArr,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'html' => '<tbody><tr style="vertical-align: middle;" valign="middle"><th class="text-center" style="border:0px;text-align:center;vertical-align:middle;">' . (config('globals.landingPageExceptionCustomErrorMessageText')) . '</th></tr></tbody>',
                'processed_array' => [],
            ]);
        }
    }

    public function getEmployeeSchedules(Request $request)
    {
        try {
            $sessionCheck = $this->searchBoxVsSessionValueRecheck($request->input('customer-search'));
            $scheduleFound = false;
            $currentPayperiodId = '';
            $payperiods = null;

            $configuedSystemFields = $request->get('data-system-fields');
            $resultArray = explode(",", $configuedSystemFields);
            if (empty($resultArray)) {
                return response()->json([
                    'html' => '',
                    'selectBoxView' => View::make('partials.welcome.employee_schedule_select_box')->with(compact(['payperiods', 'currentPayperiodId']))->render(),
                ]);
            }
            $currentPayperiod = $this->payPeriodRepository->getCurrentPayperiod();
            if (!empty($currentPayperiod)) {
                $currentPayperiodId = $currentPayperiod->id;
            }
            $selectBoxView = View::make('partials.welcome.employee_schedule_select_box')->with(compact(['payperiods', 'currentPayperiodId']))->render();
            $view = View::make('partials.welcome.widget_employee_schedule')->with(compact(['scheduleFound']))->render();
            return response()->json([
                'is_not_tbl' => true,
                'html' => $view,
                'selectBoxView' => $selectBoxView,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'html' => '<tbody><tr style="vertical-align: middle;" valign="middle"><th class="text-center" style="border:0px;text-align:center;vertical-align:middle;">' . (config('globals.landingPageExceptionCustomErrorMessageText')) . '</th></tr></tbody>',
                'processed_array' => [],
                'selectBoxView' => '',
            ]);
        }
    }

    public function getSiteStatusData(Request $request)
    {
        try {
            $sessionCheck = $this->searchBoxVsSessionValueRecheck($request->input('customer-search'));
            $latest_template = $this->customer_report_repository->getLatestTemplate();
            $customers_arr_per = $this->customer_map_repository->getCustomerMapDetails($latest_template, null, null, true);
            $customers_arr_temp = $this->customer_map_repository->getCustomerMapDetails($latest_template, 'stc', null, true);
            $customers_arr = array_merge_recursive($customers_arr_temp, $customers_arr_per);
            $customer_score = $customers_arr['customer_score'];
            $shift_flag = 0;

            $configuedSystemFields = $request->get('data-system-fields');
            $resultArray = explode(",", $configuedSystemFields);
            if (empty($resultArray)) {
                return response()->json([
                    'html' => '',
                    'customer_score' => [],
                ]);
            }

            $heading = "";
            $src = null;
            $siteStatusFlag = 0;
            if (Auth::user()->can('view_supervisorpanel')) {
                $heading = 'Site Status';
                $src = route('customers.mapping');
                $siteStatusFlag = 1;
            }
            //            elseif (Auth::user()->can('create-job') || Auth::user()->can('edit-job') || Auth::user()->can('delete-job') || Auth::user()->can('archive-job') || Auth::user()->can('job-approval') || Auth::user()->can('hr-tracking') || Auth::user()->can('job-attachement-settings') || Auth::user()->can('list-jobs-from-all')) {
            //                $heading = "Job Mapping";
            //                $src = route('site-status-list');
            //                $siteStatusFlag = 2;
            //            }
            $view = View::make('partials.welcome.widget_map')->with(compact(['customer_score', 'shift_flag', 'siteStatusFlag', 'src']))->render();

            return response()->json([
                'is_not_tbl' => true,
                'html' => $view,
                'heading' => $heading,
                'href' => $src,
                'customer_score' => $customers_arr['customer_score'],
                'shift_flag' => $shift_flag,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'html' => '<tbody><tr style="vertical-align: middle;" valign="middle"><th class="text-center" style="border:0px;text-align:center;vertical-align:middle;">' . (config('globals.landingPageExceptionCustomErrorMessageText')) . '</th></tr></tbody>',
                'heading' => 'Site Status',
                'href' => '',
                'customer_score' => [],
                'shift_flag' => 0,
                'processed_array' => [],
            ]);
        }
    }

    /**
     * Plot all jobs in google map for Dashboard
     *
     * @return void
     */
    public function jobMappingForSiteStatus(Request $request)
    {
        $sessionCheck = $this->searchBoxVsSessionValueRecheck($request->input('customer-search'));
        $jobs = $this->jobRepository->getJobsForMappingByCusromerFilter($request, $customer_session = true);
        return view('hranalytics::job.jobs-in-map', compact('jobs', 'request'));
    }

    public function getAllShiftModules(Request $request)
    {
        try {
            $sessionCheck = $this->searchBoxVsSessionValueRecheck($request->input('customer-search'));
            if (!Auth::user()->hasPermissionTo('view_shift_journal_20_transaction') && !Auth::user()->hasPermissionTo('view_all_shift_journal_20_transaction')) {
                return response()->json([
                    'html' => '<tbody><tr style="vertical-align: middle;" valign="middle"><th class="text-center" style="border:0px;text-align:center;vertical-align:middle;font-weight:normal;">' . (config('globals.landingPageInvalidPermissionDisplayMessageText')) . '</th></tr></tbody>',
                    'processed_array' => [],
                ]);
            }
            return response()->json([
                'html' => '',
                'customer_id' => $request->get('customer-id'),
                'shift_module_id' => $request->get('data-module-id'),
                'module_type' => $request->get('data-model'),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'html' => '<tbody><tr style="vertical-align: middle;" valign="middle"><th class="text-center" style="border:0px;text-align:center;vertical-align:middle;">' . (config('globals.landingPageExceptionCustomErrorMessageText')) . '</th></tr></tbody>',
                'processed_array' => [],
            ]);
        }
    }

    public function syncDashboardFilter(Request $request)
    {
        session()->put('customer_ids', []);
        $customerIds = [];
        if ($request->has('customer_ids') && is_array($request->input('customer_ids'))) {
            $customerIds = $request->input('customer_ids');
            if (!empty($customerIds)) {
                $customerIds = array_map('intval', $customerIds);
                session()->put('customer_ids', $customerIds);
            }
        }

        return response()->json([
            'success' => true,
            'sync' => true,
            'payload' => session()->get('customer_ids'),
        ]);
    }

    public function getDashboardIncidentReport(Request $request)
    {
        try {
            $sessionCheck = $this->searchBoxVsSessionValueRecheck($request->input('customer-search'));
            $incident_list = $this->incident_report_repository->getIncidentReportDashList(true, true);
            $data_list = $this->incident_report_repository->prepareIncidentReportList($incident_list);
            $records = [];

            foreach ($data_list as $key => $value) {
                $bgRowColor = strtolower($value['status']);
                if ($bgRowColor == 'open') {
                    $records[$key]['_bg_color'] = "#ff9999 !important";
                } elseif ($bgRowColor == 'in progress') {
                    $records[$key]['_bg_color'] = "#ffe690 !important";
                } else {
                    $records[$key]['_bg_color'] = "rgba(36, 169, 66, 0.62) !important";
                }

                $records[$key]['updated_time'] = $value['updated_time'];
                $records[$key]['updated_at'] = '<span class="hidden_date_span">' . $value['updated_at'] . '</span>' . $value['updated_at_date'];
                $records[$key]['employee_no'] = $value['employee_no'];
                $records[$key]['pay_period_name'] = $value['pay_period_name'];
                $records[$key]['title'] = $value['title'];
                $records[$key]['value'] = $value['value'];

                $records[$key]['client_name']['_value'] = $value['client_name'];
                $records[$key]['client_name']['_color'] = '';
                $records[$key]['client_name']['_bg_color'] = '';
                $records[$key]['client_name']['_href'] = 'supervisorpanel/customer/details/' . $value['customer_id'] . '/' . $value['payperiod_id'];
                $records[$key]['client_name']['_title'] = 'View customer details';

                $records[$key]['subject']['_value'] = $value['subject'];
                $records[$key]['subject']['_color'] = '';
                $records[$key]['subject']['_bg_color'] = '';
                $records[$key]['subject']['_href'] = 'supervisorpanel/customer/details/' . $value['customer_id'] . '/' . $value['payperiod_id'];
                $records[$key]['subject']['_title'] = 'View customer details';

                if ($value['reporter_last_name'] != null) {
                    $records[$key]['reporter_name'] = $value['reporter_first_name'] . ' ' . $value['reporter_last_name'];
                } else {
                    $records[$key]['reporter_name'] = $value['reporter_first_name'];
                }

                $records[$key]['status'] = $value['status'];
            }

            $dataApiType = $request->input('data-api-type');
            $processedArr = $this->landingPageRepository->sortArrayByKeyField($request);
            $valueArray = $this->landingPageRepository->fetchFieldsByConfiguration($request, $records, false);
            $headerArray = $this->landingPageRepository->fetchFieldsByConfiguration($request, $records, true);
            $view = View::make('partials.welcome.widget_template')->with(compact(['valueArray', 'headerArray', 'dataApiType']))->render();
            return response()->json([
                'html' => $view,
                'processed_array' => $processedArr,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'html' => '<tbody><tr style="vertical-align: middle;" valign="middle"><th class="text-center" style="border:0px;text-align:center;vertical-align:middle;">' . (config('globals.landingPageExceptionCustomErrorMessageText')) . '</th></tr></tbody>',
                'processed_array' => [],
            ]);
        }
    }

    /**
     * Show litmos dashboard
     *
     * @return void
     */
    public function getTrainingRedirectLink()
    {
        $client = new \GuzzleHttp\Client();
        $res = $client->request('GET', 'https://api.litmos.com/v1.svc/users', [
            'query' => [
                'apikey' => config('globals.litmos_api_key'),
                'source' => 'cgl360',
                'search' => \Auth::user()->email,
            ],
            'headers' => [
                'Accept' => 'application/json',
            ],
        ]);
        $body = $res->getBody();
        if ($recode = $res->getStatusCode() == 200) {
            $contents = json_decode($body->getContents());
            if (count($contents) > 0 && null != $contents[0]->Id) {
                $user_id = $contents[0]->Id;

                $res = $client->request('GET', 'https://api.litmos.com/v1.svc/users/' . $user_id, [
                    'query' => [
                        'apikey' => config('globals.litmos_api_key'),
                        'source' => 'cgl360',
                    ],
                    'headers' => [
                        'Accept' => 'application/json',
                    ],
                ]);
                $body = $res->getBody();
                $contents = json_decode($body->getContents());
                $url = $contents->LoginKey;

                return view('learningandtraining::litmos-dashboard-iframe', ['url' => $url]);
            } else {
                echo ('<h3>You are not yet added to Litmos.<br/>Please contact your administrator. </h3>');
                header('refresh:3;url=' . url('/'));
            }
        } else {
            echo ('<h3>Litmos service is unavailable. Response code from Litmos is:' . $recode . '<br/>Please contact your administrator. </h3>');
            header('refresh:3;url=' . url('/'));
        }
    }

    public function getDashboardVisitorsLog(Request $request)
    {
        try {
            $sessionCheck = $this->searchBoxVsSessionValueRecheck($request->input('customer-search'));
            $ids = $this->helperService->getCustomerIds();
            $dataApiType = $request->input('data-api-type');
            $records = $this->visitorLogRepository->list(0, $ids, '', '', false, true);
            $processedArr = $this->landingPageRepository->sortArrayByKeyField($request);
            $valueArray = $this->landingPageRepository->fetchFieldsByConfiguration($request, $records, false);
            $headerArray = $this->landingPageRepository->fetchFieldsByConfiguration($request, $records, true);
            $view = View::make('partials.welcome.widget_template')->with(compact(['valueArray', 'headerArray', 'dataApiType']))->render();
            return response()->json([
                'html' => $view,
                'processed_array' => $processedArr,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'html' => '<tbody><tr style="vertical-align: middle;" valign="middle"><th class="text-center" style="border:0px;text-align:center;vertical-align:middle;">' . (config('globals.landingPageExceptionCustomErrorMessageText')) . '</th></tr></tbody>',
                'processed_array' => [],
            ]);
        }
    }

    public function getDashboardSiteNotes(Request $request)
    {
        try {
            $sessionCheck = $this->searchBoxVsSessionValueRecheck($request->input('customer-search'));
            $ids = [];
            if (\Auth::user()->can('view_sitenote_reports')) {
                $ids = $this->helperService->getCustomerIds();
                if (empty($ids)) {
                    if (\Auth::user()->hasAnyPermission(['admin', 'super_admin'])) {
                        $ids = Customer::all()->pluck('id')->toArray();
                    } else {
                        $userArr = [Auth::User()->id];
                        $ids = $this->customerEmployeeAllocationRepository->getAllAllocatedCustomerId($userArr);
                    }
                }
            }

            $dataApiType = $request->input('data-api-type');
            $siteNotes = $this->siteNotesRepository->getSiteNotesByCustomer($ids, true);
            $records = $this->siteNotesRepository->prepaireSiteNotesArray($siteNotes);
            $processedArr = $this->landingPageRepository->sortArrayByKeyField($request);
            $valueArray = $this->landingPageRepository->fetchFieldsByConfiguration($request, $records, false);
            $headerArray = $this->landingPageRepository->fetchFieldsByConfiguration($request, $records, true);
            $view = View::make('partials.welcome.widget_template')->with(compact(['valueArray', 'headerArray', 'dataApiType']))->render();
            return response()->json([
                'html' => $view,
                'processed_array' => $processedArr,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'html' => '<tbody><tr style="vertical-align: middle;" valign="middle"><th class="text-center" style="border:0px;text-align:center;vertical-align:middle;">' . (config('globals.landingPageExceptionCustomErrorMessageText')) . '</th></tr></tbody>',
                'processed_array' => [],
            ]);
        }
    }

    public function loadDashboardTraining(Request $request)
    {
        try {
            $sessionCheck = $this->searchBoxVsSessionValueRecheck($request->input('customer-search'));
            $todo_count = $this->user_courses->getTodoCount();
            $recommended_count = $this->user_courses->getRecommendedCount();
            $completed_count = $this->user_courses->getCompletedCount();
            $over_due_count = $this->user_courses->getOverDueCountCount();
            $total_course_library = $this->user_courses->getCourseLibraryCount();
            $recent_achievements = $this->user_courses->getRecentAchivements();
            $hidePageHeading = true;

            $view = View::make('partials.welcome.training_widget')->with(compact('hidePageHeading', 'todo_count', 'recommended_count', 'completed_count', 'over_due_count', 'total_course_library', 'recent_achievements'))->render();

            $src = route('learning.dashboard');
            return response()->json([
                'is_not_tbl' => true,
                'html' => $view,
                'heading' => 'Training Management',
                'href' => $src,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'html' => '<tbody><tr style="vertical-align: middle;" valign="middle"><th class="text-center" style="border:0px;text-align:center;vertical-align:middle;">' . (config('globals.landingPageExceptionCustomErrorMessageText')) . '</th></tr></tbody>',
                'heading' => 'Site Status',
                'href' => '',
                'customer_score' => [],
                'shift_flag' => 0,
                'processed_array' => [],
            ]);
        }
    }

    private function searchBoxVsSessionValueRecheck($searchBoxValueArray)
    {
        if ($searchBoxValueArray == "") {
            $searchBoxValueArray = [];
        }
        $sessionValue = session()->get('customer_ids');

        if ((!empty($sessionValue)) || (!empty($searchBoxValueArray))) {
            $arrayDifference1 = array_diff($searchBoxValueArray, $sessionValue);
            $arrayDifference2 = array_diff($sessionValue, $searchBoxValueArray);
            if (!empty($arrayDifference1) || !empty($arrayDifference2)) {
                Log::channel('landingPageCustomerSearchSession')->info('------Landing page session - start -----');
                Log::channel('landingPageCustomerSearchSession')->info('Customer search values => ' . json_encode(implode(",", $searchBoxValueArray)));
                Log::channel('landingPageCustomerSearchSession')->info('Current Session Customer ids => ' . json_encode(implode(",", $sessionValue)));
                Log::channel('landingPageCustomerSearchSession')->info('------Landing page session recheck - end -----');

                session()->put('customer_ids', $searchBoxValueArray);
            }
        }
        return true;
    }

    public function getPostOrders(Request $request)
    {
        try {
            $records = [];
            $created_user = null;
            $sessionCheck = $this->searchBoxVsSessionValueRecheck($request->input('customer-search'));
            $ids = $this->helperService->getCustomerIds();
            if (empty($ids)) {
                if (!(\Auth::user()->can('view_post_order'))) {
                    if ((\Auth::user()->can('view_allocated_post_order'))) {
                        $userArr = [Auth::User()->id];
                        $ids = $this->customerEmployeeAllocationRepository->getAllAllocatedCustomerId($userArr);
                    } else {
                        $created_user = Auth::User()->id;
                    }
                }
            } else {
                if (!(\Auth::user()->can('view_post_order'))) {
                    if ((\Auth::user()->can('view_allocated_post_order'))) {
                        $userArr = [Auth::User()->id];
                        $allocatedArray = $this->customerEmployeeAllocationRepository->getAllAllocatedCustomerId($userArr);

                        $ids = array_intersect($ids, $allocatedArray);
                    } else {
                        $ids[] = 0;
                        $created_user = Auth::User()->id;
                    }
                }
            }
            $postOrderData = $this->postOrderRepository->getAll($ids, $created_user, true);
            foreach ($postOrderData as $key => $eachRecord) {
                $eachRow["id"] = $eachRecord->id;
                $eachRow["attachment_id"]['_value'] = '<i class="fa fa-download fa-sm" aria-hidden="true"></i>';
                $eachRow["attachment_id"]['_color'] = "";
                $eachRow["attachment_id"]['_bg_color'] = "";
                $eachRow["attachment_id"]['_href'] = "file/show/" . $eachRecord->attachment_id . "/post-order";
                $eachRow["attachment_id"]['_title'] = "Attachment";
                $eachRow["description"] = $eachRecord->description;
                $eachRow["topic"] = isset($eachRecord->topicDetails) ? $eachRecord->topicDetails->topic : '--';
                $eachRow["group"] = isset($eachRecord->groupDetails) ? $eachRecord->groupDetails->group : '--';
                $eachRow["client_name"] = isset($eachRecord->customerDetails) ? $eachRecord->customerDetails->client_name : '--';
                $eachRow["created_user_full_name"] = isset($eachRecord->getCreatedby->full_name) ? $eachRecord->getCreatedby->full_name : '--';
                $eachRow["reviewed_user_full_name"] = isset($eachRecord->getReviewedby->full_name) ? $eachRecord->getReviewedby->full_name : '--';
                $createdAt = \Carbon::parse($eachRecord->created_at)->format('M d, Y');
                $eachRow["created_at"] = '<span class="hidden_date_span">' . $eachRecord->created_at . '</span>' . $createdAt;
                $eachRow["created_at_string"] = $eachRecord->created_at;
                if (isset($eachRecord->reviewed_status) && $eachRecord->reviewed_status == 0) {
                    $eachRow["reviewed_status"] = 'Rejected';
                    $eachRow['_bg_color'] = "#ff9999 !important";
                } else if ($eachRecord->reviewed_status == 1) {
                    $eachRow["reviewed_status"] = 'Approved';
                    $eachRow['_bg_color'] = "rgba(36, 169, 66, 0.62) !important";
                } else {
                    $eachRow["reviewed_status"] = 'Pending';
                    $eachRow['_bg_color'] = "#ffe690 !important";
                }
                array_push($records, $eachRow);
            }

            $dataApiType = $request->input('data-api-type');
            $processedArr = $this->landingPageRepository->sortArrayByKeyField($request);
            $valueArray = $this->landingPageRepository->fetchFieldsByConfiguration($request, $records, false);
            $headerArray = $this->landingPageRepository->fetchFieldsByConfiguration($request, $records, true);
            $view = View::make('partials.welcome.widget_template')->with(compact(['valueArray', 'headerArray', 'dataApiType']))->render();
            return response()->json([
                'html' => $view,
                'processed_array' => $processedArr,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'html' => '<tbody><tr style="vertical-align: middle;" valign="middle"><th class="text-center" style="border:0px;text-align:center;vertical-align:middle;">' . (config('globals.landingPageExceptionCustomErrorMessageText')) . '</th></tr></tbody>',
                'processed_array' => [],
            ]);
        }
    }

    public function getKeyLogSummary(Request $request)
    {
        try {
            $records = [];
            $created_user = null;
            $sessionCheck = $this->searchBoxVsSessionValueRecheck($request->input('customer-search'));
            $ids = $this->helperService->getCustomerIds();
            if (empty($ids)) {
                if (!(\Auth::user()->can('view_all_customers_keys'))) {
                    if ((\Auth::user()->can('view_allocated_customers_keys'))) {
                        $userArr = [Auth::User()->id];
                        $ids = $this->customerEmployeeAllocationRepository->getAllAllocatedCustomerId($userArr);
                    } else {
                        $created_user = Auth::User()->id;
                    }
                }
            } else {
                if (!(\Auth::user()->can('view_all_customers_keys'))) {
                    if ((\Auth::user()->can('view_allocated_customers_keys'))) {
                        $userArr = [Auth::User()->id];
                        $allocatedArray = $this->customerEmployeeAllocationRepository->getAllAllocatedCustomerId($userArr);

                        $ids = array_intersect($ids, $allocatedArray);
                    } else {
                        $ids[] = 0;
                        $created_user = Auth::User()->id;
                    }
                }
            }

            $dataApiType = $request->input('data-api-type');
            $records = $this->customerKeyDetailRepository->getKeyLogSummaryByCustomers($ids);
            $processedArr = $this->landingPageRepository->sortArrayByKeyField($request);
            $valueArray = $this->landingPageRepository->fetchFieldsByConfiguration($request, $records, false, true, "key_log_summary_widget");
            $headerArray = $this->landingPageRepository->fetchFieldsByConfiguration($request, $records, true);
            $view = View::make('partials.welcome.widget_template')->with(compact(['valueArray', 'headerArray', 'dataApiType']))->render();
            return response()->json([
                'html' => $view,
                'processed_array' => $processedArr,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'html' => '<tbody><tr style="vertical-align: middle;" valign="middle"><th class="text-center" style="border:0px;text-align:center;vertical-align:middle;">' . (config('globals.landingPageExceptionCustomErrorMessageText')) . '</th></tr></tbody>',
                'processed_array' => [],
            ]);
        }
    }

    public function getMotionSensorSummary(Request $request)
    {
        try {
            $sessionCheck = $this->searchBoxVsSessionValueRecheck($request->input('customer-search'));
            $incident_list = $this->sensorTriggerRepository->getKeyLogSummaryByCustomers($customer_session = true);
            $records = [];
            foreach ($incident_list as $key => $value) {
                $records[$key]['date'] = $value['date'];
                $records[$key]['customer'] = $value['customer_name'];
                $records[$key]['address'] = $value['customer_address'];
                $records[$key]['room'] = $value['room_name'];
                $records[$key]['entry'] = $value['entry'];
                $records[$key]['exit'] = $value['exit'];
            }

            $dataApiType = $request->input('data-api-type');
            $processedArr = $this->landingPageRepository->sortArrayByKeyField($request);
            $valueArray = $this->landingPageRepository->fetchFieldsByConfiguration($request, $records);
            $headerArray = $this->landingPageRepository->fetchFieldsByConfiguration($request, $records, true);
            $view = View::make('partials.welcome.widget_template')->with(compact(['valueArray', 'headerArray', 'dataApiType']))->render();
            return response()->json([
                'html' => $view,
                'processed_array' => $processedArr,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'html' => '<tbody><tr style="vertical-align: middle;" valign="middle"><th class="text-center" style="border:0px;text-align:center;vertical-align:middle;">' . (config('globals.landingPageExceptionCustomErrorMessageText')) . '</th></tr></tbody>',
                'processed_array' => [],
            ]);
        }
    }

    public function getLandingPageTabs(Request $request)
    {
        try {
            $selectedCustomer = [];
            if (($request->get('customer_id') != "0") && ($request->get('customer_id') != 0) && ($request->get('customer_id') != null)) {
                if (is_array($request->get('customer_id'))) {
                    $selectedCustomer = $request->get('customer_id');
                } else {
                    $selectedCustomer[] = $request->get('customer_id');
                }
            }

            if ((count($selectedCustomer) == 1) && ($selectedCustomer != null)) {
                $tabDetails = $this->landingPageRepository->getTabsByCustomer($selectedCustomer)->select('tab_name', 'id')->get();
            } else {
                $tabDetails = $this->landingPageRepository->fetchLatestTabsByLayoutGroupping($selectedCustomer)->select('tab_name', 'id')->get();
            }

            // $tabs = null;
            // if (!empty($tabDetails)) {
            //     $tabs = $tabDetails->map(function ($item) {
            //             $data['id'] = $item['id'];
            //             $data['name'] = $item['tab_name'];
            //             return $data;
            //     });
            // }

            $tabs = [];
            if (!empty($tabDetails)) {
                foreach ($tabDetails as $key => $item) {
                    $addTab = $this->landingPageRepository->addOrRemoveTabAccordingToPermission($item['id']);

                    if ($addTab) {
                        $data['id'] = $item['id'];
                        $data['name'] = $item['tab_name'];
                        array_push($tabs, $data);
                    }
                }
            }

            return response()->json([
                'success' => true,
                'data' => $tabs,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'data' => null,
            ]);
        }
    }

    public function getScheduleDetails(Request $request)
    {
        //input parameters
        $payperiodIds = $request->get('payperiod_id');
        $customerIds = $request->get('customer-search');

        $currentPayperiodId = $payperiodIds;
        if (empty($payperiodIds)) {
            $currentPayperiod = $this->payPeriodRepository->getCurrentPayperiod();
            if (!empty($currentPayperiod)) {
                $currentPayperiodId = $currentPayperiod->id;
            }
        }

        $allocatedCustomersArray = [];
        if (Auth::user()->can('view_all_employee_schedule_requests')) {
            $allocatedCustomersArray = $this->customerRepository->getAllCustomers();
        } else if (Auth::user()->can('view_allocated_employee_schedule_requests')) {
            $allocatedCustomersArray = $this->schedulingRepository->getAllocatedCustomerIds();
        } else {
            return [
                'schedules' => null,
            ];
        }

        if (empty($allocatedCustomersArray)) {
            return [
                'schedules' => null,
            ];
        }
        //check for approval privilage -end

        if (!empty($customerIds) && !empty($allocatedCustomersArray)) {
            $allocatedCustomersArray = array_intersect($customerIds, $allocatedCustomersArray);
        }

        if (empty($allocatedCustomersArray)) {
            return [
                'schedules' => null,
            ];
        }

        $customerIds = $allocatedCustomersArray;
        $records = $this->schedulingRepository->getScheduleByParams('', $customerIds, $currentPayperiodId);
        $payPeriods = $this->payPeriodRepository->getRecentPeriods(WelcomeController::PAYPERIOD_PAST, WelcomeController::PAYPERIOD_FUTURE);
        return response()->json(array('type' => 'json', 'widgetTag' => 'widget-scheduling', 'data' => $records, 'payPeriods' => $payPeriods, 'defaultSelectedOption' => $currentPayperiodId));
    }

    public function getClientConcern(Request $request)
    {
        try {
            $records = [];
            $clientConcern = $this->concernRepository->getTableList($request->input('customer-search'), false);
            if (!empty($clientConcern)) {
                foreach ($clientConcern as $ky => $clientConcern) {
                    $record['project_name'] = $clientConcern->customer->client_name;
                    $record['client_name'] = $clientConcern->user ? ($clientConcern->user->full_name ? $clientConcern->user->full_name : $clientConcern->user->first_name) : '';
                    $record['severity'] = $clientConcern->severityLevel->severity;
                    $record['concern'] = (!empty($clientConcern->concern)) ? '<span style="white-space: pre-wrap !important;">' . $clientConcern->concern . '</span>' : '';
                    $createdAt = \Carbon::parse($clientConcern->created_at)->format('M d, Y h:m a');
                    $record['date_time'] = '<span class="hidden_date_span">' . $clientConcern->created_at . '</span>' . $createdAt;

                    $records[] = $record;
                }

                $dataApiType = $request->input('data-api-type');
                $processedArr = $this->landingPageRepository->sortArrayByKeyField($request);
                $valueArray = $this->landingPageRepository->fetchFieldsByConfiguration($request, $records, false, true);
                $headerArray = $this->landingPageRepository->fetchFieldsByConfiguration($request, $records, true);
                $view = View::make('partials.welcome.widget_template')->with(compact(['valueArray', 'headerArray', 'dataApiType']))->render();
                return response()->json([
                    'html' => $view,
                    'processed_array' => $processedArr,
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'html' => '<tbody><tr style="vertical-align: middle;" valign="middle"><th class="text-center" style="border:0px;text-align:center;vertical-align:middle;">' . (config('globals.landingPageExceptionCustomErrorMessageText')) . '</th></tr></tbody>',
                'processed_array' => [],
            ]);
        }
    }

    public function getClientFeedback(Request $request)
    {
        try {
            $records = [];
            $clientFeedbacks = $this->clientRepository->getTableList($request->input('customer-search'), false);
            if (!empty($clientFeedbacks)) {
                foreach ($clientFeedbacks as $ky => $clientFeedback) {
                    $record['project_name'] = $clientFeedback->customer->client_name;
                    $record['feedback'] = $clientFeedback->clientFeedbacks->feedback;
                    $record['full_name'] = $clientFeedback->user ? ($clientFeedback->user->full_name ? $clientFeedback->user->full_name : $clientFeedback->user->first_name) : '';
                    $record['role'] = isset($clientFeedback->user->roles[0]->name) ? HelperService::snakeToTitleCase($clientFeedback->user->roles[0]->name) : '--';
                    $createdAt = \Carbon::parse($clientFeedback->created_at)->format('M d, Y h:m a');
                    $record['date_time'] = '<span class="hidden_date_span">' . $clientFeedback->created_at . '</span>' . $createdAt;
                    $record['rating'] = $clientFeedback->userRating->rating;
                    $record['comments'] = (!empty($clientFeedback->client_feedback)) ? '<span style="white-space: pre-wrap !important;">' . $clientFeedback->client_feedback . '</span>' : '';
                    $record['rated_by'] = $clientFeedback->createdUser->full_name;

                    $records[] = $record;
                }

                $dataApiType = $request->input('data-api-type');
                $processedArr = $this->landingPageRepository->sortArrayByKeyField($request);
                $valueArray = $this->landingPageRepository->fetchFieldsByConfiguration($request, $records, false, true);
                $headerArray = $this->landingPageRepository->fetchFieldsByConfiguration($request, $records, true);
                $view = View::make('partials.welcome.widget_template')->with(compact(['valueArray', 'headerArray', 'dataApiType']))->render();
                return response()->json([
                    'html' => $view,
                    'processed_array' => $processedArr,
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'html' => '<tbody><tr style="vertical-align: middle;" valign="middle"><th class="text-center" style="border:0px;text-align:center;vertical-align:middle;">' . (config('globals.landingPageExceptionCustomErrorMessageText')) . '</th></tr></tbody>',
                'processed_array' => [],
            ]);
        }
    }
}
