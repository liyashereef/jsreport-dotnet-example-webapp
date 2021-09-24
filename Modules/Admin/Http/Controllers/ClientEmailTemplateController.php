<?php

namespace Modules\Admin\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\HelperService;
use Illuminate\Http\Request;
use Modules\Admin\Http\Requests\ClientEmailTemplateRequest;
use Modules\Admin\Http\Requests\CustomerUseridMappingRequest;
use Modules\Admin\Models\EmailNotificationType;
use Modules\Admin\Repositories\ClientEmailTemplateRepository;
use Modules\Admin\Repositories\CustomerEmployeeAllocationRepository;
use Modules\Admin\Repositories\CustomerRepository;
use Modules\Admin\Repositories\UserRepository;
use Modules\Admin\Repositories\RolesAndPermissionRepository;

class ClientEmailTemplateController extends Controller
{

    protected $repository, $helperService;

    /**
     * Create Repository instance.
     * @param  \App\Repositories\ClientEmailTemplateRepository $clientEmailTemplateRepository
     * @return void
     */
    public function __construct(RolesAndPermissionRepository $rolesAndPermissionRepository, ClientEmailTemplateRepository $clientEmailTemplateRepository, CustomerEmployeeAllocationRepository $customerEmployeeAllocationRepository, UserRepository $userRepository, HelperService $helperService, CustomerRepository $customerRepository)
    {
        $this->repository = $clientEmailTemplateRepository;
        $this->helperService = $helperService;
        $this->customerEmployeeAllocationRepository = $customerEmployeeAllocationRepository;
        $this->userRepository = $userRepository;
        $this->customerRepository = $customerRepository;
        $this->rolesAndPermissionRepository = $rolesAndPermissionRepository;
    }

    /**
     * Load the resource listing Page
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $type = EmailNotificationType::pluck('display_name', 'id')->toArray();
        return view('admin::settings.email-template', compact('type'));
    }

    /**
     * Display details of single resource
     *
     * @param $id
     * @return json
     */
    public function getSingle($id)
    {
        return response()->json($this->repository->get($id));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  $request
     * @return json
     */
    public function store(ClientEmailTemplateRequest $request)
    {
        try {
            \DB::beginTransaction();
            $lookup = $this->repository->save($request->all());
            if ($request->id == null) {
                $message = 'Email template has been successfully created.';
            } else {
                $message = 'Email template has been successfully updated.';
            }
            \DB::commit();
            return response()->json(array('success' => true, 'message' => $message, 'id' => $lookup->id));
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json($this->helperService->returnFalseResponse());
        }
    }

    /**
     * Load the resource listing Page
     *
     * @return \Illuminate\Http\Response
     */
    public function allocation($type_id = null, $customer_id = null)
    {
        $roles= $this->rolesAndPermissionRepository->getRoleList();
        $customer_based = EmailNotificationType::pluck('customer_based', 'id')->toArray();
        $customer_based_flag = EmailNotificationType::where('id', $type_id)->first();
        if (isset($customer_based_flag)) {
            $customer_id = ($customer_based_flag->customer_based == 0) ? 0 : $customer_id;
        }
        $customer = $this->customerRepository->getProjectsDropdownList('all');
        $type = EmailNotificationType::pluck('display_name', 'id')->toArray();
        $users = $this->userRepository->getUserLookup();
        asort($users);
        return view('admin::settings.email-template-allocation', compact('roles', 'customer', 'type', 'users', 'customer_based', 'type_id', 'customer_id'));
    }

    /**
     * Load the resource listing Page
     *
     * @return \Illuminate\Http\Response
     */
    public function allocationList()
    {
        $type = EmailNotificationType::pluck('display_name', 'id')->toArray();
        $customer_based = EmailNotificationType::pluck('customer_based', 'id')->toArray();
        $customer_list = $this->customerEmployeeAllocationRepository->getCustomersList();
        return view('admin::settings.email-template-allocation-list', compact('type', 'customer_list', 'customer_based'));
    }

    /**
     * Load the resource listing Page
     *
     * @return \Illuminate\Http\Response
     */
    public function allocationDatatableLoad($type_id = null, $customer_id = null, $role = null, $role_except = false)
    {

        $typeDetails = EmailNotificationType::find($type_id);
        $customer_based = isset($typeDetails) ? $typeDetails->customer_based : null;
        $data=$this->repository->allocationList($type_id, $customer_id, $role, $role_except, $customer_based);
         return datatables()
            ->eloquent($data)
            ->setTransformer(function ($item) {
                
                $users = data_get($item, 'usersIdMapping.*');
              
                 $arr_users = [];
                foreach ($users as $key => $each_user_id) {
                    if (isset($each_user_id->userDetails) && $each_user_id->userDetails->active) {
                        array_push($arr_users, $each_user_id->userDetails->full_name);
                    }
                }
                if ($item->send_to_areamanagers==1) {
                    $areamanagerlist=$this->customerEmployeeAllocationRepository->allocationList($item->customer_id, ['area_manager'], false, true)->pluck('full_name')->toArray();
                    $arr_users= array_merge($arr_users, $areamanagerlist);
                }
                if ($item->send_to_supervisors==1) {
                    $supervisorlist=$this->customerEmployeeAllocationRepository->allocationList($item->customer_id, ['supervisor'], false, true)->pluck('full_name')->toArray();
                    $arr_users=array_merge($arr_users, $supervisorlist);
                }
                return [
                    'id' => $item->id,
                    'client_name' => isset($item->customer) ? $item->customer->client_name : 'No Customer',
                    'type' => isset($item->type) ? $item->type->type : '--',
                    'customer_based' => isset($item->type) ? $item->type->customer_based : '--',
                    'display_name' => isset($item->type) ? $item->type->display_name : '--',
                    'client_id' => isset($item->customer) ? $item->customer->id : null,
                    'type_id'=>$item->template_id,
                    'user_name'=>$arr_users
                ];
            })
            ->addIndexColumn()
            ->toJson();
    }

    /**
     * Load the resource listing Page
     *
     * @return \Illuminate\Http\Response
     */
    public function getUsers()
    {
        $users = $this->userRepository->getUserLookup();
        return response()->json(['data' => $users->pluck('full_name', 'id')->toArray(), 'success' => true]);
    }

    /**
     * Load the resource listing Page
     *
     * @return \Illuminate\Http\Response
     */
    public function getAllocatedUsers(Request $request)
    {

        try {
            \DB::beginTransaction();
            $result = $this->repository->getAllocationList($request);
            \DB::commit();
            return response()->json(['allocated_list_remove' => $result['allocated_list_remove'], 'full_data' => $result['full_data'], 'success' => true]);
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json($this->helperService->returnFalseResponse());
        }
    }

    public function storeAllocation(CustomerUseridMappingRequest $request)
    {

        try {
            \DB::beginTransaction();
            $lookup = $this->repository->saveAllocation($request);
            \DB::commit();
            return response()->json($this->helperService->returnTrueResponse());
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json($this->helperService->returnFalseResponse());
        }
    }

    public function editAllocatedUsers($template_id, $customer_id = null)
    {

        try {
            \DB::beginTransaction();
            $result = $this->repository->editAllocation($template_id, $customer_id);
            \DB::commit();
            return response()->json(['result' => $result['result'], 'data' => $result['data'], 'area_manager' => $result['area_manager'], 'supervisor' => $result['supervisor'],'role_list' => $result['role_list'], 'success' => true]);
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json($this->helperService->returnFalseResponse());
        }
    }

    public function getHelpers($id)
    {
        try {
            \DB::beginTransaction();
            $includeSelectedDefaultHelpersOnly = [];
            $notificationType = EmailNotificationType::find($id);
            if (!empty($notificationType) && in_array($notificationType->type, array('document_expiry_report_notification', 'user_certificate_expiry_notification_reminder_1', 'user_certificate_expiry_notification_reminder_2', 'user_certificate_expiry_notification_reminder_3',
                'rec_candidate_register_email_script',
                'rec_candidate_login_remainder',
                'rec_candidate_application_process_completed',
                'rec_candidate_application_process_completed_candidate',
                'onboarding_deadline_remainder',
                'rec_candidate_application_evaluation_acknowledgement',
                'rejection_notes_notification_mail','select_interview_notification_mail','reject_for_role_notification_mail','begin_onboarding_notification_mail','expense_approve_notification_remainder','rec_candidate_password_reset','rec_candidate_register_email_script','rec_candidate_login_remainder','ids_remainder_email'))) {
                $includeSelectedDefaultHelpersOnly = array(
                    "{receiverFullName}",
                );
            }
            if (!empty($notificationType) && in_array($notificationType->type, array('time_sheet_approve_notification_mail_1','time_sheet_approve_notification_mail_2','time_sheet_approve_notification_mail_3'))) {
                $includeSelectedDefaultHelpersOnly = array("");
            }
            $result = $this->repository->getHelperList($id, $includeSelectedDefaultHelpersOnly);
            \DB::commit();
            return response()->json(['result' => $result, 'success' => true]);
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json($this->helperService->returnFalseResponse());
        }
    }
}
