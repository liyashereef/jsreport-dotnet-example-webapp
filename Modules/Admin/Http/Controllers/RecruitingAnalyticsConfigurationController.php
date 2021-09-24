<?php

namespace Modules\Admin\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\HelperService;
use Illuminate\Http\Request;
use Modules\Admin\Repositories\LandingPageRepository;
use Modules\Admin\Repositories\RecruitingAnalyticsConfigurationRepository;
use View;

class RecruitingAnalyticsConfigurationController extends Controller
{
    protected $helperService, $landingPageRepository, $recruitingAnalyticsConfigRepository;

    /**
     * Create Repository instance.
     * @param
     * @return void
     */
    public function __construct(HelperService $helperService, LandingPageRepository $landingPageRepository, RecruitingAnalyticsConfigurationRepository $recruitingAnalyticsConfigRepository)
    {
        $this->helperService = $helperService;
        $this->landingPageRepository = $landingPageRepository;
        $this->recruitingAnalyticsConfigRepository = $recruitingAnalyticsConfigRepository;
    }

    /**
     * Load the resource listing Page
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return view('admin::recruitment-dashboard.index');
    }

    public function getRecruitingAnalyticsDetails(Request $request)
    {
        $tab = array();
        $tabDetails = $this->recruitingAnalyticsConfigRepository->getAllTabs();
        $widgetModules = $this->landingPageRepository->getWidgetModules('RecruitmentWidget');
        $customModuleLIst = [];
        if (!empty($widgetModules)) {
            foreach ($widgetModules as $widgetModule) {
                $customModuleLIst[$widgetModule->id] = $widgetModule->name;
            }
        }

        foreach ($tabDetails as $tabKey => $tabValue) {
            $tab[$tabKey]['id'] = $tabValue->id;
            $tab[$tabKey]['active'] = $tabValue->active;
            $tab[$tabKey]['default_tab_structure'] = $tabValue->default_tab_structure;
            $tab[$tabKey]['tab_name'] = $tabValue->tab_name;

            $moduleByTabId = $this->recruitingAnalyticsConfigRepository->getModuleByTab($tabValue->id);
            $dynamicLiElements = [];
            foreach ($moduleByTabId as $existingTabDetail) {
                $moduleName = $customModuleLIst[$existingTabDetail->landing_page_module_widget_id];
                $model = "LandingPageModuleWidget";
                $dynamicLiElements[$existingTabDetail->landing_page_widget_layout_detail_id] = $moduleName;
            }

            foreach ($moduleByTabId as $keyModule => $valueModule) {
                $modelName = $valueModule->landing_page_module_widget_type;
                $moduleId = $valueModule->landing_page_module_widget_id;
                $moduleFields = $valueModule->widgetFields;

                $fieldsList = [];
                foreach ($moduleFields as $key => $value) {
                    $fieldsList[] = [
                        'field_display_name' => $value['field_display_name'],
                        'default_sort' => $value['default_sort'],
                        'visible' => $value['visible'],
                    ];
                }

                $tab[$tabKey]['tabDetails'][$dynamicLiElements[$valueModule->landing_page_widget_layout_detail_id]][] = $fieldsList;
            }
        }
        return $tab;
    }

    public function add(Request $request)
    {
        $tabId = $request->tab_id;
        $tabDetails = [];
        if (!empty($tabId)) {
            $tabDetails = $this->generateTabDetailsArrayStructure($tabId);
        }
        $widgetLayouts = $this->landingPageRepository->getAllWidgetLayouts();
        return view('admin::recruitment-dashboard.new_configuration', compact('widgetLayouts', 'tabDetails'));
    }

    public function getWidgetLayoutDetails(Request $request)
    {
        $html = '<table class="table table-responsive no-footer dtr-inline"><thead>';
        $tabStructure = $request->default_tab_structure;
        $tabId = $request->tab_id;
        $widgetCategory = $request->has('widget_category') ? $request->widget_category : null;
        $widgetModules = $this->recruitingAnalyticsConfigRepository->getWidgetModules($widgetCategory);
        $tabDetails = [];
        if (!empty($tabId)) {
            $tabDetails = $this->generateTabDetailsArrayStructure($tabId);
        }

        $modulesList = [];
        $shiftModuleList = [];
        $customModuleLIst = [];
        $customModuleTypeList = [];
        if (!empty($widgetModules)) {
            foreach ($widgetModules as $widgetModule) {
                if ($widgetModule->api_type == 2) {
                    $modulesList[] = ['model' => 'LandingPageModuleWidget',
                        'id' => $widgetModule->id,
                        'name' => $widgetModule->name,
                        'api_type' => $widgetModule->api_type];
                    $customModuleLIst[$widgetModule->id] = $widgetModule->name;
                    $customModuleTypeList[$widgetModule->id] = $widgetModule->api_type;
                }
            }
        }

        $widgetLayoutId = $request->get('widget_layout_id');
        $widgetLayoutObjects = $this->recruitingAnalyticsConfigRepository->fetchTabByParam($tabId, $widgetLayoutId);

        $dynamicLiElements = [];
        if (!empty($tabId)) {
            $existingTab = $this->recruitingAnalyticsConfigRepository->getTabDetailsByTabId($tabId);
            if (!empty($existingTab) && (count($widgetLayoutObjects) > 0)) {
                $existingTabDetails = $existingTab->tabDetails;

                $existingModuleList = [];
                foreach ($existingTabDetails as $existingTabDetail) {
                    if (array_key_exists($existingTabDetail->landing_page_module_widget_id, $customModuleLIst)) {
                        $moduleName = $customModuleLIst[$existingTabDetail->landing_page_module_widget_id];
                        $model = "LandingPageModuleWidget";
                        $apiType = $customModuleTypeList[$existingTabDetail->landing_page_module_widget_id];

                        $existingModuleList[] = ['model' => $model, 'id' => $existingTabDetail->landing_page_module_widget_id, 'name' => $moduleName, 'api_type' => $apiType];
                        $dynamicLiElements[$existingTabDetail->landing_page_widget_layout_detail_id] = ['model' => $model, 'id' => $existingTabDetail->landing_page_module_widget_id, 'name' => $moduleName, 'api_type' => $apiType];
                    }
                }

                $modulesList = array_map('unserialize', array_diff(array_map('serialize', $modulesList), array_map('serialize', $existingModuleList)));
            }
        }

        if (count($modulesList) > 0) {
            uasort($modulesList, function ($a, $b) {
                return strtoupper($a["name"]) > strtoupper($b["name"]);
            });
        }

        $widgetLayoutDetails = $this->recruitingAnalyticsConfigRepository->getWidgetLayoutDetailsByLayout($widgetLayoutId);

        $rowIndex = 0;
        $columnIndex = 0;
        foreach ($widgetLayoutDetails as $widgetLayoutDetail) {
            $selectedRow = $widgetLayoutDetail->row_index;
            $selectedColumn = $widgetLayoutDetail->column_index;

            if ($selectedRow != $rowIndex && $rowIndex != 0) {
                $html .= '</tr>';
            }

            if ($selectedRow != $rowIndex) {
                $html .= '<tr>';
                $rowIndex = $selectedRow;
            }

            $styleHeight = ($widgetLayoutDetail->rowspan > 1) ? 'height:' . ($widgetLayoutDetail->rowspan * 117) . 'px;' : '';

            if (!empty($dynamicLiElements) && array_key_exists($widgetLayoutDetail->id, $dynamicLiElements) && (count($widgetLayoutObjects) > 0)) {
                $mId = $dynamicLiElements[$widgetLayoutDetail->id]['id'];
                $mName = $dynamicLiElements[$widgetLayoutDetail->id]['name'];
                $mModel = $dynamicLiElements[$widgetLayoutDetail->id]['model'];
                $mApiType = $dynamicLiElements[$widgetLayoutDetail->id]['api_type'];

                $html .= '<th style="position:relative;" rowspan="' . $widgetLayoutDetail->rowspan . '" colspan="' . $widgetLayoutDetail->colspan . '">'
                . '<ol class="module-droppable" data-draggable="target" data-id="' . $widgetLayoutDetail->id . '"  style="width:100%;' . $styleHeight . '" data-class="module-droppable">'
                    . ' <li data-draggable="item" class="li-droppable" data-customer-widget="' . $mApiType . '" data-widget-name="' . $mName . '"  data-model="' . $mModel . '" data-id="' . $mId . '" onclick="load_field_details(event, ' . $mId . ', \'' . $mModel . '\')">' . $mName . '&nbsp;&nbsp;<span class="close-li" onclick="deleteItemSelected(event);">x</span></li>'
                    . '</ol></th>';
            } else {
                $html .= '<th style="position:relative;" rowspan="' . $widgetLayoutDetail->rowspan . '" colspan="' . $widgetLayoutDetail->colspan . '">'
                . '<ol class="module-droppable" data-draggable="target" data-id="' . $widgetLayoutDetail->id . '"  style="width:100%;' . $styleHeight . '" data-class="module-droppable"></ol>'
                    . '</th>';
            }
        }
        $html .= '</tr></thead></table>';

        $bodyHtml = View::make('admin::recruitment-dashboard.layout_detail')->with(compact(['html', 'modulesList', 'tabDetails']))->render();
        return response()->json([
            'html' => $bodyHtml,
        ]);
    }

    private function generateTabDetailsArrayStructure($tabId)
    {
        $tabDetails = [];
        $tabObject = $this->recruitingAnalyticsConfigRepository->getTabDetailsByTabId($tabId);
        if (!empty($tabObject)) {
            $tabDetails['id'] = $tabObject->id;
            $tabDetails['tab_name'] = $tabObject->tab_name;
            $tabDetails['customer_id'] = $tabObject->customer_id;
            $tabDetails['seq_no'] = $tabObject->seq_no;
            $tabDetails['landing_page_widget_layout_id'] = $tabObject->landing_page_widget_layout_id;
            $tabDetails['default_tab_structure'] = $tabObject->default_tab_structure;

            $moduleIdArray = [];
            $widgetFieldArray = [];
            $widgetLayoutDetailIdArray = [];
            $detailObjects = $tabObject->tabDetails;
            foreach ($detailObjects as $tabDetail) {
                if (!in_array($tabDetail->landing_page_module_widget_type . '_' . $tabDetail->landing_page_module_widget_id, $moduleIdArray)) {
                    $fieldsList[$tabDetail->id] = $this->recruitingAnalyticsConfigRepository->createFieldsListByModuleParams($tabDetail->landing_page_module_widget_type, $tabDetail->landing_page_module_widget_id);
                    $moduleIdArray[] = $tabDetail->landing_page_module_widget_type . '_' . $tabDetail->landing_page_module_widget_id;
                }

                foreach ($tabDetail->widgetFields as $widgetField) {
                    $widgetFieldArray[$widgetField->recruiting_analytics_tab_detail_id][] = $widgetField->field_system_name;
                    $widgetLayoutDetailIdArray[$tabDetail->landing_page_widget_layout_detail_id][$widgetField->field_system_name] = [
                        'default_sort' => $widgetField->default_sort,
                        'default_sort_order' => $widgetField->default_sort_order,
                        'field_display_name' => $widgetField->field_display_name,
                    ];
                }
            }

            if (!empty($widgetFieldArray)) {
                $tabDetails['hidden_module_array'] = [];
                foreach ($fieldsList as $tadDetailId => $fields) {
                    foreach ($fields as $ky => $field) {
                        if (!array_key_exists('field_system_name', $field)) {
                            continue;
                        }
                        if (!in_array($field['field_system_name'], $widgetFieldArray[$tadDetailId])) {
                            unset($fieldsList[$tadDetailId][$ky]);
                        } else {
                            $shiftModuleObject = "LandingPageModuleWidget";
                            $tabDetailData = $this->recruitingAnalyticsConfigRepository->getTabDetailsById($tadDetailId);
                            $moduleType = "LandingPageModuleWidget";

                            if (!empty($shiftModuleObject)) {
                                $fieldsList[$tadDetailId][$ky]['module_id'] = $tabDetailData->landing_page_module_widget_id;
                                $fieldsList[$tadDetailId][$ky]['key'] = $ky;
                                $fieldsList[$tadDetailId][$ky]['default_sort'] = $widgetLayoutDetailIdArray[$tabDetailData->landing_page_widget_layout_detail_id][$field['field_system_name']]['default_sort'];
                                $fieldsList[$tadDetailId][$ky]['default_sort_order'] = $widgetLayoutDetailIdArray[$tabDetailData->landing_page_widget_layout_detail_id][$field['field_system_name']]['default_sort_order'];
                                $fieldsList[$tadDetailId][$ky]['field_display_name'] = $widgetLayoutDetailIdArray[$tabDetailData->landing_page_widget_layout_detail_id][$field['field_system_name']]['field_display_name'];
                                $fieldsList[$tadDetailId][$ky]['layout_detail_id'] = $tabDetailData->landing_page_widget_layout_detail_id;
                                $tabDetails['hidden_module_array'][$tabDetailData->landing_page_module_widget_id . '_' . $moduleType][] = $fieldsList[$tadDetailId][$ky];
                            }
                        }
                    }
                }
            }
        }
        return $tabDetails;
    }

    public function saveTabDetails(Request $request)
    {
        $resultArray = $request->result_array;
        $customerId = $request->customer_id;
        $tabName = $request->tabName;
        $layoutId = $request->layoutId;
        $seqNo = $request->seq_no;
        $defaultStructure = $request->default_tab_structure;
        $tabId = $request->tab_id;

        try {
            \DB::beginTransaction();
            $status = $this->recruitingAnalyticsConfigRepository->validateTabName($tabName, $tabId);
            if ($status == 1) {
                return response()->json(['status_msg' => 'Error', 'msg' => 'Duplicate tab name', 'status' => 'error']);
            }
            $tab = $this->recruitingAnalyticsConfigRepository->saveTab($tabName, $customerId, $layoutId, $seqNo, $defaultStructure, $tabId);
            $fieldDetails = $this->recruitingAnalyticsConfigRepository->saveTabFieldDetails($tab, $resultArray);
            \DB::commit();
            return response()->json(['status_msg' => 'Success', 'msg' => 'Tab configurations has been saved successfully', 'status' => 'success']);
        } catch (Exception $e) {
            \DB::rollBack();
            return response()->json(['status_msg' => 'Error', 'msg' => 'Failed to save tab configurations', 'status' => 'error']);
        }
    }

    public function saveTabActiveStatus(Request $request)
    {
        try {
            $tabid = $request->input('tabid');
            $status = $request->input('status');
            $data = $this->recruitingAnalyticsConfigRepository->updateTabActiveStatus($tabid, $status);
            return response()->json(['status_msg' => 'Success', 'msg' => 'Tab status has been updated successfully', 'status' => 'success']);
        } catch (\Exception $e) {
            return response()->json(array('status_msg' => 'Error', 'msg' => $e->getMessage() . " at " . $e->getLine() . " in " . $e->getFile(), 'status' => 'error'));
        }
    }

    public function removeTab(Request $request)
    {
        try {
            $tabId = $request->input('tabid');
            \DB::beginTransaction();
            $data = $this->recruitingAnalyticsConfigRepository->deleteTab($tabId);
            \DB::commit();
            return response()->json(['status_msg' => 'Success', 'msg' => 'Tab deleted successfully', 'status' => 'success']);
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json(array('status_msg' => 'Error', 'msg' => $e->getMessage() . " at " . $e->getLine() . " in " . $e->getFile(), 'status' => 'error'));
        }
    }
}
