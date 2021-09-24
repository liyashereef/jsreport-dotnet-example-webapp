<?php

namespace Modules\IpCamera\Repositories\Admin;

use App\Services\HelperService;
use Carbon\Carbon;
use Auth;
use Modules\Admin\Models\LandingPageWidgetLayoutDetail;
use Modules\IpCamera\Models\IpCamera;
use Modules\IpCamera\Models\IpCameraConfigurationTab;
use Modules\IpCamera\Models\IpCameraConfigurationTabDetail;
use Modules\IpCamera\Models\IpCameraConfigurationWidgetField;
use Modules\IpCamera\Repositories\IpCameraAuthTokenRepository;

class IpCameraConfigurationRepository
{
    /**
     * The Model instance.
     * ''
     * @var \Illuminate\Database\Eloquent\Model
     */
    public const IP_CAMERA = 'IpCamera';
    protected $model;
    /**
     * @var HelperService
     */
    private $helperService;

    /**
     * Create a new  instance.
     *
     * @param IpCameraConfigurationTabs $ipCameraConfigurationTabModel
     * @param HelperService $helperService
     */
    public function __construct(
        IpCamera $ipCameraModuleWidget,
        IpCameraConfigurationTab $ipCameraConfigurationTabModel,
        IpCameraConfigurationTabDetail $IpCameraConfigurationTabDetailModel,
        IpCameraConfigurationWidgetField $ipCameraConfigurationWidgetField,
        LandingPageWidgetLayoutDetail $landingPageWidgetLayoutDetail,
        HelperService $helperService,
        IpCameraAuthTokenRepository $ipCameraAuthTokenRepository
    ) {
        $this->ipCameraModuleWidget = $ipCameraModuleWidget;
        $this->ipCameraTab = $ipCameraConfigurationTabModel;
        $this->IpCameraTabDetail = $IpCameraConfigurationTabDetailModel;
        $this->landingPageWidgetLayoutDetail = $landingPageWidgetLayoutDetail;
        $this->ipCameraConfigurationWidgetField = $ipCameraConfigurationWidgetField;
        $this->ipCameraAuthTokenRepository = $ipCameraAuthTokenRepository;
        $this->helperService = $helperService;
    }

    public function getAllTabs()
    {
        return $this->ipCameraTab->with(['tabDetails'])
            ->select('id', 'tab_name', 'seq_no', 'default_tab_structure', 'active')
            ->orderBy('seq_no')
            ->get();
    }

    public function getModuleByTab($tabId)
    {
        return  $this->IpCameraTabDetail->with(['moduleWidgetName', 'widgetFields'])->where('ip_camera_configuration_tab_id', $tabId)->get();
    }

    public function getTabDetailsByTabId($tabId)
    {
        return $this->ipCameraTab->with(['tabDetails', 'widgetLayouts'])->find($tabId);
    }

    public function fetchTabByParam($tabId, $layoutId)
    {
        return $this->ipCameraTab->with(['tabDetails'])
            ->where('id', $tabId)
            ->where('landing_page_widget_layout_id', $layoutId)
            ->get();
    }

    public function getActiveCameras()
    {
        $ipCameras = IpCamera::where([
            "enabled" => 1
        ])->get();
        $cameraArray = [];
        foreach ($ipCameras as $ipCamera) {
            $id = $ipCamera->id;
            $apiUrl = config('globals.ip_cam_ms_ip') . config('globals.ip_cam_view_path') . $this->ipCameraAuthTokenRepository->getIpCameraToken($id);
            $cameraArray[$id] = $apiUrl;
        }
        return [$ipCameras, $cameraArray];
    }

    public function getWidgetLayoutDetailsByLayout($widgetLayoutId)
    {
        return $this->landingPageWidgetLayoutDetail->where('landing_page_widget_layout_id', $widgetLayoutId)->get();
    }

    public function validateTabName($tabName, $tabId)
    {
        $tabName = ucwords(strtolower($tabName));
        $qry = $this->ipCameraTab->where('tab_name', $tabName)
            ->where('deleted_at', null);

        if (!empty($tabId)) {
            $tabId = (int) $tabId;
            $qry = $qry->where('id', '!=', $tabId);
        }

        $count = $qry->count();
        if ($count > 0) {
            return 1;
        }
        return 0;
    }

    public function saveTab($tabName, $customerId, $layoutId, $seqNo, $defaultStructure, $tabId = '')
    {
        $data = [
            'tab_name' => $tabName,
            'landing_page_widget_layout_id' => $layoutId,
            'seq_no' => $seqNo,
            'default_tab_structure' => $defaultStructure,
            'active' => true,
            'created_by' => \Auth::user()->id,
        ];

        if (!empty($tabId)) {
            $tab = $this->ipCameraTab->updateOrCreate(array('id' => $tabId), $data);
        } else {
            $tab = $this->ipCameraTab->updateOrCreate($data);
        }
        return $tab;
    }

    public function saveTabFieldDetails($tab, $resultArray)
    {
        $fieldDetails = [];
        if (count($resultArray) > 0) {
            $tabDetailRemovedIds = $this->removeTabDetailsByTab($tab->id);
            if (!empty($tabDetailRemovedIds)) {
                $widgetRemoveStatus = $this->removeWidgetFieldsByTab($tabDetailRemovedIds);
            }

            foreach ($resultArray as $result) {
                foreach ($result as $data) {
                    $tabDetail = [
                        'ip_camera_configuration_tab_id' => $tab->id,
                        'landing_page_widget_layout_detail_id' => $data['layout_detail_id'],
                        'ip_camera_id' => $data['module_id'],
                        'landing_page_module_widget_type' => 'IpCamera',
                        'created_by' => \Auth::user()->id,
                    ];
                    $tabDetails = $this->IpCameraTabDetail->updateOrCreate($tabDetail);

                    if (!empty($tabDetails)) {
                        if (!array_key_exists('permission_text', $data)) {
                            $data['permission_text'] = "";
                        }

                        $dataArr = [
                            'ip_camera_configuration_tab_detail_id' => $tabDetails->id,
                            'field_display_name' => $data['display_name'],
                            'field_system_name' => '-',
                            'default_sort' => $data['enable_sort'],
                            'default_sort_order' => isset($data['sort_order']) ? $data['sort_order'] : 0,
                            'visible' => (($data['visible'] == "true") ? 1 : ($data['visible'] == "false")) ? 0 : $data['visible'],
                            'created_by' => \Auth::user()->id,
                            'permission_text' => $data['permission_text'],
                        ];
                        $fieldDetails = $this->ipCameraConfigurationWidgetField->updateOrCreate($dataArr);
                    }
                }
            }
        }
        return $fieldDetails;
    }

    public function removeTabDetailsByTab($tabId)
    {
        $qry = $this->IpCameraTabDetail->where('ip_camera_configuration_tab_id', $tabId);
        $ids = $qry->pluck('id')->toArray();
        $qry->delete();
        return $ids;
    }

    public function removeWidgetFieldsByTab($tabDetailIdArray)
    {
        return $this->ipCameraConfigurationWidgetField->whereIn('ip_camera_configuration_tab_detail_id', $tabDetailIdArray)->delete();
    }

    public function createFieldsListByModuleParams($modelName, $moduleId)
    {
        $fieldsList = [];
        if ($modelName == IpCameraConfigurationRepository::IP_CAMERA) {
            // $fieldsList = $this->bindScheduleWithWidgetParams();
            $data = $this->getIpCameraModuleById($moduleId);
            if (!empty($data)) {
                foreach ($data as $ky => $value) {
                    if ($value->dropdown_id) {
                        $fieldsList[] = [
                            'field_display_name' => $value->field_name ? $value->field_name : $value->dropdown->dropdown_name,
                            'field_system_name' => $value->field_name ? $value->field_name : $value->dropdown->dropdown_name,
                            'type' => $value->fieldtype->type_name,
                            'model_name' => IpCameraConfigurationRepository::IP_CAMERA,
                            'default_sort' => false,
                            'default_sort_order' => $value->order_id,
                            'visible' => true,
                            'permission_text' => '',
                        ];
                    } else {
                        $fieldsList[] = [
                            'field_display_name' => $value->name,
                            'field_system_name' => '-',
                            'type' => '-',
                            'model_name' => IpCameraConfigurationRepository::IP_CAMERA,
                            'default_sort' => false,
                            'default_sort_order' => $value->order_id,
                            'visible' => true,
                            'permission_text' => '',
                        ];
                    }
                }
            }
        } else {
            $data = $this->getLandingPageModuleFieldsById($moduleId);
            if (!empty($data)) {
                $fieldsList = unserialize($data['fields']);
            }
        }
        return $fieldsList;
    }

    public function getLandingPageModuleFieldsById($moduleId)
    {
        return $this->ipCameraModuleWidget->find($moduleId);
    }

    public function getTabDetailsById($Id)
    {
        return $this->IpCameraTabDetail->with(['moduleWidgetName'])->find($Id);
    }

    public function getDashboardTabs()
    {
        return $this->ipCameraTab
            ->with(['tabDetails'])
            ->where('active', true)
            ->orderBy('seq_no', 'asc')
            ->pluck('tab_name', 'id');
    }

    public function addOrRemoveTabAccordingToPermission($tabId)
    {
        $moduleLayoutHierarchy = $this->getTabDetailsByTabId($tabId);
        if (empty($moduleLayoutHierarchy->tabDetails)) {
            return false;
        }
        $havePermission = 0;
        foreach ($moduleLayoutHierarchy->tabDetails as $tabDetail) {
            $widgetDetails = $tabDetail->moduleWidgetName;

            if (!empty($widgetDetails)) {
                $viewPermissionArray = $widgetDetails->view_permission;
                if (!empty($viewPermissionArray)) {
                    $viewPermissions = unserialize($viewPermissionArray);
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
                } else {
                    $havePermission++;
                }
            }
        }

        if ($havePermission > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function fetchModuleLayoutHierarchyByTab($tabId, $divWidth, $divHeight)
    {
        $moduleResultArr = [];
        $moduleLayoutHierarchy = $this->getTabDetailsByTabId($tabId);
        if (empty($moduleLayoutHierarchy->tabDetails)) {
            return $moduleResultArr;
        }
        foreach ($moduleLayoutHierarchy->tabDetails as $tabDetail) {
            $parentDivHeight = $divHeight;

            $widgetFields = $tabDetail->widgetFields;
            $widgetDetails = $tabDetail->moduleWidgetName;
            $layoutDetails = $tabDetail->widgetLayoutDetail;
            $extraHeightAdjustment = ($layoutDetails->rowspan > 1) ? ($layoutDetails->rowspan * 25) : (($moduleLayoutHierarchy->landing_page_widget_layout_id == 1) ? 50 : 0);
            $noOfColumnsPerRow = $this->getCoumnsCountByRow($layoutDetails->landing_page_widget_layout_id, $layoutDetails->row_index);
            if ($moduleLayoutHierarchy->widgetLayouts->id == 4) {
                $noOfColumnsPerRow = 2;
            }
            $layoutWidgetHeight = (round((($parentDivHeight * $layoutDetails->rowspan) / $moduleLayoutHierarchy->widgetLayouts->no_of_rows), 1) + $extraHeightAdjustment);
            $layoutWidgetWidth = round((99.2 / $noOfColumnsPerRow), 1);
            $moduleResultArr[$tabDetail->landing_page_widget_layout_detail_id]['tab_id'] = $moduleLayoutHierarchy->id;
            $moduleResultArr[$tabDetail->landing_page_widget_layout_detail_id]['tab_name'] = $moduleLayoutHierarchy->tab_name;
            $moduleResultArr[$tabDetail->landing_page_widget_layout_detail_id]['customer_id'] = $moduleLayoutHierarchy->customer_id;
            $moduleResultArr[$tabDetail->landing_page_widget_layout_detail_id]['module_name'] = $this->fetchModuleFieldNameByType($tabDetail->id, $tabDetail->landing_page_module_widget_type);
            $moduleResultArr[$tabDetail->landing_page_widget_layout_detail_id]['rowspan'] = $layoutDetails->rowspan;
            $moduleResultArr[$tabDetail->landing_page_widget_layout_detail_id]['colspan'] = $layoutDetails->colspan;
            $moduleResultArr[$tabDetail->landing_page_widget_layout_detail_id]['row_index'] = $layoutDetails->row_index;
            $moduleResultArr[$tabDetail->landing_page_widget_layout_detail_id]['col_index'] = $layoutDetails->column_index;
            $moduleResultArr[$tabDetail->landing_page_widget_layout_detail_id]['no_of_columns'] = $noOfColumnsPerRow;
            $moduleResultArr[$tabDetail->landing_page_widget_layout_detail_id]['no_of_rows'] = $moduleLayoutHierarchy->widgetLayouts->no_of_rows;

            if ($tabDetail->landing_page_module_widget_type != 'ShiftModule') {
                try {
                    $apiUrl = config('globals.ip_cam_ms_ip') . config('globals.ip_cam_view_path') . $this->ipCameraAuthTokenRepository->getIpCameraToken($tabDetail->ip_camera_id);
                } catch (\Exception $e) {
                    $apiUrl = "";
                }
                //$apiUrl = "http://3.139.2.67/pages/player/mse/6240bf1c-1e87-41f9-8991-9bd560e8354b/0";
                //                if (!empty($widgetDetails->api_url_parameters)) {
                //                    $apiUrl .= '/' . str_replace(',', '/', $widgetDetails->api_url_parameters);
                //                }

                $moduleResultArr[$tabDetail->landing_page_widget_layout_detail_id]['api_url'] = $apiUrl;
                $moduleResultArr[$tabDetail->landing_page_widget_layout_detail_id]['detail_url'] = null;
                $moduleResultArr[$tabDetail->landing_page_widget_layout_detail_id]['api_type'] = null;
                $moduleResultArr[$tabDetail->landing_page_widget_layout_detail_id]['icon'] = '<img src="' . asset('images/camera.png') . '" style="width: 2%;">';;
                $moduleResultArr[$tabDetail->landing_page_widget_layout_detail_id]['view_permission'] = null;
            }
            $moduleResultArr[$tabDetail->landing_page_widget_layout_detail_id]['model'] = $tabDetail->landing_page_module_widget_type;
            $moduleResultArr[$tabDetail->landing_page_widget_layout_detail_id]['id'] = $tabDetail->ip_camera_id;
            $moduleResultArr[$tabDetail->landing_page_widget_layout_detail_id]['width'] = $layoutWidgetWidth . '%';
            $moduleResultArr[$tabDetail->landing_page_widget_layout_detail_id]['height'] = $layoutWidgetHeight . 'px';
            $moduleResultArr[$tabDetail->landing_page_widget_layout_detail_id]['layoutId'] = $moduleLayoutHierarchy->widgetLayouts->id;

            $widgetSystemArr = [];
            $widgetDisplayArr = [];
            $fieldsOrder = [];
            $sortOrder = '';
            foreach ($widgetFields as $widgetField) {
                if (array_key_exists($tabDetail->landing_page_widget_layout_detail_id, $moduleResultArr)) {
                    if ((empty($widgetField->permission_text)) || (!empty($widgetField->permission_text) && Auth::user()->can($widgetField->permission_text))) {
                        $widgetSystemArr[] = $widgetField->field_system_name;
                    }

                    if ((empty($widgetField->permission_text)) || (!empty($widgetField->permission_text) && Auth::user()->can($widgetField->permission_text))) {
                        $widgetDisplayArr[] = $widgetField->field_display_name;
                    }
                }

                if (($widgetField->default_sort) && (!in_array($widgetField->field_system_name, $fieldsOrder))) {
                    $fieldsOrder[] = $widgetField->field_system_name;
                    $sortOrder = $widgetField->default_sort_order;
                }
            }

            $widgetSystemArr = array_unique($widgetSystemArr);
            $widgetDisplayArr = array_unique($widgetDisplayArr);
            $moduleResultArr[$tabDetail->landing_page_widget_layout_detail_id]['system_fields_arr'] = implode(",", $widgetSystemArr);
            $moduleResultArr[$tabDetail->landing_page_widget_layout_detail_id]['display_fields_arr'] = implode(",", $widgetDisplayArr);
            $moduleResultArr[$tabDetail->landing_page_widget_layout_detail_id]['fields_order'] = implode(",", $fieldsOrder);
            $moduleResultArr[$tabDetail->landing_page_widget_layout_detail_id]['sort_order'] = $sortOrder;
        }
        if (!empty($moduleResultArr)) {
            ksort($moduleResultArr);
        }
        //        dd($moduleResultArr);
        return $moduleResultArr;
    }

    public function getCoumnsCountByRow($layoutId, $rowIndex)
    {
        return $this->landingPageWidgetLayoutDetail
            ->where('landing_page_widget_layout_id', $layoutId)
            ->where('row_index', $rowIndex)
            ->count();
    }

    public function fetchModuleNameByType($moduleId, $moduleType)
    {
        $moduleData = $this->getLandingPageModuleFieldsById($moduleId);
        return (!empty($moduleData)) ? $moduleData->name : '';
    }

    public function bindScheduleWithWidgetParams($returnOptionalFieldsOnly = false)
    {
        $optionalFields = ["Created Date", "Employee ID", "Guard", "Start Date", "End Date", "Start Time", "End Time", "Shift Status", "In Hours", "Date", "Time"];

        if ($returnOptionalFieldsOnly) {
            return $optionalFields;
        }
        $resultArr = [];
        foreach ($optionalFields as $optionalField) {
            $params = [
                'field_display_name' => $optionalField,
                'field_system_name' => $optionalField,
                'type' => 'shift-module-optional',
                'model_name' =>  IpCameraConfigurationRepository::IP_CAMERA,
                'default_sort' => false,
                'default_sort_order' => 0,
                'visible' => true,
                'permission_text' => '',
            ];
            $resultArr[] = $params;
        }
        return $resultArr;
    }

    public function getIpCameraModuleById($moduleId)
    {
        return $this->ipCameraModuleWidget->withTrashed()
            ->where('id', $moduleId)
            ->where('enabled', 1)
            ->where('deleted_at', null)
            ->orderBy('id', 'ASC')
            ->get();
    }

    public function deleteTab($tabId)
    {
        $tabDetailIdArray = $this->removeTabDetailsByTab($tabId);
        if (!empty($tabDetailIdArray)) {
            $this->removeWidgetFieldsByTab($tabDetailIdArray);
        }
        return $this->ipCameraTab->find($tabId)->delete();
    }

    public function updateTabActiveStatus($tabid, $status)
    {
        return $this->ipCameraTab
            ->where('id', $tabid)
            ->update(['active' => $status]);
    }

    public function fetchModuleFieldNameByType($tabDetailId, $moduleType)
    {
        $moduleData = IpCameraConfigurationWidgetField::where('ip_camera_configuration_tab_detail_id', $tabDetailId)->first();
        return (!empty($moduleData)) ? $moduleData->field_display_name : '';
    }
}
