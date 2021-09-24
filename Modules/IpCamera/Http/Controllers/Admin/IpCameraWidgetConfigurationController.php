<?php

namespace Modules\IpCamera\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use App\Services\HelperService;
use Modules\Admin\Repositories\LandingPageRepository;
use Modules\IpCamera\Repositories\Admin\IpCameraConfigurationRepository;
use Modules\IpCamera\Repositories\Admin\IpCameraRepository;
use View;

class IpCameraWidgetConfigurationController extends Controller
{
    protected $helperService, $landingPageRepository, $ipCameraConfigurationRepository, $ipCameraRepository;

    public const IP_CAMERA = 'IpCamera';

    /**
     * Create Repository instance.
     * @param
     * @return void
     */
    public function __construct(
        HelperService $helperService,
        LandingPageRepository $landingPageRepository,
        IpCameraConfigurationRepository $ipCameraConfigurationRepository,
        IpCameraRepository $ipCameraRepository

        )
    {
        $this->helperService = $helperService;
        $this->landingPageRepository = $landingPageRepository;
        $this->ipCameraConfigurationRepository = $ipCameraConfigurationRepository;
        $this->ipCameraRepository = $ipCameraRepository;
    }
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        return view('ipcamera::admin.ip_camera_widget_configuration_view');
    }

    /**
     * Show the specified resource.
     * @return Response
     */
    public function getIpCameraConfigurationDetails(Request $request)
    {
        $tab = array();
        $tabDetails = $this->ipCameraConfigurationRepository->getAllTabs();
        $widgetModules = $this->ipCameraRepository->getIpCameraTabs();

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

            $moduleByTabId = $this->ipCameraConfigurationRepository->getModuleByTab($tabValue->id);
            $dynamicLiElements = [];
            foreach ($moduleByTabId as $existingTabDetail) {
                if(array_key_exists($existingTabDetail->ip_camera_id, $customModuleLIst)){
                    $moduleName = $customModuleLIst[$existingTabDetail->ip_camera_id];
                }else{
                    $moduleName = null;
                }
                $model = "IpCamera";
                $dynamicLiElements[$existingTabDetail->landing_page_widget_layout_detail_id] = $moduleName;
            }

            foreach ($moduleByTabId as $keyModule => $valueModule) {
                $modelName = $valueModule->landing_page_module_widget_type;
                $moduleId = $valueModule->ip_camera_id;
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
        return view('ipcamera::admin.new_configuration', compact('widgetLayouts', 'tabDetails'));
    }

    private function generateTabDetailsArrayStructure($tabId)
    {
        $tabDetails = [];
        $tabObject = $this->ipCameraConfigurationRepository->getTabDetailsByTabId($tabId);
        if (!empty($tabObject)) {
            $customerId = $tabObject->customer_id;
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
                if (!in_array($tabDetail->landing_page_module_widget_type . '_' . $tabDetail->ip_camera_id, $moduleIdArray)) {
                    $fieldsList[$tabDetail->id] = $this->ipCameraConfigurationRepository->createFieldsListByModuleParams($tabDetail->landing_page_module_widget_type, $tabDetail->ip_camera_id);
                    $moduleIdArray[] = $tabDetail->landing_page_module_widget_type . '_' . $tabDetail->ip_camera_id;
                }
                foreach ($tabDetail->widgetFields as $widgetField) {
                    $widgetFieldArray[$widgetField->ip_camera_configuration_tab_detail_id][] = $widgetField->field_system_name;
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
                            $shiftModuleObject = "IpCamera";
                            $tabDetailData = $this->ipCameraConfigurationRepository->getTabDetailsById($tadDetailId);
                            $moduleType = IpCameraWidgetConfigurationController::IP_CAMERA;
                            if ($tabDetailData->landing_page_module_widget_type != IpCameraWidgetConfigurationController::IP_CAMERA) {
                                $moduleType = "IpCamera";
                            } else {
                                $shiftModuleObject = $this->ipCameraRepository->getIpCameraTabs($tabDetailData->ip_camera_id);
                            }
                            if (!empty($shiftModuleObject)) {
                                $fieldsList[$tadDetailId][$ky]['module_id'] = $tabDetailData->ip_camera_id;
                                $fieldsList[$tadDetailId][$ky]['key'] = $ky;
                                $fieldsList[$tadDetailId][$ky]['default_sort'] = $widgetLayoutDetailIdArray[$tabDetailData->landing_page_widget_layout_detail_id][$field['field_system_name']]['default_sort'];
                                $fieldsList[$tadDetailId][$ky]['default_sort_order'] = $widgetLayoutDetailIdArray[$tabDetailData->landing_page_widget_layout_detail_id][$field['field_system_name']]['default_sort_order'];
                                $fieldsList[$tadDetailId][$ky]['field_display_name'] = $widgetLayoutDetailIdArray[$tabDetailData->landing_page_widget_layout_detail_id][$field['field_system_name']]['field_display_name'];
                                $fieldsList[$tadDetailId][$ky]['layout_detail_id'] = $tabDetailData->landing_page_widget_layout_detail_id;
                                $tabDetails['hidden_module_array'][$tabDetailData->ip_camera_id . '_' . $moduleType][] = $fieldsList[$tadDetailId][$ky];
                            }
                        }
                    }
                }
            }
        }
        return $tabDetails;
    }

    public function getWidgetLayoutDetails(Request $request)
    {

        $html = '<table class="table table-responsive no-footer dtr-inline"><thead>';
        $tabStructure = $request->default_tab_structure;
        $tabId = $request->tab_id;
        $widgetCategory = $request->has('widget_category') ? $request->widget_category : null;
        $widgetModules = $this->ipCameraRepository->getIpCameraTabs();
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
                    $modulesList[] = ['model' => 'IpCamera',
                        'id' => $widgetModule->id,
                        'name' => $widgetModule->name,
                        'api_type' => null
                    ];
                    $customModuleLIst[$widgetModule->id] = $widgetModule->name;
                    $customModuleTypeList[$widgetModule->id] = $widgetModule->api_type;
            }
        }

        $widgetLayoutId = $request->get('widget_layout_id');
        $widgetLayoutObjects = $this->ipCameraConfigurationRepository->fetchTabByParam($tabId, $widgetLayoutId);

        $dynamicLiElements = [];
        if (!empty($tabId)) {
            $existingTab = $this->ipCameraConfigurationRepository->getTabDetailsByTabId($tabId);
            if (!empty($existingTab) && (count($widgetLayoutObjects) > 0)) {
                $existingTabDetails = $existingTab->tabDetails;
                $existingModuleList = [];
                foreach ($existingTabDetails as $existingTabDetail) {
                    if (array_key_exists($existingTabDetail->ip_camera_id, $customModuleLIst)) {
                        $moduleName = $customModuleLIst[$existingTabDetail->ip_camera_id];
                        $model = "IpCamera";
                        $apiType = $customModuleTypeList[$existingTabDetail->ip_camera_id];

                        $existingModuleList[] = ['model' => $model, 'id' => $existingTabDetail->ip_camera_id, 'name' => $moduleName, 'api_type' => $apiType];
                        $dynamicLiElements[$existingTabDetail->landing_page_widget_layout_detail_id] = ['model' => $model, 'id' => $existingTabDetail->ip_camera_id, 'name' => $moduleName, 'api_type' => $apiType];
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

        $widgetLayoutDetails = $this->ipCameraConfigurationRepository->getWidgetLayoutDetailsByLayout($widgetLayoutId);

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
        $bodyHtml = View::make('ipcamera::admin.layout_detail')->with(compact(['html', 'modulesList', 'tabDetails']))->render();
        return response()->json([
            'html' => $bodyHtml,
        ]);
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
            $status = $this->ipCameraConfigurationRepository->validateTabName($tabName, $tabId);
            if ($status == 1) {
                return response()->json(['status_msg' => 'Error', 'msg' => 'Duplicate tab name', 'status' => 'error']);
            }
            $tab = $this->ipCameraConfigurationRepository->saveTab($tabName, $customerId, $layoutId, $seqNo, $defaultStructure, $tabId);
            $fieldDetails = $this->ipCameraConfigurationRepository->saveTabFieldDetails($tab, $resultArray);
            \DB::commit();
            return response()->json(['status_msg' => 'Success', 'msg' => 'Tab configurations has been saved successfully', 'status' => 'success']);
        } catch (Exception $e) {
            \DB::rollBack();
            return response()->json(['status_msg' => 'Error', 'msg' => 'Failed to save tab configurations', 'status' => 'error']);
        }
    }

    public function removeTab(Request $request)
    {
        try {
            $tabId = $request->input('tabid');
            \DB::beginTransaction();
            $data = $this->ipCameraConfigurationRepository->deleteTab($tabId);
            \DB::commit();
            return response()->json(['status_msg' => 'Success', 'msg' => 'Tab deleted successfully', 'status' => 'success']);
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json(array('status_msg' => 'Error', 'msg' => $e->getMessage() . " at " . $e->getLine() . " in " . $e->getFile(), 'status' => 'error'));
        }
    }

    public function saveTabActiveStatus(Request $request)
    {
        try {
            $tabid = $request->input('tabid');
            $status = $request->input('status');
            $data = $this->ipCameraConfigurationRepository->updateTabActiveStatus($tabid, $status);
            return response()->json(['status_msg' => 'Success', 'msg' => 'Tab status has been updated successfully', 'status' => 'success']);
        } catch (\Exception $e) {
            return response()->json(array('status_msg' => 'Error', 'msg' => $e->getMessage() . " at " . $e->getLine() . " in " . $e->getFile(), 'status' => 'error'));
        }
    }

    public function getAllWidgetModules(Request $request)
    {
        $customerId = $request->customer_id;
        $tabId = $request->tab_id;
        $customerIndependentWidgetsOnly = ($request->default_structure == "true") ? true : false;
        if (!empty($tabId)) {
            $tabDetails = $this->generateTabDetailsArrayStructure($tabId);
            $customerId = $tabDetails['customer_id'];
        }
        $widgetModules = $this->landingPageRepository->getWidgetModules();

        $shiftModules = [];
        if (!empty($customerId) && (!$customerIndependentWidgetsOnly)) {
            $shiftModules = $this->shiftModuleRepository->getAll($customerId, true);
        }

        $shiftModuleList = [];
        $customModuleLIst = [];
        $modulesList = [];
        if (!empty($widgetModules)) {
            foreach ($widgetModules as $widgetModule) {
                $dataGenerated = ['model' => 'LandingPageModuleWidget', 'id' => $widgetModule->id, 'name' => $widgetModule->name, 'api_type' => $widgetModule->api_type];
                $customModuleLIst[$widgetModule->id] = $dataGenerated;

                if (($customerIndependentWidgetsOnly && $widgetModule->api_type == 2) || ($widgetModule->api_type == 0) || ((!$customerIndependentWidgetsOnly) && ($widgetModule->api_type == 1))) {
                    $modulesList[] = $dataGenerated;
                }
            }
        }

        if (!empty($shiftModules)) {
            foreach ($shiftModules as $shiftModule) {
                $dataGenerated = ['model' => LandingPageController::SHIFT_MODULE_TYPE, 'id' => $shiftModule->id, 'name' => $shiftModule->module_name, 'api_type' => 1];
                $shiftModuleList[$shiftModule->id] = $dataGenerated;
                $modulesList[] = $dataGenerated;
            }
        }

        $moduleByTabId = $this->landingPageRepository->getModuleByTab($tabId);
        $dynamicLiElements = [];
        foreach ($moduleByTabId as $existingTabDetail) {
            if ($existingTabDetail->landing_page_module_widget_type === LandingPageController::SHIFT_MODULE_TYPE) {
                $moduleObject = $shiftModuleList[$existingTabDetail->landing_page_module_widget_id];
            } else {
                $moduleObject = $customModuleLIst[$existingTabDetail->landing_page_module_widget_id];
            }

            $dynamicLiElements[$existingTabDetail->landing_page_widget_layout_detail_id] = $moduleObject;
        }

        if (count($modulesList) > 0) {
            uasort($modulesList, function ($a, $b) {
                return strtoupper($a["name"]) > strtoupper($b["name"]);
            });
        }

        return response()->json([
            'modules' => $modulesList,
            'dropped_modules' => $dynamicLiElements,
        ]);
    }

    public function getCustomTableFieldsByModule(Request $request)
    {
        $moduleId = $request->module_id;
        $modelName = $request->model_name;
        $fieldsList = $this->ipCameraConfigurationRepository->createFieldsListByModuleParams($modelName, $moduleId);
        $bodyHtml = View::make('ipcamera::admin.fields_listing')->with(compact(['fieldsList', 'moduleId', 'modelName']))->render();
        return response()->json([
            'html' => $bodyHtml,
        ]);
    }
}
