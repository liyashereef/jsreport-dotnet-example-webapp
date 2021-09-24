<?php

namespace Modules\Hranalytics\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\HelperService;
use Auth;
use Illuminate\Http\Request;
use Modules\Admin\Repositories\LandingPageRepository;
use Modules\Admin\Repositories\RecruitingAnalyticsConfigurationRepository;
use Modules\Hranalytics\Repositories\RecruitmentAnalyticsDashboardRepository;
use View;

class RecruitmentAnalyticsDashboardController extends Controller
{
    protected $recruitingAnalyticsConfigRepository, $recruitmentAnalyticsDashboardRepository, $landingPageRepository;

    public function __construct(
        RecruitingAnalyticsConfigurationRepository $recruitingAnalyticsConfigRepository,
        RecruitmentAnalyticsDashboardRepository $recruitmentAnalyticsDashboardRepository,
        LandingPageRepository $landingPageRepository
    ) {
        $this->recruitingAnalyticsConfigRepository = $recruitingAnalyticsConfigRepository;
        $this->helperService = new HelperService();
        $this->recruitmentAnalyticsDashboardRepository = $recruitmentAnalyticsDashboardRepository;
        $this->landingPageRepository = $landingPageRepository;
    }

    public function index()
    {
        return view('hranalytics::dashboard.index');
    }

    public function getDashboardTabDetails(Request $request)
    {
        try {
            $tabId = $request->get('tab_id');
            $parentDivWidth = $request->get('parentDivWidth');
            $parentDivHeight = $request->get('parentDivHeight');

            $tabInfo = $this->recruitingAnalyticsConfigRepository->getTabDetailsByTabId($tabId);
            $tabUid = ('tabuid_' . $tabInfo['id']);

            $moduleHierarchy = $this->recruitingAnalyticsConfigRepository->fetchModuleLayoutHierarchyByTab($tabId, $parentDivWidth, $parentDivHeight);
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

                $havePermission = 0;
                $widgetCssClass = '';
                $allowedWidgetCss = 'js-widget';
                $viewPermissionsCount = 0;
                $widgetContent = '<tbody><tr><th class="custom-dashboard-th" style="border:0px;text-align:center;vertical-align:middle;font-weight:normal;">' . (config('globals.landingPageInvalidPermissionDisplayMessageText')) . '</th></tr></tbody>';
                if (!empty($viewPermissionLabel)) {
                    $viewPermissions = unserialize($viewPermissionLabel);
                    if (isset($viewPermissions['recruitment_dashboard']) && !empty($viewPermissions['recruitment_dashboard'])) {
                        $landingPagePermissions = $viewPermissions['recruitment_dashboard'];
                        $viewPermissions = explode(",", $landingPagePermissions);
                        $viewPermissionsCount = count($viewPermissions);
                        foreach ($viewPermissions as $viewPermission) {
                            $trimmedPermission = trim($viewPermission, " ");
                            if (Auth::user()->can($trimmedPermission)) {
                                $havePermission++;
                            }
                        }
                    }

                    if ($havePermission > 0) {
                        $widgetCssClass = 'widget-tables';
                        $widgetContent = '<tbody><tr style="vertical-align: middle;" valign="middle"><th class="text-center" style="vertical-align: middle;">P&nbsp;L&nbsp;E&nbsp;A&nbsp;S&nbsp;E&nbsp;&nbsp;&nbsp;&nbsp;W&nbsp;A&nbsp;I&nbsp;T&nbsp;.&nbsp;.&nbsp;.</th></tr></tbody>';
                    } else {
                        $allowedWidgetCss = '';
                    }
                } else {
                    $widgetCssClass = 'widget-tables';
                    $widgetContent = '<tbody><tr style="vertical-align: middle;" valign="middle"><th class="text-center" style="vertical-align: middle;">P&nbsp;L&nbsp;E&nbsp;A&nbsp;S&nbsp;E&nbsp;&nbsp;&nbsp;&nbsp;W&nbsp;A&nbsp;I&nbsp;T&nbsp;.&nbsp;.&nbsp;.</th></tr></tbody>';
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

                    $html .= '<td class="custom-dashboard-th" id="' . $superParentTh . '" data-width="' . $value['width'] . '" style="' . $displayCss . 'width:' . $value['width'] . ' !important;padding-right: 10px !important;vertical-align:middle !important;position:relative;border: none !important;padding-top: 10px !important;padding-left: 0px !important;" rowspan="' . $value['rowspan'] . '" colspan="' . $value['colspan'] . '">'
                        . '<div class="card-table ' . $allowedWidgetCss . ' ' . $widgetName . ' ' . $changedModelName . '" data-attr="' . $tableAttributes . '" style="padding-left: 0px !important;padding-right: 0px !important;">'
                        . '<div class="card-header">' . $icon
                        . '<span class="pl-2" style="white-space: nowrap;">'
                        . '<a class="inner-page-nav ' . $widgetTitleCssClass . '" id="heading-' . $spanId . '" href="' . $detailUrl . '">' . $moduleName . '</a>'
                        . '</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="span-site-schedule filter-content" id="' . $spanId . '" style="text-align:center;"></span>'
                        . '</div>'
                        . '<div id="tbl_responsive_' . $spanId . '" data-parent-tbl="' . $tabInfo['id'] . '" class="table-responsive widget-div dasboard-card-body" style="' . $rightPadding . 'overflow-x:scroll !important;flex: 1 1 auto !important;' . $cardHeightStyle . '">'
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

    public function getDashboardTabs(Request $request)
    {
        try {
            $tabDetails = $this->recruitingAnalyticsConfigRepository->getDashboardTabs();
            $tabs = [];
            if (!empty($tabDetails)) {
                foreach ($tabDetails as $key => $item) {
                    $addTab = $this->recruitingAnalyticsConfigRepository->addOrRemoveTabAccordingToPermission($key);

                    if ($addTab) {
                        $data['id'] = $key;
                        $data['name'] = $item;
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

    public function getJobRequisitionAnalytics()
    {
        $chartdata = $this->recruitmentAnalyticsDashboardRepository->JobRequisitionAnalytics();
        return response()->json(array(
            'type' => 'json',
            'widgetTag' => 'widget-job-requisitions',
            'data' => array('chartDetails' => $chartdata),
        ));
    }

    public function getPositionByRegionAnalytics()
    {
        $chartdata = $this->recruitmentAnalyticsDashboardRepository->PositionByRegionAnalytics();
        return response()->json(array(
            'type' => 'json',
            'widgetTag' => 'widget-position-by-region',
            'data' => array('chartDetails' => $chartdata),
        ));

    }

    public function getHighestTurnoverAnalytics()
    {
        $chartdata = $this->recruitmentAnalyticsDashboardRepository->HighestTurnoverAnalytics();
        return response()->json(array(
            'type' => 'json',
            'widgetTag' => 'widget-highest-turnover',
            'data' => array('chartDetails' => $chartdata),
        ));

    }

    public function getPositionByReasonsAnalytics()
    {
        $chartdata = $this->recruitmentAnalyticsDashboardRepository->positionByReasonsAnalytics();

        return response()->json(array(
            'type' => 'json',
            'widgetTag' => 'widget-position-by-reasons',
            'data' => array('chartDetails' => $chartdata),
        ));
    }

    public function getWageByRegionAnalytics()
    {
        $chartdata = $this->recruitmentAnalyticsDashboardRepository->WageByRegionAnalytics();
        return response()->json(array(
            'type' => 'json',
            'widgetTag' => 'widget-wage-by-region',
            'data' => $chartdata,
        ));

    }

    public function getCandidatesAnalytics()
    {
        $chartdata = $this->recruitmentAnalyticsDashboardRepository->candidatesAnalytics();
        return response()->json(array(
            'type' => 'json',
            'widgetTag' => 'widget-candidates',
            'data' => array('chartDetails' => $chartdata),
        ));
    }

    public function getCandidatesRegions()
    {
        $chartdata = $this->recruitmentAnalyticsDashboardRepository->candidatesRegions();
        return response()->json(array(
            'type' => 'json',
            'widgetTag' => 'widget-candidates-regions',
            'data' => array('chartDetails' => $chartdata),
        ));

    }

    public function getCandidatesCertificates()
    {
        $chartdata = $this->recruitmentAnalyticsDashboardRepository->candidatesCertificates();
        return response()->json(array(
            'type' => 'json',
            'widgetTag' => 'widget-candidates-certificates',
            'data' => array('chartDetails' => $chartdata),
        ));

    }

    public function getCandidatesExperiencesByRegions()
    {
        $chartdata = $this->recruitmentAnalyticsDashboardRepository->candidatesExperiencesByRegions();
        return response()->json(array(
            'type' => 'json',
            'widgetTag' => 'widget-candidates-experiences-regions',
            'data' => array('chartDetails' => $chartdata),
        ));
    }

    public function getCandidatesExperiences()
    {
        $chartdata = $this->recruitmentAnalyticsDashboardRepository->candidatesExperiences();
        if (count($chartdata) > 0) {
            return response()->json(array(
                'type' => 'json',
                'widgetTag' => 'widget-candidates-experiences',
                'data' => array('chartDetails' => ['elements' => array_values($chartdata), 'labels' => array_keys($chartdata)]),
            ));
        }

    }

    public function getCandidateWageExpectationsByRegion()
    {
        $chartdata = $this->recruitmentAnalyticsDashboardRepository->candidateWageExpectationsByRegion();
        return response()->json(array(
            'type' => 'json',
            'widgetTag' => 'widget-wage-expectations-by-region',
            'data' => $chartdata,
        ));
    }

    public function getCandidateWageExpectationsByPosition()
    {
        $chartdata = $this->recruitmentAnalyticsDashboardRepository->candidateWageExpectationsByPosition();
        return response()->json(array(
            'type' => 'json',
            'widgetTag' => 'widget-wage-expectations-by-position',
            'data' => $chartdata,
        ));
    }

    public function getCandidateWageByCompetitor()
    {
        $chartdata = $this->recruitmentAnalyticsDashboardRepository->candidateWageByCompetitor();
        return response()->json(array(
            'type' => 'json',
            'widgetTag' => 'widget-wage-by-competitor',
            'data' => array('chartDetails' => $chartdata),
        ));
    }

    public function getCandidateResidentStatusAnalytics()
    {
        $chartdata = $this->recruitmentAnalyticsDashboardRepository->candidateResidentStatusAnalytics();

        return response()->json(array(
            'type' => 'json',
            'widgetTag' => 'widget-candidate-resident-status',
            'data' => array('chartDetails' => $chartdata),
        ));
    }

    public function getCandidateMilitaryExperienceAnalytics()
    {
        $chartDetails = $this->recruitmentAnalyticsDashboardRepository->getCandidateMilitaryExperienceData();
        return response()->json(array(
            'type' => 'json',
            'widgetTag' => 'widget-candidate-military-experience',
            'data' => array('chartDetails' => $chartDetails),
        ));
    }

    public function getGuardDriversLicense()
    {
        $chartdata = $this->recruitmentAnalyticsDashboardRepository->guardDriversLicense();

        return response()->json(array(
            'type' => 'json',
            'widgetTag' => 'widget-guard-drivers-license',
            'data' => array('chartDetails' => $chartdata),
        ));
    }

    public function getAccessToPublicTransit()
    {
        $chartdata = $this->recruitmentAnalyticsDashboardRepository->accessToPublicTransit();

        return response()->json(array(
            'type' => 'json',
            'widgetTag' => 'widget-access-to-public-transport',
            'data' => array('chartDetails' => $chartdata),
        ));
    }

    public function getLimitedTransportation()
    {
        $chartdata = $this->recruitmentAnalyticsDashboardRepository->limitedTransportation();

        return response()->json(array(
            'type' => 'json',
            'widgetTag' => 'widget-limited-transportation',
            'data' => array('chartDetails' => $chartdata),
        ));
    }

    public function getLevelOfLanguageFluencyEnglish()
    {
        $chartdata = $this->recruitmentAnalyticsDashboardRepository->candidateFluencyEnglish();

        return response()->json(array(
            'type' => 'json',
            'widgetTag' => 'widget-candidates-by-level-of-language-fluency-english',
            'data' => array('chartDetails' => $chartdata),
        ));
    }

    public function getLevelOfLanguageFluencyFrench()
    {
        $chartdata = $this->recruitmentAnalyticsDashboardRepository->candidateFluencyFrench();

        return response()->json(array(
            'type' => 'json',
            'widgetTag' => 'widget-candidates-by-level-of-language-fluency-french',
            'data' => array('chartDetails' => $chartdata),
        ));
    }

    public function getCandidatesComputerSkill()
    {
        $chartdata = $this->recruitmentAnalyticsDashboardRepository->candidateComputerSkill();

        return response()->json(array(
            'type' => 'json',
            'widgetTag' => 'widget-candidates-skills-computer',
            'data' => array('chartDetails' => $chartdata),
        ));
    }

    public function getCandidateSoftSkills()
    {
        $chartdata = $this->recruitmentAnalyticsDashboardRepository->candidateSoftSkills();

        return response()->json(array(
            'type' => 'json',
            'widgetTag' => 'widget-candidates-skills-soft-skills',
            'data' => array('chartDetails' => $chartdata),
        ));
    }

    public function getEmploymentEntities()
    {
        $chartdata = $this->recruitmentAnalyticsDashboardRepository->employmentEntities();

        return response()->json(array(
            'type' => 'json',
            'widgetTag' => 'widget-employment-entities',
            'data' => array('chartDetails' => $chartdata),
        ));
    }

    public function getWageByIndustrySector()
    {
        $chartdata = $this->recruitmentAnalyticsDashboardRepository->WageByIndustrySector();
        return response()->json(array(
            'type' => 'json',
            'widgetTag' => 'widget-wage-by-industry-sector',
            'data' => $chartdata,
        ));

    }

    public function getWidgetPlannedOJT()
    {
        $chartdata = $this->recruitmentAnalyticsDashboardRepository->WidgetPlannedOJT();
        return response()->json(array(
            'type' => 'json',
            'widgetTag' => 'widget-planned-ojt',
            'data' => array('chartDetails' => $chartdata),
        ));

    }

    public function getFiredVsConvictedCandidates()
    {
        $chartdata = $this->recruitmentAnalyticsDashboardRepository->firedConvictedCandidates();

        return response()->json(array(
            'type' => 'json',
            'widgetTag' => 'widget-fired-vs-convicted-candidates',
            'data' => array('chartDetails' => $chartdata),
        ));
    }

    public function getCandidatesByCareerInterestInCgl()
    {
        $chartdata = $this->recruitmentAnalyticsDashboardRepository->candidatesByCareerInterestInCgl();

        return response()->json(array(
            'type' => 'json',
            'widgetTag' => 'widget-candidates-by-career-interest-in-cgl',
            'data' => array('chartDetails' => $chartdata),
        ));
    }

    public function getCandidatesByAverageScore()
    {
        $chartdata = $this->recruitmentAnalyticsDashboardRepository->candidatesByAverageScore();

        return response()->json(array(
            'type' => 'json',
            'widgetTag' => 'widget-candidates-by-average-score',
            'data' => array('chartDetails' => $chartdata),
        ));
    }

    public function getLoadingDocuments()
    {
        $chartdata = $this->recruitmentAnalyticsDashboardRepository->loadingDocuments();

        return response()->json(array(
            'type' => 'json',
            'widgetTag' => 'widget-loading-documents',
            'data' => array('chartDetails' => $chartdata),
        ));
    }

    public function getAverageCycleTime()
    {
        $chartdata = $this->recruitmentAnalyticsDashboardRepository->averageCycleTime();

        return response()->json(array(
            'type' => 'json',
            'widgetTag' => 'widget-average-cycle-time',
            'data' => array('chartDetails' => $chartdata),
        ));
    }
}
