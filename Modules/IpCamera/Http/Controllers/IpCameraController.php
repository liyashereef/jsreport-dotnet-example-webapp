<?php

namespace Modules\IpCamera\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\IpCamera\Repositories\Admin\IpCameraConfigurationRepository;
use Modules\IpCamera\Repositories\IpCameraAuthTokenRepository;
use View;

class IpCameraController extends Controller
{

    protected $ipCameraAuthTokenRepository;

    public function __construct(
        IpCameraAuthTokenRepository $ipCameraAuthTokenRepository,
        IpCameraConfigurationRepository $ipCameraConfigurationRepository
    ) {
        $this->ipCameraAuthTokenRepository = $ipCameraAuthTokenRepository;
        $this->ipCameraConfigurationRepository = $ipCameraConfigurationRepository;
    }
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index($start = 0)
    {
        return view('ipcamera::ipCameraWidgetView');
    }

    public function camWidgetFormat($camArr): array
    {
        $camArrCount = count($camArr);

        $rowCount = 1;
        $col1Width = 12;
        $col2Width = 12;
        $col3Width = 12;

        switch ($camArrCount) {
            case 1:
                $rowCount = 1;
                $col1Width = 12;
                $col2Width = 12;
                break;
            case 2:
                $rowCount = 2;
                $col1Width = 12;
                $col2Width = 12;
                break;
            case 3:
                $rowCount = 2;
                $col1Width = 12;
                $col2Width = 6;
                break;
            case 4:
                $rowCount = 2;
                $col1Width = 6;
                $col2Width = 6;
                break;
            case 5:
                $rowCount = 2;
                $col1Width = 4;
                $col2Width = 6;
                break;
            case 6:
                $rowCount = 2;
                $col1Width = 4;
                $col2Width = 4;
                break;
            case 7:
                $rowCount = 2;
                $col1Width = 3;
                $col2Width = 4;
                break;
            case 8:
                $rowCount = 2;
                $col1Width = 3;
                $col2Width = 3;
                break;
            default:
                $rowCount = 3;
                $col1Width = 3;
                $col2Width = 3;
                $col3Width = 3;
                break;
        }
        return array(
            'row' => $rowCount,
            'col1' => $col1Width,
            'col2' => $col2Width,
            'col3' => $col3Width,
        );
    }

    public function getDashboardTabs(Request $request)
    {
        try {
            $tabDetails = $this->ipCameraConfigurationRepository->getDashboardTabs();
            $tabs = [];
            $data['id'] = 0;
            $data['name'] = "General";
            array_push($tabs, $data);
            if (!empty($tabDetails)) {
                foreach ($tabDetails as $key => $item) {
                    $addTab = $this->ipCameraConfigurationRepository
                        ->addOrRemoveTabAccordingToPermission($key);

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

    public function getDashboardTabDetails(Request $request)
    {
        try {
            $tabId = $request->get('tab_id');
            $parentDivWidth = $request->get('parentDivWidth');
            $parentDivHeight = $request->get('parentDivHeight');
            if ($tabId == 0) {
                // $html = '<table id="tabuid_0" class="dashboard-tables" style="table-layout:fixed;margin-bottom:0px !important;width:100% !important;height:100% !important;">';

                // $html .= '<tr><td>Test Content</td></tr>';
                // $html .= '<\table>';
                $ipCameraArray = $this->ipCameraConfigurationRepository->getActiveCameras();
                $ipCameras = $ipCameraArray[0];
                $ipCameraUrl = $ipCameraArray[1];
                $html = \View::make('partials.welcome.ipcamera_generalview')
                    ->with(compact('ipCameras', 'ipCameraUrl'))
                    ->render();
                $tabView = $html;
            } else {
                $tabInfo = $this->ipCameraConfigurationRepository->getTabDetailsByTabId($tabId);;
                $tabUid = ('tabuid_' . $tabInfo['id']);
                $moduleHierarchy = $this->ipCameraConfigurationRepository->fetchModuleLayoutHierarchyByTab($tabId, $parentDivWidth, $parentDivHeight);
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
                    $rightPadding = '';
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
                            . '<iframe src="' . $value['api_url'] . '" id="test tbl_responsive_' . $spanId . '" data-parent-tbl="' . $tabInfo['id'] . '" class="table-responsive widget-div dasboard-card-body" style="' . $rightPadding . 'overflow-x:scroll !important;flex: 1 1 auto !important;' . $cardHeightStyle . '">'
                            . '</iframe>'
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
            }



            return response()->json([
                'html' => $tabView,
            ]);
        } catch (\Exception $e) {
            $message = config('globals.landingPageExceptionCustomErrorMessageText');
            if (config('app.env') !== 'production') {
                $message .= '<br/>' . $e->getMessage() . ' on ' . $e->getFile() . ' at ' . $e->getLine() . '<br/> ' . $e->getTraceAsString();
            }
            $html = '<table><tbody>';
            $html .= '<tr style="vertical-align: middle;" valign="middle">';
            $html .= '<th class="text-center" style="border:0px;text-align:center;vertical-align:middle;">';
            $html .= $message;
            $html .= '</th></tr></tbody></table>';
            return response()->json([
                'html' => $html,
            ]);
        }
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {
        return view('ipcamera::create');
    }

    /**
     * Store a newly created resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function store(Request $request)
    {
    }

    /**
     * Show the specified resource.
     * @return Response
     */
    public function show()
    {
        return view('ipcamera::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @return Response
     */
    public function edit()
    {
        return view('ipcamera::edit');
    }

    /**
     * Update the specified resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function update(Request $request)
    {
    }

    /**
     * Remove the specified resource from storage.
     * @return Response
     */
    public function destroy()
    {
    }
}
