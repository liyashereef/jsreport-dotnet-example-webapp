<?php

namespace Modules\Management\Repositories;

use App\Services\HelperService;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Modules\Admin\Models\CpidCustomerAllocations;
use Modules\Admin\Models\Customer;
use Modules\Admin\Models\ShiftModule;
use Modules\Admin\Repositories\CustomerQrcodeLocationRepository;
use Modules\Admin\Repositories\CustomerRepository;
use Modules\Admin\Repositories\LandingPageRepository;
use Modules\Admin\Repositories\ShiftModuleRepository;
use Modules\Documents\Repositories\DocumentsRepository;
use Modules\Hranalytics\Repositories\CustomerStcDetailsRepository;
use Modules\Management\Http\Requests\CustomerFenceRequest;
use Modules\Management\Http\Requests\CustomerPreferenceRequest;
use Modules\Management\Http\Requests\CustomerProfileRequest;

class CustomerViewRepository
{
    public const SHIFT_MODULE_TYPE = 'ShiftModule';

    public function __construct(CustomerQrcodeLocationRepository $customerQrcodeLocationRepository,
        DocumentsRepository $documentsRepository, CustomerRepository $customerRepository, HelperService $helperService,
        Customer $customerModel, LandingPageRepository $landingPageRepository, ShiftModuleRepository $shiftModuleRepository,
        CustomerStcDetailsRepository $stcDetailsRepository) {
        $this->customerModel = $customerModel;
        $this->customerRepository = $customerRepository;
        $this->helperService = $helperService;
        $this->documentRepository = $documentsRepository;
        $this->customerQrcodeLocationRepository = $customerQrcodeLocationRepository;
        $this->landingPageRepository = $landingPageRepository;
        $this->shiftModuleRepository = $shiftModuleRepository;
        $this->stcDetailsRepository = $stcDetailsRepository;
    }

    public function customerProfileStore(CustomerProfileRequest $request, $id)
    {
        $incidentFile = $request->get('incident_report_logo');

        $customerData = [
            'stc' => $request->get('stc'),
            'client_name' => $request->get('client_name'),
            'project_number' => $request->get('project_number'),
            'contact_person_name' => $request->get('contact_person_name'),
            'contact_person_email_id' => $request->get('contact_person_email_id'),
            'contact_person_phone' => $request->get('contact_person_phone'),
            'contact_person_phone_ext' => $request->get('contact_person_phone_ext'),
            'contact_person_cell_phone' => $request->get('contact_person_cell_phone'),
            'contact_person_position' => $request->get('contact_person_position'),
            'requester_name' => $request->get('requester_name'),
            'status' => $request->get('status'),
            'city' => $request->get('city'),
            'postal_code' => $request->get('postal_code'),
            'province' => $request->get('province'),
            'address' => $request->get('address'),
            'description' => $request->get('description'),
            'proj_open' => $request->get('proj_open'),
            'proj_expiry' => $request->get('proj_expiry'),
            'arpurchase_order_no' => $request->get('arpurchase_order_no'),
            'arcust_type' => $request->get('arcust_type'),
            'industry_sector_lookup_id' => $request->get('industry_sector_lookup_id'),
            'region_lookup_id' => $request->get('region_lookup_id'),
            'billing_address' => $request->get('billing_address'),
            'same_address_check' => $request->get('same_address_check'),
            'requester_position' => $request->get('requester_position'),
            'requester_empno' => $request->get('requester_empno'),
            'master_customer' => number_format($request->get('master_customer')),
        ];

        if ($request->hasFile('incident_report_logo')) {
            if (!empty($id)) {
                $custObj = Customer::find($id);
                if (is_object($custObj)) {
                    if (Storage::disk('public')->exists($custObj->incident_report_logo)) {
                        Storage::disk('public')->delete($custObj->incident_report_logo);
                    }
                }
            }
            //store the data
            $path = $request->incident_report_logo->store('incident_logos', 'public');
            $customerData = [
                'incident_report_logo' => $path,
            ];
        }

        $stcDetails['nmso_account'] = "no";
        $stcDetails['security_clearance_lookup_id'] = null;
        if ($request->get('is_nmso_account') == 1) {
            $stcDetails['nmso_account'] = "yes";
            $stcDetails['security_clearance_lookup_id'] = $request->get('security_clearance_lookup_id');
        }

        $custObj = Customer::updateOrCreate(['id' => $id], $customerData);
        if (empty($id)) {
            $id = $custObj->id;
        }
        $custObj = Customer::find($id);
        $stcDetails['customer_stc_details_id'] = ($custObj->stcDetails != null) ? $custObj->stcDetails->id : null;
        $customer_stc_details = $this->stcDetailsRepository->storeStcDetails($stcDetails, $id);
        return response()->json(array('success' => true));
    }

    public function customerCPIDStore(Request $request, $id)
    {
        $data['row-no'] = $request->get('row-no');
        $data[] = $request->all();
        CpidCustomerAllocations::where('customer_id', $id)->delete();
        if (isset($data['row-no'])) {
            foreach ($data['row-no'] as $row_no) {
                $data['cpid_' . $row_no] = $request->get('cpid_' . $row_no);
                $cpidLookup = intval($data['cpid_' . $row_no]);
                if ($cpidLookup != 0) {
                    $allocationData = [
                        'cpid' => $cpidLookup,
                    ];

                    $allocationData['customer_id'] = $id;
                    $allocationData['created_by'] = Auth::user()->id;
                    CpidCustomerAllocations::updateOrCreate(
                        [
                            'customer_id' => $id,
                            'cpid' => $cpidLookup,
                        ],
                        $allocationData
                    );
                }
            }
            return response()->json(array('success' => true));
        }

    }

    public function customerPreferenceStore(CustomerPreferenceRequest $request, $id)
    {

        $customerData = [

            'shift_journal_enabled' => $request->get('shift_journal_enabled') ? 1 : 0,
            'time_shift_enabled' => $request->get('time_shift_enabled') ? 1 : 0,
            'guard_tour_enabled' => $request->get('guard_tour_enabled') ? 1 : 0,
            'overstay_enabled' => $request->get('overstay_enabled') ? 1 : 0,
            'show_in_sitedashboard' => $request->get('show_in_sitedashboard') ? 1 : 0,
            'basement_mode' => $request->get('basement_mode') ? 1 : 0,
            'geo_fence' => $request->get('geo_fence') ? 1 : 0,
            'geo_fence_satellite' => $request->get('geo_fence_satellite') ? 1 : 0,
            'mobile_security_patrol_site' => $request->get('mobile_security_patrol_site') ? 1 : 0,
            'employee_rating_response' => $request->get('employee_rating_response') ? 1 : 0,
            'qr_patrol_enabled' => $request->get('qr_patrol_enabled') ? 1 : 0,
            'key_management_enabled' => $request->get('key_management_enabled') ? 1 : 0,
            'key_management_signature' => $request->get('key_management_signature') ? 1 : 0,
            'key_management_image_id' => $request->get('key_management_image_id') ? 1 : 0,
            'qr_interval_check' => $request->get('qr_interval_check') ? 1 : 0,
            'overstay_time' => $request->get('overstay_time') ?? null,
            'employee_rating_response_time' => $request->get('employee_rating_response_time') ?? null,
            'qr_picture_limit' => $request->get('qr_picture_limit') ?? null,
            'qr_duration' => $request->get('qr_duration') ?? null,
            'guard_tour_duration' => $request->get('guard_tour_duration') ?? null,
            'facility_booking' => $request->get('facility_booking') ? 1 : 0,
            'basement_interval' => $request->get('basement_interval') ?? null,
            'basement_noofrounds' => $request->get('basement_noofrounds') ?? null,
            'motion_sensor_enabled' => $request->get('motion_sensor_enabled') ? 1 : 0,
            'motion_sensor_incident_subject' => $request->get('motion_sensor_incident_subject') ?? null,
            'visitor_screening_enabled' => $request->get('visitor_screening_enabled') ? 1 : 0,
            'time_sheet_approver_id' => $request->get('time_sheet_approver_id') ?: null,
        ];

        Customer::updateOrCreate(['id' => $id], $customerData);
        return response()->json(array('success' => true));
    }

    public function fenceStore(CustomerFenceRequest $request, $id)
    {
        $customerData = [
            'contractual_visit_unit' => $request->get('contractual_visit_unit'),
            'fence_interval' => $request->get('fence_interval'),
        ];

        Customer::updateOrCreate(['id' => $id], $customerData);
        return response()->json(array('success' => true));

    }

    public function clienLookUps()
    {
        $customerList = array();
        $user = Auth::user();
        if ((\Auth::user()->can('customer_view')) || $user->hasAnyPermission(['admin', 'super_admin'])) {
            $customerList = $this->customerModel->orderBy('client_name', 'asc')->get();
        } else {
            $customerIds = $this->customerRepository->getAllAllocatedCustomerId([Auth::user()->id]);
            $customerList = $this->customerModel
                ->whereIn('id', $customerIds)
                ->orderBy('client_name', 'asc')->get();
        }
        return $customerList;
    }

    public function LandingPageDetails(Request $request)
    {
        $tab = array();
        $tabDetails = $this->landingPageRepository->getTabsByCustomerIdOnly($request->input('customerid'));
        $widgetModules = $this->landingPageRepository->getWidgetModules();
        $shiftModules = $this->getShiftModulesByCustomerId($request->input('customerid'));
        $shiftModuleList = [];
        $customModuleLIst = [];
        if (!empty($widgetModules)) {
            foreach ($widgetModules as $widgetModule) {
                $customModuleLIst[$widgetModule->id] = $widgetModule->name;
            }
        }
        if (!empty($shiftModules)) {
            foreach ($shiftModules as $shiftModule) {
                $shiftModuleList[$shiftModule->id] = $shiftModule->module_name;
            }
        }

        foreach ($tabDetails as $tabKey => $tabValue) {
            $tab[$tabKey]['id'] = $tabValue->id;
            $tab[$tabKey]['active'] = $tabValue->active;
            $tab[$tabKey]['default_tab_structure'] = $tabValue->default_tab_structure;
            $tab[$tabKey]['tab_name'] = $tabValue->tab_name;

            $moduleByTabId = $this->landingPageRepository->getModuleByTab($tabValue->id);
            $dynamicLiElements = [];
            foreach ($moduleByTabId as $existingTabDetail) {
                if ($existingTabDetail->landing_page_module_widget_type === CustomerViewRepository::SHIFT_MODULE_TYPE) {
                    $moduleName = $shiftModuleList[$existingTabDetail->landing_page_module_widget_id];
                    $model = CustomerViewRepository::SHIFT_MODULE_TYPE;
                } else {
                    $moduleName = $customModuleLIst[$existingTabDetail->landing_page_module_widget_id];
                    $model = "LandingPageModuleWidget";
                }

                $dynamicLiElements[$existingTabDetail->landing_page_widget_layout_detail_id] = $moduleName;
            }

            foreach ($moduleByTabId as $keyModule => $valueModule) {
                $modelName = $valueModule->landing_page_module_widget_type;
                $moduleId = $valueModule->landing_page_module_widget_id;
                $moduleFields = $valueModule->widgetFields;

                $fieldsList = [];
                if ($modelName == CustomerViewRepository::SHIFT_MODULE_TYPE) {
                    $data = $this->landingPageRepository->getShiftModuleFieldsById($moduleId);
                    if (!empty($data)) {
                        foreach ($data as $ky => $value) {
                            if ($value->dropdown_id) {
                                $fieldsList[] = [
                                    'field_display_name' => $value->dropdown->dropdown_name,
                                    'field_system_name' => $value->dropdown->dropdown_name,
                                    'type' => $value->fieldtype->type_name,
                                    'table' => '-',
                                    'default_sort' => true,
                                    'default_sort_order' => $value->order_id,
                                    'visible' => true,
                                ];
                            } else {
                                $fieldsList[] = [
                                    'field_display_name' => $value->field_name,
                                    'field_system_name' => $value->field_name,
                                    'type' => $value->fieldtype->type_name,
                                    'table' => '-',
                                    'default_sort' => true,
                                    'default_sort_order' => $value->order_id,
                                    'visible' => true,
                                ];
                            }
                        }
                    }
                } else {
                    foreach ($moduleFields as $key => $value) {
                        $fieldsList[] = [
                            'field_display_name' => $value['field_display_name'],
                            'default_sort' => $value['default_sort'],
                            'visible' => $value['visible'],
                        ];
                    }
                }
                $tab[$tabKey]['tabDetails'][$dynamicLiElements[$valueModule->landing_page_widget_layout_detail_id]][] = $fieldsList;
            }
        }
        return $tab;
    }

    private function getShiftModulesByCustomerId($customerId)
    {
        return ShiftModule::withTrashed()->with(['customer'])->where('customer_id', $customerId)->get();
    }
}
