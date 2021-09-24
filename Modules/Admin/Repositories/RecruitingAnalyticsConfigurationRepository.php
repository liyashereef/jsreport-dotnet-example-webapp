<?php

namespace Modules\Admin\Repositories;

use Auth;
use Modules\Admin\Models\LandingPageModuleWidget;
use Modules\Admin\Models\LandingPageWidgetLayout;
use Modules\Admin\Models\LandingPageWidgetLayoutDetail;
use Modules\Admin\Models\RecruitingAnalyticsTab;
use Modules\Admin\Models\RecruitingAnalyticsTabDetail;
use Modules\Admin\Models\RecruitingAnalyticsWidgetField;

class RecruitingAnalyticsConfigurationRepository
{

    protected $landingPageWidgetLayoutModel, $landingPageModuleWidget, $landingPageWidgetLayoutDetail, $recruitingAnalyticsTab, $recruitingAnalyticsTabDetail, $recruitingAnalyticsWidgetField;

    public const SHIFT_MODULE_TYPE = 'ShiftModule';

    public function __construct(
        LandingPageWidgetLayout $landingPageWidgetLayoutModel,
        LandingPageModuleWidget $landingPageModuleWidget,
        LandingPageWidgetLayoutDetail $landingPageWidgetLayoutDetail,
        RecruitingAnalyticsTab $recruitingAnalyticsTab,
        RecruitingAnalyticsTabDetail $recruitingAnalyticsTabDetail,
        RecruitingAnalyticsWidgetField $recruitingAnalyticsWidgetField) {
        $this->landingPageWidgetLayoutModel = $landingPageWidgetLayoutModel;
        $this->landingPageModuleWidget = $landingPageModuleWidget;
        $this->landingPageWidgetLayoutDetail = $landingPageWidgetLayoutDetail;
        $this->recruitingAnalyticsTab = $recruitingAnalyticsTab;
        $this->recruitingAnalyticsTabDetail = $recruitingAnalyticsTabDetail;
        $this->recruitingAnalyticsWidgetField = $recruitingAnalyticsWidgetField;
    }

    public function getAllWidgetLayouts()
    {
        return $this->landingPageWidgetLayoutModel
            ->with(['landingPageWidgetLayoutDetail'])
            ->select('*')->get();
    }

    public function getWidgetModules($widgetCategory = '')
    {
        $qry = $this->landingPageModuleWidget;
        if (!empty($widgetCategory)) {
            $qry = $qry->where('widget_category', $widgetCategory);
        }
        return $qry->get();
    }

    public function getWidgetLayoutDetailsByLayout($widgetLayoutId)
    {
        return $this->landingPageWidgetLayoutDetail->where('landing_page_widget_layout_id', $widgetLayoutId)->get();
    }

    public function getLandingPageModuleFieldsById($moduleId)
    {
        return $this->landingPageModuleWidget->find($moduleId);
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
            $tab = $this->recruitingAnalyticsTab->updateOrCreate(array('id' => $tabId), $data);
        } else {
            $tab = $this->recruitingAnalyticsTab->updateOrCreate($data);
        }
        return $tab;
    }

    public function validateTabName($tabName, $tabId)
    {
        $tabName = ucwords(strtolower($tabName));
        $qry = $this->recruitingAnalyticsTab->where('tab_name', $tabName)
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
                        'recruiting_analytics_tab_id' => $tab->id,
                        'landing_page_widget_layout_detail_id' => $data['layout_detail_id'],
                        'landing_page_module_widget_id' => $data['module_id'],
                        'landing_page_module_widget_type' => $data['model_name'],
                        'created_by' => \Auth::user()->id,
                    ];
                    $tabDetails = $this->recruitingAnalyticsTabDetail->updateOrCreate($tabDetail);

                    if (!empty($tabDetails)) {
                        if (!array_key_exists('permission_text', $data)) {
                            $data['permission_text'] = "";
                        }

                        $dataArr = [
                            'recruiting_analytics_tab_detail_id' => $tabDetails->id,
                            'field_display_name' => $data['display_name'],
                            'field_system_name' => $data['field_name'],
                            'default_sort' => $data['enable_sort'],
                            'default_sort_order' => isset($data['sort_order']) ? $data['sort_order'] : 0,
                            'visible' => (($data['visible'] == "true") ? 1 : ($data['visible'] == "false")) ? 0 : $data['visible'],
                            'created_by' => \Auth::user()->id,
                            'permission_text' => $data['permission_text'],
                        ];
                        $fieldDetails = $this->recruitingAnalyticsWidgetField->updateOrCreate($dataArr);
                    }
                }
            }
        }
        return $fieldDetails;
    }

    public function getAllTabs()
    {
        return $this->recruitingAnalyticsTab->with(['tabDetails'])
            ->select('id', 'tab_name', 'seq_no', 'default_tab_structure', 'active')
            ->orderBy('seq_no')
            ->get();
    }

    public function getTabsByCustomer($customerid = [])
    {
        return $this->recruitingAnalyticsTab->with(['tabDetails'])
            ->whereIn('customer_id', $customerid)
            ->where('active', true)
            ->orderBy('seq_no', 'ASC');
    }

    public function getModuleByTab($tabId)
    {
        return $this->recruitingAnalyticsTabDetail->with(['moduleWidgetName', 'widgetFields'])->where('recruiting_analytics_tab_id', $tabId)->get();
    }

    public function getTabDetailsByTabId($tabId)
    {
        return $this->recruitingAnalyticsTab->with(['tabDetails', 'widgetLayouts'])->find($tabId);
    }

    public function createFieldsListByModuleParams($modelName, $moduleId)
    {
        $fieldsList = [];
        $data = $this->getLandingPageModuleFieldsById($moduleId);
        if (!empty($data)) {
            $fieldsList = unserialize($data['fields']);
        }

        return $fieldsList;
    }

    public function getTabDetailsById($Id)
    {
        return $this->recruitingAnalyticsTabDetail->find($Id);
    }

    public function removeTabDetailsByTab($tabId)
    {
        $qry = $this->recruitingAnalyticsTabDetail->where('recruiting_analytics_tab_id', $tabId);
        $ids = $qry->pluck('id')->toArray();
        $qry->delete();
        return $ids;
    }

    public function removeWidgetFieldsByTab($tabDetailIdArray)
    {
        return $this->recruitingAnalyticsWidgetField->whereIn('recruiting_analytics_tab_detail_id', $tabDetailIdArray)->delete();
    }

    public function updateTabActiveStatus($tabid, $status)
    {
        return $this->recruitingAnalyticsTab
            ->where('id', $tabid)
            ->update(['active' => $status]);
    }

    public function getDashboardTabs()
    {
        return $this->recruitingAnalyticsTab
            ->with(['tabDetails'])
            ->where('active', true)
            ->orderBy('seq_no', 'asc')
            ->pluck('tab_name', 'id');
    }

    public function fetchModuleNameByType($moduleId, $moduleType)
    {
        $moduleData = $this->getLandingPageModuleFieldsById($moduleId);
        return (!empty($moduleData)) ? $moduleData->name : '';
    }

    public function getCoumnsCountByRow($layoutId, $rowIndex)
    {
        return $this->landingPageWidgetLayoutDetail
            ->where('landing_page_widget_layout_id', $layoutId)
            ->where('row_index', $rowIndex)
            ->count();
    }

    public function getRowsCountByColumn($layoutId, $colIndex)
    {
        return $this->landingPageWidgetLayoutDetail
            ->where('landing_page_widget_layout_id', $layoutId)
            ->where('column_index', $colIndex)
            ->count();
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
            $moduleResultArr[$tabDetail->landing_page_widget_layout_detail_id]['module_name'] = $this->fetchModuleNameByType($tabDetail->landing_page_module_widget_id, $tabDetail->landing_page_module_widget_type);

            $moduleResultArr[$tabDetail->landing_page_widget_layout_detail_id]['rowspan'] = $layoutDetails->rowspan;
            $moduleResultArr[$tabDetail->landing_page_widget_layout_detail_id]['colspan'] = $layoutDetails->colspan;
            $moduleResultArr[$tabDetail->landing_page_widget_layout_detail_id]['row_index'] = $layoutDetails->row_index;
            $moduleResultArr[$tabDetail->landing_page_widget_layout_detail_id]['col_index'] = $layoutDetails->column_index;
            $moduleResultArr[$tabDetail->landing_page_widget_layout_detail_id]['no_of_columns'] = $noOfColumnsPerRow;
            $moduleResultArr[$tabDetail->landing_page_widget_layout_detail_id]['no_of_rows'] = $moduleLayoutHierarchy->widgetLayouts->no_of_rows;

            if ($tabDetail->landing_page_module_widget_type != 'ShiftModule') {
                $apiUrl = route($widgetDetails->api_url_path);
                if (!empty($widgetDetails->api_url_parameters)) {
                    $apiUrl .= '/' . str_replace(',', '/', $widgetDetails->api_url_parameters);
                }

                $moduleResultArr[$tabDetail->landing_page_widget_layout_detail_id]['api_url'] = $apiUrl;
                $moduleResultArr[$tabDetail->landing_page_widget_layout_detail_id]['detail_url'] = $widgetDetails->detail_url_path;
                $moduleResultArr[$tabDetail->landing_page_widget_layout_detail_id]['api_type'] = $widgetDetails->api_type;
                $moduleResultArr[$tabDetail->landing_page_widget_layout_detail_id]['icon'] = $widgetDetails->icon;
                $moduleResultArr[$tabDetail->landing_page_widget_layout_detail_id]['view_permission'] = $widgetDetails->view_permission;
            } else {
                $moduleResultArr[$tabDetail->landing_page_widget_layout_detail_id]['api_url'] = route('dashboard-shift-module');
                $moduleResultArr[$tabDetail->landing_page_widget_layout_detail_id]['detail_url'] = '';
                $moduleResultArr[$tabDetail->landing_page_widget_layout_detail_id]['api_type'] = 2;
                $moduleResultArr[$tabDetail->landing_page_widget_layout_detail_id]['icon'] = '<img src="' . asset('images/candidate.png') . '" style="width: 2%;">';
                $moduleResultArr[$tabDetail->landing_page_widget_layout_detail_id]['view_permission'] = '';
            }
            $moduleResultArr[$tabDetail->landing_page_widget_layout_detail_id]['model'] = $tabDetail->landing_page_module_widget_type;
            $moduleResultArr[$tabDetail->landing_page_widget_layout_detail_id]['id'] = $tabDetail->landing_page_module_widget_id;
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

    public function fetchFieldsByConfiguration($request, $inputArray = [], $compaireDisplayFields = false, $widgetRequest = false, $widgetName = "")
    {
        $result = [];
        $resultArray = [];
        $i = 0;
        if ($compaireDisplayFields) {
            $configuedDisplayFields = $request->get('data-display-fields');
            $resultArray = explode(",", $configuedDisplayFields);
            return $resultArray;
        } else {
            $configuedSystemFields = $request->get('data-system-fields');
            $resultArray = explode(",", $configuedSystemFields);
            foreach ($inputArray as $key => $inputValue) {
                if (array_key_exists('_bg_color', $inputValue)) {
                    $result[$key]['_bg_color'] = $inputValue['_bg_color'];
                }

                foreach ($resultArray as $resultValue) {
                    if (!array_key_exists($resultValue, $inputValue)) {
                        $inputValue[$resultValue] = "";
                    }
                    $result[$key][$resultValue] = $inputValue[$resultValue];
                }
                $i++;

                if ($widgetRequest && ($widgetName == "guard_tour_shift_journal_summary_widget") && ($i == config('dashboard.guard_tour_or_shift_journal_row_limit'))) {
                    break;
                } else if ($widgetRequest && ($widgetName == "key_log_summary_widget") && ($i == config('dashboard.key_log_summary_row_limit'))) {
                    break;
                } else if ($widgetRequest && ($widgetName == "time_sheet_widget") && ($i == config('dashboard.time_sheet_row_limit'))) {
                    break;
                }
            }
        }

        return $result;
    }

    public function sortArrayByKeyField($request, $sortKeyArr = [])
    {

        $configuedDisplayFields = $request->get('data-system-fields');
        $displayArray = explode(",", $configuedDisplayFields);

        $hiddenFields = $request->get('hidden-fields');
        $hiddenFieldsArray = explode(",", $hiddenFields);
        $hiddenArr = [];
        foreach ($hiddenFieldsArray as $key => $hiddenField) {
            if (in_array($hiddenField, $displayArray)) {
                $selectedIndex = array_search($hiddenField, $displayArray);
                $hiddenArr[] = $selectedIndex;
            }
        }
        // dd($request);
        $fieldsOrder = $request->get('sort-field');
        $order = (!empty($request->get('sort-order')) && ($request->get('sort-order') == '1')) ? 'desc' : 'asc';
        $defaultSort = explode(",", $fieldsOrder);

        if (!empty($hiddenFieldsArray)) {
            $defaultSort = array_diff($defaultSort, $hiddenFieldsArray);
        }

        foreach ($defaultSort as $value) {
            $sortKeyArr[] = $value;
        }
        $configuedSystemFields = $request->get('data-system-fields');
        $resultArray = explode(",", $configuedSystemFields);
        $resultArr = [];
        foreach ($sortKeyArr as $key => $sortKey) {
            if (in_array($sortKey, $resultArray)) {
                $selectedIndex = array_search($sortKey, $resultArray);
                $resultArr[] = [$selectedIndex, $order];
            }
        }

        return ['sort_arr' => $resultArr, 'hidden_arr' => $hiddenArr];
    }

    public function fetchTabByParam($tabId, $layoutId)
    {
        return $this->recruitingAnalyticsTab->with(['tabDetails'])
            ->where('id', $tabId)
            ->where('landing_page_widget_layout_id', $layoutId)
            ->get();
    }

    public function deleteTab($tabId)
    {
        $tabDetailIdArray = $this->removeTabDetailsByTab($tabId);
        if (!empty($tabDetailIdArray)) {
            $this->removeWidgetFieldsByTab($tabDetailIdArray);
        }
        return $this->recruitingAnalyticsTab->find($tabId)->delete();
    }

    public function resetDisabledFieldsFromShiftModuleResultArray($tabId, $moduleId, $records = [])
    {
        $result = [];
        $systemFields = $this->recruitingAnalyticsWidgetField
            ->whereHas('widgetTabDetail', function ($query) use ($tabId, $moduleId) {
                return $query->where('recruiting_analytics_tab_id', $tabId)
                    ->where('landing_page_module_widget_id', $moduleId)
                    ->where('landing_page_module_widget_type', 'ShiftModule');
            })
            ->get()
            ->pluck('field_system_name')
            ->toArray();

        if (!empty($systemFields) && !empty($records)) {
            foreach ($records as $key => $record) {
                $result[$key] = array_intersect_key($record, array_flip($systemFields));
            }

            return $result;
        } else {
            return $records;
        }
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

}
