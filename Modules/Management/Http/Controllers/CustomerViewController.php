<?php

namespace Modules\Management\Http\Controllers;

use App\Services\HelperService;
use DB;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Storage;
use Modules\Admin\Http\Requests\CustomerIncidentPriorityRequest;
use Modules\Admin\Http\Requests\CustomerIncidentSubjectAllocationRequest;
use Modules\Admin\Http\Requests\CustomerQrCodeRequest;
use Modules\Admin\Http\Requests\IncidentRecipientRequest;
use Modules\Admin\Models\ContractualVisitUnitLookup;
use Modules\Admin\Models\CpidCustomerAllocations;
use Modules\Admin\Models\Customer;
use Modules\Admin\Models\CustomerIncidentSubjectAllocation;
use Modules\Admin\Models\CustomerQrcodeLocation;
use Modules\Admin\Models\IncidentPriorityLookup;
use Modules\Admin\Models\IncidentRecipient;
use Modules\Admin\Models\IncidentReportSubject;
use Modules\Admin\Models\IndustrySectorLookup;
use Modules\Admin\Models\LandingPageTab;
use Modules\Admin\Models\ParentCustomer;
use Modules\Admin\Models\RegionLookup;
use Modules\Admin\Models\User;
use Modules\Admin\Repositories\CustomerEmployeeAllocationRepository;
use Modules\Admin\Repositories\CustomerIncidentPriorityRepository;
use Modules\Admin\Repositories\CustomerIncidentSubjectAllocationRepository;
use Modules\Admin\Repositories\CustomerQrcodeLocationRepository;
use Modules\Admin\Repositories\CustomerRepository;
use Modules\Admin\Repositories\IncidentPriorityLookupRepository;
use Modules\Admin\Repositories\RegionLookupRepository;
use Modules\Admin\Repositories\UserRepository;
use Modules\Documents\Repositories\DocumentsRepository;
use Modules\Management\Http\Requests\CustomerFenceRequest;
use Modules\Management\Http\Requests\CustomerPreferenceRequest;
use Modules\Management\Http\Requests\CustomerProfileRequest;
use Modules\Management\Repositories\CustomerViewRepository;

class CustomerViewController extends Controller
{

    public function __construct(CustomerIncidentPriorityRepository $customerIncidentPriorityRepository,
        IncidentPriorityLookupRepository $incidentPriorityLookupRepository, LandingPageTab $landingPageTab, CustomerViewRepository $customerViewRepository,
        CustomerQrcodeLocationRepository $customerQrcodeLocationRepository, DocumentsRepository $documentsRepository,
        CustomerIncidentSubjectAllocationRepository $customerIncidentSubjectAllocationRepository, UserRepository $userRepository,
        CustomerRepository $customerRepository, HelperService $helperService, Customer $customerModel,
        RegionLookupRepository $regionLookupRepository, CustomerEmployeeAllocationRepository $customerEmployeeAllocationRespository) {
        $this->userRepository = $userRepository;
        $this->customerModel = $customerModel;
        $this->customerRepository = $customerRepository;
        $this->helperService = $helperService;
        $this->documentRepository = $documentsRepository;
        $this->customerQrcodeLocationRepository = $customerQrcodeLocationRepository;
        $this->customerViewRepository = $customerViewRepository;
        $this->landingPageTab = $landingPageTab;
        $this->customerIncidentPriorityRepository = $customerIncidentPriorityRepository;
        $this->incidentPriorityLookupRepository = $incidentPriorityLookupRepository;
        $this->customerIncidentSubjectAllocationRepository = $customerIncidentSubjectAllocationRepository;
        $this->regionLookupRepository = $regionLookupRepository;
        $this->customerEmployeeAllocation = $customerEmployeeAllocationRespository;

    }

    public function getCustomerList()
    {

        $projectName = request('projectname');
        $customerType = request('customerType');
        $active = request('active');
        $customerList = $this->customerModel->select([
            'project_number',
            'id',
            'client_name',
            'contact_person_name',
            'contact_person_email_id',
            'contact_person_phone',
            'contact_person_position',
            'address',
            'city',
            'province',
            'postal_code',
            'radius',
            'stc',
        ])

            ->orderBy('project_number', 'asc')
            ->where('active', $active)
            ->get();
        $customerList = $customerList->when($customerType != "ALL_CUSTOMER", function ($q) use ($customerType) {
            return $q->where('stc', $customerType);
        });
        $data = $customerList->when($projectName != null, function ($q) use ($projectName) {
            return $q->where('id', $projectName);

        });
        return datatables()->of($data)->addIndexColumn()->toJson();
    }

    /**
     * Show the specified resource.
     * @return Response
     */
    public function getDetailView($id)
    {

        $customerData = $this->customerModel->with('stcDetails')->where('id', $id)->get();
        $singleCustomer = $this->customerRepository->getSingleCustomer($id);

        $approverId = $this->customerModel->where('id', $id)->pluck('time_sheet_approver_id');
        $approverDetails = User::where('id', $approverId)->get();

        $industrySectorId = $this->customerModel->where('id', $id)->pluck('industry_sector_lookup_id');
        $industrySector = IndustrySectorLookup::where('id', $industrySectorId)->get();

        $regionLookupId = $this->customerModel->where('id', $id)->pluck('region_lookup_id');
        $region = RegionLookup::where('id', $regionLookupId)->get();

        $regionDetails = $this->regionLookupRepository->getAllRegionDescription();

        $requestorId = $this->customerModel->where('id', $id)->pluck('requester_name');

        $requestorName = $this->userRepository->getUserDetails($requestorId);

        $userList = $this->userRepository->getUserList(true, null, null, ['super_admin'], false, false);
        $userCollect = collect($userList);
        $groupedPos = $userCollect->mapToGroups(function ($item, $key) {
            return [$item['id'] => $item['employee']['employeePosition']];
        });
        $requestorPosition = $groupedPos->toArray();
        $groupedEmp = $userCollect->mapToGroups(function ($item, $key) {
            return [$item['id'] => $item['employee']['employee_no']];
        });
        $requestorEmpno = $groupedEmp->toArray();

        $masterCustId = $this->customerModel->where('id', $id)->pluck('master_customer');
        $parentCustomer = ParentCustomer::where('id', $masterCustId)->get();

        $cpid = CpidCustomerAllocations::with('cpid_lookup.position')->where('customer_id', $id)->get();

        $preference = $this->customerModel->where('id', $id)->select('show_in_sitedashboard', 'facility_booking',
            'shift_journal_enabled', 'time_shift_enabled', 'guard_tour_enabled', 'overstay_enabled', 'basement_mode', 'geo_fence',
            'qr_interval_check', 'key_management_signature', 'key_management_image_id', 'mobile_security_patrol_site',
            'geo_fence_satellite', 'employee_rating_response', 'qr_patrol_enabled', 'qr_interval_check', 'basement_interval',
            'basement_noofrounds', 'key_management_enabled', 'motion_sensor_enabled', 'motion_sensor_incident_subject',
            'guard_tour_duration', 'overstay_time', 'employee_rating_response_time', 'qr_picture_limit', 'qr_duration',
            'visitor_screening_enabled', 'time_sheet_approver_id')->get();

        $customerAllocattedUsers = $this->customerEmployeeAllocation->allocationList($id)->pluck('name_with_emp_no', 'id')->toArray();

        $customerContractId = $this->customerModel->where('id', $id)->pluck('contractual_visit_unit');
        $viewContractList = ContractualVisitUnitLookup::where('id', $customerContractId)->get();

        $qrCodeData = CustomerQrcodeLocation::where('customer_id', $id)
            ->select('id', 'qrcode', 'location', 'no_of_attempts', 'no_of_attempts_week_ends', 'tot_no_of_attempts_week_day', 'tot_no_of_attempts_week_ends', 'picture_enable_disable', 'picture_mandatory', 'location_enable_disable', 'qrcode_active')->get();

        $incidentData = CustomerIncidentSubjectAllocation::with(['subject', 'incidentPriority', 'category', 'incidentReport'])
            ->where('customer_id', $id)->get();

        $allocatedIncidentSubjects = array_pluck(
            $singleCustomer->subjectAllocation,
            'subject.subject',
            'subject.id'
        );
        $incidentSubjectId = $this->customerModel->where('id', $id)->pluck('motion_sensor_incident_subject');
        $incidentSubjectArr = IncidentReportSubject::where('id', $incidentSubjectId)->select('subject')->get();

        $landingPage = LandingPageTab::with('tabDetails', 'widgetLayouts')->where('customer_id', $id)->get();

        return view('management::customer-detail-view', compact(
            'customerData',
            'singleCustomer',
            'approverDetails',
            'industrySector',
            'region',
            'regionDetails',
            'requestorName',
            'requestorPosition',
            'requestorEmpno',
            'parentCustomer',
            'cpid',
            'preference',
            'customerAllocattedUsers',
            'viewContractList',
            'qrCodeData',
            'incidentSubjectArr',
            'incidentData',
            'allocatedIncidentSubjects',
            'landingPage',
            'id'

        ), ['lookups' => $this->customerRepository->getLookups(),
            'single_customer_details' => $singleCustomer]);
    }

    public function getAllocatedUserEmail($userId)
    {
        return $this->customerRepository->getAllocatedUserEmail($userId);
    }

    public function resetIncidentLogo(Request $request)
    {
        $customer = Customer::find($request->input('customer_id'));
        if (is_object($customer)) {
            if (Storage::disk('public')->exists($customer->incident_report_logo)) {
                Storage::disk('public')->delete($customer->incident_report_logo);
                $customer->incident_report_logo = null;
                $customer->save();
                return response()->json([
                    'success' => true,
                ]);
            }
        }
        return response()->json([
            'success' => false,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     * @return Response
     */
    public function destroy($id)
    {
        try {
            \DB::beginTransaction();
            $lookup_delete = $this->customerQrcodeLocationRepository->delete($id);
            \DB::commit();
            return response()->json($this->helperService->returnTrueResponse());
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json($this->helperService->returnFalseResponse());
        }
    }

    public function customerProfileStore(CustomerProfileRequest $request, $id)
    {
        try {
            DB::beginTransaction();
            $this->customerViewRepository->customerProfileStore($request, $id);
            DB::commit();
            return response()->json($this->helperService->returnTrueResponse());
        } catch (Exception $e) {
            DB::rollback();
            return response()->json($this->helperService->returnFalseResponse($e));
        }

    }

    public function customerCPIDStore(Request $request, $id)
    {

        try {
            DB::beginTransaction();
            $this->customerViewRepository->customerCPIDStore($request, $id);
            DB::commit();
            return response()->json($this->helperService->returnTrueResponse());
        } catch (Exception $e) {
            DB::rollback();
            return response()->json($this->helperService->returnFalseResponse($e));
        }

    }

    public function customerPreferenceStore(CustomerPreferenceRequest $request, $id)
    {

        try {
            DB::beginTransaction();
            $this->customerViewRepository->customerPreferenceStore($request, $id);
            DB::commit();
            return response()->json($this->helperService->returnTrueResponse());
        } catch (Exception $e) {
            DB::rollback();
            return response()->json($this->helperService->returnFalseResponse($e));
        }

    }

    public function fenceStore(CustomerFenceRequest $request, $id)
    {
        try {
            DB::beginTransaction();
            $this->customerViewRepository->fenceStore($request, $id);
            DB::commit();
            return response()->json($this->helperService->returnTrueResponse());
        } catch (Exception $e) {
            DB::rollback();
            return response()->json($this->helperService->returnFalseResponse($e));
        }

    }

    public function getSingleIncident($id)
    {
        return response()->json($this->customerIncidentSubjectAllocationRepository->get($id));
    }

    public function getIncidentList($id)
    {
        return datatables()->of($this->customerIncidentSubjectAllocationRepository->getAll($id))->addIndexColumn()->toJson();
    }

    public function storeIncidentAllocation(CustomerIncidentSubjectAllocationRequest $request)
    {

        try {
            \DB::beginTransaction();
            //  dd($request->all());
            $lookup = $this->customerIncidentSubjectAllocationRepository->save($request->all());
            \DB::commit();
            return response()->json($this->helperService->returnTrueResponse());
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json($this->helperService->returnFalseResponse());
        }
    }

    public function destroyIncident($id)
    {
        try {
            \DB::beginTransaction();
            $lookup_delete = $this->customerIncidentSubjectAllocationRepository->delete($id);
            \DB::commit();
            return response()->json($this->helperService->returnTrueResponse());
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json($this->helperService->returnFalseResponse());
        }
    }

    public function checkPriority($id)
    {
        $incident_priorities = $this->customerIncidentPriorityRepository->getCustomerIncidentPriority($id);
        if (!empty($incident_priorities)) {
            $arr = [];
            foreach ($incident_priorities as $key => $value) {
                $arr[$key]['id'] = $value['id'];
                $arr[$key]['priority_id'] = $value['priority_id'];
                $arr[$key]['value'] = $value['priority']['value'];
                $arr[$key]['response_time'] = isset($value['response_time']) ? $value['response_time'] / 60 : null;
            }
            return response()->json(array('status' => 1, 'response' => $arr));
        } else {
            $incident_priorities = $this->incidentPriorityLookupRepository->getAll();
            $arr = [];
            foreach ($incident_priorities as $key => $value) {
                $arr[$key]['id'] = null;
                $arr[$key]['priority_id'] = $value->id;
                $arr[$key]['value'] = $value->value;
                $arr[$key]['response_time'] = null;

            }
            return response()->json(array('status' => 2, 'response' => $arr));
        }
    }

    public function storeIncident(CustomerIncidentPriorityRequest $request)
    {

        try {
            \DB::beginTransaction();
            $lookup = $this->customerIncidentPriorityRepository->save($request->all());
            \DB::commit();
            return response()->json($this->helperService->returnTrueResponse());
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json($this->helperService->returnFalseResponse());
        }
    }

    public function storeIncidentRecipient(IncidentRecipientRequest $request)
    {
        try {
            DB::beginTransaction();
            $data_arr = array();
            $priority_arr = ['High' => 'high', 'Medium' => 'medium', 'Low' => 'low'];
            IncidentRecipient::where('customer_id', $request->customer_id)->delete();
            foreach ($request->email as $key => $each_email) {
                foreach ($priority_arr as $priorityLabel => $priority) {
                    if ($request->get($priority)[$key] == '1') {
                        $templateFrom = IncidentRecipient::create(
                            [
                                'customer_id' => $request->customer_id,
                                'priority_id' => IncidentPriorityLookup::where('value', $priorityLabel)->first()->id,
                                'email' => $each_email,
                            ]
                        );
                    }
                }
            }

            DB::commit();
            return response()->json($this->helperService->returnTrueResponse());
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json($this->helperService->returnFalseResponse($e));
        }
    }

    public function listIncidentRecipient($id)
    {
        $reciepint = IncidentRecipient::where('customer_id', $id)->get()->groupBy('email');
        $i = 0;
        $arr = array();
        foreach ($reciepint as $email => $reciepintPriorities) {
            $arr[$i]['email'] = $email;
            foreach ($reciepintPriorities as $key => $eachPriority) {
                $arr[$i][$eachPriority->priority->value] = 1;
            }
            $i++;
        }
        return response()->json(['success' => true, 'data' => $arr]);
    }

    public function getQRcodeDetails($id = null)
    {
        return datatables()->of($this->customerQrcodeLocationRepository->getAll($id))->addIndexColumn()->toJson();
    }

    public function getSingle($id)
    {
        return response()->json($this->customerQrcodeLocationRepository->get($id));
    }

    public function qrCodeLocationStore(CustomerQrCodeRequest $request)
    {
        try {
            DB::beginTransaction();
            $customerQrCode = $this->customerQrcodeLocationRepository->save($request->all());
            DB::commit();
            return response()->json($this->helperService->returnTrueResponse());
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json($this->helperService->returnFalseResponse($e));
        }

    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function getList()
    {

        $customer_list = $this->customerViewRepository->clienLookUps();
        return view('management::customer-list', compact('customer_list'));

    }

    public function getLandingPageDetails(Request $request)
    {

        $data = $this->customerViewRepository->LandingPageDetails($request);
        return $data;

    }

}
